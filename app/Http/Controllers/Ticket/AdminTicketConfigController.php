<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\Ticket\TicketEventConfig;
use App\Models\Ticket\EventDay;
use App\Models\Ticket\TicketRegistrationCategory;
use App\Models\Ticket\TicketCategory;
use App\Models\Ticket\TicketSubcategory;
use App\Models\Ticket\TicketType;
use App\Models\Ticket\TicketTypeDayAccess;
use App\Models\Ticket\TicketInventory;
use App\Models\Ticket\TicketCategoryTicketRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminTicketConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!in_array($user->role, ['admin', 'super-admin'])) {
                abort(403, 'Unauthorized access');
            }
            return $next($request);
        });
    }

    /**
     * List all events for ticket configuration
     */
    public function events()
    {
        $events = Events::orderBy('event_year', 'desc')
            ->orderBy('event_name', 'asc')
            ->get();

        return view('tickets.admin.events.index', compact('events'));
    }

    /**
     * Show setup page for an event
     */
    public function setup($eventId)
    {
        $event = Events::findOrFail($eventId);
        $config = TicketEventConfig::where('event_id', $eventId)->first();
        
        // Get setup progress
        $progress = $this->getSetupProgress($eventId);

        return view('tickets.admin.events.setup', compact('event', 'config', 'progress'));
    }

    /**
     * Update event configuration
     */
    public function updateConfig(Request $request, $eventId)
    {
        $validator = Validator::make($request->all(), [
            'auth_policy' => 'required|in:guest,otp_required,login_required',
            'selection_mode' => 'required|in:same_ticket,per_delegate',
            'allow_subcategory' => 'boolean',
            'allow_day_select' => 'boolean',
            'email_cc_json' => 'nullable|json',
            'receipt_pattern' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $emailCc = $request->email_cc_json;
        if (is_string($emailCc)) {
            $emailCc = json_decode($emailCc, true);
        }

        TicketEventConfig::updateOrCreate(
            ['event_id' => $eventId],
            [
                'auth_policy' => $request->auth_policy,
                'selection_mode' => $request->selection_mode,
                'allow_subcategory' => $request->input('allow_subcategory', '0') == '1',
                'allow_day_select' => $request->input('allow_day_select', '0') == '1',
                'email_cc_json' => $emailCc,
                'receipt_pattern' => $request->receipt_pattern,
                'is_active' => $request->input('is_active', '0') == '1',
            ]
        );

        return redirect()->route('admin.tickets.events.setup', $eventId)
            ->with('success', 'Event configuration updated successfully.');
    }

    /**
     * List event days
     */
    public function days($eventId)
    {
        $event = Events::findOrFail($eventId);
        $days = EventDay::where('event_id', $eventId)
            ->orderBy('sort_order')
            ->orderBy('date')
            ->get();

        return view('tickets.admin.events.days', compact('event', 'days'));
    }

    /**
     * Store event day
     */
    public function storeDay(Request $request, $eventId)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'date' => 'required|date',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check for duplicate date
        $exists = EventDay::where('event_id', $eventId)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return back()->withErrors(['date' => 'An event day with this date already exists.'])->withInput();
        }

        EventDay::create([
            'event_id' => $eventId,
            'label' => $request->label,
            'date' => $request->date,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.tickets.events.days', $eventId)
            ->with('success', 'Event day created successfully.');
    }

    /**
     * Update event day
     */
    public function updateDay(Request $request, $eventId, $dayId)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'date' => 'required|date',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $day = EventDay::where('event_id', $eventId)->findOrFail($dayId);

        // Check for duplicate date (excluding current day)
        $exists = EventDay::where('event_id', $eventId)
            ->where('date', $request->date)
            ->where('id', '!=', $dayId)
            ->exists();

        if ($exists) {
            return back()->withErrors(['date' => 'An event day with this date already exists.'])->withInput();
        }

        $day->update([
            'label' => $request->label,
            'date' => $request->date,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.tickets.events.days', $eventId)
            ->with('success', 'Event day updated successfully.');
    }

    /**
     * Delete event day
     */
    public function deleteDay($eventId, $dayId)
    {
        $day = EventDay::where('event_id', $eventId)->findOrFail($dayId);
        $day->delete();

        return redirect()->route('admin.tickets.events.days', $eventId)
            ->with('success', 'Event day deleted successfully.');
    }

    /**
     * Generate all days from event start_date to end_date
     */
    public function generateAllDays($eventId)
    {
        $event = Events::findOrFail($eventId);
        
        if (!$event->start_date || !$event->end_date) {
            return redirect()->route('admin.tickets.events.days', $eventId)
                ->with('error', 'Event start date and end date must be set to generate days.');
        }

        $startDate = \Carbon\Carbon::parse($event->start_date);
        $endDate = \Carbon\Carbon::parse($event->end_date);
        
        if ($startDate->gt($endDate)) {
            return redirect()->route('admin.tickets.events.days', $eventId)
                ->with('error', 'Start date cannot be after end date.');
        }

        $daysCreated = 0;
        $currentDate = $startDate->copy();
        $dayNumber = 1;

        while ($currentDate->lte($endDate)) {
            // Check if day already exists
            $exists = EventDay::where('event_id', $eventId)
                ->where('date', $currentDate->format('Y-m-d'))
                ->exists();

            if (!$exists) {
                EventDay::create([
                    'event_id' => $eventId,
                    'label' => 'Day ' . $dayNumber,
                    'date' => $currentDate->format('Y-m-d'),
                    'sort_order' => $dayNumber,
                ]);
                $daysCreated++;
            }

            $currentDate->addDay();
            $dayNumber++;
        }

        if ($daysCreated > 0) {
            return redirect()->route('admin.tickets.events.days', $eventId)
                ->with('success', "Successfully generated {$daysCreated} event day(s) from {$startDate->format('M d, Y')} to {$endDate->format('M d, Y')}.");
        } else {
            return redirect()->route('admin.tickets.events.days', $eventId)
                ->with('info', 'All days for this date range already exist.');
        }
    }

    /**
     * List registration categories
     */
    public function registrationCategories($eventId)
    {
        $event = Events::findOrFail($eventId);
        $categories = TicketRegistrationCategory::where('event_id', $eventId)
            ->orderBy('sort_order')
            ->get();

        return view('tickets.admin.events.registration-categories', compact('event', 'categories'));
    }

    /**
     * Store registration category
     */
    public function storeRegistrationCategory(Request $request, $eventId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        TicketRegistrationCategory::create([
            'event_id' => $eventId,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->input('is_active', '0') == '1',
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.tickets.events.registration-categories', $eventId)
            ->with('success', 'Registration category created successfully.');
    }

    /**
     * Update registration category
     */
    public function updateRegistrationCategory(Request $request, $eventId, $categoryId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $category = TicketRegistrationCategory::where('event_id', $eventId)->findOrFail($categoryId);
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->input('is_active', '0') == '1',
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.tickets.events.registration-categories', $eventId)
            ->with('success', 'Registration category updated successfully.');
    }

    /**
     * Delete registration category
     */
    public function deleteRegistrationCategory($eventId, $categoryId)
    {
        $category = TicketRegistrationCategory::where('event_id', $eventId)->findOrFail($categoryId);
        $category->delete();

        return redirect()->route('admin.tickets.events.registration-categories', $eventId)
            ->with('success', 'Registration category deleted successfully.');
    }

    /**
     * List ticket categories
     */
    public function categories($eventId)
    {
        $event = Events::findOrFail($eventId);
        $categories = TicketCategory::where('event_id', $eventId)
            ->with('subcategories')
            ->orderBy('sort_order')
            ->get();

        return view('tickets.admin.events.categories', compact('event', 'categories'));
    }

    /**
     * Store ticket category
     */
    public function storeCategory(Request $request, $eventId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_exhibitor_only' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        TicketCategory::create([
            'event_id' => $eventId,
            'name' => $request->name,
            'description' => $request->description,
            'is_exhibitor_only' => $request->has('is_exhibitor_only') ? (bool) $request->is_exhibitor_only : false,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.tickets.events.categories', $eventId)
            ->with('success', 'Ticket category created successfully.');
    }

    /**
     * Update ticket category
     */
    public function updateCategory(Request $request, $eventId, $categoryId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_exhibitor_only' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $category = TicketCategory::where('event_id', $eventId)->findOrFail($categoryId);
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_exhibitor_only' => $request->has('is_exhibitor_only') ? (bool) $request->is_exhibitor_only : false,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.tickets.events.categories', $eventId)
            ->with('success', 'Ticket category updated successfully.');
    }

    /**
     * Delete ticket category
     */
    public function deleteCategory($eventId, $categoryId)
    {
        $category = TicketCategory::where('event_id', $eventId)->findOrFail($categoryId);
        $category->delete();

        return redirect()->route('admin.tickets.events.categories', $eventId)
            ->with('success', 'Ticket category deleted successfully.');
    }

    /**
     * List ticket subcategories
     */
    public function subcategories($eventId, $categoryId)
    {
        $event = Events::findOrFail($eventId);
        $category = TicketCategory::where('event_id', $eventId)->findOrFail($categoryId);
        $subcategories = TicketSubcategory::where('category_id', $categoryId)
            ->orderBy('sort_order')
            ->get();

        return view('tickets.admin.events.subcategories', compact('event', 'category', 'subcategories'));
    }

    /**
     * Store ticket subcategory
     */
    public function storeSubcategory(Request $request, $eventId, $categoryId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        TicketSubcategory::create([
            'category_id' => $categoryId,
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.tickets.events.subcategories', [$eventId, $categoryId])
            ->with('success', 'Ticket subcategory created successfully.');
    }

    /**
     * Update ticket subcategory
     */
    public function updateSubcategory(Request $request, $eventId, $subcategoryId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $subcategory = TicketSubcategory::findOrFail($subcategoryId);
        $subcategory->update([
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.tickets.events.subcategories', [$eventId, $subcategory->category_id])
            ->with('success', 'Ticket subcategory updated successfully.');
    }

    /**
     * Delete ticket subcategory
     */
    public function deleteSubcategory($eventId, $subcategoryId)
    {
        $subcategory = TicketSubcategory::findOrFail($subcategoryId);
        $categoryId = $subcategory->category_id;
        $subcategory->delete();

        return redirect()->route('admin.tickets.events.subcategories', [$eventId, $categoryId])
            ->with('success', 'Ticket subcategory deleted successfully.');
    }

    /**
     * List ticket types
     */
    public function ticketTypes($eventId)
    {
        $event = Events::findOrFail($eventId);
        $ticketTypes = TicketType::where('event_id', $eventId)
            ->with(['category', 'subcategory', 'eventDays'])
            ->orderBy('sort_order')
            ->get();

        $categories = TicketCategory::where('event_id', $eventId)->get();
        $eventDays = EventDay::where('event_id', $eventId)->get();

        return view('tickets.admin.events.ticket-types.index', compact('event', 'ticketTypes', 'categories', 'eventDays'));
    }

    /**
     * Show create ticket type form
     */
    public function createTicketType($eventId)
    {
        $event = Events::findOrFail($eventId);
        $categories = TicketCategory::where('event_id', $eventId)->with('subcategories')->get();
        $eventDays = EventDay::where('event_id', $eventId)->get();

        return view('tickets.admin.events.ticket-types.create', compact('event', 'categories', 'eventDays'));
    }

    /**
     * Store ticket type
     */
    public function storeTicketType(Request $request, $eventId)
    {
        $availableFor = $request->input('available_for', 'both');
        
        // Build conditional validation rules based on available_for
        $rules = [
            'category_id' => 'required|exists:ticket_categories,id',
            'subcategory_id' => 'nullable|exists:ticket_subcategories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'available_for' => 'nullable|in:both,indian_only,international_only',
            'early_bird_price_national' => 'nullable|numeric|min:0',
            'early_bird_price_international' => 'nullable|numeric|min:0',
            'per_day_price_national' => 'nullable|numeric|min:0',
            'per_day_price_international' => 'nullable|numeric|min:0',
            'early_bird_end_date' => 'nullable|date|after_or_equal:today',
            'capacity' => 'nullable|integer|min:1',
            'sale_start_at' => 'nullable|date',
            'sale_end_at' => 'nullable|date|after:sale_start_at',
            'is_active' => 'boolean',
            'enable_day_selection' => 'boolean',
            'all_days_access' => 'boolean',
            'sort_order' => 'nullable|integer',
            'event_day_ids' => 'nullable|array',
            'event_day_ids.*' => 'exists:event_days,id',
        ];
        
        // Conditionally require national/international prices based on availability
        if ($availableFor === 'indian_only') {
            $rules['regular_price_national'] = 'required|numeric|min:0';
            $rules['regular_price_international'] = 'nullable|numeric|min:0';
        } elseif ($availableFor === 'international_only') {
            $rules['regular_price_national'] = 'nullable|numeric|min:0';
            $rules['regular_price_international'] = 'required|numeric|min:0';
        } else {
            $rules['regular_price_national'] = 'required|numeric|min:0';
            $rules['regular_price_international'] = 'required|numeric|min:0';
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request, $eventId) {
            $ticketType = TicketType::create([
                'event_id' => $eventId,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'name' => $request->name,
                'description' => $request->description,
                'available_for' => $request->input('available_for', 'both'),
                'early_bird_price' => $request->early_bird_price_national, // Fallback for backward compatibility
                'early_bird_price_national' => $request->early_bird_price_national,
                'early_bird_price_international' => $request->early_bird_price_international,
                'regular_price' => $request->regular_price_national, // Fallback for backward compatibility
                'regular_price_national' => $request->regular_price_national,
                'regular_price_international' => $request->regular_price_international,
                'per_day_price_national' => $request->per_day_price_national,
                'per_day_price_international' => $request->per_day_price_international,
                'early_bird_end_date' => $request->early_bird_end_date,
                'capacity' => $request->capacity,
                'sale_start_at' => $request->sale_start_at,
                'sale_end_at' => $request->sale_end_at,
                'is_active' => $request->input('is_active', '0') == '1',
                'enable_day_selection' => $request->input('enable_day_selection', '0') == '1',
                'all_days_access' => $request->input('all_days_access', '0') == '1',
                'sort_order' => $request->sort_order ?? 0,
            ]);

            // Create inventory record
            TicketInventory::create([
                'ticket_type_id' => $ticketType->id,
                'reserved_qty' => 0,
                'sold_qty' => 0,
            ]);

            // Handle day access - only attach specific days if day selection is enabled
            if ($request->input('enable_day_selection', '0') == '1') {
                if ($request->has('event_day_ids') && is_array($request->event_day_ids)) {
                    // Attach selected days
                    $ticketType->eventDays()->attach($request->event_day_ids);
                }
            } else {
                // Day selection disabled - attach all event days by default
                $allDays = EventDay::where('event_id', $eventId)->pluck('id');
                $ticketType->eventDays()->attach($allDays);
            }
        });

        return redirect()->route('admin.tickets.events.ticket-types', $eventId)
            ->with('success', 'Ticket type created successfully.');
    }

    /**
     * Show edit ticket type form
     */
    public function editTicketType($eventId, $ticketTypeId)
    {
        $event = Events::findOrFail($eventId);
        $ticketType = TicketType::where('event_id', $eventId)
            ->with(['category.subcategories', 'eventDays'])
            ->findOrFail($ticketTypeId);
        $categories = TicketCategory::where('event_id', $eventId)->with('subcategories')->get();
        $eventDays = EventDay::where('event_id', $eventId)->get();

        return view('tickets.admin.events.ticket-types.edit', compact('event', 'ticketType', 'categories', 'eventDays'));
    }

    /**
     * Update ticket type
     */
    public function updateTicketType(Request $request, $eventId, $ticketTypeId)
    {
        $availableFor = $request->input('available_for', 'both');
        
        // Build conditional validation rules based on available_for
        $rules = [
            'category_id' => 'required|exists:ticket_categories,id',
            'subcategory_id' => 'nullable|exists:ticket_subcategories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'available_for' => 'nullable|in:both,indian_only,international_only',
            'early_bird_price_national' => 'nullable|numeric|min:0',
            'early_bird_price_international' => 'nullable|numeric|min:0',
            'per_day_price_national' => 'nullable|numeric|min:0',
            'per_day_price_international' => 'nullable|numeric|min:0',
            'early_bird_end_date' => 'nullable|date',
            'capacity' => 'nullable|integer|min:1',
            'sale_start_at' => 'nullable|date',
            'sale_end_at' => 'nullable|date|after:sale_start_at',
            'is_active' => 'boolean',
            'enable_day_selection' => 'boolean',
            'all_days_access' => 'boolean',
            'sort_order' => 'nullable|integer',
            'event_day_ids' => 'nullable|array',
            'event_day_ids.*' => 'exists:event_days,id',
        ];
        
        // Conditionally require national/international prices based on availability
        if ($availableFor === 'indian_only') {
            $rules['regular_price_national'] = 'required|numeric|min:0';
            $rules['regular_price_international'] = 'nullable|numeric|min:0';
        } elseif ($availableFor === 'international_only') {
            $rules['regular_price_national'] = 'nullable|numeric|min:0';
            $rules['regular_price_international'] = 'required|numeric|min:0';
        } else {
            $rules['regular_price_national'] = 'required|numeric|min:0';
            $rules['regular_price_international'] = 'required|numeric|min:0';
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $ticketType = TicketType::where('event_id', $eventId)->findOrFail($ticketTypeId);

        $ticketType->update([
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'name' => $request->name,
            'description' => $request->description,
            'available_for' => $request->input('available_for', 'both'),
            'early_bird_price' => $request->early_bird_price_national, // Fallback for backward compatibility
            'early_bird_price_national' => $request->early_bird_price_national,
            'early_bird_price_international' => $request->early_bird_price_international,
            'regular_price' => $request->regular_price_national, // Fallback for backward compatibility
            'regular_price_national' => $request->regular_price_national,
            'regular_price_international' => $request->regular_price_international,
            'per_day_price_national' => $request->per_day_price_national,
            'per_day_price_international' => $request->per_day_price_international,
            'early_bird_end_date' => $request->early_bird_end_date,
            'capacity' => $request->capacity,
            'sale_start_at' => $request->sale_start_at,
            'sale_end_at' => $request->sale_end_at,
            'is_active' => $request->input('is_active', '0') == '1',
            'enable_day_selection' => $request->input('enable_day_selection', '0') == '1',
            'all_days_access' => $request->input('all_days_access', '0') == '1',
            'sort_order' => $request->sort_order ?? 0,
        ]);

        // Handle day access - only sync if day selection is enabled
        if ($request->input('enable_day_selection', '0') == '1') {
            if ($request->has('event_day_ids') && is_array($request->event_day_ids)) {
                // Sync selected days
                $ticketType->eventDays()->sync($request->event_day_ids);
            } else {
                // No days selected, detach all
                $ticketType->eventDays()->detach();
            }
        } else {
            // Day selection disabled - attach all event days by default
            $allDays = EventDay::where('event_id', $eventId)->pluck('id');
            $ticketType->eventDays()->sync($allDays);
        }

        return redirect()->route('admin.tickets.events.ticket-types', $eventId)
            ->with('success', 'Ticket type updated successfully.');
    }

    /**
     * Delete ticket type
     */
    public function deleteTicketType($eventId, $ticketTypeId)
    {
        $ticketType = TicketType::where('event_id', $eventId)->findOrFail($ticketTypeId);
        $ticketType->delete();

        return redirect()->route('admin.tickets.events.ticket-types', $eventId)
            ->with('success', 'Ticket type deleted successfully.');
    }

    /**
     * List ticket rules
     */
    public function rules($eventId)
    {
        $event = Events::findOrFail($eventId);
        $rules = TicketCategoryTicketRule::whereHas('registrationCategory', function($q) use ($eventId) {
            $q->where('event_id', $eventId);
        })->with(['registrationCategory', 'ticketType.category', 'ticketType.subcategory', 'subcategory'])->get();

        $registrationCategories = TicketRegistrationCategory::where('event_id', $eventId)->where('is_active', true)->get();
        $ticketTypes = TicketType::where('event_id', $eventId)->with(['category', 'subcategory'])->get();
        $eventDays = EventDay::where('event_id', $eventId)->orderBy('sort_order')->orderBy('date')->get();

        return view('tickets.admin.events.rules', compact('event', 'rules', 'registrationCategories', 'ticketTypes', 'eventDays'));
    }

    /**
     * Store ticket rule
     */
    public function storeRule(Request $request, $eventId)
    {
        $validator = Validator::make($request->all(), [
            'registration_category_id' => 'required|exists:ticket_registration_categories,id',
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'subcategory_id' => 'nullable|exists:ticket_subcategories,id',
            'allowed_days_json' => 'nullable|array',
            'allowed_days_json.*' => 'exists:event_days,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        TicketCategoryTicketRule::create([
            'registration_category_id' => $request->registration_category_id,
            'ticket_type_id' => $request->ticket_type_id,
            'subcategory_id' => $request->subcategory_id,
            'allowed_days_json' => $request->allowed_days_json ?? [],
        ]);

        return redirect()->route('admin.tickets.events.rules', $eventId)
            ->with('success', 'Ticket rule created successfully.');
    }

    /**
     * Delete ticket rule
     */
    public function deleteRule($eventId, $ruleId)
    {
        $rule = TicketCategoryTicketRule::findOrFail($ruleId);
        $rule->delete();

        return redirect()->route('admin.tickets.events.rules', $eventId)
            ->with('success', 'Ticket rule deleted successfully.');
    }

    /**
     * Get setup progress for an event
     */
    private function getSetupProgress($eventId)
    {
        $config = TicketEventConfig::where('event_id', $eventId)->first();
        $hasDays = EventDay::where('event_id', $eventId)->exists();
        $hasRegistrationCategories = TicketRegistrationCategory::where('event_id', $eventId)->exists();
        $hasTicketCategories = TicketCategory::where('event_id', $eventId)->exists();
        $hasTicketTypes = TicketType::where('event_id', $eventId)->exists();

        $total = 5;
        $completed = 0;

        if ($config) $completed++;
        if ($hasDays) $completed++;
        if ($hasRegistrationCategories) $completed++;
        if ($hasTicketCategories) $completed++;
        if ($hasTicketTypes) $completed++;

        return [
            'completed' => $completed,
            'total' => $total,
            'percentage' => ($completed / $total) * 100,
            'is_complete' => $completed === $total,
        ];
    }
}

