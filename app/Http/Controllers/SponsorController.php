<?php

namespace App\Http\Controllers;

use App\Helpers\ExhibitorPriceCalculator;
use App\Mail\AdminApplicationSubmitted;
use App\Mail\UserApplicationSubmitted;
use App\Models\Application;
use App\Models\BillingDetail;
use App\Models\EventContact;
use App\Models\Events;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SponsorItem;
use App\Models\SponsorCategory;

use http\Env\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Sponsorship;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SponsorInvoiceMail;



class SponsorController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware(['admin']);
    // }
    //create sponsor item
    public function create()
    {
        $categories = SponsorCategory::where('status', 'active')->get();
        $sponsorItems = SponsorItem::all();
        $slug = "Add Sponsor Item";
        return view('sponsor.items_list', compact('categories', 'sponsorItems', 'slug'));
    }


    public function store_item(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:sponsor_categories,id',
            'price' => 'required|numeric',
            'mem_price' => 'required|numeric',
            'no_of_items' => 'required|integer',
            'deliverables' => 'required|string',
            'image_url' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();

        // Handle file upload
        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store('sponsor_items', 'public');
            $data['image_url'] = $path;
        }

        SponsorItem::create($data);

        return redirect()->route('sponsor_items.index')->with('success', 'Sponsor Item created successfully.');
    }

    //view add or edit sponsor item form 
    public function add(Request $request)
    {
        $categories = SponsorCategory::where('status', 'active')->get();
        $slug = "Add Sponsor Item";
        return view('sponsorship.edit', compact('categories', 'slug'));
    }
    public function sponsor_update(Request $request, $id)
    {
        $categories = SponsorCategory::where('status', 'active')->get();
        $slug = "Update Sponsor Item";
        //find the sponsor item by id
        //validate the $id exist in the database 

        $sponsorItem = SponsorItem::findOrFail($id);
        return view('sponsorship.edit', compact('categories', 'slug', 'sponsorItem'));
    }

    public function item_store(Request $request)
    {


        Log::info('Store Sponsor Item Request', $request->all());

        $validated = $request->validate([
            'category_id' => 'required|exists:sponsor_categories,id',
            'itemName' => 'required|string',
            'itemImage' => 'nullable|file|image',
            'itemDescription' => 'nullable|string',
            'itemStatus' => 'required|string',
            'itemQuantity' => 'required|integer',
            'memberPrice' => 'required|numeric',
            'regularPrice' => 'required|numeric',
        ]);

        // Save image if uploaded
        if ($request->hasFile('itemImage')) {
            $path = $request->file('itemImage')->store('sponsor_images', 'public');
            $validated['image_url'] = asset('storage/' . $path);
        }

        SponsorItem::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['itemName'],
            'image_url' => $validated['image_url'] ?? null,
            'deliverables' => $validated['itemDescription'],
            'status' => $validated['itemStatus'],
            'no_of_items' => $validated['itemQuantity'],
            'mem_price' => $validated['memberPrice'],
            'price' => $validated['regularPrice'],
        ]);

        //redirect to route sponsor.create_new 
        return redirect()->route('sponsor.create_new')->with('success', 'Sponsor Item created successfully.');

        return response()->json(['success' => true]);
    }

    // item_inactive to inactive the item 
    public function item_inactive(Request $request, $id)
    {
        Log::info('Deactivate Sponsor Item Request', [
            'request' => $request->all(),
            'id' => $id
        ]);
        // $request->validate([
        //     'id' => 'required|exists:sponsor_items,id',
        // ]);

        // if ($errors = $request->getValidatorInstance()->errors()->all()) {
        //     Log::error('Validation error in item_inactive', ['errors' => $errors]);
        //     return response()->json(['errors' => $errors], 422);
        // }
        $item = SponsorItem::findOrFail($id);
        Log::info('Sponsor Item found', ['item' => $item]);
        $item->update(['status' => 'inactive']);
        return response()->json(['success' => true]);
    }

    public function item_update(Request $request, $id)
    {

        Log::info('Update Sponsor Item Request', [
            'request' => $request->all(),
            'id' => $id
        ]);
        $item = SponsorItem::findOrFail($id);

        $validated = $request->validate([
            'itemName' => 'required|string',
            'itemImage' => 'nullable|file|image',
            'itemDescription' => 'nullable|string',
            'itemStatus' => 'required|string',
            'itemQuantity' => 'required|integer',
            'memberPrice' => 'required|numeric',
            'regularPrice' => 'required|numeric',
        ]);

        if ($request->hasFile('itemImage')) {
            $path = $request->file('itemImage')->store('sponsor_images', 'public');
            $validated['image_url'] = asset('storage/' . $path);
        }

        $item->update([
            'name' => $validated['itemName'],
            'image_url' => $validated['image_url'] ?? $item->image_url,
            'deliverables' => $validated['itemDescription'],
            'status' => $validated['itemStatus'],
            'no_of_items' => $validated['itemQuantity'],
            'mem_price' => $validated['memberPrice'],
            'price' => $validated['regularPrice'],
        ]);

        //return to route sponsor.create_new
        return redirect()->route('sponsor.create_new')->with('success', 'Sponsor Item updated successfully.');

        return response()->json(['success' => true]);
    }


    //display the sponsor listed item
    public function index()
    {

        // Fetch all sponsor items from the database
        $sponsorItems = SponsorItem::all();

        //        dd($sponsorItems);

        // Pass the sponsor items to the view
        return view('sponsor.items', compact('sponsorItems'));
    }

    //
    public function new($event)
    {

        $eventExists = Events::where('slug', $event)->first(['event_name', 'event_year']);
        if (!$eventExists) {
            return redirect()->back()->withErrors(['error' => 'Event does not exist.']);
        }
        $name = $eventExists->event_name;
        $year = $eventExists->event_year;

        $sponsorItems = SponsorItem::all();
        //pass empty array to the view if the event does not exist
        $sponsorItems = $sponsorItems->isEmpty() ? [] : [];
        return view('sponsor.items', compact('eventExists', 'sponsorItems'));
    }
    public function new_up($event)
    {

        $eventExists = Events::where('slug', $event)->first(['event_name', 'event_year']);
        if (!$eventExists) {
            return redirect()->back()->withErrors(['error' => 'Event does not exist.']);
        }
        $name = $eventExists->event_name;
        $year = $eventExists->event_year;

        $sponsorItems = SponsorItem::all();
        //pass empty array to the view if the event does not exist
        // $sponsorItems = $sponsorItems->isEmpty() ? [] : [];
        $categories = SponsorCategory::where('status', 'active')->get();
        //just verify that user has applied for the exhibitor application or not, if yes check whether the application is approved or not.
        // if not then redirect to the exhibitor application page

        return view('sponsor.items_new', compact('eventExists', 'sponsorItems', 'categories', 'verified'));
    }
    public function listing($event)
    {


        $eventExists = Events::where('slug', $event)->first(['event_name', 'event_year']);

        //dd($eventExists);
        if (!$eventExists) {
            return redirect()->back()->withErrors(['error' => 'Event does not exist.']);
        }
        $name = $eventExists->event_name;
        $year = $eventExists->event_year;

        $sponsorItems = SponsorItem::all();
        //pass empty array to the view if the event does not exist
        // $sponsorItems = $sponsorItems->isEmpty() ? [] : [];
        $sponsorCategories = SponsorCategory::with(['items' => function ($q) {
            $q->where('status', 'active');
        }])->where('status', 'active')->get();

        $sponsorCategories = $sponsorCategories->sortByDesc(function ($category) {
            return $category->name === 'Title Sponsors' ? 1 : 0;
        });
        $categories = SponsorCategory::with(['items' => function ($q) {
            $q->where('status', 'active');
        }])->where('status', 'active')->get();

        $application = Application::where('user_id', auth()->id())->first();
        if (!$application) {
            return redirect()->route('exhibitor_application', ['event' => $event])->with('error', 'Please fill out the Onboarding form.');
        }
        //check if the application is rejected or not
        if ($application->submission_status === 'rejected') {
            return redirect()->route('exhibitor_application', ['event' => $event])->with('error', 'Your application has been rejected. Please contact the organiser.');
        }
        $verified = null;
        //check if the application is approved or not
        if ($application->submission_status === 'approved') {
            $verified = true;
            //get the allocated_sqm from the application model
            $allocated_sqm = $application->allocated_sqm;
        } else {
            $verified = false;
            //get the allocated_sqm from the application model
            $allocated_sqm = 0;
        }
        $discountEligible = $allocated_sqm >= 72;

        // check from the sponsorship how many items and there count are already applied for the sponsorship so that we can make that disabled in the frontend

        // Fetch the total applied counts for each sponsorship item
        $sponsorApplied = Sponsorship::whereIn('status', ['initiated', 'approved', 'submitted'])
            ->get()
            ->groupBy('sponsorship_item_id')
            ->map(function ($row) {
                return $row->sum('sponsorship_item_count');
            })
            ->toArray();


        $sponsorshipExists = Sponsorship::where('user_id', Auth::id())
            ->where('application_id', $application->id)
            ->whereIn('status', ['initiated', 'approved', 'rejected'])
            ->exists();



        return view('sponsor.items_ecart', compact('eventExists', 'sponsorItems', 'sponsorApplied', 'sponsorCategories', 'categories', 'verified', 'allocated_sqm', 'discountEligible', 'sponsorshipExists', 'application'));
    }



    public function store(Request $request)
    {

        Log::info('Store Sponsorship Request', $request->all());
        $user = auth()->user();

        // Decode the JSON string into an array if it's a string
        $items = is_string($request->items) ? json_decode($request->items, true) : $request->items;

        if (!is_array($items)) {
            return back()->withErrors(['error' => 'Invalid sponsorship items submitted.']);
        }

        $request->merge(['items' => $items]);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:sponsor_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'opt_out_title_sponsorship' => 'nullable|boolean',
        ]);

        $application = Application::where('user_id', $user->id)->first();

        if (!$application) {
            return back()->withErrors(['error' => 'Please fill out the Onboarding form.']);
        }

        if ($application->submission_status === 'rejected') {
            return back()->withErrors(['error' => 'Your application has been rejected. Please contact the organiser.']);
        }

        $application->update([
            'has_sponsorship' => 1,
            'discount_eligible' => $application->allocated_sqm >= 72,
            'cart_data' => $application->cart_data
                ? json_encode(array_merge(json_decode($application->cart_data, true), $items))
                : json_encode($items),
            'withdraw_title' => $request->opt_out_title_sponsorship ? 1 : 0,
        ]);

        foreach ($items as $item) {
            $sponsorItem = SponsorItem::select('no_of_items', 'price', 'name')->findOrFail($item['item_id']);

            $totalSponsorshipCount = Sponsorship::where('sponsorship_item_id', $item['item_id'])
                ->where('user_id', '!=', $user->id)
                ->sum('sponsorship_item_count');

            $existingSponsorship = Sponsorship::where('user_id', $user->id)
                ->where('sponsorship_item_id', $item['item_id'])
                ->where('application_id', $application->id)
                ->first();

            $newQuantity = $item['quantity'];
            if ($existingSponsorship) {
                $newQuantity += $existingSponsorship->sponsorship_item_count;
            }

            if ($totalSponsorshipCount + $newQuantity > $sponsorItem->no_of_items) {
                return back()->withErrors(['error' => "The requested quantity for {$sponsorItem->name} exceeds availability."]);
            }

            Sponsorship::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'sponsorship_item_id' => $item['item_id'],
                    'application_id' => $application->id
                ],
                [
                    'sponsorship_id' => $existingSponsorship ? $existingSponsorship->sponsorship_id : $this->generateSponsorId(),
                    'sponsorship_item_count' => $newQuantity,
                    'price' => $sponsorItem->price,
                    'status' => 'initiated',
                    'sponsorship_item' => $sponsorItem->name,
                    'submitted_date' => now(),
                ]
            );
        }

        return redirect()->route('sponsor.review')->with('success', 'Thank you for your submission.');


        return back()->with('success', 'Thank you for your submission.');
    }

    /*
    foreach ($items as $item) {
            $sponsorItem = SponsorItem::select('no_of_items', 'price', 'name')->findOrFail($item['item_id']);

            $totalSponsorshipCount = Sponsorship::where('sponsorship_item_id', $item['item_id'])
                ->where('user_id', '!=', $user->id)
                ->sum('sponsorship_item_count');

            if ($totalSponsorshipCount + $item['quantity'] > $sponsorItem->no_of_items) {
                return back()->withErrors(['error' => "The requested quantity for {$sponsorItem->name} exceeds availability."]);
            }

            Sponsorship::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'sponsorship_item_id' => $item['item_id'],
                    'application_id' => $application->id
                ],
                [
                    'sponsorship_id' => $this->generateSponsorId(),
                    'sponsorship_item_count' => $item['quantity'],
                    'price' => $sponsorItem->price,
                    'status' => 'initiated',
                    'sponsorship_item' => $sponsorItem->name,
                    'submitted_date' => now(),
                ]
            );
        }

        */



    //store the sponsor listed item from post method request name item_id
    public function store_olf(Request $request)
    {

        $user = auth()->user();


        $request->validate([
            'item_id' => 'required|exists:sponsor_items,id',
            'quantity' => ['required', 'integer', 'min:1', new \App\Rules\ValidSponsorItemCount($request->item_id)]
        ]);

        //check if user has filled the onboarding form from the application model
        $application = Application::where('user_id', $user->id)->first();

        if (!$application) {
            return back()->withErrors(['error' => 'Please fill out the Onboarding form.']);
        }

        //check if the application is rejected or not
        if ($application->submission_status === 'rejected') {
            return back()->withErrors(['error' => 'Your application has been rejected. Please contact the organiser.']);
        }

        // Fetch sponsor item details in one query
        $sponsorItem = SponsorItem::select('no_of_items', 'price', 'name')
            ->findOrFail($request->item_id);

        $totalSponsorshipCount = Sponsorship::where('sponsorship_item_id', $request->item_id)
            ->sum('sponsorship_item_count');

        // Check if the requested quantity exceeds availability
        if ($totalSponsorshipCount + $request->quantity > $sponsorItem->no_of_items) {
            return back()->withErrors(['error' => 'The requested quantity exceeds the available sponsor items.']);
        }

        // Ensure the user has filled out the onboarding form
        $application = Application::where('user_id', $user->id)->first();
        if (!$application) {
            return back()->withErrors(['error' => 'Please fill out the Onboarding form.']);
        }

        // Check if the user has already submitted this sponsorship item
        if (Sponsorship::where('application_id', $application->id)
            ->where('sponsorship_item_id', $request->item_id)
            ->exists()
        ) {
            return back()->withErrors(['error' => 'You have already submitted this sponsorship item.']);
        }

        //update application application_type to sponsorship
        $application->update(['application_type' => 'sponsorship']);




        // Store sponsorship details
        Sponsorship::updateOrCreate(
            ['sponsorship_item_id' => $request->item_id],
            [
                'sponsorship_id' => $this->generateSponsorId(),
                'user_id' => $user->id,
                'sponsorship_item_count' => $request->quantity,
                'price' => $sponsorItem->price,
                'status' => 'initiated',
                'sponsorship_item' => $sponsorItem->name,
                'application_id' => $application->id,
                'submitted_date' => now()
            ]
        );

        return back()->with('success', 'Thank you for your submission.');
    }

    // get all the sponsor applications and return in json
    public function get_applications2()
    {
        $page = request()->get('page', 1);
        $sortField = request()->get('sort', 'id');
        $sortDirection = request()->get('direction', 'asc');
        $perPage = request()->get('per_page', 10);

        $search = request()->get('sort', '');

        $sponsorApplications = Sponsorship::with([
            'user:id,name,email',
            'application:id,user_id',
            'application.billingDetail:application_id,billing_company',
            'application.eventContact:id,application_id,email,first_name,last_name'
        ])
            ->join('users', 'sponsorships.user_id', '=', 'users.id')
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('sponsorship_item', 'like', "%{$search}%")
                        ->orWhereHas('application.billingDetail', function ($query) use ($search) {
                            $query->where('billing_company', 'like', "%{$search}%");
                        })
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->orderBy('users.' . $sortField, $sortDirection)
            ->paginate($perPage, [
                'sponsorships.id',
                'sponsorships.user_id',
                'sponsorship_item',
                'sponsorship_item_count',
                'price',
                'status',
                'application_id'
            ], 'page', $page);

        return response()->json($sponsorApplications);
    }

    public function get_applications()
    {
        $page = request()->get('page', 1);
        $sortField = request()->get('sort', 'id');
        $sortDirection = request()->get('direction', 'asc');
        $perPage = request()->get('per_page', 10);
        $search = request()->get('search', '');

        $sponsorApplications = Sponsorship::with([
            'user:id,name,email',
            'application:id,user_id',
            'application.billingDetail:application_id,billing_company',
            'application.eventContact:id,application_id,email,first_name,last_name'
        ])
            ->join('users', 'sponsorships.user_id', '=', 'users.id')
            ->leftJoin('applications', 'sponsorships.application_id', '=', 'applications.id')
            ->leftJoin('billing_details', 'applications.id', '=', 'billing_details.application_id')
            ->when($search, function ($query) use ($search, $sortField) {
                if (in_array($sortField, ['sponsorship_item', 'status'])) {
                    $query->where("sponsorships.{$sortField}", 'like', "%{$search}%");
                } elseif ($sortField === 'name') {
                    $query->where('users.name', 'like', "%{$search}%");
                } elseif ($sortField === 'billing_company') {
                    $query->where('billing_details.billing_company', 'like', "%{$search}%");
                }
            })
            ->when(in_array($sortField, ['sponsorship_item', 'status']), function ($query) use ($sortField, $sortDirection) {
                return $query->orderBy("sponsorships.{$sortField}", $sortDirection);
            })
            ->when($sortField === 'name', function ($query) use ($sortDirection) {
                return $query->orderBy("users.name", $sortDirection);
            })
            ->when($sortField === 'billing_company', function ($query) use ($sortDirection) {
                return $query->orderBy("billing_details.billing_company", $sortDirection);
            })->when($sortField === 'email', function ($query) use ($sortDirection) {
                return $query->orderBy("billing_details.billing_company", $sortDirection);
            })
            ->paginate($perPage, [
                'sponsorships.id',
                'sponsorships.user_id',
                'sponsorship_item',
                'sponsorship_item_count',
                'price',
                'sponsorships.status',
                'applications.id as application_id',
                'billing_details.billing_company'
            ], 'page', $page);

        return response()->json($sponsorApplications);
    }

    public function generateSponsorId()
    {
        //call the construct function
        $this->__construct();

        $applicationId = config('constants.SPONSORSHIP_ID_PREFIX') . substr(uniqid(), -6);
        //make sure that it doesn't match with any existing application id
        if (Sponsorship::where('application_id', $applicationId)->exists()) {
            return $this->generateApplicationId();
        }
        return $applicationId;
    }

    public function preview()
    {
        $this->__construct();
        //if user is not logged in then redirect to login page

        $application = Application::where('user_id', auth()->id())->latest()->first();

        $sponsorship = Sponsorship::where('application_id', $application->id)->get();

        if (!$sponsorship) {
            $eventId = $application->event_id;
            //get the slug of the event from event model
            $eventSlug = Events::where('id', $eventId)->first()->slug;
            return redirect()->route('sponsorship', ['event' => $eventSlug])->with('success', 'No application found.');
        }


        //dd($sponsorship, $application);

        $eventContact = EventContact::where('application_id', $application->id)->first();
        $billing = BillingDetail::where('application_id', $application->id)->first();

        $invoice = Invoice::where('application_id', $application->id)
            ->where('type', 'like', '%sponsorship-%')
            ->first() ?? null;

        $payments = array();
        if ($invoice) {
            $in_id = $invoice->id;
            $payments = Payment::where('invoice_id', $in_id)
                ->where('type', 'Sponsorship-')
                ->get();
        }


        return view('sponsor.preview', [
            'sponsorships' => $sponsorship,
            'application' => $application,
            'eventContact' => $eventContact,
            'billing' => $billing,
            'invoice' => $invoice,
            'payments' => $payments,
        ]);
    }

    public function confirmation()
    {
        $user = auth()->user();

        $application = Application::where('user_id', $user->id)->firstOrFail();

        $sponsorships = Sponsorship::where('user_id', $user->id)
            ->with('sponsorshipItem')
            ->get();

        $titleSponsors = SponsorItem::where('category_id', 5)->get();

        //dd($titleSponsors);

        return view('sponsor.summary', compact('application', 'sponsorships', 'titleSponsors'));
    }

    //delete the sponsor application
    public function delete(Request $request)
    {
        $request->validate([
            'sponsor_id' => 'required|exists:sponsorships,id'
        ]);



        $sponsorship = Sponsorship::findOrFail($request->sponsor_id);

        // Check if the sponsorship is already approved
        if ($sponsorship->status === 'approved') {
            return back()->withErrors(['error' => 'Cannot delete an approved sponsorship.']);
        }

        $sponsorship->delete();
        $eventId = $sponsorship->application->event_id;
        //get the slug of the event from event model
        $eventSlug = Events::where('id', $eventId)->first()->slug;
        return redirect()->route('sponsorship', ['event' => $eventSlug])->with('success', 'Sponsorship deleted.');

        return back()->with('success', 'Sponsorship deleted.');
    }

    //submit application for the sponsor
    public function submit(Request $request)
    {
        $userId = auth()->id();

        // Fetch all initiated sponsorships for the user
        $initiatedSponsorships = Sponsorship::where('user_id', $userId)
            ->where('status', 'initiated')
            ->get();

        // dd($initiatedSponsorships);

        if ($initiatedSponsorships->isEmpty()) {
            return back()->withErrors(['error' => 'No sponsorship applications found with initiated status.']);
        }

        // Fetch the latest application for the user
        $application = Application::where('user_id', $userId)->latest()->first();

        if (!$application) {
            return back()->withErrors(['error' => 'No application found. Please complete the onboarding process.']);
        }

        // Update each initiated sponsorship if it contains rejected items then skip that sponsorship items and submit the rest of the sponsorship items


        foreach ($initiatedSponsorships as $sponsorship) {
            if ($sponsorship->status === 'rejected') {
                continue; // Skip rejected sponsorships
            }

            $sponsorship->update([
                'status' => 'submitted',
                'submitted_date' => now(),
            ]);
        }

        // Mark the application as submitted
        // $application->update([
        //     'submission_status' => 'submitted',
        // ]);

        // Queue emails to admin and user
        $adminEmail = ['test.interlinks@gmail.com', ''];

        Mail::to($adminEmail)->queue(new AdminApplicationSubmitted($application));
        // Send email to the user

        Mail::to(auth()->user()->email)->queue(new UserApplicationSubmitted($application));

        return back()->with('success', 'All initiated sponsorships submitted successfully.');
    }

    //approve the sponsor application with sponsorship_id from the request and update the status to approved
    public function approve0(Request $request)
    {
        Log::info('Approve Sponsorship Request', $request->all());




        //validate the request id from the sponsorship model
        $request->validate([
            'sponsor_id' => 'required|exists:sponsorships,id'
        ], [], function ($validator) {
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
        });

        $id = $request->sponsor_id;
        //        $id = 5;
        //find the sponsorship id from the request
        $sponsorship = Sponsorship::findOrFail($id);



        //check whether the status is already approved
        if ($sponsorship->status === 'approved') {


            return back()->withErrors(['error' => 'Sponsorship already approved.']);
        }

        // calculate the price by passing the sponsorship_item_id
        // Get member status from application and quantity from sponsorship
        $application = $sponsorship->application;
        $member = $application ? ($application->semi_member ?? false) : false;
        $quantity = $sponsorship->sponsorship_item_count ?? 1;
        $price = ExhibitorPriceCalculator::calculateSponsorshipPrice($sponsorship->sponsorship_item_id, $member, $quantity);

        //find from the invoice if the same sponsor_id and same type
        // as Sponsorship-{$sponsorship->sponsorship_id} exists
        $existingInvoice = Invoice::where('type', 'Sponsorship-' . $id)
            ->where('sponsorship_id', $sponsorship->id)
            ->first();

        if ($existingInvoice) {
            return response()->json(['error' => 'Invoice already exists for this sponsorship.']);
            return back()->withErrors(['error' => 'Invoice already exists for this sponsorship.']);
        } else {
            //create new invoice
            $invoice = new Invoice();
            $invoice->sponsorship_id = $sponsorship->id;
            $invoice->amount = $price['final_total_price'];
            $invoice->price = $price['actual_price'];
            $invoice->discount = $price['discount'];
            $invoice->processing_charges = $price['processing_charges'];
            $invoice->gst = $price['gst'];
            $invoice->type = 'Sponsorship-' . $id;
            $invoice->currency = 'INR';
            $invoice->payment_status = 'unpaid';
            $invoice->sponsorship_no = $sponsorship->sponsorship_id;
            $invoice->payment_due_date = now()->addDays(5);
            $invoice->discount_per = 0;

            $invoice->save();
        }




        //update the status to approved
        Log::info('Updating sponsorship status to approved', ['sponsorship_id' => $sponsorship->id]);
        $sponsorship->status = 'approved';
        $invoice->approval_date = now();
        $sponsorship->invoice_id = $invoice->id;
        $sponsorship->save();
        Log::info('Sponsorship status updated', ['sponsorship' => $sponsorship]);

        return back()->with('success', 'Sponsorship approved.');
    }


    public function approve(Request $request)
    {
        #Log::info('Approve Sponsorship Request', $request->all());

        // Validate sponsor_id before proceeding

        $validatedData = $request->validate([
            'id' => 'required|exists:applications,id',
            'sponsorship_id' => 'required|exists:sponsorships,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $id = $request->sponsorship_id;

        Log::info('Approve Sponsorship Request Validated', $validatedData);

        try {
            // Start a database transaction to ensure atomicity
            DB::beginTransaction();

            // Fetch sponsorship with invoice check in a single query
            //            $sponsorship = Sponsorship::with('invoice')
            //                ->where('sponsorship_id', $id)
            //                ->lockForUpdate()
            //                ->firstOrFail();

            # Log::info('Fetching Sponsorship with ID', ['id' => $id]);
            $sponsorship = Sponsorship::find($id);

            // update the quntity of the sponsorship item in the sponsorship table
            $sponsorship->sponsorship_item_count = $request->quantity;
            $sponsorship->save();

            if (!$sponsorship) {
                Log::warning('Sponsorship not found', ['id' => $id]);
                return response()->json(['error' => 'Sponsorship not found.'], 404);
            }

            #Log::info('Fetched Sponsorship for Approval', $sponsorship->toArray());
            // Check if sponsorship is already approved
            if ($sponsorship->status === 'approved') {
                return response()->json(['error' => 'Sponsorship already approved.'], 400);
            }

            // Check if an invoice already exists for the given sponsorship
            $existingInvoice = Invoice::where('type', 'Sponsorship-' . $id)
                ->where('sponsorship_id', $sponsorship->id)
                ->exists(); // Optimized query

            #Log::info('Checking for Existing Invoice', ['exists' => $existingInvoice]);
            $to = $sponsorship->application->eventContact->email;
            $application_id =  $sponsorship->application->application_id;

            //send email to applicant with approval
            //send a post request to send email with email_type as submission and to as applicant email
            // try {
            //     $recipients = is_array($to) ? $to : [$to];
            //     $recipients[] = 'test.interlinks@gmail.com'; // Add default email
            //     //$recipients[] = ''; // Add default email
            //     Mail::to($recipients[0])->bcc(array_slice($recipients, 1))->queue(new SponsorInvoiceMail($application_id));
            // } catch (\Exception $e) {
            //     Log::error("Error sending Sponsor Invoice email: " . $e->getMessage());
            //     return response()->json(['message' => 'Failed to send email.'], 500);
            // }

            if ($existingInvoice) {
                return response()->json(['message' => 'Invoice already exists for this sponsorship.'], 400);
            }

            # Log::info('Creating Invoice for Sponsorship', ['sponsorship' => $sponsorship->toArray(), $sponsorship->sponsorship_item_id]);
            $member = false;

            // Check if the user is a member and get the membership type
            $member_verified = $sponsorship->application->membership_verified;

            if ($member_verified) {
                $member = true;
            }

            // $quantity = $sponsorship->sponsorship_item_count;
            $quantity = $request->quantity;
            // Calculate sponsorship price (use cached results if possible)
            $price = ExhibitorPriceCalculator::calculateSponsorshipPrice($sponsorship->sponsorship_item_id, $member, $quantity, 0);

            //invoice_no format as INV-SEMI25-S. 5 random numbers check also if the invoice_no already exists
            $invoiceNo = 'INV-SEMI25-S' . strtoupper(substr(uniqid(), -5));

            #  Log::info('Calculated Sponsorship Price', $price);
            // Create invoice record
            $invoice = Invoice::create([
                'sponsorship_id' => $sponsorship->id,
                'amount' => $price['final_total_price'],
                'price' => $price['actual_price'],
                'discount' => $price['discount'],
                'processing_charges' => $price['processing_charges'],
                'gst' => $price['gst'],
                'type' => 'Sponsorship-' . "$id",
                'currency' => 'INR',
                'payment_status' => 'unpaid',
                'sponsorship_no' => $sponsorship->sponsorship_id,
                'payment_due_date' => now()->addDays(5),
                'discount_per' => 0,
                'approval_date' => now(),
                'invoice_no' => $invoiceNo,
                'application_id' => $sponsorship->application_id,
            ]);
            # Log::info('Invoice Created', ['invoice' => $invoice->toArray()]);
            $sponsorship = Sponsorship::find($id);
            # Log::info('Updating Sponsorship Status to Approved', ['sponsorship' => $sponsorship->toArray()]);
            // Update sponsorship status and invoice reference
            $sponsorship->status = 'approved';
            $sponsorship->approval_date = now();
            $sponsorship->invoice_id = $invoice->id;
            $sponsorship->save();

            #Log::info('Sponsorship Approved', ['sponsorship' => $sponsorship->toArray()]);

            // Send email to user and admin

            $to = $sponsorship->application->eventContact->email;
            $application_id =  $sponsorship->application->application_id;

            //send email to applicant with approval
            //send a post request to send email with email_type as submission and to as applicant email
            $recipients = is_array($to) ? $to : [$to];
            $recipients[] = 'test.interlinks@gmail.com'; // Add default email
            $recipients[] = ''; // Add default email
            Mail::to($recipients[0])->bcc(array_slice($recipients, 1))->queue(new SponsorInvoiceMail($application_id));

            // Commit the transaction to save all changes
            DB::commit();
            return response()->json(['success' => 'Sponsorship approved.'], 200);
        } catch (\Exception $e) {
            // Rollback changes if any failure occurs
            DB::rollBack();
            Log::error("Error approving sponsorship: " . $e->getMessage());

            return response()->json(['error' => 'An error occurred while approving sponsorship.'], 500);
        }
    }

    //review the application
    public function review()
    {
        $this->__construct();
        $application = Application::where('user_id', auth()->id())->latest()->first();
        //find the sponsor application from the sponsorship model using application_id from the application model
        //        $sponsor = Sponsorship::where('application_id', $application->id);
        $eventContact = EventContact::where('application_id', $application->id)->first();
        $billing = BillingDetail::where('application_id', $application->id)->first();
        $sponsor = Sponsorship::where('application_id', $application->id)->get();

        //        dd($sponsor, $application->id);
        //        dd($application, $eventContact,$sponsor,  $billing);

        return view('sponsor.sponsorship_status', [
            'sponsor' => $sponsor,
            'application' => $application,
            'eventContact' => $eventContact,
            'billing' => $billing,
        ]);
    }
}
