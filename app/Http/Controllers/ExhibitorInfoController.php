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
use Illuminate\Support\Facades\Storage;

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
        //dd($request->all());    
        $applicationId = $this->getApplicationId();
        // pass this application id to the request
        $request->merge(['application_id' => $applicationId]);

        // dd($request->all());
        $data = $request->validate([
            'application_id' => 'required|integer |exists:applications,id',
            'company_name' => 'required|string|max:255',
            'fascia_name' => 'required|string|max:255',
            'salutation' => 'required|string|max:15',
            'contact_first_name' => 'required |string|max:255',
            'contact_last_name' => 'required |string|max:255',
            'designation' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:16',
            'telPhone' => 'required|string|max:16',
            'description' => 'required|string|max:1000',
            'address' => 'nullable|string|max:500',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
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
                'company_name' => $data['company_name'],
                'website' => $data['website'] ?? null,
                'phone' => $this->formatPhoneNumber($data['phone']),
                'telPhone' => $this->formatPhoneNumber($data['telPhone']),
                'description' => strip_tags(trim($data['description'])),
                'address' => $data['address'],
                'country' => $data['country'],
                'state' => $data['state'],
                'city' => $data['city'],
                'zip_code' => $data['zip_code'],
                'logo' => $data['logo'] ?? (ExhibitorInfo::where('application_id', $applicationId)->value('logo')),
                'linkedin' => $data['linkedin'] ?? null,
                'instagram' => $data['instagram'] ?? null,
                'facebook' => $data['facebook'] ?? null,
                'youtube' => $data['youtube'] ?? null,
                'application_id' => $data['application_id'],
                'submission_status' => 1,
            ]
        );


        $exhibitor = ExhibitorInfo::create($data);

        //redirect back with thank you for filling out the exhibitor directory fields
        return redirect()->route('exhibitor.info')->with('success', 'Thank you for filling out the exhibitor directory information.');
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


    public function allExhibitors()
    {
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
            $socialMedia = array_filter($socialMedia, function ($value) {
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
                if ($exhibitor->logo && Storage::disk('public')->exists($exhibitor->logo)) {
                    Storage::disk('public')->delete($exhibitor->logo);
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

    /**
     * Format phone number with dash between country code and number
     */
    private function formatPhoneNumber($phoneNumber)
    {
        if (empty($phoneNumber)) {
            return $phoneNumber;
        }

        // Remove any existing formatting
        $phoneNumber = preg_replace('/[^+\d]/', '', $phoneNumber);
        
        // If it starts with +, find where country code ends and number begins
        if (strpos($phoneNumber, '+') === 0) {
            // Known country codes and their lengths
            $countryCodes = [
                '1' => 1,     // US/Canada
                '7' => 1,     // Russia/Kazakhstan
                '20' => 2,    // Egypt
                '27' => 2,    // South Africa
                '30' => 2,    // Greece
                '31' => 2,    // Netherlands
                '32' => 2,    // Belgium
                '33' => 2,    // France
                '34' => 2,    // Spain
                '36' => 2,    // Hungary
                '39' => 2,    // Italy
                '40' => 2,    // Romania
                '41' => 2,    // Switzerland
                '43' => 2,    // Austria
                '44' => 2,    // UK
                '45' => 2,    // Denmark
                '46' => 2,    // Sweden
                '47' => 2,    // Norway
                '48' => 2,    // Poland
                '49' => 2,    // Germany
                '51' => 2,    // Peru
                '52' => 2,    // Mexico
                '53' => 2,    // Cuba
                '54' => 2,    // Argentina
                '55' => 2,    // Brazil
                '56' => 2,    // Chile
                '57' => 2,    // Colombia
                '58' => 2,    // Venezuela
                '60' => 2,    // Malaysia
                '61' => 2,    // Australia
                '62' => 2,    // Indonesia
                '63' => 2,    // Philippines
                '64' => 2,    // New Zealand
                '65' => 2,    // Singapore
                '66' => 2,    // Thailand
                '81' => 2,    // Japan
                '82' => 2,    // South Korea
                '84' => 2,    // Vietnam
                '86' => 2,    // China
                '90' => 2,    // Turkey
                '91' => 2,    // India
                '92' => 2,    // Pakistan
                '93' => 2,    // Afghanistan
                '94' => 2,    // Sri Lanka
                '95' => 2,    // Myanmar
                '98' => 2,    // Iran
                '212' => 3,   // Morocco
                '213' => 3,   // Algeria
                '216' => 3,   // Tunisia
                '218' => 3,   // Libya
                '220' => 3,   // Gambia
                '221' => 3,   // Senegal
                '222' => 3,   // Mauritania
                '223' => 3,   // Mali
                '224' => 3,   // Guinea
                '225' => 3,   // Ivory Coast
                '226' => 3,   // Burkina Faso
                '227' => 3,   // Niger
                '228' => 3,   // Togo
                '229' => 3,   // Benin
                '230' => 3,   // Mauritius
                '231' => 3,   // Liberia
                '232' => 3,   // Sierra Leone
                '233' => 3,   // Ghana
                '234' => 3,   // Nigeria
                '235' => 3,   // Chad
                '236' => 3,   // Central African Republic
                '237' => 3,   // Cameroon
                '238' => 3,   // Cape Verde
                '239' => 3,   // São Tomé and Príncipe
                '240' => 3,   // Equatorial Guinea
                '241' => 3,   // Gabon
                '242' => 3,   // Republic of the Congo
                '243' => 3,   // Democratic Republic of the Congo
                '244' => 3,   // Angola
                '245' => 3,   // Guinea-Bissau
                '246' => 3,   // British Indian Ocean Territory
                '248' => 3,   // Seychelles
                '249' => 3,   // Sudan
                '250' => 3,   // Rwanda
                '251' => 3,   // Ethiopia
                '252' => 3,   // Somalia
                '253' => 3,   // Djibouti
                '254' => 3,   // Kenya
                '255' => 3,   // Tanzania
                '256' => 3,   // Uganda
                '257' => 3,   // Burundi
                '258' => 3,   // Mozambique
                '260' => 3,   // Zambia
                '261' => 3,   // Madagascar
                '262' => 3,   // Mayotte
                '263' => 3,   // Zimbabwe
                '264' => 3,   // Namibia
                '265' => 3,   // Malawi
                '266' => 3,   // Lesotho
                '267' => 3,   // Botswana
                '268' => 3,   // Swaziland
                '269' => 3,   // Comoros
                '290' => 3,   // Saint Helena
                '291' => 3,   // Eritrea
                '297' => 3,   // Aruba
                '298' => 3,   // Faroe Islands
                '299' => 3,   // Greenland
                '350' => 3,   // Gibraltar
                '351' => 3,   // Portugal
                '352' => 3,   // Luxembourg
                '353' => 3,   // Ireland
                '354' => 3,   // Iceland
                '355' => 3,   // Albania
                '356' => 3,   // Malta
                '357' => 3,   // Cyprus
                '358' => 3,   // Finland
                '359' => 3,   // Bulgaria
                '370' => 3,   // Lithuania
                '371' => 3,   // Latvia
                '372' => 3,   // Estonia
                '373' => 3,   // Moldova
                '374' => 3,   // Armenia
                '375' => 3,   // Belarus
                '376' => 3,   // Andorra
                '377' => 3,   // Monaco
                '378' => 3,   // San Marino
                '380' => 3,   // Ukraine
                '381' => 3,   // Serbia
                '382' => 3,   // Montenegro
                '383' => 3,   // Kosovo
                '385' => 3,   // Croatia
                '386' => 3,   // Slovenia
                '387' => 3,   // Bosnia and Herzegovina
                '389' => 3,   // North Macedonia
                '420' => 3,   // Czech Republic
                '421' => 3,   // Slovakia
                '423' => 3,   // Liechtenstein
                '500' => 3,   // Falkland Islands
                '501' => 3,   // Belize
                '502' => 3,   // Guatemala
                '503' => 3,   // El Salvador
                '504' => 3,   // Honduras
                '505' => 3,   // Nicaragua
                '506' => 3,   // Costa Rica
                '507' => 3,   // Panama
                '508' => 3,   // Saint Pierre and Miquelon
                '509' => 3,   // Haiti
                '590' => 3,   // Guadeloupe
                '591' => 3,   // Bolivia
                '592' => 3,   // Guyana
                '593' => 3,   // Ecuador
                '594' => 3,   // French Guiana
                '595' => 3,   // Paraguay
                '596' => 3,   // Martinique
                '597' => 3,   // Suriname
                '598' => 3,   // Uruguay
                '599' => 3,   // Netherlands Antilles
                '670' => 3,   // East Timor
                '672' => 3,   // Australian External Territories
                '673' => 3,   // Brunei
                '674' => 3,   // Nauru
                '675' => 3,   // Papua New Guinea
                '676' => 3,   // Tonga
                '677' => 3,   // Solomon Islands
                '678' => 3,   // Vanuatu
                '679' => 3,   // Fiji
                '680' => 3,   // Palau
                '681' => 3,   // Wallis and Futuna
                '682' => 3,   // Cook Islands
                '683' => 3,   // Niue
                '684' => 3,   // American Samoa
                '685' => 3,   // Samoa
                '686' => 3,   // Kiribati
                '687' => 3,   // New Caledonia
                '688' => 3,   // Tuvalu
                '689' => 3,   // French Polynesia
                '690' => 3,   // Tokelau
                '691' => 3,   // Micronesia
                '692' => 3,   // Marshall Islands
                '850' => 3,   // North Korea
                '852' => 3,   // Hong Kong
                '853' => 3,   // Macau
                '855' => 3,   // Cambodia
                '856' => 3,   // Laos
                '880' => 3,   // Bangladesh
                '886' => 3,   // Taiwan
                '960' => 3,   // Maldives
                '961' => 3,   // Lebanon
                '962' => 3,   // Jordan
                '963' => 3,   // Syria
                '964' => 3,   // Iraq
                '965' => 3,   // Kuwait
                '966' => 3,   // Saudi Arabia
                '967' => 3,   // Yemen
                '968' => 3,   // Oman
                '970' => 3,   // Palestine
                '971' => 3,   // UAE
                '972' => 3,   // Israel
                '973' => 3,   // Bahrain
                '974' => 3,   // Qatar
                '975' => 3,   // Bhutan
                '976' => 3,   // Mongolia
                '977' => 3,   // Nepal
                '992' => 3,   // Tajikistan
                '993' => 3,   // Turkmenistan
                '994' => 3,   // Azerbaijan
                '995' => 3,   // Georgia
                '996' => 3,   // Kyrgyzstan
                '998' => 3,   // Uzbekistan
            ];

            // Try to match known country codes
            foreach ($countryCodes as $code => $length) {
                if (strpos($phoneNumber, '+' . $code) === 0) {
                    $remaining = substr($phoneNumber, 1 + $length);
                    if (strlen($remaining) > 0) {
                        return '+' . $code . '-' . $remaining;
                    }
                }
            }
            
            // Fallback: try common patterns if no match found
            if (preg_match('/^\+(\d{1,3})(\d+)$/', $phoneNumber, $matches)) {
                return '+' . $matches[1] . '-' . $matches[2];
            }
        }
        
        return $phoneNumber;
    }
}
