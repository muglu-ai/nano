<?php

namespace App\Http\Controllers;

use App\Jobs\SendToApiJob;

use App\Models\OutboundRequest;

use Illuminate\Http\Request;

use Illuminate\Support\Str;

use App\Jobs\SendAPIJob;

use App\Models\Attendee;

use App\Models\StallManning;

use App\Models\ComplimentaryDelegate;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Http;

//use http

class ApiRelayController extends Controller
{
    public function enqueueToHelpTool(Request $request)
    {
         return response()->json(
            [
                "success" => false,

                "message" => "Function disabled temporarily",

                "data" => null,
            ],
            400
        );
        // If you want to accept runtime payload, validate + merge over defaults:

        $validated = $request->validate([
            "Name" => "sometimes|required|string|max:150",

            "CompanyName" => "sometimes|required|string|max:200",

            "Designation" => "sometimes|nullable|string|max:150",

            "Email" => "sometimes|required|email",

            "Mobile" => "sometimes|required|string|max:20",

            "RegistrationType" => "sometimes|required|string|max:50",

            "unique_id" => "sometimes|required|string|max:100",

            "Country" => "sometimes|required|string|max:100",

            "State" => "sometimes|required|string|max:100",

            "City" => "sometimes|required|string|max:100",

            "Inaugral" => "sometimes|in:0,1",

            "Idtype" => "sometimes|nullable|string|max:50",

            "Idpath" => "sometimes|nullable|string|max:100",

            "Imagepath" => "sometimes|nullable|url",

            "LunchStatus" => "sometimes|in:0,1",
        ]);

        // Defaults (these are your current hardcoded values)

        $validated = $request->validate([
            "Name" => "sometimes|required|string|max:150",

            "CompanyName" => "sometimes|required|string|max:200",

            "Designation" => "sometimes|nullable|string|max:150",

            "Email" => "sometimes|required|email",

            "Mobile" => "sometimes|required|string|max:20",

            "RegistrationType" => "sometimes|required|string|max:50",

            "unique_id" => "sometimes|required|string|max:100",

            "Country" => "sometimes|required|string|max:100",

            "State" => "sometimes|required|string|max:100",

            "City" => "sometimes|required|string|max:100",

            "Inaugral" => "sometimes|in:0,1",

            "Idtype" => "sometimes|nullable|string|max:50",

            "Idpath" => "sometimes|nullable|string|max:100",

            "Imagepath" => "sometimes|nullable|url",

            "LunchStatus" => "sometimes|in:0,1",
        ]);

        // Final payload (honors remote’s expected key casing)

        $data = $validated;

        // ⬅️ Use the SEMICON exhibitor endpoint config

        // $endpoint = env('REGISTRATION_API_ENDPOINT');

        $endpoint =
            "https://www.semiconindiammactiv.com/SemiconIndia2025GetAPIVD/api/participant/visitor";

        // dd($endpoint);

        $idempKey = (string) Str::uuid();

        $record = OutboundRequest::create([
            "endpoint" => $endpoint,

            "idempotency_key" => $idempKey,

            "payload" => $data,

            "status" => "queued",
        ]);

        // dd($record);

        // dd(

        //     dispatch(new SendToApiJob($record->id))->onQueue('webhooks')

        // );

        dispatch(new SendAPIJob($record->id))->onQueue("webhooks");

        return response()->json(
            [
                "success" => true,

                "message" => "Queued for delivery",

                "data" => [
                    "tracking_id" => $record->id,

                    "idempotency_key" => $record->idempotency_key,

                    "status" => $record->status,

                    // 'endpoint'        => $endpoint,

                    "payload" => $data, // DEBUG only
                ],
            ],
            202
        );
    }

    public function status(int $id)
    {
        $r = OutboundRequest::findOrFail($id);

        return response()->json([
            "success" => true,

            "data" => [
                "status" => $r->status,

                "attempts" => $r->attempts,

                "response_code" => $r->response_code,

                "responded_at" => optional($r->responded_at)->toIso8601String(),

                "last_error" => $r->last_error,
            ],
        ]);
    }

    //sample function to test api endpoint

    public function testSampleEnqueue()
    {
        // $sampleUsers = [

        //     // Organiser

        //     [

        //     'Name'             => 'Ravi Boratkar',

        //     'CompanyName'      => 'MM ACTIV SCI-TECH COMMUNICATIONS PVT LTD',

        //     'Designation'      => 'ORGANISER',

        //     'Email'            => 'test.user11+6@mmactiv.com',

        //     'Mobile'           => '9860100001',

        //     'RegistrationType' => 'Organiser',

        //     'unique_id'        => 'EXH2025-00001',

        //     'Country'          => 'India',

        //     'State'            => 'Maharashtra',

        //     'City'             => 'Mumbai',

        //     'Inaugral'         => '1',

        //     'Idtype'           => 'Adhar',

        //     'Idpath'           => '1234567890',

        //     'Imagepath'        => 'https://ui-avatars.com/api/?name=Ravi+Boratkar&background=0D8ABC&color=fff',

        //     'LunchStatus'   => '1',

        //     ],

        //     [

        //     'Name'             => 'Vibha Bhatia',

        //     'CompanyName'      => 'MM ACTIV SCI-TECH COMMUNICATIONS PVT LTD',

        //     'Designation'      => 'ORGANISER',

        //     'Email'            => 'vibha.bhatia+1@mmactiv.com',

        //     'Mobile'           => '9873442339',

        //     'RegistrationType' => 'Organiser',

        //     'unique_id'        => 'EXH2025-00002',

        //     'Country'          => 'India',

        //     'State'            => 'Maharashtra',

        //     'City'             => 'Mumbai',

        //     'Inaugral'         => '1',

        //     'Idtype'           => 'Adhar',

        //     'Idpath'           => '1234567891',

        //     'Imagepath'        => 'https://ui-avatars.com/api/?name=Vibha+Bhatia&background=0D8ABC&color=fff',

        //     'LunchStatus'   => '1',

        //     ],

        //     [

        //     'Name'             => 'Samanth Anikar',

        //     'CompanyName'      => 'MM ACTIV',

        //     'Designation'      => 'ORGANISER',

        //     'Email'            => 'test.user3+1@mmactiv.com',

        //     'Mobile'           => '9860100003',

        //     'RegistrationType' => 'Organiser',

        //     'unique_id'        => 'EXH2025-00003',

        //     'Country'          => 'India',

        //     'State'            => 'Maharashtra',

        //     'City'             => 'Mumbai',

        //     'Inaugral'         => '0',

        //     'Idtype'           => 'Adhar',

        //     'Idpath'           => '1234567892',

        //     'Imagepath'        => 'https://ui-avatars.com/api/?name=Samanth+Anikar&background=0D8ABC&color=fff',

        //     'LunchStatus'   => '0',

        //     ],

        //     // Dignitaries

        //     [

        //     'Name'             => 'Dr. A P J Abdul Kalam',

        //     'CompanyName'      => 'Government of India',

        //     'Designation'      => 'Dignitary',

        //     'Email'            => 'apj.kalam+1@scitest.com',

        //     'Mobile'           => '9000000001',

        //     'RegistrationType' => 'Dignitaries',

        //     'unique_id'        => 'EXH2025-00004',

        //     'Country'          => 'India',

        //     'State'            => 'Delhi',

        //     'City'             => 'New Delhi',

        //     'Inaugral'         => '1',

        //     'Idtype'           => 'Adhar',

        //     'Idpath'           => '1234567893',

        //     'Imagepath'        => 'https://ui-avatars.com/api/?name=APJ+Kalam&background=0D8ABC&color=fff',

        //     'LunchStatus'   => '1',

        //     ],

        //     // Delegate

        //     [

        //     'Name'             => 'Tejas Rashinkar',

        //     'CompanyName'      => 'Tech Delegates Ltd',

        //     'Designation'      => 'Delegate',

        //     'Email'            => 'tejas.rashinkar+1@interlinks.in',

        //     'Mobile'           => '9000000002',

        //     'RegistrationType' => 'Delegate',

        //     'unique_id'        => 'EXH2025-00005',

        //     'Country'          => 'India',

        //     'State'            => 'Karnataka',

        //     'City'             => 'Bangalore',

        //     'Inaugral'         => '0',

        //     'Idtype'           => 'Adhar',

        //     'Idpath'           => '1234567894',

        //     'Imagepath'        => 'https://ui-avatars.com/api/?name=Priya+Sharma&background=0D8ABC&color=fff',

        //     'LunchStatus'   => '1',

        //     ],

        //     // Event Partner

        //     [

        //     'Name'             => 'Rahul Kumar',

        //     'CompanyName'      => 'Event Partners Inc',

        //     'Designation'      => 'Event Partner',

        //     'Email'            => 'rahul.kumar+1@interlinks.in',

        //     'Mobile'           => '9000000003',

        //     'RegistrationType' => 'Event Partner',

        //     'unique_id'        => 'EXH2025-00006',

        //     'Country'          => 'India',

        //     'State'            => 'Telangana',

        //     'City'             => 'Hyderabad',

        //     'Inaugral'         => '1',

        //     'Idtype'           => 'Adhar',

        //     'Idpath'           => '1234567895',

        //     'Imagepath'        => 'https://ui-avatars.com/api/?name=Rahul+Verma&background=0D8ABC&color=fff',

        //     'LunchStatus'   => '0',

        //     ],

        //     // Media

        //     [

        //     'Name'             => 'Sneha Kapoor',

        //     'CompanyName'      => 'Media House',

        //     'Designation'      => 'Journalist',

        //     'Email'            => 'sneha.kapoor+1@scitest.com',

        //     'Mobile'           => '9000000004',

        //     'RegistrationType' => 'Media',

        //     'unique_id'        => 'EXH2025-00007',

        //     'Country'          => 'India',

        //     'State'            => 'West Bengal',

        //     'City'             => 'Kolkata',

        //     'Inaugral'         => '0',

        //     'Idtype'           => 'Adhar',

        //     'Idpath'           => '1234567896',

        //     'Imagepath'        => 'https://ui-avatars.com/api/?name=Sneha+Kapoor&background=0D8ABC&color=fff',

        //     'LunchStatus'   => '0',

        //     ],

        //     // Academia

        //     [

        //     'Name'             => 'Dr. Amit Joshi',

        //     'CompanyName'      => 'IIT Bombay',

        //     'Designation'      => 'Professor',

        //     'Email'            => 'amit.joshi+1@scitest.com',

        //     'Mobile'           => '9000000005',

        //     'RegistrationType' => 'Academia',

        //     'unique_id'        => 'EXH2025-00008',

        //     'Country'          => 'India',

        //     'State'            => 'Maharashtra',

        //     'City'             => 'Mumbai',

        //     'Inaugral'         => '1',

        //     'Idtype'           => 'Adhar',

        //     'Idpath'           => '1234567897',

        //     'Imagepath'        => 'https://ui-avatars.com/api/?name=Amit+Joshi&background=0D8ABC&color=fff',

        //     'LunchStatus'   => '0',

        //     ],

        //     // Students

        //     [

        //     'Name'             => 'Anjali Mehta',

        //     'CompanyName'      => 'IIT Delhi',

        //     'Designation'      => 'Student',

        //     'Email'            => 'anjali.mehta+1@scitest.com',

        //     'Mobile'           => '9000000006',

        //     'RegistrationType' => 'Students',

        //     'unique_id'        => 'EXH2025-00009',

        //     'Country'          => 'India',

        //     'State'            => 'Delhi',

        //     'City'             => 'New Delhi',

        //     'Inaugral'         => '0',

        //     'Idtype'           => 'Adhar',

        //     'Idpath'           => '1234567898',

        //     'Imagepath'        => 'https://ui-avatars.com/api/?name=Anjali+Mehta&background=0D8ABC&color=fff',

        //     'LunchStatus'   => '0',

        //     ],

        //     // Volunteers

        //     [

        //     'Name'             => 'Karan Singh',

        //     'CompanyName'      => 'Volunteer Group',

        //     'Designation'      => 'Volunteer',

        //     'Email'            => 'karan.singh+1@scitest.com',

        //     'Mobile'           => '9000000007',

        //     'RegistrationType' => 'Volunteers',

        //     'unique_id'        => 'EXH2025-00010',

        //     'Country'          => 'India',

        //     'State'            => 'Punjab',

        //     'City'             => 'Amritsar',

        //     'Inaugral'         => '1',

        //     'Idtype'           => 'Adhar',

        //     'Idpath'           => '1234567899',

        //     'Imagepath'        => 'https://ui-avatars.com/api/?name=Karan+Singh&background=0D8ABC&color=fff',

        //     'LunchStatus'   => '0',

        //     ],

        //     // Services

        //     [

        //     'Name'             => 'Meera Nair',

        //     'CompanyName'      => 'Service Providers Ltd',

        //     'Designation'      => 'Service Staff',

        //     'Email'            => 'meera.nair+1@scitest.com',

        //     'Mobile'           => '9000000008',

        //     'RegistrationType' => 'Services',

        //     'unique_id'        => 'EXH2025-00011',

        //     'Country'          => 'India',

        //     'State'            => 'Kerala',

        //     'City'             => 'Kochi',

        //     'Inaugral'         => '0',

        //     'Idtype'           => 'Adhar',

        //     'Idpath'           => '1234567900',

        //     'Imagepath'        => 'https://ui-avatars.com/api/?name=Meera+Nair&background=0D8ABC&color=fff',

        //     'LunchStatus'   => '0',

        //     ],

        //     // Visitors

        //     [

        //     'Name'             => 'Vivek Patil',

        //     'CompanyName'      => 'Visitor',

        //     'Designation'      => 'Visitor',

        //     'Email'            => 'vivek.patil+1@interlinks.in',

        //     'Mobile'           => '9000000009',

        //     'RegistrationType' => 'Visitors',

        //     'unique_id'        => 'EXH2025-00012',

        //     'Country'          => 'India',

        //     'State'            => 'Tamil Nadu',

        //     'City'             => 'Chennai',

        //     'Inaugral'         => '1',

        //     'Idtype'           => 'Adhar',

        //     'Idpath'           => '1234567901',

        //     'Imagepath'        => 'https://ui-avatars.com/api/?name=Vivek+Patil&background=0D8ABC&color=fff',

        //     'LunchStatus'   => '0',

        //     ],

        //     // Exhibitors

        //     [

        //     'Name'             => 'Neha Gupta',

        //     'CompanyName'      => 'Exhibitor Pvt Ltd',

        //     'Designation'      => 'Exhibitor',

        //     'Email'            => 'neha.gupta+1@scitest.com',

        //     'Mobile'           => '9000000010',

        //     'RegistrationType' => 'Exhibitors',

        //     'unique_id'        => 'EXH2025-00013',

        //     'Country'          => 'India',

        //     'State'            => 'Gujarat',

        //     'City'             => 'Ahmedabad',

        //     'Inaugral'         => '0',

        //     'Idtype'           => 'Adhar',

        //     'Idpath'           => '1234567902',

        //     'Imagepath'        => 'https://ui-avatars.com/api/?name=Neha+Gupta&background=0D8ABC&color=fff',

        //     'LunchStatus'   => '1',

        //     ],

        // ];

        $sampleUsers2 = [
            [
                "RegID" => "DEG0001",

                "Name" => "Piyush Priy",

                "CompanyName" => "ISM",

                "Designation" => "Dignitaries",

                "Email" => "piyush.priy+1@ism.gov.in",

                "Mobile" => "9860108601",

                "RegistrationType" => "Dignitaries",

                "unique_id" => "EXH2025-00014",

                "Country" => "India",

                "State" => "Delhi",

                "City" => "Delhi",

                "Inaugral" => "1",

                "Idtype" => "Aadhar Card",

                "Idpath" => "1234 1234 1234",

                "Imagepath" =>
                    "https://portal.semiconindia.org/storage/profile_pictures/SEMI25VI_V60XD.jpeg",

                "LunchStatus" => "1",
            ],

            [
                "RegID" => "DEL0001",

                "Name" => "Rajat Rai",

                "CompanyName" => config('constants.EVENT_NAME'),

                "Designation" => "Delegate",

                "Email" => "rajat.btpl+1@semiconindia.org",

                "Mobile" => "9860108602",

                "RegistrationType" => "Delegate",

                "unique_id" => "EXH2025-00015",

                "Country" => "India",

                "State" => "Delhi",

                "City" => "Delhi",

                "Inaugral" => "1",

                "Idtype" => "Aadhar Card",

                "Idpath" => "1234 1234 1234",

                "Imagepath" =>
                    "https://portal.semiconindia.org/storage/profile_pictures/SEMI25VI_V60XD.jpeg",

                "LunchStatus" => "1",
            ],

            [
                "RegID" => "VIS0001",

                "Name" => "Sudhanshu ",

                "CompanyName" => config('constants.EVENT_NAME'),

                "Designation" => "Organizer",

                "Email" => "sudhanshu.btpl+1@semiconindia.org",

                "Mobile" => "9860108603",

                "RegistrationType" => "Visitor",

                "unique_id" => "EXH2025-00016",

                "Country" => "India",

                "State" => "Delhi",

                "City" => "Delhi",

                "Inaugral" => "1",

                "Idtype" => "Aadhar Card",

                "Idpath" => "1234 1234 1234",

                "Imagepath" =>
                    "https://portal.semiconindia.org/storage/profile_pictures/SEMI25VI_V60XD.jpeg",

                "LunchStatus" => "1",
            ],

            [
                "RegID" => "EXH0001",

                "Name" => "Dheeraj ",

                "CompanyName" => config('constants.EVENT_NAME'),

                "Designation" => "Exhibitor",

                "Email" => "dheeraj.btpl+1@semiconindia.org",

                "Mobile" => "+919860108604",

                "RegistrationType" => "Exhibitor",

                "unique_id" => "EXH2025-00017",

                "Country" => "India",

                "State" => "Delhi",

                "City" => "Delhi",

                "Inaugral" => "0",

                "Idtype" => "Aadhar Card",

                "Idpath" => "1234 1234 1234",

                "Imagepath" =>
                    "https://portal.semiconindia.org/storage/profile_pictures/SEMI25VI_V60XD.jpeg",

                "LunchStatus" => "0",
            ],

            [
                "RegID" => "EVP0001",

                "Name" => "Vibha Bhatia",

                "CompanyName" => "MMActiv",

                "Designation" => "Organizer",

                "Email" => "vibha.bhatia+1@mmactiv.com",

                "Mobile" => "+919873442339",

                "RegistrationType" => "Event partner",

                "unique_id" => "EXH2025-00020",

                "Country" => "India",

                "State" => "Delhi",

                "City" => "Delhi",

                "Inaugral" => "1",

                "Idtype" => "Aadhar Card",

                "Idpath" => "1234 1234 1234",

                "Imagepath" =>
                    "https://portal.semiconindia.org/storage/profile_pictures/SEMI25VI_V60XD.jpeg",

                "LunchStatus" => "1",
            ],

            [
                "RegID" => "MED0001",

                "Name" => "Ved Mall",

                "CompanyName" => config('constants.EVENT_NAME'),

                "Designation" => "Delegate",

                "Email" => "vmall+1@semi.org",

                "Mobile" => "+919860108605",

                "RegistrationType" => "Media",

                "unique_id" => "EXH2025-00018",

                "Country" => "India",

                "State" => "Delhi",

                "City" => "Delhi",

                "Inaugral" => "1",

                "Idtype" => "Aadhar Card",

                "Idpath" => "1234 1234 1234",

                "Imagepath" =>
                    "https://portal.semiconindia.org/storage/profile_pictures/SEMI25VI_V60XD.jpeg",

                "LunchStatus" => "1",
            ],

            [
                "RegID" => "DGN0001",

                "Name" => "Ashok Chandak",

                "CompanyName" => config('constants.EVENT_NAME'),

                "Designation" => "President",

                "Email" => "achandak+1@semi.org",

                "Mobile" => "9860108606",

                "RegistrationType" => "Dignitaries",

                "unique_id" => "EXH2025-00019",

                "Country" => "India",

                "State" => "Delhi",

                "City" => "Delhi",

                "Inaugral" => "1",

                "Idtype" => "Aadhar Card",

                "Idpath" => "123412341234",

                "Imagepath" =>
                    "https://portal.semiconindia.org/storage/profile_pictures/SEMI25VI_V60XD.jpeg",

                "LunchStatus" => "1",
            ],

            [
                "RegID" => "ORG0001",

                "Name" => "Vivek Patil",

                "CompanyName" => "MMActiv",

                "Designation" => "Organiser",

                "Email" => "vivek.patil+1@mmactiv.com",

                "Mobile" => "+919860108651",

                "RegistrationType" => "Academia",

                "unique_id" => "EXH2025-00020",

                "Country" => "India",

                "State" => "Delhi",

                "City" => "Delhi",

                "Inaugral" => "1",

                "Idtype" => "Aadhar Card",

                "Idpath" => "1234 1234 1234",

                "Imagepath" =>
                    "https://portal.semiconindia.org/storage/profile_pictures/SEMI25VI_V60XD.jpeg",

                "LunchStatus" => "1",
            ],
        ];

        $sampleUsers = [
            [
                "RegID" => "DEL0021",

                "Name" => "Manish Sharma",

                "CompanyName" => "SCI Knowledge Interlinks Pvt Ltd",

                "Designation" => "Delegate",

                "Email" => "manish.sharma+2@interlinks.in",

                "Mobile" => "9801217815",

                "RegistrationType" => "Delegate",

                "unique_id" => "EXH2025-00521",

                "Country" => "India",

                "State" => "Dummy State",

                "City" => "Dummy City",

                "Inaugral" => "1",

                "Idtype" => "Aadhar Card",

                "Idpath" => "1234 5678 9012",

                "Imagepath" =>
                    "https://ui-avatars.com/api/?name=Manish+Sharma&background=0D8ABC&color=fff",

                "LunchStatus" => "1",
            ],
        ];

        $results = [];

        foreach ($sampleUsers2 as $user) {
            $endpoint =
                "https://www.semiconindiammactiv.com/SemiconIndia2025GetAPIVD/api/participant/visitor";

            $idempKey = (string) Str::uuid();

            $data = $user;

            // dd($data);

            $stub = OutboundRequest::create([
                "endpoint" =>
                    strtolower($data["RegistrationType"]) === "exhibitor"
                        ? "exhibitor"
                        : "visitor",

                "idempotency_key" => Str::random(25),

                "payload" => $data,

                "status" => "queued",

                "attempts" => 0,
            ]);

            SendAPIJob::dispatch($data, $stub->id);

            $record = $stub;

            dispatch(new SendAPIJob($user, $record->id))->onQueue("webhooks");

            $results[] = [
                "tracking_id" => $record->id,

                "idempotency_key" => $record->idempotency_key,

                "status" => $record->status,

                // 'endpoint'        => $endpoint,

                "payload" => $data, // DEBUG only
            ];
        }

        return response()->json(
            [
                "success" => true,

                "message" => "Sample users queued for delivery",

                "data" => $results,
            ],
            202
        );
    }

    //make a function where I pass the data and it will Enqueue the data to the api

    public function enqueueToHelpToolDynamic(array $data)
    {
        $endpoint =
            "https://www.semiconindiammactiv.com/SemiconIndia2025GetAPIVD/api/participant/visitor";

        $idempKey = (string) Str::uuid();

        $record = OutboundRequest::create([
            "endpoint" => $endpoint,

            "idempotency_key" => $idempKey,

            "payload" => $data,

            "status" => "queued",
        ]);

        dispatch(new SendAPIJob($data, $record->id))->onQueue("sendapi");

        return response()->json(
            [
                "success" => true,

                "message" => "Queued for delivery",

                "data" => [
                    "tracking_id" => $record->id,

                    "idempotency_key" => $record->idempotency_key,

                    "status" => $record->status,

                    // 'endpoint'        => $endpoint,

                    "payload" => $data, // DEBUG only
                ],
            ],
            202
        );
    }

    //make a function where I pass the unqiue id and it will send data to the api from different models

    public function sendDataToApi($unique_id)
    {
         return response()->json(
            [
                "success" => false,

                "message" => "Function disabled temporarily",

                "data" => null,
            ],
            400
        );
        $attendee = Attendee::where("unique_id", $unique_id)->first();

        $stallManning = StallManning::where("unique_id", $unique_id)->first();

        $complimentaryDelegate = ComplimentaryDelegate::where(
            "unique_id",
            $unique_id
        )->first();

        // dd($attendee, $stallManning, $complimentaryDelegate);

        // Use a common variable name for whatever is found

        $name = trim(
            ($attendee->first_name ?? "") .
                " " .
                ($attendee->middle_name ?? "") .
                " " .
                ($attendee->last_name ?? "")
        );

        if (empty($name)) {
            $name = trim(
                ($stallManning->first_name ?? "") .
                    " " .
                    ($stallManning->last_name ?? "")
            );
        }

        if (empty($name)) {
            $name = trim(
                ($complimentaryDelegate->first_name ?? "") .
                    " " .
                    ($complimentaryDelegate->middle_name ?? "") .
                    " " .
                    ($complimentaryDelegate->last_name ?? "")
            );
        }

        $regId =
            $attendee->regId ??
            ($stallManning->regId ?? ($complimentaryDelegate->regId ?? ""));

        // dd($name);

        $companyName =
            $attendee->company ??
            ($stallManning->organisation_name ??
                ($complimentaryDelegate->organisation_name ??
                    "Unknown Company"));

        // dd($stallManning);

        //dd($companyName);

        $designation =
            $attendee->designation ??
            ($stallManning->job_title ??
                ($complimentaryDelegate->job_title ?? ""));

        $email =
            $attendee->email ??
            ($stallManning->email ?? ($complimentaryDelegate->email ?? ""));

        $mobile =
            $attendee->mobile ??
            ($stallManning->mobile ?? ($complimentaryDelegate->mobile ?? ""));

        /*















        optional($attendee->countryRelation)->name,















            optional($attendee->stateRelation)->name,















        */

        // Fetch country, state, and city

        $country =
            $attendee->countryRelation->name ??
            ($stallManning->countryRelation->name ??
                ($complimentaryDelegate->countryRelation->name ?? null));

        $state =
            $attendee->stateRelation->name ??
            ($stallManning->stateRelation->name ??
                ($complimentaryDelegate->stateRelation->name ?? null));

        $city =
            $attendee->city ??
            ($stallManning->city ?? ($complimentaryDelegate->city ?? null));

        // dd($country, $state, $city);

        // Dynamic fields

        $registrationType = "Visitor"; // Default to 'Visitor'

        if ($attendee) {
            $registrationType =
                $attendee->approvedCate ??
                ($attendee->badge_category ?? "Visitor");
        }

        if ($stallManning) {
            $registrationType = $stallManning->confirmedCategory ?? "Exhibitor";
        }

        if ($complimentaryDelegate) {
            $registrationType =
                $complimentaryDelegate->approvedCate ?? "Exhibitor";
        }

        $idType =
            $attendee->id_card_type ??
            ($stallManning->id_type ?? ($complimentaryDelegate->id_type ?? ""));

        $idPath =
            $attendee->id_card_number ??
            ($stallManning->id_no ?? ($complimentaryDelegate->id_no ?? ""));

        $imagePath =
            $attendee->profile_picture ??
            ($stallManning->profile_pic ??
                ($complimentaryDelegate->profile_pic ?? ""));

        $lunchStatus =
            $attendee->lunchStatus ??
            ($complimentaryDelegate->lunchStatus ?? "0");

        $inaugural =
            $attendee->inauguralConfirmation ??
            ($complimentaryDelegate->inauguralConfirmation ?? "0");

        // if category is Delegate , Organizer , Dignitaries  then set lunch status to 1

        if (
            $registrationType == "Delegate" ||
            $registrationType == "Organizer" ||
            $registrationType == "Dignitaries"
        ) {
            $lunchStatus = "1";
        }

        // $email = "vivek.patil@mmactiv.com";

        // $mobile = "9860108651";

        // $regId = "VO0005";

        // Curate the data for the API

        //add url to the image path https://portal.semiconindia.org/

        // $imagePath = 'https://portal.semiconindia.org/' . $imagePath;

        // if the entries from complimentary delegate then image path is https://portal.semiconindia.org/storage/profile_pictures/

        if ($imagePath !== null && $imagePath !== "") {
            if ($complimentaryDelegate) {
                $imagePath =
                    "https://portal.semiconindia.org/storage/" . $imagePath;
            } else {
                $imagePath = "https://portal.semiconindia.org/" . $imagePath;
            }
        }

        if ($stallManning) {
            $imagePath = "";
        }

        if (str_starts_with($regId, "DAP")) {
            $inaugural = "1";

            //$lunchStatus = '1';
        }

        //if regid contains DE0 then make that as delegate with inaugural 1 and lunch status 1

        if (str_starts_with($regId, "DE0")) {
            $registrationType = "Delegate";

            $inaugural = "1";

            $lunchStatus = "1";
        }

        //if

        //if $regId contains DI0 then make that as Dignitaries with inaugural 1 and lunch status 1

        if (str_starts_with($regId, "DI0")) {
            $registrationType = "Dignitaries";

            $inaugural = "1";

            $lunchStatus = "1";
        }

        //if $regId contains DAP then make that as DAP with inaugural 1 and lunch status 1

        //rmove space from the $mobile

        $mobile = str_replace(" ", "", $mobile);

        //if regID

        // $inaugural = '1';

        // $registrationType = 'Visitor';

        $data = [
            "RegID" => $regId,

            "Name" => $name,

            "CompanyName" => $companyName,

            "Designation" => $designation,

            "Email" => $email,

            "Mobile" => $mobile,

            "RegistrationType" => $registrationType,

            "unique_id" => $unique_id,

            "Country" => $country,

            "State" => $state,

            "City" => $city,

            "Inaugral" => $inaugural,

            "Idtype" => $idType,

            "Idpath" => $idPath,

            "Imagepath" => $imagePath,

            "LunchStatus" => $lunchStatus,
        ];

        // dd($data);

        $endpoint =
            "https://www.semiconindiammactiv.com/SemiconIndia2025GetAPIVD/api/participant/visitor";

        $idempKey = (string) Str::uuid();

        // dd($data);

        $stub = OutboundRequest::create([
            "endpoint" =>
                strtolower($data["RegistrationType"]) === "exhibitor"
                    ? "exhibitor"
                    : "visitor",

            "idempotency_key" => Str::random(25),

            "payload" => $data,

            "status" => "queued",

            "attempts" => 0,
        ]);

        SendAPIJob::dispatch($data, $stub->id);

        $record = $stub;

        dispatch(new SendAPIJob($data, $record->id))->onQueue("webhooks");

        $results[] = [
            "tracking_id" => $record->id,

            "idempotency_key" => $record->idempotency_key,

            "status" => $record->status,

            // 'endpoint'        => $endpoint,

            "payload" => $data, // DEBUG only
        ];

        return response()->json(
            [
                "success" => true,

                "message" => "Data queued for delivery",

                "data" => $results,
            ],
            202
        );
    }

    public function sendDataToApiNew($unique_id)
    {

        return response()->json(
            [
                "success" => false,

                "message" => "Function disabled temporarily",

                "data" => null,
            ],
            400
        );
        $attendee = Attendee::where("unique_id", $unique_id)->first();

        $stallManning = StallManning::where("unique_id", $unique_id)->first();

        $complimentaryDelegate = ComplimentaryDelegate::where(
            "unique_id",
            $unique_id
        )->first();

        // --- Prepare Name ---

        $name = trim(
            ($attendee->first_name ?? "") .
                " " .
                ($attendee->middle_name ?? "") .
                " " .
                ($attendee->last_name ?? "")
        );

        if (empty($name)) {
            $name = trim(
                ($stallManning->first_name ?? "") .
                    " " .
                    ($stallManning->last_name ?? "")
            );
        }

        if (empty($name)) {
            $name = trim(
                ($complimentaryDelegate->first_name ?? "") .
                    " " .
                    ($complimentaryDelegate->middle_name ?? "") .
                    " " .
                    ($complimentaryDelegate->last_name ?? "")
            );
        }

        $regId =
            $attendee->regId ??
            ($stallManning->regId ?? ($complimentaryDelegate->regId ?? ""));

        $companyName =
            $attendee->company ??
            ($stallManning->organisation_name ??
                ($complimentaryDelegate->organisation_name ??
                    "Unknown Company"));

        $designation =
            $attendee->designation ??
            ($stallManning->job_title ??
                ($complimentaryDelegate->job_title ?? ""));

        $email =
            $attendee->email ??
            ($stallManning->email ?? ($complimentaryDelegate->email ?? ""));

        $mobile =
            $attendee->mobile ??
            ($stallManning->mobile ?? ($complimentaryDelegate->mobile ?? ""));

        $country =
            $attendee->countryRelation->name ??
            ($stallManning->countryRelation->name ??
                ($complimentaryDelegate->countryRelation->name ?? null));

        $state =
            $attendee->stateRelation->name ??
            ($stallManning->stateRelation->name ??
                ($complimentaryDelegate->stateRelation->name ?? null));

        $city =
            $attendee->city ??
            ($stallManning->city ?? ($complimentaryDelegate->city ?? null));

        // --- Registration Type ---

        $registrationType = "Visitor";

        if ($attendee) {
            $registrationType =
                $attendee->approvedCate ??
                ($attendee->badge_category ?? "Visitor");
        }

        if ($stallManning) {
            $registrationType = $stallManning->confirmedCategory ?? "Exhibitor";
        }

        if ($complimentaryDelegate) {
            $registrationType =
                $complimentaryDelegate->approvedCate ?? "Exhibitor";
        }

        $idType =
            $attendee->id_card_type ??
            ($stallManning->id_type ?? ($complimentaryDelegate->id_type ?? ""));

        $idPath =
            $attendee->id_card_number ??
            ($stallManning->id_no ?? ($complimentaryDelegate->id_no ?? ""));

        $imagePath =
            $attendee->profile_picture ??
            ($stallManning->profile_pic ??
                ($complimentaryDelegate->profile_pic ?? ""));

        $lunchStatus =
            $attendee->lunchStatus ??
            ($complimentaryDelegate->lunchStatus ?? "0");

        $inaugural =
            $attendee->inauguralConfirmation ??
            ($complimentaryDelegate->inauguralConfirmation ?? "0");

        if (
            $registrationType == "Delegate" ||
            $registrationType == "Organizer" ||
            $registrationType == "Dignitaries"
        ) {
            $lunchStatus = "1";
        }

        if ($imagePath !== null && $imagePath !== "") {
            if ($complimentaryDelegate) {
                $imagePath =
                    "https://portal.semiconindia.org/storage/" . $imagePath;
            } else {
                $imagePath = "https://portal.semiconindia.org/" . $imagePath;
            }
        }

        if ($stallManning) {
            $imagePath = "";
        }

        if (str_starts_with($regId, "DAP")) {
            $inaugural = "1";
        }

        if (str_starts_with($regId, "DE0")) {
            $registrationType = "Delegate";

            $inaugural = "1";

            $lunchStatus = "1";
        }

        if (str_starts_with($regId, "DI0")) {
            $registrationType = "Dignitaries";

            $inaugural = "1";

            $lunchStatus = "1";
        }

        //if the registration_type is empty then pass as visitor

        if ($registrationType == "" || $registrationType == null) {
            $registrationType = "Visitor";
        }

        $mobile = str_replace(" ", "", $mobile);

        $data = [
            "RegID" => $regId,

            "Name" => $name,

            "CompanyName" => $companyName,

            "Designation" => $designation,

            "Email" => $email,

            "Mobile" => $mobile,

            "RegistrationType" => $registrationType,

            "unique_id" => $unique_id,

            "Country" => $country,

            "State" => $state,

            "City" => $city,

            "Inaugural" => $inaugural,

            "Idtype" => $idType,

            "Idpath" => $idPath,

            "Imagepath" => $imagePath,

            "LunchStatus" => $lunchStatus,
        ];

        // dd($data);

        // --- Direct API Call ---

        $endpoint =
            "https://www.semiconindiammactiv.com/SemiconIndia2025GetAPIVD/api/participant/visitor";

        try {
            $response = Http::withHeaders([
                "Content-Type" => "application/json",

                "X-API-KEY" => "F3A9D2B7-8C41-4E5F-9A6B-1D2E3F4A5B6C",
            ])->post($endpoint, $data);

            if ($response->successful()) {
                return response()->json([
                    "success" => true,

                    "message" => "Data successfully sent to API",

                    "api_response" => $response->json(),

                    "payload" => $data,
                ]);
            } else {
                return response()->json(
                    [
                        "success" => false,

                        "message" => "API call failed",

                        "status" => $response->status(),

                        "error" => $response->body(),

                        "payload" => $data,
                    ],
                    $response->status()
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,

                    "message" => "Exception while sending API request",

                    "error" => $e->getMessage(),

                    "payload" => $data,
                ],
                500
            );
        }
    }

    // select from attendee model where regId is not null or empty and send it to the api

    public function sendAllAttendeesToApi()
    {
        $attendees = Attendee::where("source", "SID-3")
        ->get();

        // $attendees = Attendee::where('country', '!=', 351)

        //     ->where(function($query) {

        //     $query->whereNull('api_sent')

        //           ->orWhere('api_sent', '');

        //     })

        //     ->orderBy('id', 'ASC')

        //     // ->limit(699)

        //     ->get();

        //    dd($attendees);

        //WHERE EMAIL IN

        // $emails = [

        //     'hanabusa.hakumarcus@mail.sharp',

        //     'ohkawara.junpei@mail.sharp',

        //     'clear2fly@hanmail.net',

        //     'sanjay.munshi@resolutegroup.in',

        //     'nagano.gyoh@mail.sharp',

        //     'nohara.masahiro@mail.sharp',

        //     'kan.ozawa@mitsui.com',

        //     'keeann.tan@semilab.com',

        //     'tanaka.masayoshi@sharp.co.jp',

        //     'ueki.yuhya@mail.sharp',

        //     'wakita.naohiro@mail.sharp',

        //     'vikas@gargelectronics.com',

        //     'mugunthans89@yahoo.com',

        //     'Vipul@abacusperipherals.com',

        //     'dinakar@elkayindia.com',

        //     'connect@avahitagroup.com',

        //     'Stanleydubier@gmail.com'

        // ];

        // $attendees = Attendee::whereIn('email', $emails)

        //     // ->whereNotNull('regId')

        //     // ->where('regId', '!=', '')

        //     // ->where(function($query) {

        //     // $query->whereNull('api_sent')

        //         //   ->orWhere('api_sent', '');

        //     // }

        //     // )

        //     ->orderBy('id', 'ASC')

        //     ->get();

        $results = [];

        foreach ($attendees as $attendee) {
            $result = $this->sendDataToApiNew($attendee->unique_id);

            $results[] = $result->getData();

            //dd($result);

            // Update api_sent to 1

            $attendee->api_sent = 1;

            $attendee->save();
        }

        return response()->json(
            [
                "success" => true,

                "message" => "All attendees queued for delivery",

                "data" => $results,
            ],
            202
        );
    }

    // select all the complimentary delegates where api_sent is null and send it to the api

    public function sendAllComplimentaryDelegatesToApi()
    {
        $complimentaryDelegates = ComplimentaryDelegate::whereNotNull("regId")
            ->where("regId", "!=", "")
            ->whereNull("api_sent")
            ->get();

        //dd($complimentaryDelegates);

        $results = [];

        foreach ($complimentaryDelegates as $complimentaryDelegate) {
            $result = $this->sendDataToApi($complimentaryDelegate->unique_id);

            $results[] = $result->getData();

            // Update api_sent to 1

            $complimentaryDelegate->api_sent = 1;

            $complimentaryDelegate->save();
        }

        return response()->json(
            [
                "success" => true,

                "message" => "All complimentary delegates queued for delivery",

                "data" => $results,
            ],
            202
        );
    }

    //send from stall manning model where api_sent is null and send it to the api

    public function sendAllStallManningsToApi()
    {
        $stallMannings = StallManning::whereNotNull("regId")
            ->where("regId", "!=", "")
            ->whereNull("api_sent")
            ->get();

        $results = [];

        foreach ($stallMannings as $stallManning) {
            $result = $this->sendDataToApiNew($stallManning->unique_id);

            $results[] = $result->getData();

            // Update api_sent to 1

            $stallManning->api_sent = 1;

            $stallManning->save();
        }

        return response()->json(
            [
                "success" => true,

                "message" => "All stall mannings queued for delivery",

                "data" => $results,
            ],
            202
        );
    }

    public function sendAllStallManningsToApi2()
    {
        // first name not null and not empty and api_sent is null or empty

        $stallMannings = StallManning::whereNotNull("first_name")
            ->where("first_name", "!=", "")
            ->whereNull("api_sent")
            ->orWhere("api_sent", "")
            ->get();

        $results = [];

        foreach ($stallMannings as $stallManning) {
            do {
                $randomNumber = rand(3200, 9999);

                $regId = "EXAT" . $randomNumber;
            } while (
                DB::table("stall_manning")
                    ->where("regId", $regId)
                    ->exists()
            );

            $stallManning->regId = $regId;

            $stallManning->api_sent = 1;

            $stallManning->save();

            $result = $this->sendDataToApi($stallManning->unique_id);

            $results[] = $result->getData();

            // exit;
        }

        return response()->json(
            [
                "success" => true,

                "message" => "All stall mannings queued for delivery",

                "data" => $results,
            ],
            202
        );
    }
}
