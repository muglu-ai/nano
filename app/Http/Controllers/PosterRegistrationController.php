<?php

namespace App\Http\Controllers;

use App\Models\Poster;
use App\Models\PosterDraft;
use App\Models\Payment;
use App\Models\Invoice;
use App\Services\CcAvenueService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\OrderApplicationContextBuilder;

class PosterRegistrationController extends Controller
{
    // If you want strict sector values later, move them here and validate with Rule::in()
    // public array $sectorOptions = [...];
    public array $sectorOptions = [
        "Information Technology",
        "Electronics & Semiconductor",
        "Drones & Robotics",
        "EV, Energy, Climate, Water, Soil, GSDI",
        "Telecommunications",
        "Cybersecurity",
        "Artificial Intelligence",
        "Cloud Services",
        "E-Commerce",
        "Automation",
        "AVGC",
        "Aerospace, Defence & Space Tech",
        "Mobility Tech",
        "Infrastructure",
        "Biotech",
        "Agritech",
        "Medtech",
        "Fintech",
        "Healthtech",
        "Edutech",
        "Startup",
        "Unicorn/ VCs",
        "Academia & University",
        "Tech Parks / Co-Working Spaces of India",
        "Banking / Insurance",
        "R&D and Central Govt.",
        "Others"
    ];
    public $countryList = array(
        "AF" => "Afghanistan",
        "AL" => "Albania",
        "DZ" => "Algeria",
        "AS" => "AmericanSamoa",
        "AD" => "Andorra",
        "AO" => "Angola",
        "AI" => "Anguilla",
        "AQ" => "Antarctica",
        "AR" => "Argentina",
        "AM" => "Armenia",
        "AW" => "Aruba",
        "AU" => "Australia",
        "AT" => "Austria",
        "AZ" => "Azerbaijan",
        "BS" => "Bahamas",
        "BH" => "Bahrain",
        "BD" => "Bangladesh",
        "BB" => "Barbados",
        "BY" => "Belarus",
        "BE" => "Belgium",
        "BZ" => "Belize",
        "BJ" => "Benin",
        "BM" => "Bermuda",
        "BT" => "Bhutan",
        "BO" => "Bolivia",
        "BA" => "BosniaandHerzegowina",
        "BW" => "Botswana",
        "BV" => "BouvetIsland",
        "BR" => "Brazil",
        "IO" => "BritishIndianOceanTerritory",
        "BN" => "BruneiDarussalam",
        "BG" => "Bulgaria",
        "BF" => "BurkinaFaso",
        "BI" => "Burundi",
        "KH" => "Cambodia",
        "CM" => "Cameroon",
        "CA" => "Canada",
        "CV" => "CapeVerde",
        "KY" => "CaymanIslands",
        "CF" => "CentralAfricanRepublic",
        "TD" => "Chad",
        "CL" => "Chile",
        "CN" => "China",
        "CX" => "ChristmasIsland",
        "CC" => "Cocos(Keeling)Islands",
        "CO" => "Colombia",
        "KM" => "Comoros",
        "CG" => "Congo",
        "CD" => "Congo,theDemocraticRepublicofthe",
        "CK" => "CookIslands",
        "CR" => "CostaRica",
        "CI" => "Coted'Ivoire",
        "HR" => "Croatia(Hrvatska)",
        "CU" => "Cuba",
        "CY" => "Cyprus",
        "CZ" => "CzechRepublic",
        "DK" => "Denmark",
        "DJ" => "Djibouti",
        "DM" => "Dominica",
        "DO" => "DominicanRepublic",
        "EC" => "Ecuador",
        "EG" => "Egypt",
        "SV" => "ElSalvador",
        "GQ" => "EquatorialGuinea",
        "ER" => "Eritrea",
        "EE" => "Estonia",
        "ET" => "Ethiopia",
        "FK" => "FalklandIslands(Malvinas)",
        "FO" => "FaroeIslands",
        "FJ" => "Fiji",
        "FI" => "Finland",
        "FR" => "France",
        "GF" => "FrenchGuiana",
        "PF" => "FrenchPolynesia",
        "TF" => "FrenchSouthernTerritories",
        "GA" => "Gabon",
        "GM" => "Gambia",
        "GE" => "Georgia",
        "DE" => "Germany",
        "GH" => "Ghana",
        "GI" => "Gibraltar",
        "GR" => "Greece",
        "GL" => "Greenland",
        "GD" => "Grenada",
        "GP" => "Guadeloupe",
        "GU" => "Guam",
        "GT" => "Guatemala",
        "GN" => "Guinea",
        "GW" => "Guinea-Bissau",
        "GY" => "Guyana",
        "HT" => "Haiti",
        "HM" => "HeardandMcDonaldIslands",
        "VA" => "HolySee(VaticanCityState)",
        "HN" => "Honduras",
        "HK" => "HongKong",
        "HU" => "Hungary",
        "IS" => "Iceland",
        "IN" => "India",
        "ID" => "Indonesia",
        "IR" => "Iran(IslamicRepublicof)",
        "IQ" => "Iraq",
        "IE" => "Ireland",
        "IL" => "Israel",
        "IT" => "Italy",
        "JM" => "Jamaica",
        "JP" => "Japan",
        "JO" => "Jordan",
        "KZ" => "Kazakhstan",
        "KE" => "Kenya",
        "KI" => "Kiribati",
        "KP" => "Korea,DemocraticPeople'sRepublicof",
        "KR" => "Korea,Republicof",
        "KW" => "Kuwait",
        "KG" => "Kyrgyzstan",
        "LA" => "LaoPeople'sDemocraticRepublic",
        "LV" => "Latvia",
        "LB" => "Lebanon",
        "LS" => "Lesotho",
        "LR" => "Liberia",
        "LY" => "LibyanArabJamahiriya",
        "LI" => "Liechtenstein",
        "LT" => "Lithuania",
        "LU" => "Luxembourg",
        "MO" => "Macau",
        "MK" => "Macedonia,TheFormerYugoslavRepublicof",
        "MG" => "Madagascar",
        "MW" => "Malawi",
        "MY" => "Malaysia",
        "MV" => "Maldives",
        "ML" => "Mali",
        "MT" => "Malta",
        "MH" => "MarshallIslands",
        "MQ" => "Martinique",
        "MR" => "Mauritania",
        "MU" => "Mauritius",
        "YT" => "Mayotte",
        "MX" => "Mexico",
        "FM" => "Micronesia,FederatedStatesof",
        "MD" => "Moldova,Republicof",
        "MC" => "Monaco",
        "MN" => "Mongolia",
        "MS" => "Montserrat",
        "MA" => "Morocco",
        "MZ" => "Mozambique",
        "MM" => "Myanmar",
        "NA" => "Namibia",
        "NR" => "Nauru",
        "NP" => "Nepal",
        "NL" => "Netherlands",
        "AN" => "NetherlandsAntilles",
        "NC" => "NewCaledonia",
        "NZ" => "NewZealand",
        "NI" => "Nicaragua",
        "NE" => "Niger",
        "NG" => "Nigeria",
        "NU" => "Niue",
        "NF" => "NorfolkIsland",
        "MP" => "NorthernMarianaIslands",
        "NO" => "Norway",
        "OM" => "Oman",
        "PK" => "Pakistan",
        "PW" => "Palau",
        "PA" => "Panama",
        "PG" => "PapuaNewGuinea",
        "PY" => "Paraguay",
        "PE" => "Peru",
        "PH" => "Philippines",
        "PN" => "Pitcairn",
        "PL" => "Poland",
        "PT" => "Portugal",
        "PR" => "PuertoRico",
        "QA" => "Qatar",
        "RE" => "Reunion",
        "RO" => "Romania",
        "RU" => "RussianFederation",
        "RW" => "Rwanda",
        "KN" => "SaintKittsandNevis",
        "LC" => "SaintLUCIA",
        "VC" => "SaintVincentandtheGrenadines",
        "WS" => "Samoa",
        "SM" => "SanMarino",
        "ST" => "SaoTomeandPrincipe",
        "SA" => "SaudiArabia",
        "SN" => "Senegal",
        "SC" => "Seychelles",
        "SL" => "SierraLeone",
        "SG" => "Singapore",
        "SK" => "Slovakia(SlovakRepublic)",
        "SI" => "Slovenia",
        "SB" => "SolomonIslands",
        "SO" => "Somalia",
        "ZA" => "SouthAfrica",
        "GS" => "SouthGeorgiaandtheSouthSandwichIslands",
        "ES" => "Spain",
        "LK" => "SriLanka",
        "SH" => "St.Helena",
        "PM" => "St.PierreandMiquelon",
        "SD" => "Sudan",
        "SR" => "Suriname",
        "SJ" => "SvalbardandJanMayenIslands",
        "SZ" => "Swaziland",
        "SE" => "Sweden",
        "CH" => "Switzerland",
        "SY" => "SyrianArabRepublic",
        "TW" => "Taiwan,ProvinceofChina",
        "TJ" => "Tajikistan",
        "TZ" => "Tanzania,UnitedRepublicof",
        "TH" => "Thailand",
        "TG" => "Togo",
        "TK" => "Tokelau",
        "TO" => "Tonga",
        "TT" => "TrinidadandTobago",
        "TN" => "Tunisia",
        "TR" => "Turkey",
        "TM" => "Turkmenistan",
        "TC" => "TurksandCaicosIslands",
        "TV" => "Tuvalu",
        "UG" => "Uganda",
        "UA" => "Ukraine",
        "AE" => "UnitedArabEmirates",
        "GB" => "UnitedKingdom",
        "US" => "UnitedStates",
        "UM" => "UnitedStatesMinorOutlyingIslands",
        "UY" => "Uruguay",
        "UZ" => "Uzbekistan",
        "VU" => "Vanuatu",
        "VE" => "Venezuela",
        "VN" => "VietNam",
        "VG" => "VirginIslands(British)",
        "VI" => "VirginIslands(U.S.)",
        "WF" => "WallisandFutunaIslands",
        "EH" => "WesternSahara",
        "YE" => "Yemen",
        "ZM" => "Zambia",
        "ZW" => "Zimbabwe"
    );

    // STEP 1 (blank)
    public function create()
    {
        return view('poster.register', [
            'draft' => null,
            'sectorOptions' => $this->sectorOptions,
            'countryList' => $this->countryList,
        ]);
    }

    // STEP 1 (prefilled)
    public function edit(Request $request, string $token)
    {
        $draft = PosterDraft::where('token', $token)->firstOrFail();

        $sessionToken = $request->session()->get('poster_draft_token');
        
        // More lenient session check: allow if session matches OR draft was created recently (within 24 hours)
        $isRecentDraft = $draft->created_at && $draft->created_at->isAfter(now()->subHours(24));
        
        if ($sessionToken !== $token && !$isRecentDraft) {
            abort(403, 'This draft does not belong to your session. Please start a new registration.');
        }
        
        // If session doesn't match but draft is recent, update session to allow continuation
        if ($sessionToken !== $token && $isRecentDraft) {
            $request->session()->put('poster_draft_token', $token);
        }

        return view('poster.register', [
            'draft' => $draft,
            'sectorOptions' => $this->sectorOptions,
            'countryList' => $this->countryList,
        ]);
    }

    // STEP 1 POST: create OR update draft
    public function storeDraft(Request $request)
    {
        $validated = $request->validate([
            'token' => ['nullable', 'uuid'],

            // Core
            'sector'       => ['required', Rule::in($this->sectorOptions)],
            'nationality'  => ['required', Rule::in(['India', 'International'])],
            'title'        => ['required', 'string', 'max:200'],

            // Lead Author
            'lead_name'    => ['required', 'string', 'max:200'],
            // 'lead_email'   => ['required', 'email', 'max:200'],
            'lead_email' => [
                'required',
                'email',
                'max:200',
                function ($attribute, $value, $fail) {
                    $email = strtolower(trim($value));

                    $exists = \App\Models\Poster::query()
                        ->where('lead_email', $email)
                        ->orWhere('pp_email', $email)
                        ->exists();

                    if ($exists) {
                        $fail('Lead auther email already registered.');
                    }
                },
            ],
            'lead_org'     => ['required', 'string', 'max:250'],
            // 'lead_ccode'   => ['nullable', 'string', 'max:5'],
            // 'lead_phone'   => ['required', 'string', 'max:15'],
            'lead_ccode' => ['nullable', 'regex:/^\+\d{1,4}$/'],
            'lead_phone' => ['required', 'regex:/^\d{6,15}$/'],

            'lead_addr'    => ['required', 'string'],
            'lead_city'    => ['required', 'string', 'max:120'],
            'lead_state'   => ['required', 'string', 'max:120'],
            'lead_country' => ['required', Rule::in(array_values($this->countryList))],
            'lead_zip'     => ['required', 'string', 'max:30'],

            // Poster Presenter
            'pp_name'      => ['required', 'string', 'max:200'],
            'pp_email'     => ['required', 'email', 'max:200'],
            // 'pp_email' => [
            //     'required',
            //     'email',
            //     'max:200',
            //     function ($attribute, $value, $fail) {
            //         $email = strtolower(trim($value));

            //         $exists = \App\Models\Poster::query()
            //             ->where('lead_email', $email)
            //             ->orWhere('pp_email', $email)
            //             ->exists();

            //         if ($exists) {
            //             $fail('Poster Presenter email already registered.');
            //         }
            //     },
            // ],
            'pp_org'       => ['required', 'string', 'max:250'],
            'pp_website'   => ['nullable', 'url', 'max:255'],
            // 'pp_ccode'     => ['nullable', 'string', 'max:5'],
            // 'pp_phone'     => ['required', 'string', 'max:15'],
            'pp_ccode' => ['nullable', 'regex:/^\+\d{1,4}$/'],
            'pp_phone' => ['required', 'regex:/^\d{6,15}$/'],

            'pp_addr'      => ['required', 'string'],
            'pp_city'      => ['required', 'string', 'max:120'],
            'pp_state'     => ['required', 'string', 'max:120'],
            'pp_country'   => ['required', Rule::in(array_values($this->countryList))],
            'pp_zip'       => ['required', 'string', 'max:30'],

            // Co-authors
            'co_auth_name_1' => ['nullable', 'string', 'max:200'],
            'co_auth_name_2' => ['nullable', 'string', 'max:200'],
            'co_auth_name_3' => ['nullable', 'string', 'max:200'],
            'co_auth_name_4' => ['nullable', 'string', 'max:200'],

            // Accompanying co-authors
            'acc_co_auth_name_1' => ['nullable', 'string', 'max:200'],
            'acc_co_auth_name_2' => ['nullable', 'string', 'max:200'],
            'acc_co_auth_name_3' => ['nullable', 'string', 'max:200'],
            'acc_co_auth_name_4' => ['nullable', 'string', 'max:200'],

            // Theme + Abstract
            'theme'         => ['nullable', 'string', 'max:150'],
            // 'abstract_text' => ['required', 'string'],
            'abstract_text' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $words = preg_split('/\s+/', trim((string) $value));
                    $words = array_filter($words, fn($w) => $w !== '');
                    if (count($words) < 250) {
                        $fail('Abstract must be at least 250 words.');
                    }
                },
            ],


            // Files
            // 'sess_abstract' => ['nullable', 'file', 'max:2048', 'mimes:doc,docx,pdf'], // 2MB
            // 'lead_auth_cv'  => ['nullable', 'file', 'max:2048', 'mimes:doc,docx,pdf'],  // 2MB
            'sess_abstract' => [
                Rule::requiredIf(function () use ($request) {

                    $token = $request->input('token');

                    if (!$token) return true; // new draft must upload
                    $draft = PosterDraft::where('token', $token)->first();
                    return empty($draft?->sess_abstract_path); // require if not already uploaded
                }),
                'file',
                'max:2048',
                'mimes:doc,docx,pdf'
            ],
            'lead_auth_cv' => [
                Rule::requiredIf(function () use ($request) {

                    $token = $request->input('token');

                    if (!$token) return true;
                    $draft = PosterDraft::where('token', $token)->first();
                    return empty($draft?->lead_auth_cv_path);
                }),
                'file',
                'max:2048',
                'mimes:doc,docx,pdf'
            ],


            // Payment (all optional for now)
            // 'paymode'          => ['nullable', 'string', 'max:50'],
            'paymode' => ['required', Rule::in([
                'CCAvenue (Indian Payments)',
                'PayPal (International payments)',
            ])],
            'currency'         => ['nullable', Rule::in(['INR', 'USD'])], // if you change to enum, keep validation in ['INR','USD']
            'base_amount'      => ['nullable',  'numeric'],
            'total_amount'     => ['nullable',  'numeric'],
            'discount_code'    => ['nullable',   'string', 'max:100'],
            'discount_amount'  => ['nullable',  'numeric'],
            'gst_amount'       => ['nullable',  'numeric'],
            'processing_fee'   => ['nullable',  'numeric'],

            // Extra fields (we accept but will recompute)
            'acc_count'         => ['nullable', 'integer', 'min:0', 'max:4'],
            'acc_unit_cost'     => ['nullable', 'numeric', 'min:0'],
            'additional_charge' => ['nullable', 'numeric', 'min:0'],
        ]);

        $token = $validated['token'] ?? null;
        unset($validated['token']);

        // Handle uploads into $validated so create/update can use it
        $validated = $this->handleUploads($request, $validated);

        /*
    |--------------------------------------------------------------------------
    | SERVER-SIDE AUTHORITATIVE PRICING (ADD HERE)
    |--------------------------------------------------------------------------
    | We recompute pricing based on nationality + accompanying count so users
    | cannot manipulate hidden fields.
    */
        $nat = $validated['nationality'];

        // Base values by nationality
        $currency = $nat === 'India' ? 'INR' : 'USD';
        $baseAmount = $nat === 'India' ? 3000 : 50;

        // Force paymode to match nationality (don’t trust client)
        $validated['paymode'] = $nat === 'India'
            ? 'CCAvenue (Indian Payments)'
            : 'PayPal (International payments)';

        $validated['currency'] = $currency;
        $validated['base_amount'] = $this->money((float) $baseAmount);

        $discount = (float) ($validated['discount_amount'] ?? 0);

        // Force safe defaults so NOT NULL decimal columns never get NULL
        $validated['discount_amount'] = $this->money($discount);
        $validated['discount_code'] = $validated['discount_code'] ?? null; // ok to be null if column is nullable

        // Count accompanying co-authors from the actual name fields
        $accCount = $this->countAccompanying($validated);

        // Your current rule: unit cost = base amount
        $accUnitCost = (float) $baseAmount;
        $additional = $accUnitCost * $accCount;

        // Subtotal = base + additional - discount
        $subTotal = ($baseAmount + $additional) - $discount;
        if ($subTotal < 0) $subTotal = 0;

        // GST = 18% of subtotal
        $gst = $subTotal * 0.18;

        // Processing fee = on (subtotal + gst)
        $procRate = $nat === 'India' ? 0.03 : 0.09;
        $processing = ($subTotal + $gst) * $procRate;

        // Total
        $total = $subTotal + $gst + $processing;

        // Store breakdown fields
        $validated['acc_count'] = $accCount;
        $validated['acc_unit_cost'] = $this->money($accUnitCost);
        $validated['additional_charge'] = $this->money($additional);

        $validated['gst_amount'] = $this->money($gst);
        $validated['processing_fee'] = $this->money($processing);
        $validated['total_amount'] = $this->money($total);

        // Create or update draft
        if ($token) {
            $draft = PosterDraft::where('token', $token)->firstOrFail();

            $sessionToken = $request->session()->get('poster_draft_token');
            
            // More lenient session check: allow if session matches OR draft was created recently (within 24 hours)
            $isRecentDraft = $draft->created_at && $draft->created_at->isAfter(now()->subHours(24));
            
            if ($sessionToken !== $token && !$isRecentDraft) {
                abort(403, 'This registration page does not belong to your session. Please start a new registration.');
            }
            
            // If session doesn't match but draft is recent, update session to allow continuation
            if ($sessionToken !== $token && $isRecentDraft) {
                $request->session()->put('poster_draft_token', $token);
            }

            $draft->update($validated);
        } else {
            $draft = PosterDraft::create([
                'token' => (string) Str::uuid(),
                'status' => 'draft',
                ...$validated,
            ]);

            $request->session()->put('poster_draft_token', $draft->token);
        }

        return redirect()->route('poster.preview', ['token' => $draft->token]);
    }

    // STEP 2: preview
    public function preview(Request $request, string $token)
    {
        $draft = PosterDraft::where('token', $token)->firstOrFail();

        $sessionToken = $request->session()->get('poster_draft_token');
        
        // More lenient session check: allow if session matches OR draft was created recently (within 24 hours)
        $isRecentDraft = $draft->created_at && $draft->created_at->isAfter(now()->subHours(24));
        
        if ($sessionToken !== $token && !$isRecentDraft) {
            abort(403, 'This draft does not belong to your session. Please start a new registration.');
        }
        
        // If session doesn't match but draft is recent, update session to allow continuation
        if ($sessionToken !== $token && $isRecentDraft) {
            $request->session()->put('poster_draft_token', $token);
        }

        return view('poster.preview', compact('draft'));
    }

    // STEP 2: final submit → posters table, keep drafts as backup
    public function submit(Request $request, string $token)
    {
        $draft = PosterDraft::where('token', $token)->firstOrFail();

        $sessionToken = $request->session()->get('poster_draft_token');
        
        // More lenient session check: allow if session matches OR draft was created recently (within 24 hours)
        // This handles cases where session might have expired but draft is still valid
        $isRecentDraft = $draft->created_at && $draft->created_at->isAfter(now()->subHours(24));
        
        if ($sessionToken !== $token && !$isRecentDraft) {
            // If session doesn't match and draft is old, require session match for security
            abort(403, 'This draft does not belong to your session. Please start a new registration.');
        }
        
        // If session doesn't match but draft is recent, update session to allow continuation
        if ($sessionToken !== $token && $isRecentDraft) {
            $request->session()->put('poster_draft_token', $token);
        }

        // Idempotency: prevent multiple poster rows for same draft token
        $existing = Poster::where('draft_token', $draft->token)->first();
        if ($existing) {
            // already submitted earlier
            $request->session()->forget('poster_draft_token');
            
            // If payment is pending, redirect to payment page
            if ($existing->payment_status === 'pending') {
                return redirect()->route('poster.payment', ['tin_no' => $existing->tin_no]);
            }
            
            // Otherwise redirect to success
            return redirect()->route('poster.success', ['tin_no' => $existing->tin_no]);
        }

        // Generate TIN number
        $tinNo = $this->generateTinNumber();

        $poster = Poster::create([
            'tin_no' => $tinNo,
            'pin_no' => null, // Will be set after payment
            'draft_token' => $draft->token,

            // Copy all the same fields
            'sector'       => $draft->sector,
            'nationality'  => $draft->nationality,
            'title'        => $draft->title,

            'lead_name'    => $draft->lead_name,
            'lead_email'   => $draft->lead_email,
            'lead_org'     => $draft->lead_org,
            'lead_ccode'   => $draft->lead_ccode,
            'lead_phone'   => $draft->lead_phone,
            'lead_addr'    => $draft->lead_addr,
            'lead_city'    => $draft->lead_city,
            'lead_state'   => $draft->lead_state,
            'lead_country' => $draft->lead_country,
            'lead_zip'     => $draft->lead_zip,

            'pp_name'      => $draft->pp_name,
            'pp_email'     => $draft->pp_email,
            'pp_org'       => $draft->pp_org,
            'pp_website'   => $draft->pp_website,
            'pp_ccode'     => $draft->pp_ccode,
            'pp_phone'     => $draft->pp_phone,
            'pp_addr'      => $draft->pp_addr,
            'pp_city'      => $draft->pp_city,
            'pp_state'     => $draft->pp_state,
            'pp_country'   => $draft->pp_country,
            'pp_zip'       => $draft->pp_zip,

            'co_auth_name_1' => $draft->co_auth_name_1,
            'co_auth_name_2' => $draft->co_auth_name_2,
            'co_auth_name_3' => $draft->co_auth_name_3,
            'co_auth_name_4' => $draft->co_auth_name_4,

            'acc_co_auth_name_1' => $draft->acc_co_auth_name_1,
            'acc_co_auth_name_2' => $draft->acc_co_auth_name_2,
            'acc_co_auth_name_3' => $draft->acc_co_auth_name_3,
            'acc_co_auth_name_4' => $draft->acc_co_auth_name_4,

            'theme'         => $draft->theme,
            'abstract_text' => $draft->abstract_text,

            'sess_abstract_path'          => $draft->sess_abstract_path,
            'sess_abstract_original_name' => $draft->sess_abstract_original_name,
            'sess_abstract_size'          => $draft->sess_abstract_size,
            'sess_abstract_mime'          => $draft->sess_abstract_mime,

            'lead_auth_cv_path'           => $draft->lead_auth_cv_path,
            'lead_auth_cv_original_name'  => $draft->lead_auth_cv_original_name,
            'lead_auth_cv_size'           => $draft->lead_auth_cv_size,
            'lead_auth_cv_mime'           => $draft->lead_auth_cv_mime,

            'paymode'         => $draft->paymode,
            'currency'        => $draft->currency,
            'base_amount'     => $draft->base_amount,
            'discount_code'   => $draft->discount_code,
            'discount_amount' => $draft->discount_amount,
            'gst_amount'      => $draft->gst_amount,
            'processing_fee'  => $draft->processing_fee,
            'total_amount'    => $draft->total_amount,

            'acc_count'         => $draft->acc_count,
            'acc_unit_cost'     => $draft->acc_unit_cost,
            'additional_charge' => $draft->additional_charge,

            'status' => 'submitted',
            'payment_status' => 'pending',
        ]);

        // Mark draft as submitted (backup remains)
        $draft->update(['status' => 'submitted']);

        // Store poster ID in session for payment
        $request->session()->put('poster_payment_id', $poster->id);
        $request->session()->put('poster_tin_no', $poster->tin_no);

        // Redirect to payment instead of success
        return redirect()->route('poster.payment', ['tin_no' => $poster->tin_no]);
    }

    // STEP 3: success
    public function success(string $tin_no)
    {
        $poster = Poster::where('tin_no', $tin_no)->firstOrFail();
        return view('poster.success', compact('poster'));
    }

    // Generate unique TIN number
    private function generateTinNumber(): string
    {
        do {
            $randomNumber = str_pad((string) mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $tinNo = 'TIN-BTS2026-PSTR-' . $randomNumber;
        } while (Poster::where('tin_no', $tinNo)->exists());

        return $tinNo;
    }

    private function handleUploads(Request $request, array $data): array
    {
        // sess_abstract file
        if ($request->hasFile('sess_abstract')) {
            $file = $request->file('sess_abstract');
            $path = $file->store('sess_abstract', 'public');

            $data['sess_abstract_path'] = $path;
            $data['sess_abstract_original_name'] = $file->getClientOriginalName();
            $data['sess_abstract_size'] = $file->getSize();
            $data['sess_abstract_mime'] = $file->getMimeType();
        }

        // lead_auth_cv file
        if ($request->hasFile('lead_auth_cv')) {
            $file = $request->file('lead_auth_cv');
            $path = $file->store('lead_auth_cv', 'public');

            $data['lead_auth_cv_path'] = $path;
            $data['lead_auth_cv_original_name'] = $file->getClientOriginalName();
            $data['lead_auth_cv_size'] = $file->getSize();
            $data['lead_auth_cv_mime'] = $file->getMimeType();
        }

        return $data;
    }

    // Download file securely
    public function downloadFile(Request $request, string $type, string $token)
    {
        $draft = PosterDraft::where('token', $token)->first();
        
        if (!$draft) {
            abort(404, 'Draft not found');
        }

        $path = null;
        $filename = null;

        if ($type === 'sess_abstract') {
            $path = $draft->sess_abstract_path;
            $filename = $draft->sess_abstract_original_name ?? 'abstract.pdf';
        } elseif ($type === 'lead_auth_cv') {
            $path = $draft->lead_auth_cv_path;
            $filename = $draft->lead_auth_cv_original_name ?? 'cv.pdf';
        } else {
            abort(404, 'Invalid file type');
        }

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($path, $filename);
    }

    // AJAX: check if email exists in posters or drafts
    public function checkEmail(Request $request)
    {
        $email = strtolower(trim((string) $request->query('email')));

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid email.',
                'exists' => false,
            ], 422);
        }

        // Cache key (short TTL to keep it fresh)
        $cacheKey = 'poster_email_exists:' . sha1($email);

        $exists = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($email) {
            // Check in posters (final) first (most important)
            $inPosters = \App\Models\Poster::query()
                ->where('lead_email', $email)
                ->orWhere('pp_email', $email)
                ->exists();

            if ($inPosters) return true;

            // Optional: also block if present in drafts
            // $inDrafts = \App\Models\PosterDraft::query()
            //     ->where('lead_email', $email)
            //     ->orWhere('pp_email', $email)
            //     ->exists();

            // return $inDrafts;
            return false;
        });

        return response()->json([
            'ok' => true,
            'exists' => $exists,
            'message' => $exists ? 'Email is already registered.' : 'Email is available.',
        ]);
    }

    // Count non-empty accompanying co-authors
    private function countAccompanying(array $data): int
    {
        $fields = [
            'acc_co_auth_name_1',
            'acc_co_auth_name_2',
            'acc_co_auth_name_3',
            'acc_co_auth_name_4',
        ];

        $count = 0;
        foreach ($fields as $f) {
            if (!empty(trim((string)($data[$f] ?? '')))) {
                $count++;
            }
        }
        return $count;
    }

    private function money(float $v): float
    {
        return round($v, 2);
    }

    // Payment methods
    private $ccAvenueService;
    private $paypalClient;

    public function __construct()
    {
        $this->ccAvenueService = new CcAvenueService();
        
        // Initialize PayPal client
        $paypalMode = strtolower(config('constants.PAYPAL_MODE', 'live'));
        $isSandbox = ($paypalMode === 'sandbox');
        
        if ($isSandbox) {
            $clientId = config('constants.PAYPAL_SANDBOX_CLIENT_ID');
            $clientSecret = config('constants.PAYPAL_SANDBOX_SECRET');
            $environment = 'Sandbox';
            
            if (empty($clientId) || empty($clientSecret)) {
                $clientId = config('constants.PAYPAL_LIVE_CLIENT_ID');
                $clientSecret = config('constants.PAYPAL_LIVE_SECRET');
            }
        } else {
            $clientId = config('constants.PAYPAL_LIVE_CLIENT_ID');
            $clientSecret = config('constants.PAYPAL_LIVE_SECRET');
            $environment = 'Production';
        }
        
        $clientId = trim($clientId ?? '');
        $clientSecret = trim($clientSecret ?? '');
        
        if (empty($clientId) || empty($clientSecret)) {
            $legacyId = config('constants.PAYPAL_CLIENT_ID');
            $legacySecret = config('constants.PAYPAL_SECRET');
            if (!empty($legacyId) && !empty($legacySecret)) {
                $clientId = trim($legacyId);
                $clientSecret = trim($legacySecret);
            }
        }
        
        if (!empty($clientId) && !empty($clientSecret)) {
            $this->paypalClient = PaypalServerSdkClientBuilder::init()
                ->clientCredentialsAuthCredentials(
                    ClientCredentialsAuthCredentialsBuilder::init($clientId, $clientSecret)
                )
                ->environment($environment)
                ->build();
        }
    }

    /**
     * Show payment page and initiate payment
     */
    public function payment(Request $request, string $tin_no)
    {
        Log::info('Poster Payment - Method called', ['tin_no' => $tin_no]);
        
        $poster = Poster::where('tin_no', $tin_no)->firstOrFail();
        
        Log::info('Poster Payment - Poster found', [
            'poster_id' => $poster->id,
            'payment_status' => $poster->payment_status,
            'nationality' => $poster->nationality,
        ]);
        
        // Check if already paid
        if ($poster->payment_status === 'successful') {
            Log::info('Poster Payment - Already paid, redirecting to success');
            return redirect()->route('poster.success', ['tin_no' => $tin_no]);
        }

        // Determine payment gateway based on nationality
        $paymentGateway = $poster->nationality === 'India' ? 'CCAvenue' : 'PayPal';
        
        Log::info('Poster Payment - Initiating gateway', ['gateway' => $paymentGateway]);
        
        // Use existing PaymentGatewayController route (same as startup zone)
        // This ensures we use the exact same proven payment flow
        $invoice = Invoice::where('invoice_no', $poster->tin_no)
            ->where('type', 'poster_registration')
            ->first();
            
        if (!$invoice) {
            // Create invoice if it doesn't exist
            $invoice = Invoice::create([
                'type' => 'poster_registration',
                'invoice_no' => $poster->tin_no,
                'poster_reg_id' => $poster->id,
                'currency' => $poster->currency ?? ($poster->nationality === 'India' ? 'INR' : 'USD'),
                'amount' => (float) $poster->total_amount,
                'price' => $poster->base_amount ?? $poster->total_amount,
                'gst' => $poster->gst_amount ?? 0,
                'processing_charges' => $poster->processing_fee ?? 0,
                'total_final_price' => (float) $poster->total_amount,
                'amount_paid' => 0,
                'pending_amount' => (float) $poster->total_amount,
                'payment_status' => 'unpaid',
            ]);
        }
        
        // Store poster info in session for PaymentGatewayController callback handling
        session([
            'poster_id' => $poster->id,
            'poster_tin_no' => $poster->tin_no,
            'payment_application_type' => 'poster',
        ]);
        
        // Redirect to existing payment gateway (same as startup zone)
        if ($paymentGateway === 'CCAvenue') {
            return redirect()->route('payment.ccavenue', ['id' => $invoice->invoice_no]);
        } else {
            return redirect()->route('paypal.form', ['id' => $invoice->invoice_no]);
        }
    }

    /**
     * Initiate CCAvenue payment
     */
    private function initiateCcAvenuePayment(Poster $poster)
    {
        try {
            $orderId = $poster->tin_no . '_' . time();
            $amount = (float) $poster->total_amount;
            $currency = $poster->currency ?? 'INR';

            // Prepare billing details
            $billingName = $poster->lead_name;
            $billingEmail = $poster->lead_email;
            $billingPhone = trim(($poster->lead_ccode ?? '') . ' ' . ($poster->lead_phone ?? ''));
            $billingAddress = $poster->lead_addr;
            $billingCity = $poster->lead_city;
            $billingState = $poster->lead_state;
            $billingZip = $poster->lead_zip;
            $billingCountry = $poster->lead_country;

            // Create or get invoice record for the poster (required for foreign key constraint)
            $invoice = Invoice::firstOrCreate(
                ['invoice_no' => $poster->tin_no], // Use TIN as invoice number
                [
                    'type' => 'poster_registration',
                    'invoice_no' => $poster->tin_no,
                    'poster_reg_id' => $poster->id, // Map to posters table
                    'currency' => $currency,
                    'amount' => $amount,
                    'price' => $poster->base_amount ?? $amount,
                    'gst' => $poster->gst_amount ?? 0,
                    'processing_charges' => $poster->processing_fee ?? 0,
                    'total_final_price' => $amount,
                    'amount_paid' => 0,
                    'pending_amount' => $amount,
                    'payment_status' => 'unpaid',
                ]
            );

            // Update poster_reg_id if invoice exists but doesn't have it
            if ($invoice->poster_reg_id !== $poster->id) {
                $invoice->update(['poster_reg_id' => $poster->id]);
            }

            // Create payment record with pending status
            $payment = Payment::create([
                'invoice_id' => $invoice->id, // Use invoice ID (foreign key constraint)
                'payment_method' => 'CCAvenue',
                'amount' => $amount,
                'amount_paid' => 0,
                'amount_received' => 0,
                'transaction_id' => $orderId,
                'status' => 'pending',
                'order_id' => $orderId,
                'currency' => $currency,
                'payment_date' => now(),
            ]);

            // Store in session
            session([
                'poster_id' => $poster->id,
                'poster_tin_no' => $poster->tin_no,
                'poster_payment_id' => $payment->id,
                'poster_order_id' => $orderId,
            ]);

            // Prepare order data for CCAvenue (format exactly like PaymentGatewayController)
            // Use same redirect URL format as PaymentGatewayController
            $redirectUrl = config('constants.APP_URL') . '/payment/ccavenue-success';
            
            // Format amount exactly like PaymentGatewayController - use raw decimal value
            // PaymentGatewayController uses $invoice->total_final_price directly (not formatted)
            // But http_build_query will convert it to string anyway, so format it to ensure 2 decimals
            $formattedAmount = number_format((float) $amount, 2, '.', '');
            
            $orderData = [
                'order_id' => $orderId,
                'amount' => $formattedAmount, // String with 2 decimals
                'currency' => $currency,
                'redirect_url' => $redirectUrl, // Use same format as PaymentGatewayController
                'cancel_url' => $redirectUrl, // Use same format as PaymentGatewayController
                'language' => 'EN', // Add language like PaymentGatewayController
                'billing_name' => $billingName ?? '',
                'billing_address' => $billingAddress ?? '',
                'billing_city' => $billingCity ?? '',
                'billing_state' => $billingState ?? '',
                'billing_zip' => $billingZip ?? '',
                'billing_country' => $billingCountry ?? 'India',
                'billing_tel' => preg_replace('/^.*-/', '', $billingPhone ?? ''), // Clean phone like PaymentGatewayController
                'billing_email' => $billingEmail ?? '',
            ];
            
            // Log the order data for debugging (without sensitive info)
            Log::info('Poster CCAvenue Payment - Order data prepared', [
                'order_id' => $orderData['order_id'],
                'amount' => $orderData['amount'],
                'amount_type' => gettype($orderData['amount']),
                'currency' => $orderData['currency'],
                'redirect_url' => $orderData['redirect_url'],
                'has_billing' => !empty($orderData['billing_name']),
                'merchant_id_will_be' => '7700', // From service
            ]);

            // Log before calling service
            Log::info('Poster CCAvenue Payment - Calling initiateTransaction', [
                'poster_id' => $poster->id,
                'order_id' => $orderId,
                'amount' => $amount,
                'has_service' => !is_null($this->ccAvenueService),
            ]);

            $result = $this->ccAvenueService->initiateTransaction($orderData);

            // Log the result
            Log::info('Poster CCAvenue Payment - Service response', [
                'poster_id' => $poster->id,
                'success' => $result['success'] ?? false,
                'has_encrypted_data' => !empty($result['encrypted_data'] ?? null),
                'error' => $result['error'] ?? null,
            ]);

            if (!$result['success']) {
                $errorMessage = $result['error'] ?? 'Unknown error';
                Log::error('Poster CCAvenue Payment Initiation Failed', [
                    'poster_id' => $poster->id,
                    'tin_no' => $poster->tin_no,
                    'error' => $errorMessage,
                    'result' => $result,
                    'order_data' => $orderData,
                ]);
                
                // Get draft token for redirect
                $draftToken = $poster->draft_token ?? null;
                if ($draftToken) {
                    return redirect()->route('poster.preview', ['token' => $draftToken])
                        ->with('error', 'Failed to initiate payment: ' . $errorMessage . '. Please try again.');
                }
                
                return redirect()->route('poster.register')
                    ->with('error', 'Failed to initiate payment: ' . $errorMessage . '. Please start a new registration.');
            }

            // Store payment gateway response (matching PaymentGatewayController format)
            $merchantData = json_encode($orderData);
            DB::table('payment_gateway_response')->insert([
                'merchant_data' => $merchantData,
                'order_id' => $orderId,
                'amount' => $amount,
                'status' => 'Pending',
                'gateway' => 'CCAvenue',
                'currency' => $currency,
                'email' => $billingEmail,
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Log successful initiation
            Log::info('Poster CCAvenue Payment - Successfully initiated', [
                'poster_id' => $poster->id,
                'order_id' => $orderId,
                'amount' => $amount,
                'encrypted_data_length' => strlen($result['encrypted_data'] ?? ''),
            ]);

            // Store in session for callback handling
            session([
                'poster_id' => $poster->id,
                'poster_tin_no' => $poster->tin_no,
                'poster_payment_id' => $payment->id,
                'poster_order_id' => $orderId,
                'payment_application_type' => 'poster',
            ]);

            // Validate encrypted data exists
            if (empty($result['encrypted_data'])) {
                Log::error('Poster CCAvenue Payment - Empty encrypted data', [
                    'poster_id' => $poster->id,
                    'result' => $result,
                ]);
                $draftToken = $poster->draft_token ?? null;
                if ($draftToken) {
                    return redirect()->route('poster.preview', ['token' => $draftToken])
                        ->with('error', 'Payment gateway error: No encrypted data received. Please try again.');
                }
                return redirect()->route('poster.register')
                    ->with('error', 'Payment gateway error. Please start a new registration.');
            }

            // Verify access code is available in config (required by pgway.ccavenue view)
            $accessCode = config('constants.CCAVENUE_ACCESS_CODE');
            if (empty($accessCode)) {
                // Try to get from service credentials
                $credentials = $this->ccAvenueService->getCredentials();
                $accessCode = $credentials['access_code'] ?? null;
                
                if (empty($accessCode)) {
                    Log::error('Poster CCAvenue Payment - Access code not found', [
                        'poster_id' => $poster->id,
                        'config_exists' => config('constants.CCAVENUE_ACCESS_CODE') !== null,
                    ]);
                    $draftToken = $poster->draft_token ?? null;
                    if ($draftToken) {
                        return redirect()->route('poster.preview', ['token' => $draftToken])
                            ->with('error', 'Payment gateway configuration error. Please contact support.');
                    }
                    return redirect()->route('poster.register')
                        ->with('error', 'Payment gateway configuration error. Please contact support.');
                }
            }

            // Return form to redirect to CCAvenue using standard view (same as PaymentGatewayController)
            // PaymentGatewayController doesn't pass accessCode, it uses config in the view
            // But we need to ensure the config value matches what we used for encryption
            $credentials = $this->ccAvenueService->getCredentials();
            $serviceAccessCode = $credentials['access_code'];
            $configAccessCode = config('constants.CCAVENUE_ACCESS_CODE');
            
            Log::info('Poster CCAvenue Payment - Returning redirect view', [
                'poster_id' => $poster->id,
                'has_encrypted_data' => !empty($result['encrypted_data']),
                'service_access_code' => $serviceAccessCode, // Full code for verification
                'config_access_code' => $configAccessCode, // Full code for verification
                'access_codes_match' => $serviceAccessCode === $configAccessCode,
            ]);
            
            // Verify access codes match - if not, there's a config issue
            if ($serviceAccessCode !== $configAccessCode) {
                Log::error('Poster CCAvenue Payment - Access code mismatch!', [
                    'service_code' => $serviceAccessCode,
                    'config_code' => $configAccessCode,
                ]);
                // Still proceed, but log the issue
            }
            
            // Use same approach as PaymentGatewayController - just pass encryptedData
            // The view will use config('constants.CCAVENUE_ACCESS_CODE')
            return view('pgway.ccavenue', [
                'encryptedData' => $result['encrypted_data'],
            ]);

        } catch (\Exception $e) {
            Log::error('Poster CCAvenue Payment Error', [
                'poster_id' => $poster->id,
                'tin_no' => $poster->tin_no,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Try to get draft token for redirect
            $draftToken = $poster->draft_token ?? null;
            if ($draftToken) {
                return redirect()->route('poster.preview', ['token' => $draftToken])
                    ->with('error', 'An error occurred while initiating payment: ' . $e->getMessage());
            }
            
            return redirect()->route('poster.register')
                ->with('error', 'An error occurred while initiating payment. Please start a new registration.');
        }
    }

    /**
     * Initiate PayPal payment
     */
    private function initiatePayPalPayment(Poster $poster)
    {
        try {
            if (!$this->paypalClient) {
                return redirect()->back()->with('error', 'PayPal is not configured.');
            }

            $orderId = $poster->tin_no . '_' . time();
            $amount = (float) $poster->total_amount;
            $currency = $poster->currency ?? 'USD';

            // Create or get invoice record for the poster (required for foreign key constraint)
            $invoice = Invoice::firstOrCreate(
                ['invoice_no' => $poster->tin_no], // Use TIN as invoice number
                [
                    'type' => 'poster_registration',
                    'invoice_no' => $poster->tin_no,
                    'poster_reg_id' => $poster->id, // Map to posters table
                    'currency' => $currency,
                    'amount' => $amount,
                    'price' => $poster->base_amount ?? $amount,
                    'gst' => $poster->gst_amount ?? 0,
                    'processing_charges' => $poster->processing_fee ?? 0,
                    'total_final_price' => $amount,
                    'amount_paid' => 0,
                    'pending_amount' => $amount,
                    'payment_status' => 'unpaid',
                ]
            );

            // Update poster_reg_id if invoice exists but doesn't have it
            if ($invoice->poster_reg_id !== $poster->id) {
                $invoice->update(['poster_reg_id' => $poster->id]);
            }

            // Create payment record with pending status
            $payment = Payment::create([
                'invoice_id' => $invoice->id, // Use invoice ID (foreign key constraint)
                'payment_method' => 'PayPal',
                'amount' => $amount,
                'amount_paid' => 0,
                'amount_received' => 0,
                'transaction_id' => $orderId,
                'status' => 'pending',
                'order_id' => $orderId,
                'currency' => $currency,
                'payment_date' => now(),
            ]);

            // Store in session
            session([
                'poster_id' => $poster->id,
                'poster_tin_no' => $poster->tin_no,
                'poster_payment_id' => $payment->id,
                'poster_order_id' => $orderId,
            ]);

            // Create PayPal order
            $ordersController = $this->paypalClient->getOrdersController();
            
            $orderRequest = OrderRequestBuilder::init()
                ->checkoutPaymentIntent(CheckoutPaymentIntent::CAPTURE)
                ->purchaseUnits([
                    PurchaseUnitRequestBuilder::init()
                        ->amountWithBreakdown(
                            AmountWithBreakdownBuilder::init()
                                ->currencyCode($currency)
                                ->value(number_format($amount, 2, '.', ''))
                        )
                        ->description('Poster Registration - ' . $poster->title)
                        ->build()
                ])
                ->applicationContext(
                    OrderApplicationContextBuilder::init()
                        ->returnUrl(route('poster.payment.callback', ['gateway' => 'paypal']))
                        ->cancelUrl(route('poster.payment.callback', ['gateway' => 'paypal']) . '?cancel=1')
                        ->build()
                )
                ->build();

            $response = $ordersController->ordersCreate($orderRequest);
            $orderResult = $response->getResult();

            if ($orderResult && $orderResult->getId()) {
                $paypalOrderId = $orderResult->getId();
                $approvalUrl = null;

                foreach ($orderResult->getLinks() as $link) {
                    if ($link->getRel() === 'approve') {
                        $approvalUrl = $link->getHref();
                        break;
                    }
                }

                // Store payment gateway response
                DB::table('payment_gateway_response')->insert([
                    'payment_id' => $paypalOrderId,
                    'order_id' => $orderId,
                    'status' => 'Pending',
                    'response_json' => json_encode($orderResult),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update payment with PayPal order ID
                $payment->update(['transaction_id' => $paypalOrderId]);

                if ($approvalUrl) {
                    return redirect($approvalUrl);
                } else {
                    return redirect()->back()->with('error', 'Failed to get PayPal approval URL.');
                }
            } else {
                return redirect()->back()->with('error', 'Failed to create PayPal order.');
            }

        } catch (\Exception $e) {
            Log::error('Poster PayPal Payment Error', [
                'poster_id' => $poster->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'An error occurred while initiating PayPal payment.');
        }
    }

    /**
     * Handle payment callback from gateway
     */
    public function paymentCallback(Request $request, string $gateway)
    {
        if ($gateway === 'ccavenue') {
            return $this->handleCcAvenueCallback($request);
        } elseif ($gateway === 'paypal') {
            return $this->handlePayPalCallback($request);
        }

        return redirect()->route('poster.register')
            ->with('error', 'Invalid payment gateway.');
    }

    /**
     * Handle CCAvenue callback
     */
    private function handleCcAvenueCallback(Request $request)
    {
        $encResponse = $request->input('encResp');

        if (empty($encResponse)) {
            // Try to get from session if redirect from PaymentGatewayController
            $posterTinNo = session('poster_tin_no');
            if ($posterTinNo) {
                return redirect()->route('poster.payment', ['tin_no' => $posterTinNo])
                    ->with('error', 'Payment response incomplete. Please try again.');
            }
            return redirect()->route('poster.register')
                ->with('error', 'Payment response incomplete.');
        }

        try {
            // Use CcAvenueService decrypt method (same as used in initiation)
            $credentials = $this->ccAvenueService->getCredentials();
            $decryptedResponse = $this->ccAvenueService->decrypt($encResponse, $credentials['working_key']);
            parse_str($decryptedResponse, $responseArray);

            $orderId = $responseArray['order_id'] ?? null;
            $orderStatus = $responseArray['order_status'] ?? null;
            $trackingId = $responseArray['tracking_id'] ?? null;
            $amount = $responseArray['mer_amount'] ?? null;

            if (!$orderId) {
                return redirect()->route('poster.register')
                    ->with('error', 'Invalid payment response.');
            }

            // Extract TIN from order_id (format: TIN-BTS2026-PSTR-123456_timestamp)
            $tinNo = explode('_', $orderId)[0];
            $poster = Poster::where('tin_no', $tinNo)->first();

            if (!$poster) {
                return redirect()->route('poster.register')
                    ->with('error', 'Poster registration not found.');
            }

            // Find invoice for this poster (using TIN as invoice_no)
            $invoice = Invoice::where('invoice_no', $tinNo)
                ->where('type', 'poster_registration')
                ->first();

            if (!$invoice) {
                // Create invoice if it doesn't exist (shouldn't happen, but safety check)
                $invoice = Invoice::create([
                    'type' => 'poster_registration',
                    'invoice_no' => $tinNo,
                    'poster_reg_id' => $poster->id, // Map to posters table
                    'currency' => $poster->currency ?? ($poster->nationality === 'India' ? 'INR' : 'USD'),
                    'amount' => (float) ($amount ?? $poster->total_amount),
                    'price' => $poster->base_amount ?? ($amount ?? $poster->total_amount),
                    'gst' => $poster->gst_amount ?? 0,
                    'processing_charges' => $poster->processing_fee ?? 0,
                    'total_final_price' => (float) ($amount ?? $poster->total_amount),
                    'amount_paid' => 0,
                    'pending_amount' => (float) ($amount ?? $poster->total_amount),
                    'payment_status' => 'unpaid',
                ]);
            } else {
                // Update poster_reg_id if invoice exists but doesn't have it
                if ($invoice->poster_reg_id !== $poster->id) {
                    $invoice->update(['poster_reg_id' => $poster->id]);
                }
            }
            
            // Update payment gateway response
            DB::table('payment_gateway_response')
                ->where('order_id', $orderId)
                ->update([
                    'status' => $orderStatus === 'Success' ? 'Success' : 'Failed',
                    'response_json' => json_encode($responseArray),
                    'updated_at' => now(),
                ]);
            
            // Process payment only if status is Success
            if ($orderStatus === 'Success') {
                // Update invoice
                $invoice->update([
                    'payment_status' => 'paid',
                    'amount_paid' => (float) $amount,
                    'pending_amount' => 0,
                    'updated_at' => now(),
                ]);
                
                // Find or create payment record
                $payment = Payment::where('order_id', $orderId)
                    ->where('invoice_id', $invoice->id)
                    ->first();
                
                if ($payment) {
                    $payment->update([
                        'status' => 'successful',
                        'amount_paid' => (float) $amount,
                        'amount_received' => (float) $amount,
                        'transaction_id' => $trackingId,
                        'pg_result' => 'Success',
                        'payment_method' => $responseArray['payment_mode'] ?? 'CCAvenue',
                        'payment_date' => now(),
                        'pg_response_json' => json_encode($responseArray),
                    ]);
                } else {
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'payment_method' => $responseArray['payment_mode'] ?? 'CCAvenue',
                        'amount' => (float) $amount,
                        'amount_paid' => (float) $amount,
                        'amount_received' => (float) $amount,
                        'transaction_id' => $trackingId,
                        'status' => 'successful',
                        'order_id' => $orderId,
                        'currency' => $invoice->currency ?? 'INR',
                        'payment_date' => now(),
                        'pg_result' => 'Success',
                        'pg_response_json' => json_encode($responseArray),
                    ]);
                }
                
                // Update poster payment status
                $poster->update(['payment_status' => 'successful']);
                
                // Clear session
                session()->forget(['poster_id', 'poster_tin_no', 'poster_payment_id', 'poster_order_id']);
                
                Log::info('Poster CCAvenue Payment Success via Callback', [
                    'poster_id' => $poster->id,
                    'tin_no' => $poster->tin_no,
                    'invoice_id' => $invoice->id,
                    'amount' => $amount,
                    'transaction_id' => $trackingId,
                ]);
                
                // Redirect to success page
                return redirect()
                    ->route('poster.success', ['tin_no' => $poster->tin_no])
                    ->with('success', 'Payment successful! Your registration is complete.');
            } else {
                // Payment failed - update all tables
                $payment = Payment::where('order_id', $orderId)
                    ->where('invoice_id', $invoice->id)
                    ->first();
                
                if ($payment) {
                    $payment->update([
                        'status' => 'failed',
                        'pg_result' => $orderStatus ?? 'Failed',
                        'pg_response_json' => json_encode($responseArray),
                    ]);
                }
                
                $poster->update(['payment_status' => 'failed']);
                
                Log::warning('Poster CCAvenue Payment Failed', [
                    'poster_id' => $poster->id,
                    'tin_no' => $poster->tin_no,
                    'order_status' => $orderStatus,
                    'response' => $responseArray,
                ]);
                
                return redirect()
                    ->route('poster.payment', ['tin_no' => $poster->tin_no])
                    ->with('error', 'Payment failed: ' . ($responseArray['failure_message'] ?? 'Unknown error'));
            }

        } catch (\Exception $e) {
            Log::error('Poster CCAvenue Callback Error', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);
            return redirect()->route('poster.register')
                ->with('error', 'An error occurred while processing payment.');
        }
    }

    /**
     * Handle PayPal callback
     */
    private function handlePayPalCallback(Request $request)
    {
        // Check if cancelled
        if ($request->has('cancel') && $request->get('cancel') == '1') {
            $posterId = session('poster_id');
            if ($posterId) {
                $poster = Poster::find($posterId);
                if ($poster) {
                    return redirect()->route('poster.payment', ['tin_no' => $poster->tin_no])
                        ->with('error', 'Payment was cancelled.');
                }
            }
            return redirect()->route('poster.register')
                ->with('error', 'Payment was cancelled.');
        }

        $paypalOrderId = $request->input('token') ?? session('poster_paypal_order_id');

        if (!$paypalOrderId) {
            return redirect()->route('poster.register')
                ->with('error', 'Payment response incomplete.');
        }

        try {
            if (!$this->paypalClient) {
                return redirect()->route('poster.register')
                    ->with('error', 'PayPal is not configured.');
            }

            // Capture the order
            $ordersController = $this->paypalClient->getOrdersController();
            $captureResponse = $ordersController->ordersCapture($paypalOrderId);
            $captureResult = $captureResponse->getResult();

            $status = $captureResult->getStatus();
            $orderId = session('poster_order_id');

            if (!$orderId) {
                // Try to get from payment_gateway_response
                $pgRow = DB::table('payment_gateway_response')
                    ->where('payment_id', $paypalOrderId)
                    ->first();
                $orderId = $pgRow->order_id ?? null;
            }

            if (!$orderId) {
                return redirect()->route('poster.register')
                    ->with('error', 'Order not found.');
            }

            // Extract TIN from order_id
            $tinNo = explode('_', $orderId)[0];
            $poster = Poster::where('tin_no', $tinNo)->first();

            if (!$poster) {
                return redirect()->route('poster.register')
                    ->with('error', 'Poster registration not found.');
            }

            // Find or create invoice for this poster
            $invoice = Invoice::firstOrCreate(
                ['invoice_no' => $tinNo],
                [
                    'type' => 'poster_registration',
                    'invoice_no' => $tinNo,
                    'poster_reg_id' => $poster->id, // Map to posters table
                    'currency' => $poster->currency ?? 'USD',
                    'amount' => (float) $poster->total_amount,
                    'price' => $poster->base_amount ?? $poster->total_amount,
                    'gst' => $poster->gst_amount ?? 0,
                    'processing_charges' => $poster->processing_fee ?? 0,
                    'total_final_price' => (float) $poster->total_amount,
                    'amount_paid' => 0,
                    'pending_amount' => (float) $poster->total_amount,
                    'payment_status' => 'unpaid',
                ]
            );

            // Update poster_reg_id if invoice exists but doesn't have it
            if ($invoice->poster_reg_id !== $poster->id) {
                $invoice->update(['poster_reg_id' => $poster->id]);
            }

            // Update payment gateway response
            DB::table('payment_gateway_response')
                ->where('payment_id', $paypalOrderId)
                ->update([
                    'status' => $status === 'COMPLETED' ? 'Success' : 'Failed',
                    'response_json' => json_encode($captureResult),
                    'updated_at' => now(),
                ]);

            // Find or create payment record
            $payment = Payment::where('order_id', $orderId)
                ->where('invoice_id', $invoice->id)
                ->first();

            if (!$payment) {
                $payment = Payment::create([
                    'invoice_id' => $invoice->id, // Use invoice ID (foreign key constraint)
                    'payment_method' => 'PayPal',
                    'amount' => (float) $poster->total_amount,
                    'amount_paid' => 0,
                    'amount_received' => 0,
                    'transaction_id' => $paypalOrderId,
                    'status' => 'pending',
                    'order_id' => $orderId,
                    'currency' => $poster->currency ?? 'USD',
                    'payment_date' => now(),
                ]);
            }

            if ($status === 'COMPLETED') {
                // Get amount from PayPal response
                $amount = 0;
                if ($captureResult->getPurchaseUnits() && count($captureResult->getPurchaseUnits()) > 0) {
                    $purchaseUnit = $captureResult->getPurchaseUnits()[0];
                    if ($purchaseUnit->getPayments() && $purchaseUnit->getPayments()->getCaptures()) {
                        $captures = $purchaseUnit->getPayments()->getCaptures();
                        if (count($captures) > 0) {
                            $capture = $captures[0];
                            $amount = (float) $capture->getAmount()->getValue();
                        }
                    }
                }

                // Update payment record
                $paymentAmount = $amount > 0 ? $amount : (float) $poster->total_amount;
                $payment->update([
                    'status' => 'successful',
                    'amount_paid' => $paymentAmount,
                    'amount_received' => $paymentAmount,
                    'transaction_id' => $paypalOrderId,
                    'pg_result' => 'Success',
                    'response' => json_encode($captureResult),
                    'payment_date' => now(),
                ]);

                // Update invoice payment status
                $invoice->update([
                    'payment_status' => 'paid',
                    'amount_paid' => $paymentAmount,
                    'pending_amount' => 0,
                ]);

                // Update poster payment status
                $poster->update(['payment_status' => 'successful']);

                // Clear session
                session()->forget(['poster_id', 'poster_tin_no', 'poster_payment_id', 'poster_order_id', 'poster_paypal_order_id']);

                return redirect()->route('poster.success', ['tin_no' => $poster->tin_no])
                    ->with('success', 'Payment successful!');
            } else {
                // Payment failed
                $payment->update([
                    'status' => 'failed',
                    'pg_result' => $status ?? 'Failed',
                    'response' => json_encode($captureResult),
                ]);

                $poster->update(['payment_status' => 'failed']);

                return redirect()->route('poster.payment', ['tin_no' => $poster->tin_no])
                    ->with('error', 'Payment was not completed. Please try again.');
            }

        } catch (\Exception $e) {
            Log::error('Poster PayPal Callback Error', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);
            return redirect()->route('poster.register')
                ->with('error', 'An error occurred while processing payment.');
        }
    }
}
