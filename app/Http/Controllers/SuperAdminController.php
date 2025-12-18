<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Events;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'super-admin') {
                abort(403, 'Unauthorized access');
            }
            return $next($request);
        });
    }

    public function eventConfig()
    {
        $config = DB::table('event_configurations')->where('id', 1)->first();
        return view('super-admin.event-config', compact('config'));
    }

    public function updateEventConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_name' => 'required|string|max:255',
            'event_year' => 'required|string|max:10',
            'short_name' => 'required|string|max:50',
            'event_website' => 'nullable|url',
            'event_date_start' => 'nullable|string',
            'event_date_end' => 'nullable|string',
            'event_venue' => 'nullable|string',
            'organizer_name' => 'nullable|string|max:255',
            'organizer_email' => 'nullable|email|max:255',
            'organizer_phone' => 'nullable|string|max:50',
            'organizer_website' => 'nullable|url',
            'organizer_address' => 'nullable|string',
            'shell_scheme_rate' => 'nullable|numeric|min:0',
            'raw_space_rate' => 'nullable|numeric|min:0',
            'ind_processing_charge' => 'nullable|numeric|min:0|max:100',
            'int_processing_charge' => 'nullable|numeric|min:0|max:100',
            'gst_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'event_name', 'event_year', 'short_name', 'event_website',
            'event_date_start', 'event_date_end', 'event_venue',
            'organizer_name', 'organizer_email', 'organizer_phone',
            'organizer_website', 'organizer_address',
            'shell_scheme_rate', 'raw_space_rate',
            'ind_processing_charge', 'int_processing_charge', 'gst_rate'
        ]);

        if ($request->has('social_links')) {
            $data['social_links'] = json_encode($request->social_links);
        }

        $data['updated_at'] = now();

        DB::table('event_configurations')->updateOrInsert(
            ['id' => 1],
            $data
        );

        // Update constants.php file
        $this->updateConstantsFile($data);

        return back()->with('success', 'Event configuration updated successfully!');
    }

    public function sectors()
    {
        $sectors = DB::table('sectors')->orderBy('sort_order')->orderBy('name')->get();
        $subSectors = DB::table('sub_sectors')->orderBy('sort_order')->orderBy('name')->get();
        $orgTypes = DB::table('organization_types')->orderBy('sort_order')->orderBy('name')->get();
        
        return view('super-admin.sectors', compact('sectors', 'subSectors', 'orgTypes'));
    }

    public function addSector(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sectors,name',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        DB::table('sectors')->insert([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Sector added successfully!');
    }

    public function updateSector(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sectors,name,' . $id,
            'sort_order' => 'nullable|integer|min:0',
        ]);

        DB::table('sectors')->where('id', $id)->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Sector updated successfully!');
    }

    public function deleteSector(Request $request, $id)
    {
        DB::table('sectors')->where('id', $id)->delete();
        return back()->with('success', 'Sector deleted successfully!');
    }

    public function addSubSector(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sub_sectors,name',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        DB::table('sub_sectors')->insert([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Sub-sector added successfully!');
    }

    public function updateSubSector(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sub_sectors,name,' . $id,
            'sort_order' => 'nullable|integer|min:0',
        ]);

        DB::table('sub_sectors')->where('id', $id)->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Sub-sector updated successfully!');
    }

    public function deleteSubSector(Request $request, $id)
    {
        DB::table('sub_sectors')->where('id', $id)->delete();
        return back()->with('success', 'Sub-sector deleted successfully!');
    }

    public function addOrgType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:organization_types,name',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        DB::table('organization_types')->insert([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Organization type added successfully!');
    }

    public function updateOrgType(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:organization_types,name,' . $id,
            'sort_order' => 'nullable|integer|min:0',
        ]);

        DB::table('organization_types')->where('id', $id)->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Organization type updated successfully!');
    }

    public function deleteOrgType(Request $request, $id)
    {
        DB::table('organization_types')->where('id', $id)->delete();
        return back()->with('success', 'Organization type deleted successfully!');
    }

    // Event CRUD Methods
    public function events()
    {
        $events = Events::orderBy('event_year', 'desc')
            ->orderBy('event_name', 'asc')
            ->get();
        return view('super-admin.events.index', compact('events'));
    }

    public function createEvent()
    {
        return view('super-admin.events.create');
    }

    public function storeEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_name' => 'required|string|max:255',
            'event_year' => 'required|string|max:10',
            'event_date' => 'required|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'event_location' => 'required|string|max:255',
            'event_description' => 'required|string',
            'event_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|in:upcoming,ongoing,over',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'event_name', 'event_year', 'event_date', 'start_date', 'end_date',
            'event_location', 'event_description', 'status'
        ]);
        
        // Set default status if not provided
        if (!isset($data['status']) || empty($data['status'])) {
            $data['status'] = 'upcoming';
        }

        // Generate slug
        $slug = Str::slug($request->event_name . '-' . $request->event_year);
        $data['slug'] = $slug;

        // Handle image upload
        if ($request->hasFile('event_image')) {
            $image = $request->file('event_image');
            $imageName = time() . '_' . Str::slug($request->event_name) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/events'), $imageName);
            $data['event_image'] = 'uploads/events/' . $imageName;
        } else {
            $data['event_image'] = 'default-event.jpg';
        }

        Events::create($data);

        return redirect()->route('super-admin.events')->with('success', 'Event created successfully!');
    }

    public function editEvent($id)
    {
        $event = Events::findOrFail($id);
        return view('super-admin.events.edit', compact('event'));
    }

    public function updateEvent(Request $request, $id)
    {
        $event = Events::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'event_name' => 'required|string|max:255',
            'event_year' => 'required|string|max:10',
            'event_date' => 'required|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'event_location' => 'required|string|max:255',
            'event_description' => 'required|string',
            'event_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|in:upcoming,ongoing,over',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'event_name', 'event_year', 'event_date', 'start_date', 'end_date',
            'event_location', 'event_description', 'status'
        ]);
        
        // Set default status if not provided
        if (!isset($data['status']) || empty($data['status'])) {
            $data['status'] = $event->status ?? 'upcoming';
        }

        // Generate slug
        $slug = Str::slug($request->event_name . '-' . $request->event_year);
        $data['slug'] = $slug;

        // Handle image upload
        if ($request->hasFile('event_image')) {
            // Delete old image if exists
            if ($event->event_image && File::exists(public_path($event->event_image))) {
                File::delete(public_path($event->event_image));
            }

            $image = $request->file('event_image');
            $imageName = time() . '_' . Str::slug($request->event_name) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/events'), $imageName);
            $data['event_image'] = 'uploads/events/' . $imageName;
        }

        $event->update($data);

        return redirect()->route('super-admin.events')->with('success', 'Event updated successfully!');
    }

    public function deleteEvent($id)
    {
        $event = Events::findOrFail($id);

        // Delete image if exists
        if ($event->event_image && File::exists(public_path($event->event_image))) {
            File::delete(public_path($event->event_image));
        }

        $event->delete();

        return redirect()->route('super-admin.events')->with('success', 'Event deleted successfully!');
    }

    private function updateConstantsFile(array $data)
    {
        $constantsPath = config_path('constants.php');
        
        if (!File::exists($constantsPath)) {
            return;
        }

        $content = File::get($constantsPath);
        
        // Update constants
        $replacements = [
            "/const EVENT_NAME = '[^']*';/" => "const EVENT_NAME = '{$data['event_name']}';",
            "/const EVENT_YEAR = '[^']*';/" => "const EVENT_YEAR = '{$data['event_year']}';",
            "/const SHORT_NAME = '[^']*';/" => "const SHORT_NAME = '{$data['short_name']}';",
            "/const EVENT_WEBSITE = '[^']*';/" => "const EVENT_WEBSITE = '{$data['event_website']}';",
            "/const ORGANIZER_NAME = '[^']*';/" => "const ORGANIZER_NAME = '{$data['organizer_name']}';",
            "/const ORGANIZER_EMAIL = '[^']*';/" => "const ORGANIZER_EMAIL = '{$data['organizer_email']}';",
            "/const ORGANIZER_PHONE = '[^']*';/" => "const ORGANIZER_PHONE = '{$data['organizer_phone']}';",
        ];

        foreach ($replacements as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        File::put($constantsPath, $content);
    }
}
