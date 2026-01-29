<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CoExhibitor;
use App\Models\User;
use App\Models\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
//use App\Mail\AdminNotificationMail;
use Illuminate\Support\Facades\Hash;
use App\Mail\CoExhibitorRequestMail;
use App\Mail\CoExhibitorRequest;

use App\Mail\CoExhibitorApprovalMail;
use App\Models\Invoice;
use App\Mail\CoExhibitorInvoiceMail;
use App\Models\ExhibitionParticipant;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckUser;
//valdator 
use Illuminate\Support\Facades\Validator;

class CoExhibitorController extends Controller
{

    //user_list for main exhibitor to display list of all co exhibitors
    public function user_list()
    {

//        dd('dd');
        //find co exhibitors for the logged in user or applicationid
        //get the user id
        $userID = auth()->user()->id;
        //get the application id with user_Id
        $application = Application::where('user_id', $userID)->first();
        $application_id = $application->id;

        //get the application->coex_terms_accepted and pass it to the view
        $coex_terms_accepted = $application->coex_terms_accepted ?? false;

        $coExhibitors = CoExhibitor::where('application_id', $application_id)->get();
        //$application = Application::find(auth()->user()->application_id);

        return view('exhibitor.co_exhibitors', compact('coExhibitors', 'application', 'coex_terms_accepted'));
    }


    public function store(Request $request)
    {
        Log::info('Co-Exhibitor request', $request->all());

        $userID = auth()->user()->id;
        $application = Application::where('user_id', $userID)->first();

        if (!$application) {
            return response()->json(['error' => 'Application not found'], 404);
        }

        //check if the application has already accepted coex_terms
        if (!$application->coex_terms_accepted) {
            return response()->json(['error' => 'You must accept the Co-Exhibitor Terms & Conditions first.'], 403);
        }

        $request->merge(['application_id' => $application->id]);

        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'co_exhibitor_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'proof_document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $filePath = null;

        // Handle proof_document file upload
        if ($request->hasFile('proof_document')) {
            $file = $request->file('proof_document');

            $uploadPath = public_path('co-exhibitor');

            // Ensure directory exists
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $fileName = uniqid('proof_') . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);

            $filePath = 'co-exhibitor/' . $fileName; // This is the public path to store in DB
        }

        // Store co-exhibitor
        $coExhibitor = CoExhibitor::create([
            'application_id' => $application->id,
            'co_exhibitor_name' => $request->co_exhibitor_name,
            'contact_person' => $request->contact_person,
            'job_title' => $request->job_title,
            'email' => $request->email,
            'phone' => $request->full_phone,
            'proof_document' => $filePath,
            'status' => 'pending',
        ]);

        // make array of coExhibitor
        $coExhibitorArray = [
            'co_exhibitor_name' => $coExhibitor->co_exhibitor_name,
            'company_name' => $application->company_name,
            'contact_person' => $coExhibitor->contact_person,
            'job_title' => $coExhibitor->job_title,
            'email' => $coExhibitor->email,
            'phone' => $coExhibitor->phone,
            'proof_document' => $filePath,
            'status' => 'pending',
        ];

        // if the application membership_verified not null and == 1 
        // then get the  Member: ₹25,000 per co-exhibitor (excluding tax)
        // Non-member: ₹32,500 per co-exhibitor (excluding tax)
        //calcultate the tax    with 18% GST
        // rate = 25,000 or 32,500
        //currency = INR , payment_status = unpaid 
        // payment_due_date 15th july
        // gst = 18% of rate 
        //processing_charges = 0 
        // price = rate + gst + processing_charges
        //amount = price 
        // total_final_price == amount 

        //application_no = application_no


        //create an invoice for the co-exhibitor 
        // application_id, type = Co-Exhibitor, 
        // $invoice = Invoice::create([


        // Notify admin (optional)
        // Mail::to('admin@example.com')->send(new AdminNotificationMail($coExhibitor));
        $adminEmail = "test.interlinks@mail.com"; // Change to your admin email
        Mail::to($adminEmail)
            ->bcc('test.interlinks@gmail.com', ORGANIZER_EMAIL)
            ->send(new CoExhibitorRequest($coExhibitorArray)); // Send to user and BCC


        return response()->json(['message' => 'Co-Exhibitor request submitted for approval!'], 201);
    }


    //render email view for co-exhibitor request
    public function emailView()
    {
        $coExhibitor = CoExhibitor::first(); // For demonstration, get the first co-exhibitor

        //make array of coExhibitor
        $coExhibitor = [
            'company_name' => $coExhibitor->application->company_name,
            'co_exhibitor_name' => $coExhibitor->co_exhibitor_name,
            'contact_person' => $coExhibitor->contact_person,
            'job_title' => $coExhibitor->job_title,
            'email' => $coExhibitor->email,
            'phone' => $coExhibitor->phone,
            'proof_document' => $coExhibitor->proof_document,
            'status' => $coExhibitor->status,
        ];

        //send email with coExhibitor data

        // Mail::to('manish.sharma@interlinks.in')->queue(new CoExhibitorRequest($coExhibitor));
        return new CoExhibitorRequest($coExhibitor);
    }




    public function store2(Request $request)
    {

        Log::info('Co-Exhibitor request', $request->all());

        // get the application_id from the authenticated user

        $userID = auth()->user()->id;
        $application = Application::where('user_id', $userID)->first();
        if (!$application) {
            return response()->json(['error' => 'Application not found'], 404);
        }
        $request->merge(['application_id' => $application->id]);
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'co_exhibitor_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
        ]);

        // Store the co-exhibitor with status = pending
        $coExhibitor = CoExhibitor::create([
            'application_id' => $application->id,
            'co_exhibitor_name' => $request->co_exhibitor_name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => 'pending',
        ]);

        // Notify admin
        $adminEmail = "admin@example.com"; // Change to your admin email
        //Mail::to($adminEmail)->send(new AdminNotificationMail($coExhibitor));

        return response()->json(
            ['message' => 'Co-Exhibitor request submitted for approval!'],
            201
        );
    }


    //list of all co-exhibitors for admin
    public function index()
    {
        $coExhibitors = CoExhibitor::orderByRaw("status = 'pending' DESC")->get();

        //return view from admin.co_exhibitors
        return view('admin.co_exhibitors', compact('coExhibitors'));
    }


    //generate coexhibitor invoice no as SEC-INVC-41455
    //match also so that invoice no is not already generated use random number
    public function generateInvoiceNo()
    {
        $randomNumber = rand(10000, 99999);
        $invoiceNo = 'SEC-INVC-' . $randomNumber;
        // Check if invoice number already exists
        while (Invoice::where('invoice_no', $invoiceNo)->exists()) {
            $randomNumber = rand(10000, 99999);
            $invoiceNo = 'SEC-INVC-' . $randomNumber;
        }
        return $invoiceNo;
    }




    public function approve(Request $request, $id)
    {
        $coExhibitor = CoExhibitor::findOrFail($id);

        if ($coExhibitor->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 400);
        }

        // Generate a random password
        $password = substr(md5(uniqid()), 0, 10);

        // Create a new user for co-exhibitor
        try {
            $user = User::create([
                'name' => $coExhibitor->co_exhibitor_name,
                'email' => $coExhibitor->email,
                'password' => Hash::make($password),
                'role' => 'co-exhibitor', // Assuming roles exist
                'email_verified_at' => now(),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json(['error' => 'Email already exists'], 400);
            }
            return response()->json(['error' => 'Failed to create user'], 500);
        }
        //generate a coexhibitor ID 
        $coExhibitor->co_exhibitor_id = 'SI25-COEXH-' . strtoupper(substr(md5(uniqid()), 0, 6));

        // Update co-exhibitor status
        $coExhibitor->status = 'approved';
        $coExhibitor->user_id = $user->id;
        $coExhibitor->approved_At = now();
        $coExhibitor->save();
        //also send that you are exhibiting under
        $application = Application::find($coExhibitor->application_id);
        $exhibiting_under = $application->company_name;

        //create an invoice for the co-exhibitor
        $rate = ($application->membership_verified == 1) ? 25000 : 32500;
        $gst = round($rate * 0.18, 2);
        $processing_charges = 0;
        $price = $rate + $gst + $processing_charges;
        $amount = $price;
        $total_final_price = $amount;
        $currency = 'INR';
        $payment_status = 'unpaid';
        $payment_due_date = date('Y-m-d', strtotime(date('Y') . '-07-15'));
        $application_no = $application->application_id;
        $type = 'Co-Exhibitor';
        $invoice_no = $this->generateInvoiceNo();




        // Create or update invoice based on co_exhibitorID
        Invoice::updateOrCreate(
            [
                'co_exhibitorID' => $coExhibitor->co_exhibitor_id,
                'type' => $type,
            ],
            [
                'application_id' => $coExhibitor->application_id,
                'invoice_no' => $invoice_no,
                'rate' => $rate,
                'gst' => $gst,
                'processing_charges' => $processing_charges,
                'price' => $rate,
                'amount' => $amount,
                'total_final_price' => $total_final_price,
                'currency' => $currency,
                'payment_status' => $payment_status,
                'payment_due_date' => $payment_due_date,
                'application_no' => $application_no,
                'pending_amount' => $amount, // Assuming pending amount is the same as amount
            ]
        );

        //mail to // CoExhibitor with approval details
        //send mail to contact person, coexhibitor person and user 
        $userEmail = $application->user->email;

        $exhibitorEmail = $application->eventContact->email;
        $coExhibitorEmail = $coExhibitor->email;

        Mail::to([$userEmail, $exhibitorEmail, $coExhibitorEmail])
            ->bcc(ORGANIZER_EMAIL, 'test.interlinks@gmail.com')
            ->send(new CoExhibitorInvoiceMail($coExhibitor->co_exhibitor_id));





        // Send email with login credentials
        //Mail::to($coExhibitor->email)->send(new CoExhibitorApprovalMail($coExhibitor, $password, $exhibiting_under));

        return response()->json(['message' => 'Co-Exhibitor Approved & Invoice Sent!']);
    }

    public function reject($id)
    {
        $coExhibitor = CoExhibitor::findOrFail($id);

        if ($coExhibitor->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 400);
        }

        // Update status to rejected
        $coExhibitor->update(['status' => 'rejected']);

        return response()->json(['message' => 'Co-Exhibitor Request Rejected']);
    }


    //write a function to handle badge allocation based on the stall size
    public function handleBadgeAllocation($stallSize)
    {

        if ($stallSize >= 9 && $stallSize <= 17) {
            return 5;
        } elseif ($stallSize >= 18 && $stallSize <= 26) {
            return 10;
        } elseif ($stallSize >= 27 && $stallSize <= 54) {
            return 20;
        } elseif ($stallSize >= 55 && $stallSize <= 100) {
            return 30;
        } elseif ($stallSize >= 101 && $stallSize <= 400) {
            return 40;
        } elseif ($stallSize > 400) {
            return 50;
        }
        return 0;
    }

    //make a function to exhibition_participants co-exhibitor_id stall_manning_count complimentary_delegate_count
    public function updateExhibitionParticipant($coExhibitorId, $stallSize)
    {
        // Calculate stall manning count and complimentary delegate count
        $stallManningCount = $this->handleBadgeAllocation($stallSize);
        $complimentaryDelegateCount = 0; // Assuming no complimentary delegates for co-exhibitors

        // Update or create exhibition participant record
        ExhibitionParticipant::updateOrCreate(
            ['coExhibitor_id' => $coExhibitorId],
            [
                'stall_manning_count' => 5,
                'complimentary_delegate_count' => 3,
            ]
        );
    }

    //make a function co_exhibitor_name	Address Line 1	City	State	Country	Pincode	Contact Person	Job Title	Email Address	Contact Number	Booth Number	Booth Size (sqm)
    // to insert into co_exhibitors table
    public function createCoExhibitor(Request $request)
    {

        // return;



        // $rows = [
        //     [
        //         "APP SYSTEMS SERVICES PTE LTD", "11 TOH GUAN ROAD EAST #03-01", "Singapore", "Singapore", "Singapore", "608603", "LIM PEI LIN", "GROUP MARKETING LEAD", "peilin.lim@appsystems.com.sg", "+65 6668 4664", "1341", 9
        //     ],
        //     [
        //         "SPECMAX TECHNOLOGIES PTE LTD", "10 Tech Park Crescent", "Singapore", "Singapore", "Singapore", "638122", "Karthikeyan Balaji", "Project Manager", "balaji@specmaxtech.com", "+65 8421 1953", "1336", 18
        //     ],
        //     [
        //         "Dou Yee Enterprises (S) Pte Ltd", "2304 Bedok Reservoir Road, Dou Yee Building", "Singapore", "Singapore", "Singapore", "479223", "Samy", "Technical Sales Manager", "kylie.tan@douyee.com.sg", "+65 6444 2678", "1236", 18
        //     ],
        //     [
        //         "GLOBAL NOW PTE. LTD.", "9 TEMASEK BOULEVARD, #41-02, SUNTEC TOWER TWO", "Singapore", "Singapore", "Singapore", "38989", "ZOE LEE", "Admin", "ZOE.LEE@NOW-INTERFACE.COM", "+65 9795 9965", "1242", 9
        //     ],
        //     [
        //         "HMC SALES & SERVICE PTE LTD", "8, Ubi Road 2, #05-30 Zervex", "Singapore", "Singapore", "Singapore", "408538", "Glen Choo Jun Rui", "Sales & Finance Executive", "glenchoo@hmcasia.com", "+65 6702 0093", "1142", 9
        //     ],
        //     [
        //         "Aligned Test Pte Ltd", "50 Tagore Lane #05-06 (IJ)", "Singapore", "Singapore", "Singapore", "787494", "Lee Heng Huat", "Director", "lee.hh@aligned-test.com.sg", "+65 91850551", "1342", 9
        //     ],
        //     [
        //         "Rokko Systems Pte Ltd", "61 Kaki Bukit Road 2", "Singapore", "Singapore", "Singapore", "417869", "Steven Lee", "Technical & Sales Executive", "exhibition@rokko.net", "+65 6749 5885", "1235", 18
        //     ],
        //     [
        //         "DR Laser Singapore Pte Ltd", "#05-05, 31 International Business Park", "Singapore", "Singapore", "Singapore", "609921", "Summer Shen", "Project Coordinator", "summershen@drlaser.com.sg", "+65 9005 5371", "1441", 9
        //     ],
        //     [
        //         "GASTRON ASIA PACIFIC PTE. LTD.", "10 ANSON ROAD #18-07 INTERNATIONAL PLAZA", "Singapore", "Singapore", "Singapore", "79903", "OH SEUNG WEON", "Management Director", "global@gastron.com", "+65 9061 3270", "1040", 18
        //     ],
        //     [
        //         "PI (Physik Instrumente) Singapore LLP", "26 Sin Ming Lane #04-125 Midview City", "Singapore", "Singapore", "Singapore", "573971", "Jermwiwat", "Deputy Sales Manager", "a.jermwiwat@pi.ws", "+65 9362 4951", "1435", 18
        //     ],
        //     [
        //         "PICOTECH SOLUTIONS PTE LTD", "North View Bizhub, 6 Yishun Industrial Street 1 #05-19", "Singapore", "Singapore", "Singapore", "768090", "ENJUM VENKATESA MANDADI", "DIRECTOR", "venkat@pico-tech.sg", "+65 9226 1502", "1139", 18
        //     ],
        //     [
        //         "Quantel Pte Ltd", "25 Kallang Avenue #05-02", "Singapore", "Singapore", "Singapore", "339416", "Ms Adeline Yeo", "Marketing", "adeline.yeo@sg.quantel-global.com", "+65 91713372", "1336", 18
        //     ],
        //     [
        //         "MayAir Singapore Pte.Ltd.", "63 Jln Pemimpin, #03-02", "Singapore", "Singapore", "Singapore", "577219", " Lo Zhi Yang", " Marketing Executive", " zhiyang.lo@mayairgroup.com ", "+60 126185930", "1435", 18
        //     ],
        //     [
        //         "PS SOLUTIONS & SERVICES PTE LTD", "30 LOYANG WAY #07-07 LOYANG INDUSTRIAL ESTATE", "Singapore", "Singapore", "Singapore", "508769", "TAN SEOW YING", "SR. OFFICE MGR", "seowying@pssolutions.com.sg", "+65 6542 5489", "1239", 9
        //     ],
        //     [
        //         "Singapore Precision Engineering and Technology Association", "2 Ang Mo Kio Drive BLK C Unit 204", "Singapore", "Singapore", "Singapore", "567720", "Jimmy Leong", "Project Manager", "jimmy_leong@speta.org", "+65 9841 5604", "1439", 9
        //     ],
        // ];

        // $rows = [
        //     [
        //         "Symphony Engineering Sdn Bhd",
        //         "Blk-B-G-28, Kompleks Suria Kinrara, Persiaran Kinrara Seksyen 3, Taman Kinrara Sek 3",
        //         "Puchong",
        //         "Selangor",
        //         "Malaysia",
        //         "47100",
        //         "Angeline Ngoi",
        //         "Marketing Manager",
        //         "angelinengoi@symphony-eng.com.my",
        //         "+60 12-5943608",
        //         "1541",
        //         9
        //     ],
        //     [
        //         "NSW Automation Sdn. Bhd.",
        //         "No.1225, Lorong PSPN 1, Penang Science Park North",
        //         "Simpang Ampat",
        //         "Penang",
        //         "Malaysia",
        //         "14100",
        //         "Joan Khaw",
        //         "Marketing Communication Executive",
        //         "joankhaw@nswautomation.com",
        //         "+60 1117991932",
        //         "1436",
        //         18
        //     ],
        //     [
        //         "Cryogenic Specialty Manufacturing Sdn Bhd",
        //         "8, Jln PPU 3A, Taman Perindustrian Pusat Bandar Puchong",
        //         "Puchong",
        //         "Selangor",
        //         "Malaysia",
        //         "47150",
        //         "Chang Lee Shin",
        //         "Marketing Coordinator",
        //         "leeshin.chang@csm-cryogenic.com",
        //         "+60 127289609",
        //         "1535",
        //         18
        //     ],
        // ];

        // $rows = [
        //     [
        //         "Dou Yee Enterprises (S) Pte Ltd",
        //         "2304 Bedok Reservoir Road, Dou Yee Building",
        //         "Singapore",
        //         "Singapore",
        //         "Singapore",
        //         "479223",
        //         "Samy",
        //         "Technical Sales Manager",
        //         "samy@douyee.com.sg",
        //         "+65 6444 2678",
        //         "1236",
        //         18
        //     ],
        // ];

        // $rows = [
        //     [
        //         "SL METALS PTE LTD",
        //         "6 Tuas Lane",
        //         "Singapore",
        //         "Singapore",
        //         "Singapore",
        //         "638615",
        //         "Tan Li Shuan",
        //         "Business Development Manager",
        //         "lishuan@slmetalsgroup.com",
        //         "65 8186 5198",
        //         "1241",
        //         9,
        //         "Singapore Pavilion"
        //     ],
        //     [
        //         "Possehl Electronics (Malaysia) Sdn. Bhd.",
        //         "Lot 33, Phase III, Batu Berendam Free Trade Zone",
        //         "Melaka",
        //         "Melaka",
        //         "Malaysia",
        //         "75350",
        //         "David Cham",
        //         "Head Of Supply Chain",
        //         "yongpeng.cham@possehlelectronics.com",
        //         "60 123316944",
        //         "1440",
        //         18,
        //         "Malaysia Pavilion"
        //     ],
        // ];

        // $rows = [
        //     [
        //         "Brain Domain Pte Ltd",
        //         "3, ANG MO KIO IND PK2A #06-13, AMK TECH-1",
        //         "Singapore",
        //         "Singapore",
        //         "Singapore",
        //         "568050",
        //         "SANDEEP SHARMA",
        //         "CEO",
        //         "SANDEEP@BRAINDOMAIN.CO",
        //         "+65 98572084",
        //         "1036",
        //         36
        //     ],
        //     [
        //         "Hibex Singapore Pte Ltd",
        //         "100G Pasir Panjang Rd, #06-25 Interlocal Centre",
        //         "Singapore",
        //         "Singapore",
        //         "Singapore",
        //         "118523",
        //         "Maneesh Kumar",
        //         "Manager - Sales",
        //         "maneesh@hibex.com.sg",
        //         "+91 989976147",
        //         "1340",
        //         9
        //     ],
        //     [
        //         "International Facility Engineering Pte Ltd",
        //         "10 Science Park Road, The Alpha, #03-03, Science Park II",
        //         "Singapore",
        //         "Singapore",
        //         "Singapore",
        //         "117684",
        //         "Mr. Patrick Asante",
        //         "Managing Director",
        //         "patrick.asante@intl-fe.com",
        //         "+65 6776 7308",
        //         "1240",
        //         9
        //     ],
        // ];

        // $rows = [
        //     [
        //         "Inventec Performance Chemicals South East Asia Sdn Bhd",
        //         "No. 3, Jalan Industri Kidamai 2/1, Taman Industri Kidamai 2",
        //         "Kajang",
        //         "Selangor",
        //         "Malaysia",
        //         "43000",
        //         "Devendra Rao",
        //         "Consulting Project Manager",
        //         "draob@inventec.dehon.com",
        //         "91-9900148344",
        //         "1539",
        //         9
        //     ],
        // ];

        // $rows = [
        //     [
        //         "JLK TECHNOLOGY PTE LTD",
        //         "8 Burn Road, Trivex, #06-16",
        //         "Singapore",
        //         "Singapore",
        //         "Singapore",
        //         "369977",
        //         "Scott Ng",
        //         "Sales Director",
        //         "scottng@jlk-tech.com",
        //         "+65-96332140",
        //         "1339",
        //         9
        //     ],
        // ];

        // $rows[] = [
        //     "MILLICE PTE LTD",
        //     "Blk 4012 Techplace I #05-09 Ang Mo Kio Ave 10",
        //     "Singapore",
        //     "Singapore",
        //     "Singapore",
        //     "569628",
        //     "Ong Kay Huat (KH)",
        //     "General Manager",
        //     "kh.ong@millice.com.sg",
        //     "+65-96350015",
        //     "1140",
        //     9
        // ];

        // $rows = [
        //     [
        //         "MacDermid Alpha Electronics Solutions",
        //         "MacDermid Alpha Electronics Solutions Developed Plot No. 16, North Phase, SIDCO Industrial Estate, Ambattur",
        //         "Chennai",
        //         "Tamil Nadu",
        //         "India",
        //         "600098",
        //         "Ajit Salunke",
        //         "Senior Manager - Sales & CTS",
        //         "ajit.Salunke@macdermidalpha.com",
        //         "8925874148",
        //         "",
        //         0
        //     ],
        //     [
        //         "InCore Semiconductors",
        //         "No 22 (1st Floor) Tower 2, Rayala Towers, 158 Anna Salai",
        //         "Chennai",
        //         "Tamil Nadu",
        //         "India",
        //         "600002",
        //         "Deepak Sahoo",
        //         "Chief Marketing Officer",
        //         "deepak.sahoo@incoresemi.com",
        //         "7020520742",
        //         "",
        //         0
        //     ],
        //     [
        //         "Mindgrove Technologies Pvt Ltd",
        //         "No.12 B, Vedanta Desikar Swamy Street, Mylapore",
        //         "Chennai",
        //         "Tamil Nadu",
        //         "India",
        //         "600004",
        //         "Abhay Abbu",
        //         "Manager - Sales and Marketing",
        //         "abhay@mindgrovetech.in",
        //         "9902322882",
        //         "",
        //         0
        //     ],
        //     [
        //         "IIT Madras",
        //         "IIT Madras",
        //         "Chennai",
        //         "Tamil Nadu",
        //         "India",
        //         "600036",
        //         "R. Sarathi",
        //         "Professor",
        //         "rsarathi@iitm.ac.in",
        //         "9444246436",
        //         "",
        //         0
        //     ],
        // ];
        $rows = [
            // [
            // 'Zettaone Technologies India Pvt Ltd',
            // 'P-4A, SIDCO Industrial Estate, Krishnagiri, Tamilnadu India –635001',
            // 'Krishnagiri',
            // 'Tamil Nadu',
            // 'India',
            // '635001',
            // 'Raam Narain',
            // 'CFO',
            // 'raam.narain@zettaone.com',
            // '9741800177',
            // "",
            // 0
            // ],


            // [
            //     "Netrasemi",
            //     "TrEST Research park, Opp. CET",
            //     "Thiruvananthapuram",
            //     "Kerala",
            //     "India",
            //     "695016",
            //     "Jyothis Indirabhai",
            //     "CEO",
            //     "jyothis@netrasemi.com",
            //     "8129385444",
            //     "",
            //     0
            // ],
            // [
            //     "Vervesemi Microelectronics",
            //     "Contact Ic Plot No 21, Techzone 4,",
            //     "Greater Noida",
            //     "UP",
            //     "India",
            //     "201009",
            //     "Rakesh Malik",
            //     "CEO",
            //     "rakesh@vervesemi.com",
            //     "9810495655",
            //     "",
            //     0
            // ],
            // [
            //     "3rdiTech",
            //     "2150 Shattuck Avenue",
            //     "Berkeley",
            //     "CA",
            //     "United States",
            //     "94704",
            //     "Vrinda Kapoor",
            //     "CEO",
            //     "vrinda@3rditech.in",
            //     "9871016388",
            //     "",
            //     0
            // ],
            // [
            //     "Fermionic",
            //     "#268, GVR Vision, 2nd & 3rd Floor AECS Layout - Block A",
            //     "Bengaluru",
            //     "Karnataka ",
            //     "India",
            //     "560037",
            //     "Gautam Kumar Singh",
            //     "",
            //     "gautam@fermionic.design",
            //     "9845153800",
            //     "",
            //     0
            // ],
            // [
            //     "Wisig Network",
            //     "Sohini Tech Park, 8th floor, Nanakramguda Road",
            //     "Gachibowli",
            //     "Telangana ",
            //     "India",
            //     "50032",
            //     "Dr. Kiran Kuchi",
            //     "",
            //     "kkuchi@wisig.com",
            //     "9491398508",
            //     "",
            //     0
            // ],
            // [
            //     "Moschip",
            //     "7th Floor, My Home Twitza, Plot No. 30/A,",
            //     "TSIIC Hyderabad Knowledge City",
            //     "Telangana",
            //     "India",
            //     "500081",
            //     "Srinivasa Kakumanu",
            //     '',
            //     "srinivas.kakumanu@moschip.com",
            //     "994997931",
            //     "",
            //     0
            // ],
            // [
            //     "Resonant Electronics",
            //     "",
            //     "",
            //     "",
            //     "",
            //     "",
            //     "Amit",
            //     "",
            //     "amitg@resonant-sys.com",
            //     "9811909775",
            //     "",
            //     0
            // ],
            // [
            //     "CerboTech Education Pvt Ltd.",
            //     "T/303, Shiv Sharnam Complex",
            //     "Anand",
            //     "Gujarat ",
            //     "India",
            //     "388001",
            //     "Sweta",
            //     "",
            //     "sweta@cerbotech.in",
            //     "9081846089",
            //     "",
            //     0
            // ],
            [
                "Calligo Technologies",
                "Nandi Mansion, No.55/c-42/1, 40th Cross, 2nd Main, 8th Block, Jayanagar",
                "Bangalore",
                "Karnataka",
                "India",
                "560070",
                "Anantha Kinnal",
                "",
                "anantha.kinnal@calligotech.com",
                "9845517260",
                "",
                0
            ],
            [
                "Lightspeed Photonics",
                "Unit No. 203, 2nd Floor, SBR CV Towers Sector-I, SY No64, Huda Techno Enclave",
                "Madhapur ",
                "Telangana",
                "India",
                "500081",
                "Jatin Pratap",
                "",
                "jatin@lightspeedphotonics.com",
                "9700234033",
                "",
                0
            ],




        ];


        // dd($rows);

        foreach ($rows as $row) {
            // Generate a unique co_exhibitor_id for each row
            $co_exhibitor_id = 'SI25-COEXH-' . strtoupper(substr(md5(uniqid() . microtime()), 0, 6));

            //vallidate email doesn't exist in users table 
            if (User::where('email', $row[8])->exists()) {
                //if email exists then skip this row
                echo "Email already exists: " . $row[8] . "\n";
                echo "<br>";
                continue;
            }

            //if co_exhibitor_name is already exist then skip this row
            if (CoExhibitor::where('co_exhibitor_name', $row[0])->exists()) {
                //if co_exhibitor_name exists then skip this row
                echo "Co-Exhibitor Name already exists: " . $row[0] . "\n";
                echo "<br>";
                continue;
            }


            // Prepare data for each co-exhibitor
            $data = [
                'application_id'    => $request->input('application_id', 565),
                'co_exhibitor_name' => $row[0],
                'address1'          => $row[1],
                'city'              => $row[2],
                'state'             => $row[3],
                'country'           => $row[4],
                'zip'               => $row[5],
                'contact_person'    => $row[6],
                'job_title'         => $row[7],
                'email'             => $row[8],
                'phone'             => str_replace(' ', '', $row[9]),
                'booth_number'      => $row[10],
                'stall_size'        => $row[11],
                'pavilion_name'     => 'Startup Pavilion', // Default to 'General Pavilion' if not provided
                'status'            => 'approved',
                'proof_document'    => '',
                'co_exhibitor_id'   => $co_exhibitor_id,
                'purchase_allowed'  => 1,
                'approved_At'       => now(),
                'relation'          => 'Co-Exhibitor',
                'user_id'           => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];

           // dd($data);

            echo (json_encode($data) . "\n");
            echo "<br>";

            // Merge with any additional request data
            // $data = array_merge($data, $request->all());

            // // Generate a random password for the user
            $password = substr(md5(uniqid()), 0, 10);

            // // Create user and get user id
            $user = User::create([
                'name' => $data['co_exhibitor_name'],
                'email' => $data['email'],
                'password' => Hash::make($password),
                'role' => 'co-exhibitor',
                'email_verified_at' => now(),
            ]);
            $data['user_id'] = $user->id;

            echo "User created: " . $data['co_exhibitor_name'] . " with email: " . $data['email'] . " " . $password . "\n";
            echo "<br>";
            // Create the co-exhibitor record
            $coExhibitor = CoExhibitor::create($data);

            // Update the co_exhibitor_id and user_id in the record
            $coExhibitor->co_exhibitor_id = $co_exhibitor_id;
            $coExhibitor->user_id = $user->id;
            $coExhibitor->save();

            // Update the exhibition participant record
            $this->updateExhibitionParticipant($coExhibitor->id, $data['stall_size']);


            $exhibiting_under = Application::where('id', $coExhibitor->application_id)->value('company_name');

            Mail::to($coExhibitor->email)
                ->cc(['gaurav@investtn.in', ORGANIZER_EMAIL])
                ->bcc('test.interlinks@gmail.com')
                ->send(new CoExhibitorApprovalMail(
                    $coExhibitor,
                    $password,
                    $exhibiting_under
                ));

            // exit;

            // Optionally, send email notification here if needed
        }

        // exit;


        /*
        // Generate a unique co_exhibitor_id
        $co_exhibitor_id = 'SI25-COEXH-' . strtoupper(substr(md5(uniqid()), 0, 6));




        // Use request data, fallback to defaults if not present
        $data = [
            'application_id'    => $request->input('application_id', 199),
            'co_exhibitor_name' => $request->input('co_exhibitor_name', 'Test Co Exhibitor'),
            'address1'          => $request->input('address1', 'Test Address Line 1'),
            'city'              => $request->input('city', 'Test City'),
            'state'             => $request->input('state', 'Test State'),
            'country'           => $request->input('country', 'Test Country'),
            'zip'               => $request->input('zip', '123456'),
            'contact_person'    => $request->input('contact_person', 'Test Contact Person'),
            'job_title'         => $request->input('job_title', 'Test Job Title'),
            'email'             => $request->input('email', 'test+2@test.com'),
            'phone'             => $request->input('phone', '1234567890'),
            'booth_number'      => $request->input('booth_number', 'B123'),
            'stall_size'        => $request->input('stall_size', 9),
            'pavilion_name'     => $request->input('pavilion_name', 'Test Pavilion'),
            'status'            => 'approved',
            'proof_document'    => '',
            'co_exhibitor_id'   => $co_exhibitor_id,
            'purchase_allowed'  => 1,
            'approved_At'       => now(),
            'relation'          => 'Co-Exhibitor',
            'user_id'           => null,
            'created_at'        => now(),
            'updated_at'        => now(),
        ];

        //merge the data with the request data
        $data = array_merge($data, $request->all());

        // For debugging: see the request data


        // dd($request->all());

        // For debugging: see the merged data
        // dd($data);
        // Validate the request data
        // $validator = \Validator::make($request->all(), [
        //     'application_id' => 'required|exists:applications,id',
        //     'co_exhibitor_name' => 'required|string|max:255',
        //     'address1' => 'required|string|max:255',
        //     'city' => 'required|string|max:100',
        //     'state' => 'required|string|max:100',
        //     'country' => 'required|string|max:100',
        //     'zip' => 'required|string|max:20',
        //     'contact_person' => 'required|string|max:255',
        //     'job_title' => 'required|string|max:255',
        //     'email' => 'required|email|unique:co_exhibitors,email',
        //     'phone' => 'required|string|max:20',
        //     'booth_number' => 'nullable|string|max:50',
        //     'stall_size' => 'nullable|integer|min:1',
        //     'pavilion_name' => 'nullable|string|max:100',
        // ]);

        // if ($validator->fails()) {
        //     // Show error and stop further execution
        //     dd($validator->errors()->all());
        // }

        // dd($data);
        //crreate user and get user id
        $password = substr(md5(uniqid()), 0, 10); // Generate a random password
        $user = User::create([
            'name' => $data['co_exhibitor_name'],
            'email' => $data['email'],
            'password' => Hash::make($password), // Set a default password or generate one
            'role' => 'co-exhibitor', // Assuming roles exist
            'email_verified_at' => now(),
        ]);
        // Set the user_id in the data array
        $data['user_id'] = $user->id;
        // Create the co-exhibitor record
        $coExhibitor = CoExhibitor::create($data);
        // Update the co_exhibitor_id in the record
        $coExhibitor->co_exhibitor_id = $co_exhibitor_id;
        $coExhibitor->user_id = $user->id; // Set the user_id
        $coExhibitor->save();
        // Update the exhibition participant record
        $this->updateExhibitionParticipant($coExhibitor->id, $data['stall_size']);




        //dd($user);


        $exhibiting_under = Application::where('id', $coExhibitor->application_id)->value('company_name');

        Mail::to($coExhibitor->email)
        ->cc(['yhwang@semi.org', ORGANIZER_EMAIL])
            ->bcc('test.interlinks@gmail.com')
            ->send(new CoExhibitorApprovalMail(
            $coExhibitor,
            $password,
            $exhibiting_under
            ));
*/
        return response()->json([
            'message' => 'Co-Exhibitor created successfully',
            'co_exhibitor_id' => $coExhibitor->co_exhibitor_id,
            'user_id' => $coExhibitor->user_id,
            'password' => $password, // Return the generated password
        ], 201);
    }
}
