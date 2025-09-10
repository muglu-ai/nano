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



class SponsorshipController extends Controller
{

    //  public function __construct()
    // {
    //     $this->middleware(['admin']);
    // }

    public function generateSponsorId()
    {
        //call the construct function
        // $this->__construct();

        $applicationId = config('constants.SPONSORSHIP_ID_PREFIX') . substr(uniqid(), -6);
        //make sure that it doesn't match with any existing application id
        if (Sponsorship::where('application_id', $applicationId)->exists()) {
            return $this->generateApplicationId();
        }
        return $applicationId;
    }


    public function listing_dev($event)
    {

        $eventExists = Events::where('slug', $event)->first(['event_name', 'event_year']);
        if (!$eventExists) {
            return redirect()->back()->withErrors(['error' => 'Event does not exist.']);
        }
        $name = $eventExists->event_name;
        $year = $eventExists->event_year;

        $sponsorItems = SponsorItem::where('status', 'actives')->get();
        //pass empty array to the view if the event does not exist
        // $sponsorItems = $sponsorItems->isEmpty() ? [] : [];
        $sponsorCategories = SponsorCategory::with(['items' => function ($q) {
            $q->where('status', 'actives');
        }])->where('status', 'actives')->get();

        $sponsorCategories = $sponsorCategories->sortByDesc(function ($category) {
            return $category->name === 'Title Sponsorss' ? 1 : 0;
        });
        $categories = SponsorCategory::with(['items' => function ($q) {
            $q->where('status', 'activess');
        }])->where('status', 'activess')->get();

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

        $existingBaseConferenceItemIds = DB::table('sponsorships')
            ->join('sponsor_items', 'sponsorships.sponsorship_item_id', '=', 'sponsor_items.id')
            ->join('sponsor_categories', 'sponsor_items.category_id', '=', 'sponsor_categories.id')
            ->where('sponsorships.user_id', auth()->id())
            ->where('sponsor_items.is_addon', 0)
            ->where('sponsor_categories.name', 'Conference Sponsorship')
            ->pluck('sponsorships.sponsorship_item_id')
            ->toArray();


        // dd($existingBaseConferenceItemIds);
        //dd($sponsorItems);

        return view('sponsor.items_ecart', compact(
            'eventExists',
            'sponsorItems',
            'sponsorApplied',
            'sponsorCategories',
            'categories',
            'verified',
            'allocated_sqm',
            'discountEligible',
            'sponsorshipExists',
            'application',
            'existingBaseConferenceItemIds'

        ));
    }
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
        $categories = SponsorCategory::where('status', 'actives')->get();
        //just verify that user has applied for the exhibitor application or not, if yes check whether the application is approved or not.
        // if not then redirect to the exhibitor application page

        return view('sponsor.items_new', compact('eventExists', 'sponsorItems', 'categories', 'verified'));
    }
    public function listing($event)
    {

        $eventExists = Events::where('slug', $event)->first(['event_name', 'event_year']);
        if (!$eventExists) {
            return redirect()->back()->withErrors(['error' => 'Event does not exist.']);
        }
        $name = $eventExists->event_name;
        $year = $eventExists->event_year;

        $sponsorItems = SponsorItem::where('status', 'active')->get();
        //skip 
        //pass empty array to the view if the event does not exist
        // $sponsorItems = $sponsorItems->isEmpty() ? [] : [];
        $sponsorCategories = SponsorCategory::with(['items' => function ($q) {
            $q->where('status', 'active');
        }])
            ->where('status', 'active')
            ->where('name', '!=', 'State Sponsors')
            ->get();

        $sponsorCategories = $sponsorCategories->sortByDesc(function ($category) {
            return $category->name === 'Title Sponsors' ? 1 : 0;
        });
        $categories = SponsorCategory::with(['items' => function ($q) {
            $q->where('status', 'active');
        }])->where('status', 'active')->get();

        $application = Application::where('user_id', auth()->id())->first();
        if (!$application) {
            return redirect()->route('new_form', ['event' => $event])->with('error', 'Please fill out the Onboarding form.');
        }
        //check if the application is rejected or not
        if ($application->submission_status === 'rejected') {
            return redirect()->route('new_form', ['event' => $event])->with('error', 'Your application has been rejected. Please contact the organiser.');
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
            ->whereIn('status', ['initiated', 'approved', 'submitted', 'rejected'])
            ->exists();

        $existingBaseConferenceItemIds = DB::table('sponsorships')
            ->join('sponsor_items', 'sponsorships.sponsorship_item_id', '=', 'sponsor_items.id')
            ->join('sponsor_categories', 'sponsor_items.category_id', '=', 'sponsor_categories.id')
            ->where('sponsorships.user_id', auth()->id())
            ->where('sponsor_items.is_addon', 0)
            ->where('sponsor_categories.name', 'Conference Sponsorship')
            ->pluck('sponsorships.sponsorship_item_id')
            ->toArray();


        // dd($existingBaseConferenceItemIds);
        //dd($sponsorItems);

        return view('sponsor.items_ecart', compact(
            'eventExists',
            'sponsorItems',
            'sponsorApplied',
            'sponsorCategories',
            'categories',
            'verified',
            'allocated_sqm',
            'discountEligible',
            'sponsorshipExists',
            'application',
            'existingBaseConferenceItemIds'

        ));
    }
    public function listing_state($event)
    {

        $eventExists = Events::where('slug', $event)->first(['event_name', 'event_year']);
        if (!$eventExists) {
            return redirect()->back()->withErrors(['error' => 'Event does not exist.']);
        }
        $name = $eventExists->event_name;
        $year = $eventExists->event_year;

        $sponsorItems = SponsorItem::where('status', 'active')->get();
        //pass empty array to the view if the event does not exist
        // $sponsorItems = $sponsorItems->isEmpty() ? [] : [];
        $excludedCategories = [
            'Onsite Promotional Opportunities',
            'Onsite Branding Opportunities',
            'Conference Sponsorship',
            'Welcome Dinner Sponsors',
            'Title Sponsors'
        ];

        $sponsorCategories = SponsorCategory::with(['items' => function ($q) {
            $q->where('status', 'active');
        }])
            ->where('status', 'active')
            ->whereNotIn('name', $excludedCategories)
            ->get();

        $sponsorCategories = $sponsorCategories->sortByDesc(function ($category) {
            return $category->name === 'Title Sponsors' ? 1 : 0;
        });
        $categories = SponsorCategory::with(['items' => function ($q) {
            $q->where('status', 'active');
        }])->where('status', 'active')->get();

        $application = Application::where('user_id', auth()->id())->first();
        if (!$application) {
            return redirect()->route('new_form', ['event' => $event])->with('error', 'Please fill out the Onboarding form.');
        }
        //check if the application is rejected or not
        if ($application->submission_status === 'rejected') {
            return redirect()->route('new_form', ['event' => $event])->with('error', 'Your application has been rejected. Please contact the organiser.');
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

        $existingBaseConferenceItemIds = DB::table('sponsorships')
            ->join('sponsor_items', 'sponsorships.sponsorship_item_id', '=', 'sponsor_items.id')
            ->join('sponsor_categories', 'sponsor_items.category_id', '=', 'sponsor_categories.id')
            ->where('sponsorships.user_id', auth()->id())
            ->where('sponsor_items.is_addon', 0)
            ->where('sponsor_categories.name', 'Conference Sponsorship')
            ->pluck('sponsorships.sponsorship_item_id')
            ->toArray();


        // dd($existingBaseConferenceItemIds);
        //dd($sponsorItems);

        return view('sponsor.items_ecart_state', compact(
            'eventExists',
            'sponsorItems',
            'sponsorApplied',
            'sponsorCategories',
            'categories',
            'verified',
            'allocated_sqm',
            'discountEligible',
            'sponsorshipExists',
            'application',
            'existingBaseConferenceItemIds'

        ));
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



        // 🧠 STEP 1: Collect all item_ids being submitted
        $itemIds = collect($items)->pluck('item_id')->toArray();

        // 🧠 STEP 2: Load all sponsor item models at once
        $sponsorItems = SponsorItem::whereIn('id', $itemIds)->get()->keyBy('id');

        // 🧠 STEP 3: Validate Add-on items
        $addonItems = $sponsorItems->filter(
            fn($item) =>
            $item->is_addon && $item->category->name === 'Conference Sponsorship'
        );

        if ($addonItems->isNotEmpty()) {
            // Check if any base Conference Sponsorship item exists (submitted now or already added)
            $baseExistsNow = $sponsorItems->contains(
                fn($item) =>
                !$item->is_addon && $item->category->name === 'Conference Sponsorship'
            );

            $baseExistsBefore = Sponsorship::where('user_id', $user->id)
                ->where('application_id', $application->id)
                ->whereHas('sponsorItem', function ($q) {
                    $q->where('is_addon', 0)->whereHas('category', function ($c) {
                        $c->where('name', 'Conference Sponsorship');
                    });
                })->exists();

            if (!$baseExistsNow && !$baseExistsBefore) {
                return back()->withErrors([
                    'error' => 'Conference Sponsorship is required before selecting Add-on items.'
                ]);
            }
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
            $sponsorItem = SponsorItem::select('no_of_items', 'price', 'mem_price', 'name')->findOrFail($item['item_id']);

            $totalSponsorshipCount = Sponsorship::where('sponsorship_item_id', $item['item_id'])
                ->where('user_id', '!=', $user->id)
                ->whereNotIn('status', ['rejected', 'cancelled'])
                ->sum('sponsorship_item_count');

            $existingSponsorship = Sponsorship::where('user_id', $user->id)
                ->where('sponsorship_item_id', $item['item_id'])
                ->where('application_id', $application->id)
                ->whereNotIn('status', ['rejected', 'cancelled'])
                ->first();



            $newQuantity = $item['quantity'];
            if ($existingSponsorship) {
                $newQuantity += $existingSponsorship->sponsorship_item_count;
            }

            if ($totalSponsorshipCount + $newQuantity > $sponsorItem->no_of_items) {
                return back()->withErrors(['error' => "The requested quantity for {$sponsorItem->name} exceeds availability."]);
            }

            // if the $application->membership_verified == 1 then apply the mem_price else apply the price
            $price = $application->membership_verified ? $sponsorItem->mem_price : $sponsorItem->price;


            Sponsorship::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'sponsorship_item_id' => $item['item_id'],
                    'application_id' => $application->id
                ],
                [
                    'sponsorship_id' => $existingSponsorship ? $existingSponsorship->sponsorship_id : $this->generateSponsorId(),
                    'sponsorship_item_count' => $newQuantity,
                    'price' => $price,
                    'status' => 'initiated',
                    'sponsorship_item' => $sponsorItem->name,
                    'submitted_date' => now(),
                ]
            );
        }

        return redirect()->route('sponsor.review')->with('success', 'Thank you for your submission.');


        return back()->with('success', 'Thank you for your submission.');
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
}
