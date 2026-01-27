<?php

namespace App\Http\Controllers;

use App\Mail\ExtraRequirementsMail;
use App\Models\Application;
use App\Models\BillingDetail;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\RequirementsBilling;
use App\Models\RequirementsOrder;
use App\Models\User;
use App\Services\CcAvenueService;
use App\Services\ExtraRequirementsMailService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class PaymentGatewayController extends Controller
{
    //

    private $merchantId;
    private $accessCode;
    private $workingKey;
    private $redirectUrl;
    private $cancelUrl;

    // public function __construct()
    // {
    //     $this->merchantId = env('CCAVENUE_MERCHANT_ID');
    //     $this->accessCode = env('CCAVENUE_ACCESS_CODE');
    //     $this->workingKey = env('CCAVENUE_WORKING_KEY');
    //     $this->redirectUrl = env('CCAVENUE_REDIRECT_URL');
    //     $this->cancelUrl = env('CCAVENUE_REDIRECT_URL');
    // }

    public function __construct()
    {
        $this->merchantId = '7700';
        $this->accessCode = 'AVJS71ME17AS68SJSA';
        $this->workingKey = '7AF39D44C8DC0DE71EDD69C288C96694';
        $this->redirectUrl = config('constants.APP_URL') . '/payment/ccavenue-success';
        $this->cancelUrl = config('constants.APP_URL') . '/payment/ccavenue-success';
    }

    public function handleResponse(Request $request)
    {
        $encResponse = $request->input('encResp');
        $decryptedResponse = $this->decrypt($encResponse, $this->workingKey);
        parse_str($decryptedResponse, $responseArray);

        return response()->json($responseArray);
    }

    private function encrypt($plainText, $key)
    {
        $key = pack('H*', md5($key));
        $initVector = pack('C*', 0x0, 0x1, 0x2, 0x3, 0x4, 0x5, 0x6, 0x7, 0x8, 0x9, 0xA, 0xB, 0xC, 0xD, 0xE, 0xF);
        $encryptedText = bin2hex(openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector));
        return $encryptedText;
    }

    private function decrypt($encryptedText, $key)
    {
        $key = pack('H*', md5($key));
        $initVector = pack('C*', 0x0, 0x1, 0x2, 0x3, 0x4, 0x5, 0x6, 0x7, 0x8, 0x9, 0xA, 0xB, 0xC, 0xD, 0xE, 0xF);
        $encryptedText = pack('H*', $encryptedText);
        return openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
    }

    public function ccAvenuePayment($orderID, Request $request)
    {
    //    dd($orderID); 
        if (!$orderID) {
            return redirect()
                ->route('payment.lookup')
                ->with('error', 'Invoice ID is required');
        }

        // get the invoice details from the Invoice model where invoice_no = $id
        $invoice = Invoice::where('invoice_no', $orderID)->first();

        // if invoice not found then redirect appropriately
        if (!$invoice) {
            return redirect()
                ->route('payment.lookup')
                ->with('error', 'Invoice not found w')
                ->with('invoice_hint', $orderID);
        }

        // Get application to check if it's startup-zone (before checking payment status)
        $application = null;
        $isStartupZone = false;
        if ($invoice->application_id) {
            $application = Application::find($invoice->application_id);
            if ($application && $application->application_type === 'startup-zone') {
                $isStartupZone = true;
            }
        }

        // if invoice is already paid then redirect appropriately
        if ($invoice->payment_status == 'paid') {
            if ($isStartupZone && $application) {
                return redirect()
                    ->route('startup-zone.confirmation', $application->application_id)
                    ->with('info', 'Payment already completed');
            }
            return redirect()->route('payment.lookup');
        }
        
        // For startup-zone, check if application is approved - payment only allowed after approval
        if ($isStartupZone && $application) {
            if ($application->submission_status !== 'approved') {
                return redirect()
                    ->route('startup-zone.payment', $application->application_id)
                    ->with('error', 'Your profile is not approved yet for payment. Please wait for admin approval.');
            }
        }

        // Fetch billing detail - handle startup zone differently
        $billingDetail = null;

        if ($isStartupZone && $application) {
            // For startup zone, get billing from EventContact
            $eventContact = \App\Models\EventContact::where('application_id', $invoice->application_id)->first();
            if ($eventContact && $application) {
                // Build contact name properly (trim extra spaces)
                $contactName = trim(($eventContact->salutation ?? '') . ' ' . ($eventContact->first_name ?? '') . ' ' . ($eventContact->last_name ?? ''));

                // Get phone number and strip country code if present (format: 91-9801217815 -> 9801217815)
                $phone = $eventContact->contact_number ?? $application->landline ?? '';
                $phone = preg_replace('/^.*-/', '', $phone);  // Remove country code prefix

                // Get city name from city_id
                $cityName = '';
                if ($application->city_id) {
                    $city = \DB::table('cities')->where('id', $application->city_id)->first();
                    $cityName = $city->name ?? '';
                }

                // Get state and country names
                $stateName = '';
                if ($application->state_id) {
                    $state = \App\Models\State::find($application->state_id);
                    $stateName = $state->name ?? '';
                }

                $countryName = '';
                if ($application->country_id) {
                    $country = \App\Models\Country::find($application->country_id);
                    $countryName = $country->name ?? '';
                }

                $billingDetail = (object) [
                    'billing_company' => $application->company_name ?? '',
                    'contact_name' => $contactName,
                    'email' => $eventContact->email ?? $application->company_email ?? '',
                    'phone' => $phone,
                    'address' => $application->address ?? '',
                    'country_id' => $application->country_id ?? null,
                    'state_id' => $application->state_id ?? null,
                    'postal_code' => $application->postal_code ?? '',
                    'state' => (object) ['name' => $stateName],
                    'country' => (object) [
                        'name' => $countryName,
                        'states' => collect()
                    ],
                    'gst' => $application->gst_no ?? null,
                    'pan_no' => $application->pan_no ?? null,
                    'city_id' => $application->city_id ?? null,
                    'city_name' => $cityName,  // Add city name for billing_city
                ];
            }
        }

        // For non-startup-zone, use BillingDetail
        if (!$billingDetail) {
            $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();
        }

        $requirementsBilling = \DB::table('requirements_billings')
            ->where('invoice_id', $invoice->id)
            ->first();

        // 'phone' => $requirementsBilling->billing_phone, in this 91-9801217815 pass only 9801217815

        // if billingDetail
        if ($requirementsBilling) {
            // Get city name if billing_city is an ID
            $cityName = '';
            if (!empty($requirementsBilling->billing_city)) {
                // Check if it's numeric (ID) or string (name)
                if (is_numeric($requirementsBilling->billing_city)) {
                    $city = \DB::table('cities')->where('id', $requirementsBilling->billing_city)->first();
                    $cityName = $city->name ?? $requirementsBilling->billing_city;
                } else {
                    $cityName = $requirementsBilling->billing_city;
                }
            }

            $billingDetail = (object) [
                'billing_company' => $requirementsBilling->billing_company,
                'contact_name' => $requirementsBilling->billing_name,
                'email' => $requirementsBilling->billing_email,
                'phone' => preg_replace('/^91-/', '', $requirementsBilling->billing_phone),
                'address' => $requirementsBilling->billing_address,
                'country_id' => $requirementsBilling->country_id,
                'state_id' => $requirementsBilling->state_id,
                'postal_code' => $requirementsBilling->zipcode,
                // Add a dummy state object to mimic $billingDetail->state->name
                'state' => (object) [
                    'name' => optional(\App\Models\State::find($requirementsBilling->state_id))->name
                ],
                // Add a dummy country object to mimic $billingDetail->country->name
                'country' => (object) [
                    'name' => optional(\App\Models\Country::find($requirementsBilling->country_id))->name,
                    'states' => ($requirementsBilling->country_id)
                        ? optional(\App\Models\Country::with('states')->find($requirementsBilling->country_id))->states ?? collect()
                        : collect(),
                ],
                'gst' => $requirementsBilling->gst_no ?? null,
                'pan_no' => $requirementsBilling->pan_no ?? null,
                'city_id' => $requirementsBilling->billing_city ?? null,
                'city_name' => $cityName,  // Add city name for billing_city
            ];
        }

        // Check if this is a poster registration invoice
        $isPosterInvoice = ($invoice->type === 'poster_registration' && $invoice->poster_reg_id);
        $poster = null;
        
        if ($isPosterInvoice) {
            $poster = \App\Models\Poster::find($invoice->poster_reg_id);
            if ($poster) {
                // Create billing detail object from poster data
                $billingDetail = (object) [
                    'contact_name' => $poster->lead_name ?? '',
                    'email' => $poster->lead_email ?? '',
                    'phone' => trim(($poster->lead_ccode ?? '') . ' ' . ($poster->lead_phone ?? '')),
                    'address' => $poster->lead_addr ?? '',
                    'postal_code' => $poster->lead_zip ?? '',
                    'state' => (object) ['name' => $poster->lead_state ?? ''],
                    'country' => (object) ['name' => $poster->lead_country ?? 'India'],
                    'city_name' => $poster->lead_city ?? '',
                ];
                
                // Store poster info in session for callback
                session([
                    'poster_id' => $poster->id,
                    'poster_tin_no' => $poster->tin_no,
                    'payment_application_type' => 'poster',
                ]);
            }
        }

        // Ensure billingDetail exists
        if (!$billingDetail) {
            Log::error('CCAvenue Payment: Billing details not found', [
                'invoice_id' => $invoice->id,
                'invoice_no' => $orderID,
                'application_id' => $invoice->application_id,
                'is_startup_zone' => $isStartupZone,
                'is_poster' => $isPosterInvoice,
            ]);

            if ($isStartupZone && $application) {
                return redirect()
                    ->route('startup-zone.payment', $application->application_id)
                    ->with('error', 'Billing details not found. Please contact support.');
            }
            
            if ($isPosterInvoice) {
                return redirect()
                    ->route('poster.payment', ['tin_no' => $poster->tin_no ?? $invoice->invoice_no])
                    ->with('error', 'Billing details not found. Please contact support.');
            }

            return redirect()
                ->route('payment.lookup')
                ->with('error', 'Billing details not found. Please contact support.');
        }

        // Generate order_id with TIN prefix format: {application_id}_{timestamp}
        // If application exists, use application_id (TIN), otherwise use invoice_no
        // For poster invoices, use poster TIN
        if ($isPosterInvoice && $poster) {
            $tinPrefix = $poster->tin_no;
        } elseif ($application && $application->application_id) {
            $tinPrefix = $application->application_id;
        } else {
            $tinPrefix = $orderID;
        }
        $orderIdWithTimestamp = $tinPrefix . '_' . time();

        // Determine currency - use invoice currency or default based on type
        $currency = $invoice->currency ?? ($isPosterInvoice && $poster ? ($poster->currency ?? ($poster->nationality === 'India' ? 'INR' : 'USD')) : 'INR');
        
        $data = [
            'merchant_id' => $this->merchantId,
            'order_id' => $orderIdWithTimestamp,
            'currency' => $currency,
            'amount' => $invoice->total_final_price,
            'redirect_url' => $this->redirectUrl,
            'cancel_url' => $this->cancelUrl,
            'language' => 'EN',
            'billing_name' => $billingDetail->contact_name ?? '',
            'billing_address' => $billingDetail->address ?? '',
            'billing_city' => isset($billingDetail->city_name) ? $billingDetail->city_name : ($billingDetail->city_id ?? ''),
            'billing_state' => $billingDetail->state->name ?? '',
            'billing_zip' => $billingDetail->postal_code ?? '',
            'billing_country' => $billingDetail->country->name ?? '',
            'billing_tel' => preg_replace('/^.*-/', '', $billingDetail->phone ?? ''),
            'billing_email' => $billingDetail->email ?? '',
        ];



        // dd(config('constants.APP_URL'));


        //dd($data);

        $merchantData = json_encode($data);

        // insert into payment_gateway_response table
        \DB::table('payment_gateway_response')->insert([
            'merchant_data' => $merchantData,
            'order_id' => $data['order_id'],
            'amount' => $data['amount'],
            'status' => 'Pending',
            'gateway' => 'CCAvenue',
            'currency' => $currency,
            'email' => $data['billing_email'],
            'user_id' => $application ? $application->user_id : null,
            'created_at' => now(),
        ]);

        // Note: Payment record will be created only after payment is completed
        // (in ccAvenueSuccess or ccAvenueWebhook methods when we have transaction_id)

        // dd($data);

        $queryString = http_build_query($data);
        $encryptedData = $this->encrypt($queryString, $this->workingKey);

        // Store invoice_no in session for fallback handling
        session([
            'invoice_no' => $orderID,
            'payment_user_id' => auth()->check() ? auth()->id() : null,
            'payment_application_id' => $application ? $application->application_id : ($isPosterInvoice && $poster ? $poster->tin_no : null),
            'payment_application_type' => $application ? $application->application_type : ($isPosterInvoice ? 'poster' : null),
        ]);
        
        // Also store poster info if it's a poster invoice (in case session was cleared)
        if ($isPosterInvoice && $poster) {
            session([
                'poster_id' => $poster->id,
                'poster_tin_no' => $poster->tin_no,
            ]);
        }
        
        // Also store poster info if it's a poster invoice
        if ($isPosterInvoice && $poster) {
            session([
                'poster_id' => $poster->id,
                'poster_tin_no' => $poster->tin_no,
            ]);
        }

        return view('pgway.ccavenue', compact('encryptedData'));
    }

    /**
     * Centralized helper to decide where to send the user when
     * something goes wrong in the payment response flow.
     *
     * It tries (in order):
     *  - Startup Registration payment page
     *  - Extra Requirements orders page
     *  - Payment lookup page (user enters Application ID / Invoice No)
     */
    private function redirectForPaymentError(
        ?string $invoiceNo,
        ?string $applicationTin,
        ?string $orderId,
        string $message
    ) {
        // 1. If we have an application TIN in session, try startup-zone directly
        if ($applicationTin) {
            $application = Application::where('application_id', $applicationTin)->first();
            if ($application && $application->application_type === 'startup-zone') {
                return redirect()
                    ->route('startup-zone.payment', $application->application_id)
                    ->with('error', $message);
            }
        }

        Log::error('CCAvenue Payment Error: No invoice found', [
            'order_id' => $orderId ?? 'N/A',
            'message' => $message ?? 'N/A',
            'invoice_no' => $invoiceNo ?? 'N/A',
            'application_tin' => $applicationTin ?? 'N/A',
        ]);


        // 2. If we have an invoice_no, try to resolve the invoice and its type
        if ($invoiceNo) {
            $invoice = Invoice::where('invoice_no', $invoiceNo)->first();
            if ($invoice) {
                // Try to resolve application from invoice
                $application = null;
                if ($invoice->application_id) {
                    $application = Application::find($invoice->application_id);
                }

                // 2.a Startup Zone Registration
                if (
                    ($invoice->type === 'Startup Zone Registration') ||
                    ($application && $application->application_type === 'startup-zone')
                ) {
                    $tin = $application ? $application->application_id : $invoice->application_no;
                    if ($tin) {
                        return redirect()
                            ->route('startup-zone.payment', $tin)
                            ->with('error', $message);
                    }
                }

                // 2.b Extra Requirements order (based on invoice type / relation)
                if (
                    $invoice->type === 'extra_requirement' ||
                    $invoice->requirementsOrder()->exists()
                ) {
                    // User-facing extra requirements page
                    return redirect()
                        ->route('extra_requirements.index')
                        ->with('error', $message);
                }

                // 2.c Exhibitor Registration
                if (
                    ($invoice->type === 'exhibitor-registration') ||
                    ($application && $application->application_type === 'exhibitor-registration')
                ) {
                    $applicationId = $application ? $application->application_id : ($invoice->application_no ?? null);
                    if ($applicationId) {
                        return redirect()
                            ->route('exhibitor-registration.payment', $applicationId)
                            ->with('error', $message);
                    }
                }

                // 2.d Fallback for other invoice types – send to lookup
                return redirect()
                    ->route('payment.lookup')
                    ->with('error', $message)
                    ->with('invoice_hint', $invoiceNo);
            }
        }

        // 3. If we only have order_id (from gateway), try to derive invoice_no
        if ($orderId) {
            // Order ID formats we support:
            //  - {invoice_no}_{timestamp}
            //  - {application_id}_{timestamp}
            $base = explode('_', $orderId)[0] ?? null;
            if ($base) {
                return $this->redirectForPaymentError($base, null, null, $message);
            }
        }

        

        // 4. Final fallback – take user to lookup page where they can
        //    enter Application ID or Invoice No to resume payment.
        return redirect()
            ->route('payment.lookup')
            ->with('error', $message);
    }

    /**
     * Show a simple page where the user can enter
     * Application ID, TIN No, or Invoice Number to
     * be redirected to the appropriate payment page.
     */
    public function showPaymentLookup(Request $request)
    {
        $prefillInvoice = session('invoice_hint');

        // Share association logo (if any) for the layout - set to null for lookup page
        view()->share('associationLogo', null);

        return view('payment.lookup', [
            'prefillInvoice' => $prefillInvoice,
        ]);
    }

    /**
     * Handle lookup form submission and redirect user
     * to the correct payment page based on what they provide.
     */
    public function handlePaymentLookup(Request $request)
    {
        $data = $request->validate([
            'application_id' => ['nullable', 'string'],
            'tin_no' => ['nullable', 'string'],
            'invoice_no' => ['nullable', 'string'],
        ]);

        if (empty($data['application_id']) && empty($data['tin_no']) && empty($data['invoice_no'])) {
            return back()
                ->withInput()
                ->with('error', 'Please enter an Application ID, TIN No, or Invoice Number.');
        }

        $application = null;
        $invoice = null;

        // 1. Try by Application ID (this is the TIN stored in applications table)
        if (!empty($data['application_id'])) {
            $application = Application::where('application_id', trim($data['application_id']))->first();
            if ($application) {
                $invoice = Invoice::where('application_id', $application->id)->first();
            }
        }

        // 2. Try by TIN No (can be in application_id field of Application or application_no field of Invoice)
        if (!$application && !empty($data['tin_no'])) {
            $tinNo = trim($data['tin_no']);

            // Try to find by application_id in Application table
            $application = Application::where('application_id', $tinNo)->first();
            if ($application) {
                $invoice = Invoice::where('application_id', $application->id)->first();
            }

            // If not found, try to find invoice by application_no (which stores TIN)
            if (!$invoice) {
                $invoice = Invoice::where('application_no', $tinNo)->first();
                if ($invoice && !$application && $invoice->application_id) {
                    $application = Application::find($invoice->application_id);
                }
            }
        }

        // 3. If still no invoice and invoice_no provided, try by Invoice No
        if (!$invoice && !empty($data['invoice_no'])) {
            $invoiceNo = trim($data['invoice_no']);
            $invoice = Invoice::where('invoice_no', $invoiceNo)->first();

            if ($invoice && !$application && $invoice->application_id) {
                $application = Application::find($invoice->application_id);
            }
        }

        if (!$invoice && !$application) {
            return back()
                ->withInput()
                ->with('error', 'No matching Application or Invoice found. Please check the details and try again.');
        }

        // If this is a startup-zone application, send to startup payment page
        if ($application && $application->application_type === 'startup-zone') {
            return redirect()->route('startup-zone.payment', $application->application_id);
        }

        // Extra requirement invoices – send to extra requirements list so they can retry from there
        if ($invoice && ($invoice->type === 'extra_requirement' || $invoice->requirementsOrder()->exists())) {
            return redirect()
                ->route('extra_requirements.index')
                ->with('info', 'We found your extra requirements order. Please continue payment from the list.');
        }

        // Fallback: exhibitor orders page
        return redirect()
            ->route('exhibitor.orders')
            ->with('info', 'We found your order. Please continue from your orders list.');
    }

    
    //

    public function downloadInvoicePdf($invoiceId)
    {
        $service = new ExtraRequirementsMailService();
        $data = $service->prepareMailData($invoiceId);
        // $mail = new ExtraRequirementsMail($data);
        // render documents.extraOrder to HTML
        $pdf = Pdf::loadView('documents.extraOrder', $data)->setPaper('a3', 'portrait')->set_option('isRemoteEnabled', true);;

        // display the PDF in the browser or download it
        return $pdf->stream('invoice_' . $invoiceId . '.pdf');
        return $pdf->download('OrderConfirmation_' . $data['invoice_Id'] . '.pdf');

        // Or, to display in browser:
        // return $pdf->stream('invoice_' . $invoiceId . '.pdf');
    }

    public function ccAvenueSuccess(Request $request)
    {
        // Log incoming request for debugging
        Log::info('CCAvenue Success Callback', [
            'request_data' => $request->all(),
            'has_encResp' => $request->has('encResp'),
        ]);


        // extract the order_id and explod the order_id to get the invoice_no 
        // {"request_data": "orderNo": 
        // "order_id": "TIN-BTS-2026-EXH-830358_1769512940"
        $orderId = $request->input('order_id');
        $invoiceNo = $orderId ? explode('_', $orderId)[0] : null;

        // Check if encResp parameter exists
        $encResponse = $request->input('encResp');
        $orderNo = $invoiceNo;

        // {"request_data":{"encResp":"7dde374e74e78d29d2fcb344bdac4f8dc395fa8935a0c61c04a9eed8716036433c76851406bcc50ef787d9ad57594d40afde5dc1a983700b0b1134e523ce6dee26ba0fcd824132128a19ef0ca9c0a6b5de3dcf2b16456f85f06e4aa50b2b153c33e5595d0974762424050d1e7d0862f94233dac1a550a4424d4dbabcfa61cb5faaad8c364e40f7eaa09387457805d7bfeaa07e54ce6ce06c83f9c9d5acfd64e5f6daa1e92b4e1a0d40a212db4b95425954888df1c8898b1ed4a9dd977276cf134016eeec294cea59507b76d5fe1603b1c886640fc2b86e5f06ee94a9d847b363d7b3f84cd22d5ffbd611f931e31fad8445cf74baa7dbaea76fb2e5660f34ba1b8dfd9bb4a017d34128bf2d453fba113af8b59fca8a098e97de8848f1e3cade6da47ca9fec399641e54ff67a21da1cbfe48dbbdf2d0dc285e568c09d765d41c30fe599a1a902b9e2affa52a82de81c3e08aa24197ba899b458e61f269122934057100ba77fdd6cb8ccea3409969feb31653ce0be1007704c8e3905702b5925f8443dba29afc50f599b076299ef1d815521b7cc1195439a0801becdb25a2ece453f60bff6e4cf148014c600697ffdd23a4fbcbc4f95425b8e9576099004fdf4f82c4aad128d3126f8014d87af8df9cb0b239e367556c921b6b7e4138a1d85b0265765d96c5039aa54d1d78dc9b164da1e1329d7cbf5ffbd4ab42a9b4708457be6c2477f7cfbe6331a61ed6e4247a1897de3dbcd7b6fdc84dfd4e3016afea3123d6b4ae17671e58be107a39f0911cee539ad3f4057556e734ef9a25bb0d34131dfaf8e56f7f8f0de50bb35f60ab7daa0f8636eb5fa1b063442c61fbcf030063cf7468df38a3f9bc59e060eae156e85150b8e635be64f462bb1658de87a8b66d730452fff1a5aaafc5da755e3bfbad2e1f19d8e5c30c0a0fb6cb1ef14b79e93b23e992ccad5a2a3d69fda3d98d3efd0c9f95a9c57bf3ca1c9ffbebcf15149a3ace7b193f986f0cf0547aac099a195ab9491e8611035060cc6ff718fc51d8fb1021f843b57b3ff5a0e5f55582e1e8ed2546c9e18f86bebcba0f3cf59b0f4739e320f2f878f6d4227aea7f6de8bb2f614586460142fd0f44900ef1febed9004be85476bb6eaa74c1368608937bf6ba0b59e531c59d62f4dc67be6a1c59aecf32f4ce44","orderNo":"TIN-BTS-2026-EXH-830358_1769512940","settingIntegrationType":null,"accessCode":"AVJS71ME17AS68SJSA"},"has_encResp":true} 

        // dd($encResponse);


        if (empty($encResponse)) {
            Log::warning('CCAvenue Success: Missing encResp parameter', [
                'request_data' => $request->all(),
                'orderNo' => $orderNo,
                'session_invoice' => session('invoice_no'),
            ]);

            // If orderNo is provided, try to extract order ID and find invoice/application
            if ($orderNo) {
                // Extract base order ID (remove timestamp suffix if present)
                // Format: TIN-BTS-2026-EXH-830358_1769512940 -> TIN-BTS-2026-EXH-830358
                $baseOrderId = explode('_', $orderNo)[0];
                
                Log::info('CCAvenue Success: Attempting to resolve from orderNo', [
                    'orderNo' => $orderNo,
                    'baseOrderId' => $baseOrderId,
                ]);

                // Try to find invoice by invoice_no
                $invoice = Invoice::where('invoice_no', $baseOrderId)->first();
                
                // Try to find application by application_id (for exhibitor-registration, startup-zone)
                $application = null;
                if (!$invoice) {
                    $application = Application::where('application_id', $baseOrderId)->first();
                    if ($application && $application->invoice) {
                        $invoice = $application->invoice;
                    }
                } elseif ($invoice->application_id) {
                    $application = Application::find($invoice->application_id);
                }

                // Handle exhibitor-registration
                if ($application && $application->application_type === 'exhibitor-registration') {
                    Log::info('CCAvenue Success: Found exhibitor-registration application', [
                        'application_id' => $application->application_id,
                        'invoice_id' => $invoice->id ?? null,
                    ]);
                    
                    // Check payment gateway response table for this order
                    $pgResponse = \DB::table('payment_gateway_response')
                        ->where('order_id', $orderNo)
                        ->orWhere('order_id', 'like', $baseOrderId . '_%')
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    if ($pgResponse && $pgResponse->status === 'Success' && $invoice) {
                        // Payment was successful - update invoice and payment records if not already updated
                        if ($invoice->payment_status !== 'paid') {
                            try {
                                // Parse response JSON if available
                                $responseData = $pgResponse->response_json ? json_decode($pgResponse->response_json, true) : [];
                                $amount = $pgResponse->amount ?? ($responseData['mer_amount'] ?? $invoice->total_final_price);
                                
                                // Update invoice
                                $invoice->update([
                                    'payment_status' => 'paid',
                                    'amount_paid' => (float) $amount,
                                    'pending_amount' => 0,
                                    'updated_at' => now(),
                                ]);
                                
                                // Find or create payment record
                                $payment = Payment::where('order_id', $orderNo)
                                    ->orWhere('order_id', 'like', $baseOrderId . '_%')
                                    ->where('invoice_id', $invoice->id)
                                    ->first();
                                
                                if ($payment) {
                                    $payment->update([
                                        'status' => 'successful',
                                        'amount_paid' => (float) $amount,
                                        'amount_received' => (float) $amount,
                                        'transaction_id' => $pgResponse->transaction_id ?? ($responseData['tracking_id'] ?? null),
                                        'pg_result' => 'Success',
                                        'payment_method' => $pgResponse->payment_method ?? ($responseData['payment_mode'] ?? 'CCAvenue'),
                                        'payment_date' => $pgResponse->trans_date ?? now(),
                                        'pg_response_json' => $pgResponse->response_json,
                                    ]);
                                } else {
                                    Payment::create([
                                        'invoice_id' => $invoice->id,
                                        'payment_method' => $pgResponse->payment_method ?? ($responseData['payment_mode'] ?? 'CCAvenue'),
                                        'amount' => (float) $amount,
                                        'amount_paid' => (float) $amount,
                                        'amount_received' => (float) $amount,
                                        'transaction_id' => $pgResponse->transaction_id ?? ($responseData['tracking_id'] ?? null),
                                        'status' => 'successful',
                                        'order_id' => $orderNo,
                                        'currency' => $invoice->currency ?? 'INR',
                                        'payment_date' => $pgResponse->trans_date ?? now(),
                                        'pg_result' => 'Success',
                                        'pg_response_json' => $pgResponse->response_json,
                                        'user_id' => $application->user_id ?? null,
                                    ]);
                                }
                                
                                Log::info('Exhibitor Registration: Payment processed from payment_gateway_response', [
                                    'application_id' => $application->application_id,
                                    'invoice_id' => $invoice->id,
                                    'amount' => $amount,
                                ]);
                            } catch (\Exception $e) {
                                Log::error('Exhibitor Registration: Failed to process payment from pg_response', [
                                    'application_id' => $application->application_id,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }
                        
                        // Redirect to confirmation
                        return redirect()
                            ->route('exhibitor-registration.confirmation', $application->application_id)
                            ->with('success', 'Payment successful!');
                    } else {
                        // Payment status unclear, redirect to payment page
                        return redirect()
                            ->route('exhibitor-registration.payment', $application->application_id)
                            ->with('error', 'Payment response incomplete. Please check your payment status or contact support if amount was deducted.');
                    }
                }

                // Handle startup-zone
                if ($application && $application->application_type === 'startup-zone') {
                    return $this->redirectForPaymentError(
                        $invoice->invoice_no ?? null,
                        $application->application_id,
                        $orderNo,
                        'Payment response incomplete. Please try again. If amount was deducted, please contact support.'
                    );
                }

                // Handle poster
                if (strpos($baseOrderId, 'TIN-BTS2026-PSTR-') === 0 || strpos($baseOrderId, 'TIN-BTS') === 0) {
                    $poster = \App\Models\Poster::where('tin_no', $baseOrderId)->first();
                    if ($poster) {
                        $pgResponse = \DB::table('payment_gateway_response')
                            ->where('order_id', $orderNo)
                            ->orWhere('order_id', 'like', $baseOrderId . '_%')
                            ->orderBy('created_at', 'desc')
                            ->first();
                        
                        if ($pgResponse && $pgResponse->status === 'Success') {
                            return redirect()
                                ->route('poster.success', ['tin_no' => $poster->tin_no])
                                ->with('success', 'Payment successful! Your registration is complete.');
                        } else {
                            return redirect()
                                ->route('poster.payment', ['tin_no' => $poster->tin_no])
                                ->with('error', 'Payment response incomplete. Please check your payment status or contact support if amount was deducted.');
                        }
                    }
                }

                // If we found invoice/application but couldn't determine type, use redirectForPaymentError
                if ($invoice || $application) {
                    return $this->redirectForPaymentError(
                        $invoice->invoice_no ?? null,
                        $application->application_id ?? null,
                        $orderNo,
                        'Payment response incomplete. Please try again. If amount was deducted, please contact support.'
                    );
                }
            }

            // Fallback: Try to resolve from session and redirect appropriately
            $invoiceNo = session('invoice_no');
            $applicationTin = session('payment_application_id');  // TIN for startup-zone

            return $this->redirectForPaymentError(
                $invoiceNo,
                $applicationTin,
                $orderNo,
                'Payment response incomplete. Please try again. If amount was deducted, please contact support.'
            );
        }

        // Decrypt response
        $workingKey = env('CCAVENUE_WORKING_KEY') ?: $this->workingKey;

        try {
            $decryptedResponse = $this->decrypt($encResponse, $workingKey);
            parse_str($decryptedResponse, $responseArray);
        } catch (\Exception $e) {
            Log::error('CCAvenue Success: Decryption failed', [
                'error' => $e->getMessage(),
                'encResp_length' => strlen($encResponse),
            ]);

            // Try to resolve from session and redirect appropriately
            $invoiceNo = session('invoice_no');
            $applicationTin = session('payment_application_id');  // TIN for startup-zone

            return $this->redirectForPaymentError(
                $invoiceNo,
                $applicationTin,
                null,
                'Payment response error. Please try again. If amount was deducted, please contact support.'
            );
        }

        // Validate response array
        if (empty($responseArray) || !isset($responseArray['order_id'])) {
            Log::error('CCAvenue Success: Invalid response array', [
                'response_array' => $responseArray,
                'decrypted_response' => $decryptedResponse ?? null,
            ]);

            // Try to resolve from session and redirect appropriately
            $invoiceNo = session('invoice_no');
            $applicationTin = session('payment_application_id');  // TIN for startup-zone

            return $this->redirectForPaymentError(
                $invoiceNo,
                $applicationTin,
                $responseArray['order_id'] ?? null,
                'Invalid payment response. Please try again. If amount was deducted, please contact support.'
            );
        }

        // dd($responseArray);
        if ($responseArray['order_status'] == 'Success') {
            $trans_date = Carbon::createFromFormat('d/m/Y H:i:s', $responseArray['trans_date'])->format('Y-m-d H:i:s');
            // Update database with successful payment
            \DB::table('payment_gateway_response')
                ->where('order_id', $responseArray['order_id'])
                ->update([
                    'amount' => $responseArray['mer_amount'],
                    'transaction_id' => $responseArray['tracking_id'],
                    'payment_method' => $responseArray['payment_mode'],
                    'trans_date' => $trans_date,
                    'reference_id' => $responseArray['bank_ref_no'],
                    'response_json' => json_encode($responseArray),
                    'status' => 'Success',
                    'updated_at' => now(),
                ]);

            $order_id = explode('_', $responseArray['order_id'])[0];

            $invoice = Invoice::where('invoice_no', $order_id)->first();

            // Check if this is a poster payment
            $isPosterPayment = (session('payment_application_type') === 'poster');
            $posterTinNo = session('poster_tin_no');
            
            // Also check invoice type if invoice exists
            if ($invoice && $invoice->type === 'poster_registration' && $invoice->poster_reg_id) {
                $isPosterPayment = true;
                $poster = \App\Models\Poster::find($invoice->poster_reg_id);
                if ($poster) {
                    $posterTinNo = $poster->tin_no;
                }
            } elseif ($isPosterPayment && $posterTinNo) {
                $poster = \App\Models\Poster::where('tin_no', $posterTinNo)->first();
            } else {
                $poster = null;
            }
            
            // Handle poster payment - update all tables and redirect to success
            if ($isPosterPayment && $poster && $invoice && $invoice->type === 'poster_registration') {
                // Update invoice
                $invoice->update([
                    'payment_status' => 'paid',
                    'amount_paid' => (float) $responseArray['mer_amount'],
                    'pending_amount' => 0,
                    'updated_at' => now(),
                ]);
                
                // Find or create payment record
                $payment = Payment::where('order_id', $responseArray['order_id'])
                    ->where('invoice_id', $invoice->id)
                    ->first();
                
                if ($payment) {
                    $payment->update([
                        'status' => 'successful',
                        'amount_paid' => (float) $responseArray['mer_amount'],
                        'amount_received' => (float) $responseArray['mer_amount'],
                        'transaction_id' => $responseArray['tracking_id'] ?? null,
                        'pg_result' => 'Success',
                        'payment_method' => $responseArray['payment_mode'] ?? 'CCAvenue',
                        'payment_date' => $trans_date ?? now(),
                        'pg_response_json' => json_encode($responseArray),
                    ]);
                } else {
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'payment_method' => $responseArray['payment_mode'] ?? 'CCAvenue',
                        'amount' => (float) $responseArray['mer_amount'],
                        'amount_paid' => (float) $responseArray['mer_amount'],
                        'amount_received' => (float) $responseArray['mer_amount'],
                        'transaction_id' => $responseArray['tracking_id'] ?? null,
                        'status' => 'successful',
                        'order_id' => $responseArray['order_id'],
                        'currency' => $invoice->currency ?? 'INR',
                        'payment_date' => $trans_date ?? now(),
                        'pg_result' => 'Success',
                        'pg_response_json' => json_encode($responseArray),
                    ]);
                }
                
                // Update poster payment status
                $poster->update(['payment_status' => 'successful']);
                
                Log::info('Poster CCAvenue Payment Success via PaymentGatewayController', [
                    'poster_id' => $poster->id,
                    'tin_no' => $poster->tin_no,
                    'invoice_id' => $invoice->id,
                    'amount' => $responseArray['mer_amount'],
                    'transaction_id' => $responseArray['tracking_id'] ?? null,
                ]);
                
                // Send thank you email after payment confirmation to both lead author and poster presenter
                try {
                    // Refresh poster to ensure we have latest data
                    $poster->refresh();
                    
                    $paymentDetails = [
                        'transaction_id' => $responseArray['tracking_id'] ?? null,
                        'payment_method' => $responseArray['payment_mode'] ?? 'CCAvenue',
                        'amount' => $responseArray['mer_amount'],
                        'currency' => $invoice->currency ?? 'INR',
                    ];
                    
                    Log::info('Poster Payment: Preparing to send emails', [
                        'poster_id' => $poster->id,
                        'tin_no' => $poster->tin_no,
                        'lead_email' => $poster->lead_email,
                        'pp_email' => $poster->pp_email,
                    ]);
                    
                    // Send email to lead author
                    if ($poster->lead_email) {
                        Log::info('Poster Payment: Sending email to lead author', [
                            'email' => $poster->lead_email,
                        ]);
                        Mail::to($poster->lead_email)
                            ->bcc(['test.interlinks@gmail.com'])
                            ->send(new \App\Mail\PosterMail($poster, 'payment_thank_you', $invoice, $paymentDetails));
                        Log::info('Poster Payment: Email sent to lead author', [
                            'email' => $poster->lead_email,
                        ]);
                    } else {
                        Log::warning('Poster Payment: No lead email found', [
                            'poster_id' => $poster->id,
                        ]);
                    }
                    
                    // Send email to poster presenter (if different from lead author)
                    if ($poster->pp_email && $poster->pp_email !== $poster->lead_email) {
                        Log::info('Poster Payment: Sending email to poster presenter', [
                            'email' => $poster->pp_email,
                        ]);
                        Mail::to($poster->pp_email)
                            ->bcc(['test.interlinks@gmail.com'])
                            ->send(new \App\Mail\PosterMail($poster, 'payment_thank_you', $invoice, $paymentDetails));
                        Log::info('Poster Payment: Email sent to poster presenter', [
                            'email' => $poster->pp_email,
                        ]);
                    } elseif ($poster->pp_email && $poster->pp_email === $poster->lead_email) {
                        Log::info('Poster Payment: Skipping poster presenter email (same as lead author)', [
                            'email' => $poster->pp_email,
                        ]);
                    } else {
                        Log::warning('Poster Payment: No poster presenter email found', [
                            'poster_id' => $poster->id,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send poster payment thank you email', [
                        'poster_id' => $poster->id,
                        'tin_no' => $poster->tin_no,
                        'lead_email' => $poster->lead_email ?? 'unknown',
                        'pp_email' => $poster->pp_email ?? 'unknown',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Don't fail the payment if email fails
                }
                
                // Redirect to poster success page
                return redirect()
                    ->route('poster.success', ['tin_no' => $poster->tin_no])
                    ->with('success', 'Payment successful! Your registration is complete.');
            }

            // Check if this is a startup zone invoice FIRST (before any other processing)
            // This helps us redirect correctly even if invoice is not found
            $isStartupZone = false;
            $application = null;
            $applicationId = session('payment_application_id');

            if ($invoice && $invoice->application_id) {
                $application = Application::find($invoice->application_id);
                if ($application && $application->application_type === 'startup-zone') {
                    $isStartupZone = true;
                    $applicationId = $application->application_id;  // Update from invoice if found
                }
            } elseif ($applicationId) {
                // Try to get application from session application_id
                $application = Application::where('application_id', $applicationId)
                    ->where('application_type', 'startup-zone')
                    ->first();
                if ($application) {
                    $isStartupZone = true;
                }
            }

            // If invoice not found, check if it's a poster payment
            if (!$invoice) {
                // Check if this is a poster payment (order_id format: TIN-BTS2026-PSTR-123456_timestamp)
                // Extract TIN from order_id (remove timestamp part after underscore)
                $possibleTinNo = $order_id;
                if (strpos($order_id, '_') !== false) {
                    $possibleTinNo = explode('_', $order_id)[0];
                }
                
                if (strpos($possibleTinNo, 'TIN-BTS2026-PSTR-') === 0 || strpos($possibleTinNo, 'TIN-BTS') === 0) {
                    $poster = \App\Models\Poster::where('tin_no', $possibleTinNo)->first();
                    
                    if ($poster) {
                        // Create invoice if it doesn't exist (shouldn't happen, but safety check)
                        $invoice = Invoice::firstOrCreate(
                            ['invoice_no' => $possibleTinNo, 'type' => 'poster_registration'],
                            [
                                'poster_reg_id' => $poster->id,
                                'currency' => $poster->currency ?? ($poster->nationality === 'India' ? 'INR' : 'USD'),
                                'amount' => (float) ($responseArray['mer_amount'] ?? $poster->total_amount),
                                'price' => $poster->base_amount ?? ($responseArray['mer_amount'] ?? $poster->total_amount),
                                'gst' => $poster->gst_amount ?? 0,
                                'processing_charges' => $poster->processing_fee ?? 0,
                                'total_final_price' => (float) ($responseArray['mer_amount'] ?? $poster->total_amount),
                                'amount_paid' => 0,
                                'pending_amount' => (float) ($responseArray['mer_amount'] ?? $poster->total_amount),
                                'payment_status' => 'unpaid',
                            ]
                        );
                        
                        // Now handle the payment update (same as above)
                        if ($responseArray['order_status'] == 'Success') {
                            // Update invoice
                            $invoice->update([
                                'payment_status' => 'paid',
                                'amount_paid' => (float) $responseArray['mer_amount'],
                                'pending_amount' => 0,
                                'updated_at' => now(),
                            ]);
                            
                            // Find or create payment record
                            $payment = Payment::where('order_id', $responseArray['order_id'])
                                ->where('invoice_id', $invoice->id)
                                ->first();
                            
                            if ($payment) {
                                $payment->update([
                                    'status' => 'successful',
                                    'amount_paid' => (float) $responseArray['mer_amount'],
                                    'amount_received' => (float) $responseArray['mer_amount'],
                                    'transaction_id' => $responseArray['tracking_id'] ?? null,
                                    'pg_result' => 'Success',
                                    'payment_method' => $responseArray['payment_mode'] ?? 'CCAvenue',
                                    'payment_date' => $trans_date ?? now(),
                                    'pg_response_json' => json_encode($responseArray),
                                ]);
                            } else {
                                Payment::create([
                                    'invoice_id' => $invoice->id,
                                    'payment_method' => $responseArray['payment_mode'] ?? 'CCAvenue',
                                    'amount' => (float) $responseArray['mer_amount'],
                                    'amount_paid' => (float) $responseArray['mer_amount'],
                                    'amount_received' => (float) $responseArray['mer_amount'],
                                    'transaction_id' => $responseArray['tracking_id'] ?? null,
                                    'status' => 'successful',
                                    'order_id' => $responseArray['order_id'],
                                    'currency' => $invoice->currency ?? 'INR',
                                    'payment_date' => $trans_date ?? now(),
                                    'pg_result' => 'Success',
                                    'pg_response_json' => json_encode($responseArray),
                                ]);
                            }
                            
                            // Update poster payment status
                            $poster->update(['payment_status' => 'successful']);
                            
                            Log::info('Poster CCAvenue Payment Success (invoice created)', [
                                'poster_id' => $poster->id,
                                'tin_no' => $poster->tin_no,
                                'invoice_id' => $invoice->id,
                                'amount' => $responseArray['mer_amount'],
                                'transaction_id' => $responseArray['tracking_id'] ?? null,
                            ]);
                            
                            // Redirect to poster success page
                            return redirect()
                                ->route('poster.success', ['tin_no' => $poster->tin_no])
                                ->with('success', 'Payment successful! Your registration is complete.');
                        }
                    }
                }
                
                Log::error('CCAvenue Success: Invoice not found', [
                    'order_id' => $order_id,
                    'response' => $responseArray,
                    'is_startup_zone' => $isStartupZone,
                    'application_id' => $applicationId,
                    'is_poster' => (strpos($order_id, 'TIN-BTS2026-PSTR-') === 0)
                ]);

                // If startup zone, redirect to confirmation page
                if ($isStartupZone && $applicationId) {
                    return redirect()
                        ->route('startup-zone.confirmation', $applicationId)
                        ->with('error', 'Invoice not found. Please contact support.')
                        ->with('payment_response', $responseArray);
                }

                return redirect()
                    ->route('payment.lookup')
                    ->with('error', 'Invoice not found. Please contact support.');
            }

            // update the invoice table with the status as paid
            if ($responseArray['order_status'] == 'Success') {
                // Generate PIN number if not already set (for startup zone)
                if (!$invoice->pin_no && $isStartupZone && $application) {
                    $pinNo = $this->generatePinNo();
                    $invoice->pin_no = $pinNo;
                }
                
                $invoice->update([
                    'payment_status' => 'paid',
                    'amount_paid' => $responseArray['mer_amount'],
                    'updated_at' => now(),
                    'pending_amount' => 0,
                    'currency' => 'INR',
                ]);

                // Create payment record for startup zone (only after payment is completed)
                if ($isStartupZone && $application) {
                    // Check if payment record already exists (from webhook or previous attempt)
                    $payment = Payment::where('order_id', $responseArray['order_id'])
                        ->where('invoice_id', $invoice->id)
                        ->first();

                    if ($payment) {
                        // Update existing payment record
                        $payment->update([
                            'payment_method' => $responseArray['payment_mode'] ?? 'CCAvenue',
                            'amount' => $responseArray['mer_amount'],
                            'amount_paid' => $responseArray['mer_amount'],
                            'amount_received' => $responseArray['mer_amount'],
                            'transaction_id' => $responseArray['tracking_id'] ?? null,
                            'pg_result' => $responseArray['order_status'],
                            'track_id' => $responseArray['tracking_id'] ?? null,
                            'pg_response_json' => json_encode($responseArray),
                            'payment_date' => $trans_date ?? now(),
                            'status' => 'successful',
                        ]);
                    } else {
                        // Create new payment record (payment records are only created after payment completion)
                        Payment::create([
                            'invoice_id' => $invoice->id,
                            'payment_method' => $responseArray['payment_mode'] ?? 'CCAvenue',
                            'amount' => $responseArray['mer_amount'],
                            'amount_paid' => $responseArray['mer_amount'],
                            'amount_received' => $responseArray['mer_amount'],
                            'transaction_id' => $responseArray['tracking_id'] ?? null,
                            'pg_result' => $responseArray['order_status'],
                            'track_id' => $responseArray['tracking_id'] ?? null,
                            'pg_response_json' => json_encode($responseArray),
                            'payment_date' => $trans_date ?? now(),
                            'currency' => 'INR',
                            'status' => 'successful',
                            'order_id' => $responseArray['order_id'],
                            'user_id' => $application->user_id ?? null,
                        ]);
                    }

                    Log::info('Startup Zone CCAvenue Payment Success', [
                        'application_id' => $application->application_id,
                        'invoice_no' => $invoice->invoice_no,
                        'amount' => $responseArray['mer_amount'],
                        'transaction_id' => $responseArray['tracking_id'] ?? null,
                    ]);

                    // Send thank you email after payment confirmation
                    try {
                        $contact = \App\Models\EventContact::where('application_id', $application->id)->first();
                        $application->load(['country', 'state', 'eventContact']);
                        
                        $userEmail = $contact && $contact->email ? $contact->email : $application->company_email;
                        
                        if ($userEmail) {
                            $paymentDetails = [
                                'transaction_id' => $responseArray['tracking_id'] ?? null,
                                'payment_method' => $responseArray['payment_mode'] ?? 'CCAvenue',
                                'amount' => $responseArray['mer_amount'],
                                'currency' => 'INR',
                            ];
                            
                            Mail::to($userEmail)->send(new \App\Mail\StartupZoneMail($application, 'payment_thank_you', $invoice, $contact, $paymentDetails));
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to send payment thank you email', [
                            'application_id' => $application->application_id,
                            'email' => $userEmail ?? 'unknown',
                            'error' => $e->getMessage()
                        ]);
                        // Don't fail the payment if email fails
                    }
                    

                    // Redirect to startup zone confirmation with payment response - MUST RETURN HERE
                    return redirect()
                        ->route('startup-zone.confirmation', $application->application_id)
                        ->with('success', 'Payment successful!')
                        ->with('payment_response', $responseArray);
                } else {
                    // For other invoice types, send extra requirements mail
                    $service = new ExtraRequirementsMailService();
                    $data = $service->prepareMailData($order_id);
                    $email = $data['billingEmail'];

                    Mail::to($email)
                        ->bcc(['test.interlinks@gmail.com'])
                        ->send(new ExtraRequirementsMail($data));
                }
            }

            // check the application_id from the invoice and theen from the application use user_id to authenticate the user
            // Only authenticate for non-startup-zone invoices
            // IMPORTANT: If it's startup zone, we should have already returned above
            if (!$isStartupZone && $invoice) {
                // check if the invoices doesn't have co_exhibitorID
                if ($invoice->co_exhibitorID) {
                    // If co_exhibitor_id is present, authenticate as co-exhibitor user only
                    $coExhibitor = \DB::table('co_exhibitors')->where('id', $invoice->co_exhibitorID)->first();
                    Log::info('CoExhibitor ID: ' . $invoice->co_exhibitorID);
                    Log::info('CoExhibitor Details: ' . json_encode($coExhibitor));
                    if ($coExhibitor) {
                        $userId = $coExhibitor->user_id;
                        if (auth()->check() && auth()->id() != $userId) {
                            auth()->logout();
                        }
                        Auth::loginUsingId($userId);
                    }
                } else {
                    // Otherwise, authenticate as main exhibitor (application user)
                    $applicationId = $invoice->application_id;
                    $application = \DB::table('applications')->where('id', $applicationId)->first();
                    if ($application) {
                        $userId = $application->user_id;
                        if (auth()->check() && auth()->id() != $userId) {
                            auth()->logout();
                        }
                        Auth::loginUsingId($userId);
                    }
                    Log::info('Application ID: ' . $applicationId);
                    Log::info('Application User ID: ' . $userId);
                }

                // put in session that paymeent is successful
                session(['payment_success' => true, 'invoice_no' => $order_id, 'payment_message' => 'Payment is successful.']);
                return redirect()->route('payment.lookup');
            }

            // IMPORTANT: Startup zone should have already redirected above (line 529-531)
            // This is a safety fallback in case something went wrong
            if ($isStartupZone) {
                // Try to get application from various sources
                if ($application && $application->application_id) {
                    return redirect()
                        ->route('startup-zone.confirmation', $application->application_id)
                        ->with('success', 'Payment successful!')
                        ->with('payment_response', $responseArray);
                } elseif ($applicationId) {
                    // Try to get from session application_id
                    $application = Application::where('application_id', $applicationId)
                        ->where('application_type', 'startup-zone')
                        ->first();
                    if ($application) {
                        return redirect()
                            ->route('startup-zone.confirmation', $application->application_id)
                            ->with('success', 'Payment successful!')
                            ->with('payment_response', $responseArray);
                    }
                } elseif ($invoice && $invoice->application_id) {
                    // Try to get from invoice
                    $application = Application::find($invoice->application_id);
                    if ($application && $application->application_type === 'startup-zone' && $application->application_id) {
                        return redirect()
                            ->route('startup-zone.confirmation', $application->application_id)
                            ->with('success', 'Payment successful!')
                            ->with('payment_response', $responseArray);
                    }
                }
            }
        } elseif (isset($responseArray)) {
            // update the table with failed payment details
            if (!empty($responseArray['trans_date'])) {
                $trans_date = Carbon::createFromFormat('d/m/Y H:i:s', $responseArray['trans_date'])->format('Y-m-d H:i:s');
            } else {
                $trans_date = now();
            }

            \DB::table('payment_gateway_response')
                ->where('order_id', $responseArray['order_id'])
                ->update([
                    'amount' => $responseArray['mer_amount'] ?? 0,
                    'transaction_id' => $responseArray['tracking_id'] ?? null,
                    'payment_method' => $responseArray['payment_mode'] ?? null,
                    'trans_date' => $trans_date,
                    'reference_id' => $responseArray['bank_ref_no'] ?? null,
                    'response_json' => json_encode($responseArray),
                    'status' => 'Failed',
                    'updated_at' => now(),
                ]);

            // order_id
            $order_id = explode('_', $responseArray['order_id'])[0];

            // dd($order_id);



            // Find invoice for failure handling
            $invoice = Invoice::where('invoice_no', $order_id)->first();

            // Check if this is a startup zone invoice
            $isStartupZone = false;
            $application = null;
            if ($invoice && $invoice->application_id) {
                $application = Application::find($invoice->application_id);
                if ($application && $application->application_type === 'startup-zone') {
                    $isStartupZone = true;
                }
            }

            // dd($invoice);

            // If invoice not found, check session
            if (!$invoice) {
                $applicationId = session('payment_application_id');
                if ($applicationId) {
                    return redirect()
                        ->route('startup-zone.payment', $applicationId)
                        ->with('error', 'Payment failed. Please try again.');
                }
            }

            echo "isStartupZone: " . $isStartupZone;
            echo "application: " . $application;

            exit;

            if ($isStartupZone && $application) {
                // Create failed payment record for startup zone
                if ($invoice) {
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'payment_method' => $responseArray['payment_mode'] ?? 'CCAvenue',
                        'amount' => $responseArray['mer_amount'] ?? $invoice->total_final_price,
                        'amount_paid' => 0,
                        'amount_received' => 0,
                        'transaction_id' => $responseArray['tracking_id'] ?? null,
                        'pg_result' => $responseArray['order_status'] ?? 'Failed',
                        'track_id' => $responseArray['tracking_id'] ?? null,
                        'pg_response_json' => json_encode($responseArray),
                        'payment_date' => $trans_date ?? now(),
                        'currency' => 'INR',
                        'status' => 'failed',
                        'rejection_reason' => $responseArray['failure_message'] ?? 'Payment failed',
                        'order_id' => $responseArray['order_id'],
                        'user_id' => $application->user_id ?? null,
                    ]);
                }

                return redirect()
                    ->route('startup-zone.payment', $application->application_id)
                    ->with('error', 'Payment failed. Please try again.')
                    ->with('payment_response', $responseArray);
            }

            // For non-startup-zone invoices or if invoice not found
            if ($invoice) {
                return redirect('/payment/' . $order_id . '?status=failed');
            } else {
                // If invoice not found, redirect to a safe page
                return redirect()
                    ->route('payment.lookup')
                    ->with('error', 'Payment failed. Invoice not found.');
            }

            // return to /payment/{id}
        } else {
            // No response array or unexpected format
            Log::warning('CCAvenue Success: Unexpected response format', [
                'has_response_array' => isset($responseArray),
                'response_array' => $responseArray ?? null,
                'request_data' => $request->all(),
            ]);

            // Try to get invoice from session
            $invoiceNo = session('invoice_no');
            if ($invoiceNo) {
                $invoice = Invoice::where('invoice_no', $invoiceNo)->first();
                if ($invoice && $invoice->application_id) {
                    $application = Application::find($invoice->application_id);
                    if ($application && $application->application_type === 'startup-zone') {
                        return redirect()
                            ->route('startup-zone.payment', $application->application_id)
                            ->with('error', 'Payment response incomplete. Please try again or contact support.');
                    }
                }
            }

            // If we have response array but no order_id, try to update what we can
            if (isset($responseArray) && isset($responseArray['order_id'])) {
                \DB::table('payment_gateway_response')
                    ->where('order_id', $responseArray['order_id'])
                    ->update([
                        'status' => 'Failed',
                        'updated_at' => now(),
                    ]);
            }
        }

        // Final fallback redirect
        return redirect()
            ->route('exhibitor.orders')
            ->with('error', 'Payment response incomplete. Please contact support if payment was deducted.');
    }

    /**
     * Display the invoice email for testing purposes.
     */
    public function showInvoiceEmail($invoiceId)
    {
        $service = new ExtraRequirementsMailService();
        $data = $service->prepareMailData($invoiceId);
        $mail = new ExtraRequirementsMail($data);
        return $mail->render();

        //  dd($data);

        // Log::info('Invoice email data: ' . json_encode($data));
        return view('emails.extra_requirements_mail', compact('data'));
    }

    public function sendInvoice($invoiceId)
    {
        $start = microtime(true);
        $service = new ExtraRequirementsMailService();
        $data = $service->prepareMailData($invoiceId);

        // Log::info('Invoice email data: ' . json_encode($data));
        return response()->json($data);
        // Mail::to($toEmail)->send(new ExtraRequirementsMail($invoiceId));
        $email = $data['billingEmail'];
        $email = 'manish.sharma@interlinks.in';
        Mail::to($email)->send(new ExtraRequirementsMail($data));
        $end = microtime(true);
        return response()->json(['message' => 'Invoice email sent successfully!' . $end - $start]);
    }

    /**
     * Handle CCAvenue webhook callback
     * Receives payment status updates from CCAvenue
     */
    public function ccAvenueWebhook(Request $request)
    {
        try {
            // Log incoming webhook for debugging
            Log::info('CCAvenue Webhook Received', [
                'request_data' => $request->all(),
                'ip' => $request->ip(),
            ]);

            // Extract webhook parameters
            $orderId = $request->input('order_id');
            $trackingId = $request->input('tracking_id');
            $bankRefNo = $request->input('bank_ref_no');
            $orderStatus = $request->input('order_status');
            $amount = $request->input('amount');
            $paymentMode = $request->input('payment_mode');
            $cardName = $request->input('card_name');
            $statusCode = $request->input('status_code');
            $statusMessage = $request->input('status_message');
            $currency = $request->input('currency');
            $failureMessage = $request->input('failure_message');

            if (!$orderId) {
                Log::error('CCAvenue Webhook: Missing order_id');
                return response()->json(['error' => 'Missing order_id'], 400);
            }

            // Extract TIN from order_id (format: BTS-2026-EXH-123456_timestamp)
            $ccAvenueService = new CcAvenueService();
            $tinNumber = $ccAvenueService->extractTinFromOrderId($orderId);

            // Find application by TIN (application_id)
            $application = Application::where('application_id', $tinNumber)->first();

            if (!$application) {
                Log::error('CCAvenue Webhook: Application not found', [
                    'tin_number' => $tinNumber,
                    'order_id' => $orderId,
                ]);
                // Still update payment_gateway_response even if application not found
            }

            // Find invoice by application_id or by invoice_no from order_id
            $invoice = null;
            if ($application) {
                // Try to find invoice by application_id first
                $invoice = Invoice::where('application_id', $application->id)
                    ->where('currency', $currency ?? 'INR')
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            // If invoice not found by application, try to extract from order_id
            // Order ID format might be: {invoice_no}_{timestamp} or {application_id}_{timestamp}
            if (!$invoice && strpos($orderId, '_') !== false) {
                $possibleInvoiceNo = explode('_', $orderId)[0];
                $invoice = Invoice::where('invoice_no', $possibleInvoiceNo)->first();
            }

            // Store all webhook data
            $webhookData = $request->all();

            // Begin transaction
            DB::beginTransaction();

            try {
                // Update or create payment_gateway_response record
                $paymentResponse = DB::table('payment_gateway_response')
                    ->where('order_id', $orderId)
                    ->first();

                $updateData = [
                    'transaction_id' => $trackingId,
                    'reference_id' => $bankRefNo,
                    'status' => $orderStatus === 'Success' ? 'Success' : ($orderStatus === 'Failure' ? 'Failed' : 'Pending'),
                    'amount_received' => $amount,
                    'payment_method' => $paymentMode,
                    'response_json' => json_encode($webhookData),
                    'bank_ref_no' => $bankRefNo,
                    'trans_date' => now()->format('Y-m-d H:i:s'),
                    'updated_at' => now(),
                ];

                if ($paymentResponse) {
                    DB::table('payment_gateway_response')
                        ->where('id', $paymentResponse->id)
                        ->update($updateData);
                } else {
                    // Create new record if not exists
                    $updateData['order_id'] = $orderId;
                    $updateData['currency'] = $currency ?? 'INR';
                    $updateData['gateway'] = 'CCAvenue';
                    $updateData['amount'] = $amount;
                    $updateData['email'] = $request->input('billing_email', '');
                    $updateData['created_at'] = now();
                    DB::table('payment_gateway_response')->insert($updateData);
                }

                // Update invoice if found
                if ($invoice && $orderStatus === 'Success') {
                    $invoice->update([
                        'payment_status' => 'paid',
                        'amount_paid' => $amount,
                        'pending_amount' => 0,
                        'updated_at' => now(),
                    ]);

                    // Create or update payment record
                    $payment = Payment::where('invoice_id', $invoice->id)
                        ->where('transaction_id', $trackingId)
                        ->first();

                    if ($payment) {
                        $payment->update([
                            'status' => 'successful',
                            'amount_paid' => $amount,
                            'amount_received' => $amount,
                            'payment_date' => now(),
                            'pg_response_json' => is_array($webhookData) ? json_encode($webhookData) : $webhookData,
                            'updated_at' => now(),
                        ]);
                    } else {
                        Payment::create([
                            'invoice_id' => $invoice->id,
                            'payment_method' => $paymentMode ?? 'CCAvenue',
                            'amount' => $amount,
                            'amount_paid' => $amount,
                            'amount_received' => $amount,
                            'transaction_id' => $trackingId ?? $orderId,
                            'pg_result' => $orderStatus,
                            'track_id' => $trackingId,
                            'pg_response_json' => is_array($webhookData) ? json_encode($webhookData) : $webhookData,
                            'payment_date' => now(),
                            'currency' => $currency ?? 'INR',
                            'status' => 'successful',
                            'order_id' => $orderId,
                            'user_id' => $application->user_id ?? null,
                        ]);
                    }

                    Log::info('CCAvenue Webhook: Payment processed successfully', [
                        'order_id' => $orderId,
                        'tin_number' => $tinNumber,
                        'invoice_id' => $invoice->id,
                        'amount' => $amount,
                    ]);
                } elseif ($invoice && $orderStatus === 'Failure') {
                    // Log failed payment
                    $invoice->update([
                        'payment_status' => 'unpaid',
                        'updated_at' => now(),
                    ]);

                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'payment_method' => $paymentMode ?? 'CCAvenue',
                        'amount' => $amount,
                        'amount_paid' => 0,
                        'amount_received' => 0,
                        'transaction_id' => $trackingId ?? $orderId,
                        'pg_result' => $orderStatus,
                        'track_id' => $trackingId,
                        'pg_response_json' => is_array($webhookData) ? json_encode($webhookData) : $webhookData,
                        'payment_date' => now(),
                        'currency' => $currency ?? 'INR',
                        'status' => 'failed',
                        'rejection_reason' => $failureMessage ?? $statusMessage ?? 'Payment failed',
                        'order_id' => $orderId,
                        'user_id' => $application->user_id ?? null,
                    ]);

                    Log::warning('CCAvenue Webhook: Payment failed', [
                        'order_id' => $orderId,
                        'tin_number' => $tinNumber,
                        'failure_message' => $failureMessage,
                    ]);
                }

                DB::commit();

                // Return success response to CCAvenue
                return response()->json(['status' => 'success'], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('CCAvenue Webhook: Database update failed', [
                    'error' => $e->getMessage(),
                    'order_id' => $orderId,
                    'trace' => $e->getTraceAsString(),
                ]);
                return response()->json(['error' => 'Processing failed'], 500);
            }
        } catch (\Exception $e) {
            Log::error('CCAvenue Webhook: Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * List all CCAvenue transactions (Admin page)
     */
    public function listTransactions(Request $request)
    {
        $query = DB::table('payment_gateway_response')
            ->where('gateway', 'CCAvenue')
            ->orderBy('created_at', 'desc');

        // Search filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q
                    ->where('order_id', 'like', "%{$search}%")
                    ->orWhere('transaction_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('reference_id', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        $perPage = $request->input('per_page', 20);
        $transactions = $query->paginate($perPage);
        $transactions->appends($request->query());

        // Extract TIN from order_id and fetch application details
        $ccAvenueService = new CcAvenueService();
        foreach ($transactions as $transaction) {
            $tinNumber = $ccAvenueService->extractTinFromOrderId($transaction->order_id);
            $transaction->tin_number = $tinNumber;

            // Find application
            $application = Application::where('application_id', $tinNumber)->first();
            if ($application) {
                $transaction->application_id = $application->application_id;
                $transaction->company_name = $application->company_name;
                $transaction->application = $application;
            }
        }

        return view('admin.ccavenue-transactions', compact('transactions'));
    }

    /**
     * Get transaction details for modal
     */
    public function getTransactionDetails($id)
    {
        $transaction = DB::table('payment_gateway_response')
            ->where('id', $id)
            ->where('gateway', 'CCAvenue')
            ->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        // Extract TIN and get application details
        $ccAvenueService = new CcAvenueService();
        $tinNumber = $ccAvenueService->extractTinFromOrderId($transaction->order_id);
        $application = Application::where('application_id', $tinNumber)->first();

        $transaction->tin_number = $tinNumber;
        if ($application) {
            $transaction->application_id = $application->application_id;
            $transaction->company_name = $application->company_name;
        }

        return response()->json([
            'success' => true,
            'transaction' => $transaction,
        ]);
    }

    /**
     * Generate unique PIN number using PIN_NO_PREFIX
     * Format: PRN-BTS-2026-EXHP-XXXXXX (6-digit random number)
     */
    private function generatePinNo()
    {
        $prefix = config('constants.PIN_NO_PREFIX');
        $maxAttempts = 100; // Prevent infinite loop
        $attempts = 0;
        
        while ($attempts < $maxAttempts) {
            // Generate 6-digit random number
            $randomNumber = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $pinNo = $prefix . $randomNumber;
            $attempts++;
            
            // Check if it already exists in invoices table
            if (!\App\Models\Invoice::where('pin_no', $pinNo)->exists()) {
                return $pinNo;
            }
        }
        
        // If we've tried too many times, use timestamp-based fallback
        $timestamp = substr(time(), -6); // Last 6 digits of timestamp
        $pinNo = $prefix . $timestamp;
        if (!\App\Models\Invoice::where('pin_no', $pinNo)->exists()) {
            return $pinNo;
        }
        
        // Last resort: use microtime
        $microtime = substr(str_replace('.', '', microtime(true)), -6);
        return $prefix . $microtime;
    }
}
