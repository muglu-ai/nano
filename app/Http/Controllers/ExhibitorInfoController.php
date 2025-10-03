<?php


namespace App\Http\Controllers;

use App\Models\ExhibitorInfo;
use App\Models\ExhibitorProduct;
use App\Models\ExhibitorPressRelease;
use App\Models\Application;
use http\Env\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Sponsorship;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SponsorInvoiceMail;

class ExhibitorInfoController extends Controller
{

    //construct function 
    /*public function __construct()
    {
        // $user = auth()->user();
        // if (!auth()->check()) {
        //     redirect('/login')->send();
        // }
        // if ($user && $user->role == 'exhibitor') {
        //     $application = Application::where('user_id', $user->id)
        //         ->where('submission_status', 'approved')
        //         ->whereHas('invoices.payments', function ($query) {
        //             $query->where('status', 'successful');
        //         })
        //         ->first();

        //         dd($application);

        //     if (!$application) {
        //         redirect()->route('event.list')->send();
        //     }
        // }
    }
*/

// get the application id from application table where user_id is logged in user id
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // get the current controller method name
            $method = $request->route()->getActionMethod();

            // skip middleware logic for specific methods
            if (in_array($method, ['listExhibitors', 'getExhibitorDetails', 'getExhibitorForEdit', 'updateExhibitor'])) {
                return $next($request);
            }

            // ✅ Normal checks
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // check if the user is exhibitor
            if (Auth::user()->role != 'exhibitor') {
                return redirect()->route('event.list')->with('error', 'You are not authorized to access this page.');
            }

            $applicationId = Application::where('user_id', Auth::id())
                ->where('submission_status', 'approved')
                ->whereHas('invoices.payments', function ($query) {
                    $query->where('status', 'successful');
                })
                ->value('id');

            if (!$applicationId) {
                return redirect()->route('event.list')->with('error', 'You are not authorized to access this page.');
            }

            return $next($request);
        });
    }

    //function application id
    public function getApplicationId()
    {
        return Application::where('user_id', Auth::id())
            ->where('submission_status', 'approved')
            ->whereHas('invoices.payments', function ($query) {
                $query->where('status', 'successful');
            })
            ->value('id');
    }


    public function showForm(Request $request)
    {

        // dd($request->all());

        //check user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // // check if the user is exhibitor
        // if (Auth::user()->role != 'exhibitor') {
        //     return redirect()->route('event.list')->with('error', 'You are not authorized to access this page.');
        // }

        // get the application id from application table where user_id is logged in user id
        $applicationId = Application::where('user_id', Auth::id())
            ->where('submission_status', 'approved')
            ->whereHas('invoices.payments', function ($query) {
                $query->where('status', 'successful');
            })
            ->value('id');

        // dd($applicationId);


        // check if the user is
        $application = Application::findOrFail($applicationId);
        $add1 = $application->address ?? '';
        $city = $application->city_id ?? '';
        $state = ($application->state && isset($application->state->name)) ? $application->state->name : '';
        $country = ($application->country && isset($application->country->name)) ? $application->country->name : '';
        $zip = $application->postal_code ?? '';
        $application->full_address = $add1 . ', ' . $city . ', ' . $state . ', ' . $country . ', ' . $zip;

        //changes in backend


        $slug = "Exhibitor Directory Information";

        //find the exhibitor info from exhibitor_info table where application_id is application id
        $exhibitorInfo = ExhibitorInfo::where('application_id', $applicationId)->first();
        return view('exhibitor_info.form', compact('application', 'slug', 'exhibitorInfo'));
    }

    public function storeExhibitor(Request $request)
    {
        $applicationId = $this->getApplicationId();
        // pass this application id to the request
        $request->merge(['application_id' => $applicationId]);

        // dd($request->all());
        $data = $request->validate([
            'application_id' => 'required|integer |exists:applications,id',
            'fascia_name' => 'required|string|max:255',
            'salutation' => 'required|string|max:15',
            'contact_first_name' => 'required |string|max:255',
            'contact_last_name' => 'required |string|max:255',
            'designation' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:16',
            'description' => 'required|string|max:1000',
            'address' => 'nullable|string|max:500',
            'logo' => 'nullable|image|max:2048',
            'website' => 'nullable|string|max:500',
            'linkedin' => 'nullable|url',
            'instagram' => 'nullable|url',
            'facebook' => 'nullable|url',
            'youtube' => 'nullable|url',

        ]);

        // get the application id from application table where user_id is logged in user id


        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        // create or update if already exists give the individual column names 

        $exhibitor = ExhibitorInfo::updateOrCreate(
            ['application_id' => $applicationId],
            [
                'fascia_name' => $data['fascia_name'],
                'contact_person' => $data['salutation'] . ' ' . $data['contact_first_name'] . ' ' . $data['contact_last_name'],
                'designation' => $data['designation'],
                'email' => $data['email'],
                'website' => $data['website'] ?? null,
                'phone' => $data['phone'],
                'description' => $data['description'],
                'address' => $data['address'],
                'logo' => $data['logo'] ?? (ExhibitorInfo::where('application_id', $applicationId)->value('logo')),
                'linkedin' => $data['linkedin'] ?? null,
                'instagram' => $data['instagram'] ?? null,
                'facebook' => $data['facebook'] ?? null,
                'youtube' => $data['youtube'] ?? null,
                'application_id' => $data['application_id'],
                'submission_status' => 1,
            ]
        );


        // $exhibitor = ExhibitorInfo::create($data);

        //redirect back with thank you for filling out the exhibitor directory fields
        return redirect()->route('exhibitor.info')->with('success', 'Thank you for filling out the exhibitor directory information.');


        return redirect()->route('exhibitor.products', $exhibitor->id);
    }

    public function showProductForm(Request $request)
    {
        $slug = "Exhibitor Product Information";
        $applicationId = $this->getApplicationId();
        // check if the user is exhibitor
        if (Auth::user()->role != 'exhibitor') {
            return redirect()->route('event.list')->with('error', 'You are not authorized to access this page.');
        }

        // find the exhibitor info from exhibitor_info table where application_id is application id
        $exhibitorInfo = ExhibitorInfo::where('application_id', $applicationId)->first();
        // check if the exhibitor info is there or not
        if (!$exhibitorInfo) {
            return redirect()->route('exhibitor.form')->with('error', 'Please fill the exhibitor information form first.');
        }


        // check if the exhibitorProduct is there or not
        $exhibitorProducts = ExhibitorProduct::where('application_id', $applicationId);

        // print_r($applicationId);
        // dd($exhibitorProducts);

        // dd($exhibitorProducts->count());
        if ($exhibitorProducts->count() == 1) {
            $exhibitorProducts = $exhibitorProducts->get();
        } else {
            $exhibitorProducts = $exhibitorProducts->get();
        }


        // $exhibitor = ExhibitorInfo::findOrFail($id);
        return view('exhibitor_info.product_form', compact('exhibitorInfo', 'slug', 'exhibitorProducts'));
    }

    public function productStore(Request $request)
    {
        $applicationId = $this->getApplicationId();


        $data = $request->validate([
            'product_name' => 'required',
            'description' => 'required|string|max:1000',
            'product_image' => 'required|image|max:2048',
        ]);


        if ($request->hasFile('product_image')) {
            $data['product_image'] = $request->file('product_image')->store('products', 'public');
        }

        // create a new product using application id

        $exhibitorInfo = ExhibitorInfo::where('application_id', $applicationId)->first();
        //create a new product using application id
        $data['application_id'] = $applicationId;
        $product = ExhibitorProduct::create([
            'application_id' => $applicationId,
            'product_name' => $data['product_name'],
            'description' => $data['description'],
            'product_image' => $data['product_image'] ?? null,
        ]);


        // $data['exhibitor_id'] = $id;
        // ExhibitorProduct::create($data);

        return back()->with('success', 'Product added.');
    }

    public function showPressForm($id)
    {
        $exhibitor = ExhibitorInfo::findOrFail($id);
        return view('exhibitor.press-form', compact('exhibitor'));
    }

    public function storePress(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required',
            'summary' => 'nullable',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file')->store('press', 'public');
        }

        $data['exhibitor_id'] = $id;
        ExhibitorPressRelease::create($data);

        return back()->with('success', 'Press release uploaded.');
    }

    // make a function to work as middleware to check if the user is logged in and is admin
    public function adminMiddleware()
    {
//        dd('admin middleware');
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        if (Auth::user()->role !== 'admin') {
            return redirect()->route('user.dashboard')->with('error', 'You are not authorized to access this page.');
        }

        return null;
    }

    // make a function to show all the exhibitor info to the admin also analytics to the admin how many have filled and how many are left to fill
    public function listExhibitors()
    {

        //dd('admin exhibitor info');
        // Check admin middleware
        $middlewareResponse = $this->adminMiddleware();
        if ($middlewareResponse) {
            return $middlewareResponse;
        }


        $totalApplications = Application::where('submission_status', 'approved')
            ->whereHas('invoices.payments', function ($query) {
                $query->where('status', 'successful');
            })
            ->count();

        // Get exhibitor info with application details
        $exhibitorInfo = ExhibitorInfo::with(['application.user'])
            ->get();



        // Calculate analytics
        $filledCount = $exhibitorInfo->count();
        $notFilledCount = $totalApplications - $filledCount;
        $completionRate = $totalApplications > 0 ? round(($filledCount / $totalApplications) * 100, 1) : 0;

        // Get detailed breakdown
        $submissionStatusBreakdown = $exhibitorInfo->groupBy('submission_status')
            ->map(function ($group) {
                return $group->count();
            });

        // Get exhibitor info with missing data
        $incompleteInfo = $exhibitorInfo->filter(function ($exhibitor) {
            return empty($exhibitor->description) ||
                empty($exhibitor->logo) ||
                empty($exhibitor->website) ||
                empty($exhibitor->address);
        });

        // Get recent submissions (last 30 days)
        $recentSubmissions = $exhibitorInfo->filter(function ($exhibitor) {
            return $exhibitor->created_at && $exhibitor->created_at->diffInDays(now()) <= 30;
        });

        // Analytics data
        $analytics = [
            'total_applications' => $totalApplications,
            'filled_count' => $filledCount,
            'not_filled_count' => $notFilledCount,
            'completion_rate' => $completionRate,
            'submission_status_breakdown' => $submissionStatusBreakdown,
            'incomplete_count' => $incompleteInfo->count(),
            'recent_submissions' => $recentSubmissions->count(),
            'products_count' => 0,
            'press_releases_count' => 0
        ];

        return view('admin.exhibitor-info', compact('exhibitorInfo', 'analytics'));
    }


    public function allExhibitors(){
        // how to ignore the construct function in this function

        dd('all exhibitors');
    }

    // API endpoint to get exhibitor details
    public function getExhibitorDetails($id)
    {
        try {
            $exhibitor = ExhibitorInfo::with(['application.user'])
                ->findOrFail($id);

            // Get social media links
            $socialMedia = [
                'website' => $exhibitor->website,
                'linkedin' => $exhibitor->linkedin,
                'instagram' => $exhibitor->instagram,
                'facebook' => $exhibitor->facebook,
                'youtube' => $exhibitor->youtube,
            ];

            // Filter out empty social media links
            $socialMedia = array_filter($socialMedia, function($value) {
                return !empty($value);
            });

            $data = [
                'id' => $exhibitor->id,
                'fascia_name' => $exhibitor->fascia_name,
                'contact_person' => $exhibitor->contact_person,
                'designation' => $exhibitor->designation,
                'email' => $exhibitor->email,
                'phone' => $exhibitor->phone,
                'address' => $exhibitor->address,
                'description' => $exhibitor->description,
                'logo' => $exhibitor->logo ? asset('storage/' . $exhibitor->logo) : null,
                'social_media' => $socialMedia,
                'submission_status' => $exhibitor->submission_status,
                'created_at' => $exhibitor->created_at ? $exhibitor->created_at->format('M d, Y \a\t h:i A') : null,
                'updated_at' => $exhibitor->updated_at ? $exhibitor->updated_at->format('M d, Y \a\t h:i A') : null,
                'application' => [
                    'company_name' => $exhibitor->application->company_name ?? 'N/A',
                    'user' => [
                        'name' => $exhibitor->application->user->name ?? 'N/A',
                        'email' => $exhibitor->application->user->email ?? 'N/A',
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exhibitor not found or error occurred',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // API endpoint to get exhibitor data for editing
    public function getExhibitorForEdit($id)
    {
        try {
            $exhibitor = ExhibitorInfo::with(['application.user'])
                ->findOrFail($id);

            $data = [
                'id' => $exhibitor->id,
                'fascia_name' => $exhibitor->fascia_name,
                'contact_person' => $exhibitor->contact_person,
                'designation' => $exhibitor->designation,
                'email' => $exhibitor->email,
                'phone' => $exhibitor->phone,
                'address' => $exhibitor->address,
                'description' => $exhibitor->description,
                'logo' => $exhibitor->logo,
                'website' => $exhibitor->website,
                'linkedin' => $exhibitor->linkedin,
                'instagram' => $exhibitor->instagram,
                'facebook' => $exhibitor->facebook,
                'youtube' => $exhibitor->youtube,
                'submission_status' => $exhibitor->submission_status,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exhibitor not found or error occurred',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // API endpoint to update exhibitor information
    public function updateExhibitor(Request $request, $id)
    {
        try {
            $exhibitor = ExhibitorInfo::findOrFail($id);

            $data = $request->validate([
                'fascia_name' => 'required|string|max:255',
                'contact_person' => 'required|string|max:255',
                'designation' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:16',
                'address' => 'nullable|string|max:500',
                'description' => 'required|string|max:1000',
                'website' => 'nullable|string|max:500',
                'linkedin' => 'nullable|url|max:500',
                'instagram' => 'nullable|url|max:500',
                'facebook' => 'nullable|url|max:500',
                'youtube' => 'nullable|url|max:500',
                'submission_status' => 'required|integer|in:0,1',
                'logo' => 'nullable|image|max:2048',
            ]);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($exhibitor->logo && \Storage::disk('public')->exists($exhibitor->logo)) {
                    \Storage::disk('public')->delete($exhibitor->logo);
                }
                $data['logo'] = $request->file('logo')->store('logos', 'public');
            } else {
                // Keep existing logo if no new one uploaded
                unset($data['logo']);
            }

            $exhibitor->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Exhibitor information updated successfully',
                'data' => [
                    'id' => $exhibitor->id,
                    'fascia_name' => $exhibitor->fascia_name,
                    'submission_status' => $exhibitor->submission_status,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update exhibitor information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
