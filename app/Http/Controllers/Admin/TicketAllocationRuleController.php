<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketAllocationRule;
use App\Models\Events;
use App\Models\Ticket\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TicketAllocationRuleController extends Controller
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
     * Display a listing of allocation rules
     */
    public function index(Request $request)
    {
        $query = TicketAllocationRule::with('event');

        // Filter by event
        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by application type
        if ($request->has('application_type') && $request->application_type) {
            $query->where('application_type', $request->application_type);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        } else {
            $query->where('is_active', true);
        }

        $rules = $query->orderBy('sort_order')
            ->orderBy('booth_area_min')
            ->paginate(20);

        $events = Events::orderBy('event_year', 'desc')->get();
        $applicationTypes = ['exhibitor-registration', 'startup-zone'];

        return view('admin.ticket-allocation-rules.index', compact('rules', 'events', 'applicationTypes'));
    }

    /**
     * Show the form for creating a new allocation rule
     */
    public function create(Request $request)
    {
        $events = Events::orderBy('event_year', 'desc')->get();
        $applicationTypes = ['exhibitor-registration', 'startup-zone'];
        $ticketTypes = TicketType::where('is_active', true)
            ->with(['category', 'subcategory', 'event'])
            ->orderBy('name')
            ->get();

        $eventId = $request->get('event_id');
        
        // Get predefined special booth types from config
        $specialBoothTypes = array_keys(config('ticket_allocation.special_booth_types', []));

        return view('admin.ticket-allocation-rules.create', compact('events', 'applicationTypes', 'ticketTypes', 'eventId', 'specialBoothTypes'));
    }

    /**
     * Store a newly created allocation rule
     */
    public function store(Request $request)
    {
        // Determine if using special booth type or numeric range
        $useSpecialType = !empty($request->booth_type);
        
        $rules = [
            'event_id' => 'nullable|exists:events,id',
            'application_type' => 'nullable|in:exhibitor-registration,startup-zone',
            'ticket_allocations' => 'required|array',
            'ticket_allocations.*' => 'required|integer|min:0|exists:ticket_types,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];

        if ($useSpecialType) {
            // For special booth types, booth_type is required, booth_area fields are null
            $rules['booth_type'] = 'required|string|max:255';
            $rules['booth_area_min'] = 'nullable|integer|min:0';
            $rules['booth_area_max'] = 'nullable|integer|min:0';
        } else {
            // For numeric ranges, booth_area fields are required, booth_type is null
            $rules['booth_type'] = 'nullable|string|max:255';
            $rules['booth_area_min'] = 'required|integer|min:0';
            $rules['booth_area_max'] = 'required|integer|min:0|gte:booth_area_min';
        }

        $validator = Validator::make($request->all(), $rules);

        // Validate no duplicate special booth types for same event/application_type
        if ($useSpecialType) {
            $duplicate = TicketAllocationRule::where(function($q) use ($request) {
                if ($request->event_id) {
                    $q->where('event_id', $request->event_id)->orWhereNull('event_id');
                }
                if ($request->application_type) {
                    $q->where('application_type', $request->application_type)->orWhereNull('application_type');
                }
            })
            ->where('booth_type', $request->booth_type)
            ->where('is_active', true)
            ->exists();

            if ($duplicate) {
                return back()->withErrors(['booth_type' => 'A rule for this special booth type already exists.'])->withInput();
            }
        }

        // Validate no overlapping ranges for same event/application_type (only for numeric ranges)
        if (!$useSpecialType && ($request->event_id || $request->application_type)) {
            $overlapping = TicketAllocationRule::where(function($q) use ($request) {
                if ($request->event_id) {
                    $q->where('event_id', $request->event_id)->orWhereNull('event_id');
                }
                if ($request->application_type) {
                    $q->where('application_type', $request->application_type)->orWhereNull('application_type');
                }
            })
            ->whereNull('booth_type') // Only check numeric range rules
            ->where(function($q) use ($request) {
                $q->whereBetween('booth_area_min', [$request->booth_area_min, $request->booth_area_max])
                  ->orWhereBetween('booth_area_max', [$request->booth_area_min, $request->booth_area_max])
                  ->orWhere(function($q) use ($request) {
                      $q->where('booth_area_min', '<=', $request->booth_area_min)
                        ->where('booth_area_max', '>=', $request->booth_area_max);
                  });
            })
            ->where('is_active', true)
            ->exists();

            if ($overlapping) {
                return back()->withErrors(['booth_area_min' => 'This booth area range overlaps with an existing active rule.'])->withInput();
            }
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Convert ticket_allocations array to JSON format: {"ticket_type_id": count}
            $ticketAllocations = [];
            foreach ($request->ticket_allocations as $ticketTypeId => $count) {
                if ($count > 0) {
                    $ticketAllocations[$ticketTypeId] = (int) $count;
                }
            }

            if (empty($ticketAllocations)) {
                return back()->withErrors(['ticket_allocations' => 'At least one ticket type with count > 0 is required.'])->withInput();
            }

            $rule = TicketAllocationRule::create([
                'event_id' => $request->event_id ?: null,
                'application_type' => $request->application_type ?: null,
                'booth_type' => $useSpecialType ? trim($request->booth_type) : null,
                'booth_area_min' => $useSpecialType ? null : $request->booth_area_min,
                'booth_area_max' => $useSpecialType ? null : $request->booth_area_max,
                'ticket_allocations' => $ticketAllocations,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
            ]);

            Log::info('Ticket allocation rule created', [
                'rule_id' => $rule->id,
                'event_id' => $rule->event_id,
                'application_type' => $rule->application_type,
                'booth_type' => $rule->booth_type,
                'booth_area_range' => $rule->booth_type ? $rule->booth_type : "{$rule->booth_area_min}-{$rule->booth_area_max}",
            ]);

            return redirect()->route('admin.ticket-allocation-rules.index')
                ->with('success', 'Allocation rule created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create ticket allocation rule', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Failed to create allocation rule: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified allocation rule
     */
    public function show($id)
    {
        $rule = TicketAllocationRule::with(['event'])->findOrFail($id);
        $ticketTypes = TicketType::whereIn('id', array_keys($rule->ticket_allocations ?? []))
            ->with(['category', 'subcategory', 'event'])
            ->get();

        return view('admin.ticket-allocation-rules.show', compact('rule', 'ticketTypes'));
    }

    /**
     * Show the form for editing the specified allocation rule
     */
    public function edit($id)
    {
        $rule = TicketAllocationRule::findOrFail($id);
        $events = Events::orderBy('event_year', 'desc')->get();
        $applicationTypes = ['exhibitor-registration', 'startup-zone'];
        $ticketTypes = TicketType::where('is_active', true)
            ->with(['category', 'subcategory', 'event'])
            ->orderBy('name')
            ->get();
        
        // Get predefined special booth types from config
        $specialBoothTypes = array_keys(config('ticket_allocation.special_booth_types', []));

        return view('admin.ticket-allocation-rules.edit', compact('rule', 'events', 'applicationTypes', 'ticketTypes', 'specialBoothTypes'));
    }

    /**
     * Update the specified allocation rule
     */
    public function update(Request $request, $id)
    {
        $rule = TicketAllocationRule::findOrFail($id);

        // Determine if using special booth type or numeric range
        $useSpecialType = !empty($request->booth_type);
        
        $rules = [
            'event_id' => 'nullable|exists:events,id',
            'application_type' => 'nullable|in:exhibitor-registration,startup-zone',
            'ticket_allocations' => 'required|array',
            'ticket_allocations.*' => 'required|integer|min:0|exists:ticket_types,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];

        if ($useSpecialType) {
            $rules['booth_type'] = 'required|string|max:255';
            $rules['booth_area_min'] = 'nullable|integer|min:0';
            $rules['booth_area_max'] = 'nullable|integer|min:0';
        } else {
            $rules['booth_type'] = 'nullable|string|max:255';
            $rules['booth_area_min'] = 'required|integer|min:0';
            $rules['booth_area_max'] = 'required|integer|min:0|gte:booth_area_min';
        }

        $validator = Validator::make($request->all(), $rules);

        // Validate no duplicate special booth types (excluding current rule)
        if ($useSpecialType) {
            $duplicate = TicketAllocationRule::where('id', '!=', $id)
                ->where(function($q) use ($request) {
                    if ($request->event_id) {
                        $q->where('event_id', $request->event_id)->orWhereNull('event_id');
                    }
                    if ($request->application_type) {
                        $q->where('application_type', $request->application_type)->orWhereNull('application_type');
                    }
                })
                ->where('booth_type', $request->booth_type)
                ->where('is_active', true)
                ->exists();

            if ($duplicate) {
                return back()->withErrors(['booth_type' => 'A rule for this special booth type already exists.'])->withInput();
            }
        }

        // Validate no overlapping ranges (excluding current rule, only for numeric ranges)
        if (!$useSpecialType && ($request->event_id || $request->application_type)) {
            $overlapping = TicketAllocationRule::where('id', '!=', $id)
                ->where(function($q) use ($request) {
                    if ($request->event_id) {
                        $q->where('event_id', $request->event_id)->orWhereNull('event_id');
                    }
                    if ($request->application_type) {
                        $q->where('application_type', $request->application_type)->orWhereNull('application_type');
                    }
                })
                ->whereNull('booth_type') // Only check numeric range rules
                ->where(function($q) use ($request) {
                    $q->whereBetween('booth_area_min', [$request->booth_area_min, $request->booth_area_max])
                      ->orWhereBetween('booth_area_max', [$request->booth_area_min, $request->booth_area_max])
                      ->orWhere(function($q) use ($request) {
                          $q->where('booth_area_min', '<=', $request->booth_area_min)
                            ->where('booth_area_max', '>=', $request->booth_area_max);
                      });
                })
                ->where('is_active', true)
                ->exists();

            if ($overlapping) {
                return back()->withErrors(['booth_area_min' => 'This booth area range overlaps with an existing active rule.'])->withInput();
            }
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Convert ticket_allocations array to JSON format
            $ticketAllocations = [];
            foreach ($request->ticket_allocations as $ticketTypeId => $count) {
                if ($count > 0) {
                    $ticketAllocations[$ticketTypeId] = (int) $count;
                }
            }

            if (empty($ticketAllocations)) {
                return back()->withErrors(['ticket_allocations' => 'At least one ticket type with count > 0 is required.'])->withInput();
            }

            $rule->update([
                'event_id' => $request->event_id ?: null,
                'application_type' => $request->application_type ?: null,
                'booth_type' => $useSpecialType ? trim($request->booth_type) : null,
                'booth_area_min' => $useSpecialType ? null : $request->booth_area_min,
                'booth_area_max' => $useSpecialType ? null : $request->booth_area_max,
                'ticket_allocations' => $ticketAllocations,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
            ]);

            Log::info('Ticket allocation rule updated', [
                'rule_id' => $rule->id,
                'event_id' => $rule->event_id,
                'application_type' => $rule->application_type,
                'booth_type' => $rule->booth_type,
            ]);

            return redirect()->route('admin.ticket-allocation-rules.index')
                ->with('success', 'Allocation rule updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update ticket allocation rule', [
                'rule_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to update allocation rule: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified allocation rule
     */
    public function destroy($id)
    {
        try {
            $rule = TicketAllocationRule::findOrFail($id);
            $rule->delete();

            Log::info('Ticket allocation rule deleted', ['rule_id' => $id]);

            return redirect()->route('admin.ticket-allocation-rules.index')
                ->with('success', 'Allocation rule deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete ticket allocation rule', [
                'rule_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Failed to delete allocation rule: ' . $e->getMessage()]);
        }
    }

    /**
     * Preview allocation for a test booth area
     */
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booth_area' => 'required|numeric|min:0',
            'event_id' => 'nullable|exists:events,id',
            'application_type' => 'nullable|in:exhibitor-registration,startup-zone',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            $allocationData = \App\Helpers\TicketAllocationHelper::calculateAllocationFromBoothArea(
                $request->booth_area,
                $request->event_id,
                $request->application_type
            );

            $ticketAllocations = $allocationData['ticket_allocations'] ?? [];
            $ticketTypes = TicketType::whereIn('id', array_keys($ticketAllocations))
                ->with(['category', 'subcategory'])
                ->get();

            $result = [];
            foreach ($ticketAllocations as $ticketTypeId => $count) {
                $ticketType = $ticketTypes->firstWhere('id', $ticketTypeId);
                if ($ticketType) {
                    $result[] = [
                        'id' => $ticketType->id,
                        'name' => $ticketType->name,
                        'category' => $ticketType->category->name ?? 'N/A',
                        'count' => $count,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'booth_area' => $request->booth_area,
                'allocations' => $result,
                'total_allocated' => array_sum($ticketAllocations),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to preview allocation: ' . $e->getMessage()], 500);
        }
    }
}
