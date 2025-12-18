<?php


namespace App\Http\Controllers;


use App\Models\Application;
use Illuminate\Http\Request;

use App\Models\Attendee;

use Illuminate\Support\Str;

use Illuminate\Support\Facades\Validator;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Mail\AttendeeConfirmationMail;

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\DB;

use App\Exports\AttendeesExport;

use Maatwebsite\Excel\Facades\Excel;

use App\Models\OTP;

use App\Services\CaptchaService;

use App\Models\Country;

use Illuminate\Support\Facades\Log;

use Barryvdh\DomPDF\Facade\Pdf;

use Dompdf\Dompdf;

use App\Models\ComplimentaryDelegate;

use App\Models\StallManning;

// Assuming StallManning is the

use App\Mail\AttendeeApprovalMail;

// Assuming this is the mail class for attendee approval

use App\Exports\ExhibitorInauguralExport;

// Assuming this is the export class for exhibitor inaugural

use Illuminate\Support\Collection;

use Maatwebsite\Excel\Excel as ExcelFormat;


//pdf


class AttendeeController extends Controller

{


    protected $captchaService;


    public function __construct(CaptchaService $captchaService)

    {

        $this->captchaService = $captchaService;

    }

    //generate unique id  if exist then generate again

    public function generateUniqueId()

    {

        $uniqueId = uniqid('SM_VST_', true);

        // Check if the ID already exists in the database

        // Assuming you have a Visitor model and a visitors table

        while (Attendee::where('unique_id', $uniqueId)->exists()) {

            $uniqueId = uniqid('SM_VST_', true);

        }

        return $uniqueId;

    }

    //

    public function showForm()

    {


        // Generate the CAPTCHA SVG

        $captchaSvg = $this->captchaService->generate();


        $maxAttendees = config('constants.max_attendees');

        $natureOfBusiness = config('constants.sectors');

        $natureOfBusiness = array_map(function ($sector) {

            return ['name' => $sector];

        }, $natureOfBusiness);


        // $maxAttendees = 5;

        $productCategories = config('constants.product_categories');

        $jobFunctions = config('constants.job_functions');

        $countries = Country::all();


        return view('attendee.register', compact(

            'maxAttendees',

            'natureOfBusiness',

            'productCategories',

            'jobFunctions',

            'captchaSvg',

            'countries'

        ));

    }

    public function showForm2()

    {


        // Generate the CAPTCHA SVG

        $captchaSvg = $this->captchaService->generate();


        $maxAttendees = config('constants.max_attendees');

        $natureOfBusiness = config('constants.sectors');

        $natureOfBusiness = array_map(function ($sector) {

            return ['name' => $sector];

        }, $natureOfBusiness);


        // $maxAttendees = 5;

        $productCategories = config('constants.product_categories');

        $jobFunctions = config('constants.job_functions');

        $countries = Country::all();


        return view('attendee.register_new', compact(

            'maxAttendees',

            'natureOfBusiness',

            'productCategories',

            'jobFunctions',

            'captchaSvg',

            'countries'

        ));

    }


    public function visitor_reg(Request $request)

    {


        Log::info('Visitor registration started', [

            'request_data' => $request->all(),

        ]);


        // Validate the incoming request

        $validator = Validator::make($request->all(), [

            'attendees' => 'required|array|min:1', // Example for attendees

            'captcha' => [

                'required',

                // Custom validation logic using a closure

                function ($attribute, $value, $fail) {

                    if (!$this->captchaService->validate($value)) {

                        $fail('The CAPTCHA is incorrect. Please try again.');

                    }

                },

            ],

        ]);


        // if ($validator->fails()) {

        //     if ($validator->errors()->has('captcha')) {

        //         return redirect()->back()->withErrors(['captcha' => 'Please enter the correct captcha.'])->withInput();

        //     }

        //     return redirect()->back()->withErrors($validator)->withInput();

        // }


        // $validated = $validator->validated();


        $attendees = $request->input('attendees');


        // Google reCAPTCHA check

        // $recaptchaResponse = $request->input('g-recaptcha-response');

        // $recaptchaSecret = "6LdNTRorAAAAAGmpwzLuEPV5syp42NDJwkBM4pF4";

        // $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';


        // $recaptchaResponse = file_get_contents($recaptchaUrl . '?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);

        // $recaptchaResponseKeys = json_decode($recaptchaResponse, true);


        // if (!$recaptchaResponseKeys['success']) {

        //     return redirect()->back()->withErrors(['recaptcha' => 'reCAPTCHA verification failed.'])->withInput();

        // }


        //


        $maxAttendees = 1;

        if (count($attendees) > $maxAttendees) {

            return redirect()->back()->withErrors(['attendees' => 'You can only register a maximum of ' . $maxAttendees . ' attendee(s).'])->withInput();

        }


        foreach ($attendees as $index => $attendee) {


            $validator = Validator::make($attendee, [

                'title' => 'nullable|string|max:10',

                'first_name' => 'required|string|max:100',

                'last_name' => 'required|string|max:100',

                'designation' => 'required|string|max:100',

                'company' => 'required|string|max:150',

                'address' => 'nullable|string|max:255',

                'country' => 'required|string|max:100',

                'state' => 'nullable|string|max:100',

                'city' => 'nullable|string|max:100',

                'postal_code' => 'nullable|string|max:20',

                'mobile' => 'required|string|max:20',

                'email' => [

                    'required',

                    'email',

                    'max:100',

                    function ($attribute, $value, $fail) {

                        $tables = ['attendees', 'complimentary_delegates', 'stall_manning'];

                        foreach ($tables as $table) {

                            if (\DB::table($table)->where('email', $value)->exists()) {

                                $fail('The email has already been taken.');

                            }

                        }

                    },

                ],

                'purpose' => [

                    function ($attribute, $value, $fail) use ($attendee) {

                        $category = $attendee['badge_category'] ?? 'Visitor';

                        if (in_array($category, ['Industry', 'Exhibitor'])) {

                            if (!isset($value) || !is_array($value) || count($value) < 1) {

                                $fail('The purpose field is required for Industry and Exhibitor.');

                            }

                        }

                    },

                ],

                'products' => [

                    function ($attribute, $value, $fail) use ($attendee) {

                        $category = $attendee['badge_category'] ?? 'Visitor';

                        if (in_array($category, ['Industry', 'Exhibitor'])) {

                            if (!isset($value) || !is_array($value) || count($value) < 1) {

                                $fail('The products field is required for Industry and Exhibitor.');

                            }

                        }

                    },

                ],

                'business_nature' => [

                    function ($attribute, $value, $fail) use ($attendee) {

                        $category = $attendee['badge_category'] ?? 'Visitor';

                        if (in_array($category, ['Industry', 'Exhibitor'])) {

                            if (!isset($value) || !is_array($value) || count($value) < 1) {

                                $fail('The business nature field is required for Industry and Exhibitor.');

                            }

                        }

                    },

                ],

                'job_category' => 'nullable|string|max:150',

                "job_subcategory" => 'nullable|string|max:150',

                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:1024', // 1MB max size

                'id_card_type' => 'nullable|string|max:50',

                'id_card_number' => 'nullable|string|max:20',

                'consent' => 'required',

                'inaugural_session' => 'nullable|boolean',

                'pm_inaugural' => 'nullable|boolean',

                'source' => 'nullable|string',

                'event_days' => 'required|array',

                'registration_type' => 'required|in:Online,Onsite,In-Person',

                'other_job_category' => 'nullable|string|max:150',

                'email_consent' => 'nullable|string|in:on,off',

                'startup' => [

                    function ($attribute, $value, $fail) use ($attendee) {

                        $category = $attendee['badge_category'] ?? 'Visitor';

                        if ($category === 'Industry') {

                            if (!isset($value)) {

                                $fail('The startup field is required for Industry.');

                            } elseif (!is_bool($value) && !in_array($value, [0, 1, '0', '1'], true)) {

                                $fail('The startup field must be boolean.');

                            }

                        }

                    },

                ],

            ]);


            if ($validator->fails()) {

                // Retain all submitted data using withInput

                return redirect()->back()->withErrors($validator)->withInput();

            }


            // Generate unique ID

            do {

                $uniqueId = 'SEMI25VI_' . strtoupper(Str::random(6));

            } while (Attendee::where('unique_id', $uniqueId)->exists());


            // Upload profile picture if exists

            if ($request->hasFile('attendees.' . $index . '.profile_picture')) {

                $profilePicture = $request->file('attendees.' . $index . '.profile_picture');

                $extension = strtolower($profilePicture->getClientOriginalExtension());


                // Double-check file type

                if (!in_array($extension, ['jpeg', 'jpg', 'png'])) {

                    return redirect()->back()->withErrors(['profile_picture' => 'Only JPEG, JPG and PNG files are allowed.'])->withInput();

                }


                $profilePicturePath = $profilePicture->storeAs(

                    'profile_pictures',

                    $uniqueId . '.' . $extension . '.' . $extension,

                    'public'

                );

            } else {

                $profilePicturePath = null;

            }

            // Generate QR

            //$qrCodePath = public_path('qrcodes/' . $uniqueId . '.png');

            //\QrCode::size(200)->format('png')->generate($uniqueId, $qrCodePath);


            //$qrCodePath = str_replace(public_path(), 'https://portal.semiconindia.org', $qrCodePath);


            $qrCodePath = null;


            // check if the user otp is verified or not from the  OTP model and email of the user

            $otpVerified = OTP::where('identifier', $attendee['email'])
                ->where('verified', true)
                ->exists();


            if (!$otpVerified) {

                return redirect()->back()->withErrors(['email' => 'OTP verification is required for this email.'])->withInput();

            }


            //var_dump($attendee['event_days']);


            // dd($attendee);

            // die();


            // Save attendee

            Attendee::create([

                'unique_id' => $uniqueId,

                'status' => 'pending',

                'badge_category' => 'Visitor',

                'title' => $attendee['title'] ?? null,

                'first_name' => $attendee['first_name'],

                'middle_name' => $attendee['middle_name'] ?? null,

                'last_name' => $attendee['last_name'],

                'designation' => $attendee['designation'],

                'company' => $attendee['company'],

                'address' => $attendee['address'] ?? null,

                'country' => $attendee['country'],

                'state' => $attendee['state'] ?? null,

                'city' => $attendee['city'] ?? null,

                'postal_code' => $attendee['postal_code'] ?? null,

                'mobile' => $attendee['mobile'],

                'email' => $attendee['email'],

                'purpose' => json_encode($attendee['purpose'] ?? []),

                'products' => json_encode($attendee['products'] ?? []),

                'business_nature' => json_encode($attendee['business_nature'] ?? []),

                'job_category' => $attendee['job_category'] ?? null,

                'job_subcategory' => $attendee['job_subcategory'] ?? null,

                'consent' => $attendee['consent'] === 'on' ? true : false,

                'qr_code_path' => $qrCodePath,

                'profile_picture' => $profilePicturePath ? 'storage/' . $profilePicturePath : null,

                'id_card_type' => $attendee['id_card_type'] ?? null,

                'id_card_number' => $attendee['id_card_number'] ?? null,

                'source' => $attendee['source'] ?? null,

                'inaugural_session' => $attendee['pm_inaugural'] ?? false,

                'registration_type' => $attendee['registration_type'],

                'event_days' => json_encode($attendee['event_days']) ?? [],

                'other_job_category' => $attendee['other_job_category'] ?? null,

                'promotion_consent' => $attendee['email_consent'] ?? false,

                'startup' => $attendee['startup'] ?? false,

            ]);


            // Send email

            $data = [

                'unique_id' => $uniqueId,

                'email' => $attendee['email'],

                'qr_code_path' => $qrCodePath,

                'name' => $attendee['first_name'] . ' ' . $attendee['middle_name'] . ' ' . $attendee['last_name'],

                'ticket_type' => 'Visitor',

                'mobile' => $attendee['mobile'],

                'company_name' => $attendee['company'] ?? '-',

                'designation' => $attendee['designation'] ?? '-',

                'registration_date' => now()->format('Y-m-d'),

                'registration_type' => $attendee['registration_type'] === 'Online' ? 1 : 0,

                'id_card_number' => $attendee['id_card_number'] ?? 'N/A',

                'id_card_type' => $attendee['id_card_type'] ?? 'N/A',

                'dates' => is_array($attendee['event_days'])

                    ? implode(', ', $attendee['event_days'])

                    : implode(', ', json_decode($attendee['event_days'], true) ?? []),


            ];

            $apiRelayController = new \App\Http\Controllers\ApiRelayController();
            $apiRelayController->sendDataToApiNew($uniqueId);


            // Mail::to($attendee['email'])
            //     ->bcc(['test.interlinks@gmail.com'])
            //     ->queue(new AttendeeConfirmationMail($data));

        }


        return redirect()->route('visitor_thankyou', ['id' => $uniqueId]);

    }


    public function thankyou($id)

    {

        $attendee = Attendee::where('unique_id', $id)->firstOrFail();


        return view('attendee.thankyou', [

            'attendee' => $attendee,

            'qrCode' => $attendee->qr_code_path,

        ]);

    }


    //makee a private function to validatee the admin user

    private function validateAdminUser()

    {

        $user = auth()->user();

        //if not user is logged in then redirect to login page

        if (!auth()->check()) {

            return redirect('/login');

        }

        //if user is not admin then redirect to home page

        if ($user->role !== 'admin') {

            return redirect()->back()->withErrors(['error' => 'You do not have permission to access this page.']);

        }

    }


    ///list of all attendees to admin

    public function listAttendees(Request $request)

    {

        // Validate the admin user

        $this->validateAdminUser();


        $attendees = Attendee::query();


        if ($request->has('search')) {

            $search = $request->input('search');

            $attendees->where(function ($query) use ($search) {

                $query->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('company', 'like', "%$search%")
                    ->orWhere('unique_id', 'like', "%$search%");

            });

        }


        $attendees = $attendees->paginate(20);

        $slug = "Attendee List";


        return view('attendee.attendee_list', compact('attendees', 'slug'));

    }

    public function export()

    {


        ini_set('memory_limit', '-1');

        $this->validateAdminUser();

        $status = request()->input('status', 'all');

        // Log into useractivity channel

        Log::channel('useractivity')->info('Exporting attendees', [

            'status' => $status,

            'user_ip' => request()->ip(),

            'user_id' => auth()->id(),

            'timestamp' => now(),

        ]);


        //get the status from the request

        // Validate the status

        $validStatuses = ['1', '0', 'all'];

        if (!in_array($status, $validStatuses)) {

            return redirect()->back()->withErrors(['status' => 'Invalid status provided.']);

        }


        $filename = 'attendees_' . $status . '_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new AttendeesExport($status), 'attendees.csv', ExcelFormat::CSV, [

            'Content-Type' => 'text/csv',

        ]);

    }


    public function exportExhibitor()

    {

        $this->validateAdminUser();


        $status = request()->input('status', 'all');

        //get the status from the request

        // Validate the status

        $validStatuses = ['1', '0', 'all'];

        if (!in_array($status, $validStatuses)) {

            return redirect()->back()->withErrors(['status' => 'Invalid status provided.']);

        }


        // Log into useractivity channel

        Log::channel('useractivity')->info('Exporting attendees', [

            'status' => $status,

            'user_ip' => request()->ip(),

            'user_id' => auth()->id(),

            'timestamp' => now(),

        ]);


        // dd('Exporting attendees with status: ' . $status);


        $filename = 'Exhibitor_Inaugural' . $status . '_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new ExhibitorInauguralExport($status), $filename);

    }


    // MAKE a function to view the attendee details from emailer view

    public function viewAttendeeDetailsOld($id)

    {


        $attendee = Attendee::where('unique_id', $id)->firstOrFail();


        // Render the email view to get the HTML content


        $data = [

            'name' => trim($attendee->first_name . ' ' . ($attendee->middle_name ?? '') . ' ' . $attendee->last_name),

            'company_name' => $attendee->company,

            'email' => $attendee->email,

            'mobile' => $attendee->mobile,

            'qr_code_path' => $attendee->qr_code_path,

            'unique_id' => $attendee->unique_id,

            'ticket_type' => $attendee->badge_category ?? 'Visitor',

            'designation' => $attendee->designation ?? '-',

            'registration_date' => $attendee->created_at->format('Y-m-d'),

            'registration_type' => $attendee['registration_type'] === 'Online' ? 1 : 0,

        ];


        // dd($data);


        $emailContent = view('mail.attendee_confirmation', ['data' => $data])->render();


        return response($emailContent)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="attendee_details.html"');

    }


    public function viewAttendeeDetailsExhibitor($id)

    {


        $attendee = null;

        $company_name = null;


        if (str_starts_with($id, 'SEMI25VI_')) {

            $attendee = ComplimentaryDelegate::where('unique_id', $id)->first();

            if ($attendee) {

                $company_name = $attendee->company ?? $attendee->organisation_name ?? 'N/A';

            }

        } else if (str_starts_with($id, 'SEMI25VIE')) {

            $attendee = StallManning::where('unique_id', $id)->first();

            if ($attendee) {

                $company_name = $attendee->getCompanyNameAttribute() ?? 'N/A';

                //if first_name is null then set the attendee to null 

                if (is_null($attendee->first_name)) {

                    $attendee = null;

                }

            }

        }


        if (!$attendee) {

            abort(404, 'Attendee not found');

        }


        //dd($attendee);


        // Render the email view to get the HTML content


        $data = [

            'name' => trim($attendee->first_name . ' ' . ($attendee->middle_name ?? '') . ' ' . $attendee->last_name),

            'company_name' => $company_name,

            'email' => $attendee->email,

            'mobile' => $attendee->mobile,

            'qr_code_path' => $attendee->qr_code_path,

            'unique_id' => $attendee->unique_id,

            'ticket_type' => $attendee->badge_category ?? 'Visitor',

            'designation' => $attendee->designation ?? '-',

            'registration_date' => $attendee->created_at->format('Y-m-d'),

            'registration_type' => $attendee['registration_type'] === 'Online' ? 1 : 0,

            'type' => str_starts_with($id, 'SEMI25VIE-') ? 'Exhibitor' : 'Inaugural Passes',

        ];


        // dd($data);


        $emailContent = view('mail.visitor_confirmation', ['data' => $data])->render();


        return response($emailContent)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="attendee_details.html"');

    }

    //function to render pdf version of the attendee details

    public function viewAttendeeDetailsPdf($id)

    {

        $attendee = Attendee::where('unique_id', $id)->firstOrFail();


        $data = [

            'name' => trim($attendee->first_name . ' ' . ($attendee->middle_name ?? '') . ' ' . $attendee->last_name),

            'company_name' => $attendee->company,

            'email' => $attendee->email,

            'mobile' => $attendee->mobile,

            'qr_code_path' => $attendee->qr_code_path,

            'unique_id' => $attendee->unique_id,

            'ticket_type' => $attendee->badge_category ?? 'Visitor',

            'designation' => $attendee->designation ?? '-',

            'registration_date' => $attendee->created_at->format('Y-m-d'),

            'registration_type' => $attendee['registration_type'] === 'Online' ? 1 : 0,

            'id_card_number' => $attendee->id_card_number ?? 'N/A',

            'id_card_type' => $attendee->id_card_type ?? 'N/A',

            'dates' => is_array($attendee->event_days)

                ? implode(', ', $attendee->event_days)

                : implode(', ', json_decode($attendee->event_days, true) ?? []),

        ];


        $dompdf = new Dompdf();


        // Render the PDF view

        $html = view('mail.attendee_confirmation_pdf', ['data' => $data])->render();

        $dompdf->set_option('isRemoteEnabled', true); // Allow loading images from remote URLs

        //set top margin to 0


        //$dompdf->setPaper('A3', 'portrait'); // Set paper size and orientation

        //margin top 0


        $dompdf->loadHtml($html);

        $dompdf->render();

        $pdf = $dompdf;

        $name = $data['unique_id'] ?? 'Attendee';

        return $pdf->stream('' . $name . '_details.pdf', [

            'Attachment' => false,

        ]);

    }

    public function viewAttendeeDetailsPdfExhibitor($id)
    {
        $attendee = ComplimentaryDelegate::where('unique_id', $id)->first();

        if ($attendee) {

            $company_name = $attendee->company ?? $attendee->organisation_name ?? 'N/A';

        }


        if (is_null($attendee)) {


            $attendee = StallManning::where('unique_id', $id)->first();

            if ($attendee) {

                $company_name = $attendee->getCompanyNameAttribute() ?? 'N/A';

                //if first_name is null then set the attendee to null

                if (is_null($attendee->first_name)) {

                    $attendee = null;

                }

            }
        }


        if (!$attendee) {
            abort(404, 'Attendee not found');
        }

        // if exhibition_participant_id from attendee then get the application_id from exhibition_participants table and then get the company name from applications table
        // $company_name = null;
        $data['address'] = '';
        $data['city'] = '';
        $data['state'] = '';
        $data['country'] = '';
        $data['postalCode'] = '';

         if ($attendee->exhibition_participant_id) {
             $applicationId = DB::table('exhibition_participants')
                 ->where('id', $attendee->exhibition_participant_id)
                 ->value('application_id');

                if ($applicationId) {
                    //companyName,
                   $application = Application::find($applicationId);
                    $company_name = $application->company_name ?? 'N/A';
                    $address = $application->address ?? '';
                    if ($application->address_line_2) {
                        $address .= ', ' . $application->address_line_2;
                    }
                    $city = $application->city_id ?? '';
                    $state = $application->state->name ?? '';
                    $country = $application->country->name ?? '';
                    $postalCode = $application->postal_code ?? '';

                    $data['company_name'] = $company_name;
                    $data['address'] = $address;
                    $data['city'] = $city;
                    $data['state'] = $state;
                    $data['country'] = $country;
                    $data['postalCode'] = $postalCode;


                }

         }


//         dd($attendee);

        //$attendee = ComplimentaryDelegate::where('unique_id', $id)->firstOrFail();
        $attendee->event_days = json_encode(["All"]);




        $data = array_merge($data, [

            'fullName' => trim($attendee->first_name . ' ' . ($attendee->middle_name ?? '') . ' ' . $attendee->last_name),

            'title' => $attendee->title ?? '',

            'first_name' => $attendee->first_name ?? '',

            'last_name' => $attendee->last_name ?? '',

            'middle_name' => $attendee->middle_name ?? '',

            'company_name' => $company_name,

            'email' => $attendee->email,

            'mobile' => $attendee->mobile,

            'qr_code_path' => $attendee->qr_code_path,



            'unique_id' => $attendee->unique_id,

            'pinNo' => $attendee->pinNo ?? 'N/A',
            'ticket_type' => $attendee->ticketType,

            'designation' => $attendee->designation ?? $attendee->job_title,

            'registration_date' => $attendee->created_at->format('Y-m-d'),

            'registration_type' => $attendee['registration_type'] === 'Online' ? 1 : 0,

            'id_card_number' => $attendee->id_card_number ?? $attendee->id_no,

            'id_card_type' => $attendee->id_card_type ?? $attendee->id_type,

            'dates' => is_array($attendee->event_days)

                ? implode(', ', $attendee->event_days)

                : implode(', ', json_decode($attendee->event_days, true) ?? []),

            'type' => $attendee->ticketType,
        ]);

//        dd($data);


//        $dompdf = new Dompdf();


        // Render the PDF view

        $html = view('mail.ExhibitorRegMail', ['data' => $data])->render();

        echo $html;
        exit;
//
        $dompdf->set_option('isRemoteEnabled', true); // Allow loading images from remote URLs

        //set top margin to 0


        $dompdf->setPaper('A3', 'portrait'); // Set paper size and orientation

        //margin top 0


        $dompdf->loadHtml($html);

        $dompdf->render();

        $pdf = $dompdf;

        $name = $data['unique_id'] ?? 'Attendee';

        return $pdf->stream('' . $name . '_details.pdf', [

            'Attachment' => false,

        ]);

    }


    public function dashboard_old()

    {

        $user = auth()->user();

        //if not user is logged in then redirect to login page

        if (!auth()->check()) {

            return redirect('/login');

        }

        //if user is not admin then redirect to home page

        if ($user->role !== 'admin') {

            return redirect()->back()->withErrors(['error' => 'You do not have permission to access this page.']);

        }


        // Calculate total attendees first

        $totalAttendees = Attendee::count();


        // 1. Daily Registration Count

        $dailyRegistrations = Attendee::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();


        // 2. Country Breakdown

        $countryStats = Attendee::with('countryRelation')
            ->selectRaw('country, COUNT(*) as count')
            ->groupBy('country')
            ->get()
            ->map(function ($item) use ($totalAttendees) {

                return [

                    'country' => optional($item->countryRelation)->name ?? 'Unknown',

                    'count' => $item->count,

                    'percentage' => round(($item->count / $totalAttendees) * 100, 2)

                ];

            });


        // 3. Inauguration Session Stats

        $inauguralStats = [

            'applied' => Attendee::where('inaugural_session', true)->count(),

            'total' => $totalAttendees,

            'percentage' => $totalAttendees > 0 ? round((Attendee::where('inaugural_session', true)->count() / $totalAttendees) * 100, 2) : 0

        ];


        // 4. Sector/Business Nature Breakdown - Fixed to handle JSON arrays

        $sectors = collect();

        Attendee::whereNotNull('business_nature')->chunk(100, function ($attendees) use ($sectors) {

            foreach ($attendees as $attendee) {

                $businessNature = json_decode($attendee->business_nature, true);

                if (is_array($businessNature)) {

                    foreach ($businessNature as $sector) {

                        $sectors->push($sector);

                    }

                }

            }

        });


        $sectorStats = $sectors->countBy()
            ->map(function ($count, $sector) use ($totalAttendees) {

                return [

                    'sector' => $sector,

                    'count' => $count,

                    'percentage' => round(($count / $totalAttendees) * 100, 2)

                ];

            })
            ->sortByDesc('count')
            ->values();


        // Summary Statistics

        $summary = [

            'total_registrations' => $totalAttendees,

            'today_registrations' => Attendee::whereDate('created_at', today())->count(),

            'countries_represented' => $countryStats->count(),

            'inauguration_applicants' => $inauguralStats['applied']

        ];


        // Format data for charts

        $chartData = [

            'dates' => $dailyRegistrations->pluck('date'),

            'counts' => $dailyRegistrations->pluck('count'),

            'countries' => $countryStats->pluck('country'),

            'countryData' => $countryStats->pluck('count'),

            'sectors' => $sectorStats->pluck('sector'),

            'sectorData' => $sectorStats->pluck('count')

        ];


        // dd( $chartData, $summary, $countryStats, $inauguralStats, $sectorStats);


        return view('attendee.dashboard', compact(

            'chartData',

            'summary',

            'countryStats',

            'inauguralStats',

            'sectorStats'

        ));

    }


    public function dashboard()

    {

        $user = auth()->user();

        //if not user is logged in then redirect to login page

        if (!auth()->check()) {

            return redirect('/login');

        }

        //if user is not admin then redirect to home page

        if ($user->role !== 'admin') {

            return redirect()->back()->withErrors(['error' => 'You do not have permission to access this page.']);

        }


        // Calculate total attendees first

        $totalAttendees = Attendee::count();


        // 1. Daily Registration Count

        $dailyRegistrations = Attendee::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();


        // 2. Country Breakdown

        $countryStats = Attendee::with('countryRelation')
            ->selectRaw('country, COUNT(*) as count')
            ->groupBy('country')
            ->get()
            ->map(function ($item) use ($totalAttendees) {

                return [

                    'country' => optional($item->countryRelation)->name ?? 'Unknown',

                    'count' => $item->count,

                    'percentage' => round(($item->count / $totalAttendees) * 100, 2)

                ];

            });


        // 3. Inauguration Session Stats

        $inauguralStats = [

            'applied' => Attendee::where('inaugural_session', true)->count(),

            'total' => $totalAttendees,

            'percentage' => $totalAttendees > 0 ? round((Attendee::where('inaugural_session', true)->count() / $totalAttendees) * 100, 2) : 0

        ];


        // 4. Sector/Business Nature Breakdown - Fixed to handle JSON arrays

        $sectors = collect();

        Attendee::whereNotNull('business_nature')->chunk(100, function ($attendees) use ($sectors) {

            foreach ($attendees as $attendee) {

                $businessNature = json_decode($attendee->business_nature, true);

                if (is_array($businessNature)) {

                    foreach ($businessNature as $sector) {

                        $sectors->push($sector);

                    }

                }

            }

        });


        $sectorStats = $sectors->countBy()
            ->map(function ($count, $sector) use ($totalAttendees) {

                return [

                    'sector' => $sector,

                    'count' => $count,

                    'percentage' => round(($count / $totalAttendees) * 100, 2)

                ];

            })
            ->sortByDesc('count')
            ->values();


        // 5. Job Category Breakdown

        $jobCategoryStats = Attendee::select('job_category')
            ->whereNotNull('job_category')
            ->groupBy('job_category')
            ->selectRaw('job_category, COUNT(*) as count')
            ->get();


        // 6. Job Subcategory Breakdown

        $jobSubcategoryStats = Attendee::select('job_subcategory', 'job_category')
            ->whereNotNull('job_subcategory')
            ->get()
            ->groupBy(function ($item) {

                // If subcategory is 'Others', return 'Category - Others'

                if (trim(strtolower($item->job_subcategory)) === 'others' && !empty($item->job_category)) {

                    return $item->job_category . ' - Others';

                }

                return $item->job_subcategory;

            })
            ->map(function ($group) {

                return [

                    'count' => $group->count(),

                    'labels' => $group->first()->job_subcategory,

                    'job_category' => $group->first()->job_category,

                    'job_subcategory' => $group->first()->job_subcategory === 'Others' && $group->first()->job_category

                        ? $group->first()->job_category . ' - Others'

                        : $group->first()->job_subcategory,

                ];

            })
            ->values();


        // Summary Statistics

        $summary = [

            'total_registrations' => $totalAttendees,

            'today_registrations' => Attendee::whereDate('created_at', today())->count(),

            'countries_represented' => $countryStats->count(),

            'inauguration_applicants' => $inauguralStats['applied']

        ];


        // Format data for charts

        $chartData = [

            'dates' => $dailyRegistrations->pluck('date'),

            'counts' => $dailyRegistrations->pluck('count'),

            'countries' => $countryStats->pluck('country'),

            'countryData' => $countryStats->pluck('count'),

            'sectors' => $sectorStats->pluck('sector'),

            'sectorData' => $sectorStats->pluck('count'),

            'jobCategories' => $jobCategoryStats->pluck('job_category'),

            'jobCategoryData' => $jobCategoryStats->pluck('count'),

            'jobSubcategories' => $jobSubcategoryStats->pluck('job_subcategory'),

            'jobSubcategoryData' => $jobSubcategoryStats->pluck('count'),

        ];


        return view('attendee.dashboard', compact(

            'chartData',

            'summary',

            'countryStats',

            'inauguralStats',

            'sectorStats',

            'jobCategoryStats',

            'jobSubcategoryStats'

        ));

    }


    //approve the attendee for inaugural session

    public function approveInauguralSession(Request $request)

    {


        //dd($request->all());

        // get from the request and validate the request

        $request->validate([

            'unique_id' => 'required|string|exists:attendees,unique_id',

            'status' => 'required|in:approved,rejected',

        ]);

        //find the attendee by unique_id

        $attendee = Attendee::where('unique_id', $request->input('unique_id'))->firstOrFail();


        //dd($attendee);

        //update the status of the attendee

        $attendee->status = $request->input('status');


        // approved json into approvedHistory

        if ($attendee->status === 'approved') {

            $attendee->approvedHistory = json_encode([

                'approved_by' => auth()->user()->name,

                'user_ip' => request()->ip(),

                'approved_at' => now(),

            ]);

        } else {

            $attendee->approvedHistory = null;

        }

        $attendee->inauguralConfirmation = true; // Ensure inaugural session is set to true

        $attendee->save();

        // draft a email to the attendee

        $data = [

            'name' => $attendee->first_name . ' ' . $attendee->middle_name . ' ' . $attendee->last_name,

            'email' => $attendee->email,

            'status' => $attendee->status,

            'unique_id' => $attendee->unique_id,

            'qr_code_path' => $attendee->qr_code_path,

        ];

        $html = "

            <p>Dear {$data['name']},</p>

            <p>Your application for the Inaugural Session has been <strong>{$data['status']}</strong>.</p>

            <p>Your Unique ID: <strong>{$data['unique_id']}</strong></p>

            <p>

                " . ($data['status'] === 'approved'

                ? 'Please bring this email and your QR code to the event for entry.'

                : 'If you have any questions, please contact the event team.') . "

            </p>

            <p>Best regards,<br>' . config('constants.EVENT_NAME') . ' Team</p>

        ";

        Mail::send([], [], function ($message) use ($data, $html) {

            //->to($data['email'])

            $message
                ->bcc(['test.interlinks@gmail.com'])
                ->subject('Inaugural Session Status')
                ->html($html);

        });


        //return back with success message

        return redirect()->back()->with('success', 'Inaugural session status updated successfully.');

    }


    // view individual attendee details

    public function viewAttendee0($id)

    {

        $attendee = Attendee::where('unique_id', $id)->firstOrFail();

        // Check if the user is authenticated and has the right role

        if (!auth()->check() || auth()->user()->role !== 'admin') {

            return redirect()->back()->withErrors(['error' => 'You do not have permission to access this page.']);

        }


        // Render the attendee details view

        return view('attendee.attendeeView', [

            'attendee' => $attendee,

        ]);

    }


    public function viewAttendee($id)

    {

        $attendee = Attendee::where('unique_id', $id)->first();

        $type = "Attendee";

        if (!$attendee) {

            $attendee = ComplimentaryDelegate::where('unique_id', $id)->firstOrFail();

            $type = "Exhibitor";

        }


        // Check if the user is authenticated and has the right role

        if (!auth()->check() || auth()->user()->role !== 'admin') {

            return redirect()->back()->withErrors(['error' => 'You do not have permission to access this page.']);

        }


        // Render the attendee details view

        return view('attendee.attendeeView', [

            'attendee' => $attendee,

            'attendeeType' => $type,

        ]);

    }


    public function update(Request $request, $unique_id)

    {

        $attendee = Attendee::where('unique_id', $unique_id)->firstOrFail();


        // Only allow admin

        if (!auth()->user() || auth()->user()->role !== 'admin') {

            abort(403);

        }


        $request->validate([

            'company' => 'nullable|string|max:255',

            'id_card_type' => 'nullable|string|max:255',

            'id_card_number' => 'nullable|string|max:255',

            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',

            'event_days' => 'nullable|array',

            // 1MB max size

        ]);


        $data = $request->only(['company', 'id_card_type', 'id_card_number', 'event_days']);


        // Handle profile picture upload

        if ($request->hasFile('profile_picture')) {

            $file = $request->file('profile_picture');

            $path = $file->store('profile_pictures', 'public');

            $data['profile_picture'] = 'storage/' . $path;

        }


        $admin = auth()->user();

        $timestamp = now()->format('Y-m-d H:i:s');

        $ip = $request->ip();

        $logEntry = "{$admin->name} ({$admin->email}) IP: {$ip} at {$timestamp}";


        // Merge with existing logs

        $existingLog = $attendee->updated_by;

        $mergedLog = $existingLog ? $existingLog . "\n" . $logEntry : $logEntry;

        $data['updated_by'] = $mergedLog;

        // $attendee->update($data);

        // dd($data);


        $attendee->update($data);


        return redirect()->back()->with('success', 'Attendee details updated successfully.');

    }


    public function listExhibitor(Request $request)

    {

        // Validate the admin user

        $this->validateAdminUser();


        $attendees = ComplimentaryDelegate::whereNotNull('first_name');


        if ($request->has('search')) {

            $search = $request->input('search');

            $attendees->where(function ($query) use ($search) {

                $query->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('organisation_name', 'like', "%$search%")
                    ->orWhere('unique_id', 'like', "%$search%");

            });

        }


        // dd($attendees);


        $attendees = $attendees->paginate(50);

        $slug = "Exhibitor Inaugural List";


        return view('attendee.exhibitor_list', compact('attendees', 'slug'));

    }


    public function massApprove(Request $request)

    {


        $ids = explode(',', $request->input('selected_ids'));

        // $ids = [194, 195, 196];


        $attendees = Attendee::whereIn('id', $ids)->get();


        foreach ($attendees as $attendee) {

            // $attendee->inauguralConfirmation = 1;


            $attendee->approvedHistory = json_encode([

                'approved_by' => auth()->user()->name ?? 'System',

                'user_ip' => request()->ip(),

                'approved_at' => now()->toDateTimeString(),

            ]);


            $attendee->inauguralConfirmation = true;

            $attendee->save();


            $data = [

                'name' => trim($attendee->first_name . ' ' . $attendee->middle_name . ' ' . $attendee->last_name),

                'email' => $attendee->email,

                'status' => ucfirst($attendee->status),

                'unique_id' => $attendee->unique_id,

            ];


            $html = "

            <p>Dear {$data['name']},</p>

            <p>Your application for the Inaugural Session has been <strong>{$data['status']}</strong>.</p>

            <p>Your Unique ID: <strong>{$data['unique_id']}</strong></p>

            <p>" . ($attendee->status === 'approved'

                    ? 'Please bring this email and your QR code to the event for entry.'

                    : 'If you have any questions, please contact the event team.') . "</p>

            <p>Best regards,<br>' . config('constants.EVENT_NAME') . ' Team</p>

        ";


            // Dispatch to queue

            Mail::to('test.interlinks@gmail.com')
                ->queue(new AttendeeApprovalMail($data));

        }


        return back()->with('success', 'Selected attendees approved and emails queued.');

    }


    public function ExhibitormassApprove(Request $request)

    {

        $ids = explode(',', $request->input('selected_ids'));


        // dd($ids);


        $attendees = ComplimentaryDelegate::whereIn('id', $ids)->get();


        foreach ($attendees as $attendee) {

            // $attendee->inauguralConfirmation = 1;


            $attendee->approvedHistory = json_encode([

                'approved_by' => auth()->user()->name ?? 'System',

                'user_ip' => request()->ip(),

                'approved_at' => now()->toDateTimeString(),

            ]);


            $attendee->inauguralConfirmation = true;

            $attendee->save();


            $data = [

                'name' => trim($attendee->first_name . ' ' . $attendee->middle_name . ' ' . $attendee->last_name),

                'email' => $attendee->email,

                'status' => ucfirst($attendee->status),

                'unique_id' => $attendee->unique_id,

            ];

            try {

                Mail::to('test.interlinks@gmail.com')
                    ->queue(new AttendeeApprovalMail($data));

            } catch (\Exception $e) {

                Log::error('Failed to send attendee approval mail: ' . $e->getMessage(), [

                    'email' => 'manish.sharma@interlinks.in',

                    'data' => $data,

                ]);

            }


            // dd($data);


            $html = "

            <p>Dear {$data['name']},</p>

            <p>Your application for the Inaugural Session has been <strong>{$data['status']}</strong>.</p>

            <p>Your Unique ID: <strong>{$data['unique_id']}</strong></p>

            <p>" . ($attendee->status === 'approved'

                    ? 'Please bring this email and your QR code to the event for entry.'

                    : 'If you have any questions, please contact the event team.') . "</p>

            <p>Best regards,<br>' . config('constants.EVENT_NAME') . ' Team</p>

        ";


            // Dispatch to queue

            // Mail::to('manish.sharma@interlinks.in')

            //     ->send(new AttendeeApprovalMail($data));

        }


        return back()->with('success', 'Selected attendees approved and emails queued.');

    }


    /**
     * Display the jobs matrix report.
     *
     * @return \Illuminate\View\View
     */

    public function jobsMatrix2()

    {


        // dd('This feature is temporarily disabled. Please contact support for more information.');

        // 1) Subcategory-level counts

        $rows = Attendee::query()
            ->selectRaw('job_category, job_subcategory,

                         COUNT(*) AS cnt,

                         SUM(CASE WHEN inaugural_session = 1 THEN 1 ELSE 0 END) AS inaug_cnt')
            ->groupBy('job_category', 'job_subcategory')
            ->orderBy('job_category')
            ->orderBy('job_subcategory')
            ->get();


        $exhibitorPasses = StallManning::whereNotNull('unique_id')->count();            // Exhibitor passes

        $exhibitorInaugPasses = ComplimentaryDelegate::whereNotNull('unique_id')->count();   // Exhibitor inaugural passes


        // dd($rows);


        // 2) Group by category and compute category totals + rowspans

        $grouped = $rows->groupBy('job_category')->map(function (Collection $items) {

            $categoryTotal = (int)$items->sum('cnt');

            $inaugTotal = (int)$items->sum('inaug_cnt'); // if you also want category inaug total

            return [

                'rowspan' => $items->count(),

                'total' => $categoryTotal,

                'inaug_total' => $inaugTotal,

                'items' => $items->values(),

            ];

        });


        // 3) Grand total

        $grandTotal = (int)$rows->sum('cnt');


        // 4) Optional: background colors per category (match your design)

        $bgMap = [

            'Academic' => '#fde9e0',

            'Government' => '#e4eef8',

            'Industry' => '#f4f8e8',

            'Media' => '#efe6d8',

            'Others' => '#e7e2e2',

        ];


        return view('attendee.jobs-matrix', compact('grouped', 'grandTotal', 'bgMap'));

    }


    public function jobsMatrix()

    {

        // 1) Subcategory-level counts from Attendees

        $rows = Attendee::query()
            ->selectRaw('job_category, job_subcategory,

                     COUNT(*) AS cnt,

                     SUM(CASE WHEN inaugural_session = 1 THEN 1 ELSE 0 END) AS inaug_cnt')
            ->groupBy('job_category', 'job_subcategory')
            ->orderBy('job_category')
            ->orderBy('job_subcategory')
            ->get();


        // 2) Exhibitor data

        $exhibitorPasses = StallManning::whereNotNull('unique_id')->count();

        $exhibitorInaugPasses = ComplimentaryDelegate::whereNotNull('unique_id')->count();


        // 3) Add Exhibitor rows

        $extra = collect([

            (object)[

                'job_category' => 'Exhibitor',

                'job_subcategory' => 'Exhibitor Passes',

                'cnt' => (int)$exhibitorPasses,

                'inaug_cnt' => (int)$exhibitorInaugPasses,

            ],

            // (object) [

            //     'job_category'    => 'Exhibitor',

            //     'job_subcategory' => 'Exhibitor Inaugural Passes',

            //     'cnt'             => 0,

            //     'inaug_cnt'       => (int) $exhibitorInaugPasses,

            // ],

        ]);


        $rows = $rows->concat($extra);


        // 4) Group by category

        $grouped = $rows->groupBy('job_category')->map(function (Collection $items, $category) {

            // Normal categories → only sum cnt

            if ($category !== 'Exhibitor') {

                return [

                    'rowspan' => $items->count(),

                    'total' => (int)$items->sum('cnt'),

                    'inaug_total' => (int)$items->sum('inaug_cnt'),

                    'items' => $items->values(),

                ];

            }


            // Exhibitor → sum of cnt + inaug_cnt

            return [

                'rowspan' => $items->count(),

                'total' => (int)($items->sum('cnt') + $items->sum('inaug_cnt')),

                'inaug_total' => (int)$items->sum('inaug_cnt'),

                'items' => $items->values(),

            ];

        });


        // 5) Grand total (normal categories cnt + exhibitor total including inaug)

        $grandTotal = $grouped->sum('total');


        // 6) Background colors

        $bgMap = [

            'Academic' => '#fde9e0',

            'Government' => '#e4eef8',

            'Industry' => '#f4f8e8',

            'Media' => '#efe6d8',

            'Others' => '#e7e2e2',

            'Exhibitor' => '#fff2cc',

        ];


        return view('attendee.jobs-matrix', compact('grouped', 'grandTotal', 'bgMap'));

    }

    // make a controller which takes registration count in different categories from stall_manning table as Exhibitor Registration 
    // from complimentary_delegates  take the category from ticketType and give the count of it as Complimentary Registration
    // give the total of both as Total Registration
    // give the percentage of each category as a percentage of total registration
    // give the percentage of each category as a percentage of total registration

    public function registrationCount()
    {
        // Stall Manning = Exhibitor Passes (count all stall manning records)
        $exhibitorPasses = StallManning::count();
        
        // Complimentary Delegates - group by ticketType from ComplimentaryDelegate model
        $complimentaryBreakdown = ComplimentaryDelegate::selectRaw('ticketType, COUNT(*) as count')
            ->whereNotNull('ticketType')
            ->where('ticketType', '!=', '')
            ->groupBy('ticketType')
            ->get()
            ->keyBy('ticketType');
        
        $totalComplimentaryDelegates = $complimentaryBreakdown->sum('count');
        
        // Total registration is only from actual consumed records
        $totalRegistration = $exhibitorPasses + $totalComplimentaryDelegates;
        
        // All ticket types come from actual records (StallManning + ComplimentaryDelegate)
        $allTicketTypes = collect();
        
        // Add complimentary delegates by ticket type
        foreach ($complimentaryBreakdown as $ticketType => $data) {
            $allTicketTypes->put($ticketType, ($allTicketTypes->get($ticketType, 0) + $data->count));
        }
        
        // Add exhibitor passes by ticket type (if StallManning has ticketType)
        $stallManningBreakdown = StallManning::selectRaw('ticketType, COUNT(*) as count')
            ->whereNotNull('ticketType')
            ->where('ticketType', '!=', '')
            ->groupBy('ticketType')
            ->get()
            ->keyBy('ticketType');
        
        foreach ($stallManningBreakdown as $ticketType => $data) {
            $allTicketTypes->put($ticketType, ($allTicketTypes->get($ticketType, 0) + $data->count));
        }

        // Debug: Let's see what we're getting from each source
       
        
        $ticketAllocations = 0;
        $ticketBreakdown = collect();
        
        return view('attendee.registration-count', compact(
            'exhibitorPasses', 
            'totalComplimentaryDelegates', 
            'complimentaryBreakdown',
            'stallManningBreakdown',
            'ticketAllocations',
            'ticketBreakdown',
            'allTicketTypes',
            'totalRegistration'
        ));
    }

    /**
     * Get registration count data as JSON for AJAX updates
     */
    public function getRegistrationCountData()
    {
        // Stall Manning = Exhibitor Passes (count all stall manning records)
        $exhibitorPasses = StallManning::count();
        
        // Complimentary Delegates - group by ticketType from ComplimentaryDelegate model
        $complimentaryBreakdown = ComplimentaryDelegate::selectRaw('ticketType, COUNT(*) as count')
            ->whereNotNull('ticketType')
            ->where('ticketType', '!=', '')
            ->groupBy('ticketType')
            ->get()
            ->keyBy('ticketType');
        
        $totalComplimentaryDelegates = $complimentaryBreakdown->sum('count');
        
        // Add exhibitor passes by ticket type (if StallManning has ticketType)
        $stallManningBreakdown = StallManning::selectRaw('ticketType, COUNT(*) as count')
            ->whereNotNull('ticketType')
            ->where('ticketType', '!=', '')
            ->groupBy('ticketType')
            ->get()
            ->keyBy('ticketType');
        
        // Ticket Allocations from ExhibitionParticipant (separate from complimentary delegates)
        $ticketAllocations = \App\Models\ExhibitionParticipant::whereNotNull('ticketAllocation')
            ->where('ticketAllocation', '!=', '')
            ->get()
            ->sum(function($participant) {
                $allocations = json_decode($participant->ticketAllocation, true) ?? [];
                return array_sum($allocations);
            });
        
        // Get detailed breakdown by ticket type from ExhibitionParticipant
        $ticketBreakdown = \App\Models\ExhibitionParticipant::whereNotNull('ticketAllocation')
            ->where('ticketAllocation', '!=', '')
            ->get()
            ->flatMap(function($participant) {
                $allocations = json_decode($participant->ticketAllocation, true) ?? [];
                $breakdown = [];
                foreach ($allocations as $ticketId => $count) {
                    if ($count > 0) {
                        $ticket = \App\Models\Ticket::find($ticketId);
                        if ($ticket) {
                            $breakdown[] = [
                                'ticket_type' => $ticket->ticket_type,
                                'count' => $count
                            ];
                        }
                    }
                }
                return $breakdown;
            })
            ->groupBy('ticket_type')
            ->map(function($group) {
                return $group->sum('count');
            });
        
        // Combine all ticket types (exhibitor passes + complimentary + ticket allocations)
        $allTicketTypes = collect();
        
        // Add exhibitor passes by ticket type
        foreach ($stallManningBreakdown as $ticketType => $data) {
            $allTicketTypes->put($ticketType, ($allTicketTypes->get($ticketType, 0) + $data->count));
        }
        
        // Add complimentary delegates by ticket type
        foreach ($complimentaryBreakdown as $ticketType => $data) {
            $allTicketTypes->put($ticketType, ($allTicketTypes->get($ticketType, 0) + $data->count));
        }
        
        // Add ticket allocations by ticket type
        foreach ($ticketBreakdown as $ticketType => $count) {
            $allTicketTypes->put($ticketType, ($allTicketTypes->get($ticketType, 0) + $count));
        }
        
        $totalRegistration = $exhibitorPasses + $totalComplimentaryDelegates + $ticketAllocations;

        return response()->json([
            'success' => true,
            'data' => [
                'exhibitor_passes' => $exhibitorPasses,
                'complimentary_delegates' => $totalComplimentaryDelegates,
                'complimentary_breakdown' => $complimentaryBreakdown,
                'stall_manning_breakdown' => $stallManningBreakdown,
                'ticket_allocations' => $ticketAllocations,
                'ticket_breakdown' => $ticketBreakdown,
                'all_ticket_types' => $allTicketTypes,
                'total_registration' => $totalRegistration,
                'last_updated' => now()->format('M d, Y \a\t h:i A')
            ]
        ]);
    }
}

