<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\BillingDetail;
use App\Models\RequirementsOrder;
use App\Models\Application;
use App\Models\Payment;
use App\Mail\ExtraRequirementsMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\ExtraRequirementsMailService;
use App\Services\CcAvenueService;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\RequirementsBilling;
use Barryvdh\DomPDF\Facade\Pdf;




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
        $this->merchantId  = '7700';
        $this->accessCode  = 'AVJS71ME17AS68SJSA';
        $this->workingKey  = '7AF39D44C8DC0DE71EDD69C288C96694';
        $this->redirectUrl = 'https://bengalurutechsummit.com/bts-portal/public/payment/ccavenue-success';
        $this->cancelUrl   = 'https://bengalurutechsummit.com/bts-portal/public/payment/ccavenue-success';
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
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = bin2hex(openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector));
        return $encryptedText;
    }

    private function decrypt($encryptedText, $key)
    {
        $key = pack('H*', md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = pack("H*", $encryptedText);
        return openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
    }

    public function ccAvenuePayment($orderID, Request $request)
    {
        if (!$orderID) {
            return redirect()->route('exhibitor.orders');
        }

        // get the invoice details from the Invoice model where invoice_no = $id
        $invoice = Invoice::where('invoice_no', $orderID)->first();

        //if invoice not found then redirect to route exhibitor.orders
        if (!$invoice) {
            return redirect()->route('exhibitor.orders');
        }

        //if invoice is already paid then redirect to route exhibitor.orders
        if ($invoice->payment_status == 'paid') {
            return redirect()->route('exhibitor.orders');
        }

        // Get application to extract application_id (TIN) for order_id format
        $application = null;
        if ($invoice->application_id) {
            $application = Application::find($invoice->application_id);
        }

        // Fetch billing detail - handle startup zone differently
        $billingDetail = null;
        $isStartupZone = false;
        
        if ($invoice->application_id) {
            $application = Application::find($invoice->application_id);
            if ($application && $application->application_type === 'startup-zone') {
                $isStartupZone = true;
                // For startup zone, get billing from EventContact
                $eventContact = \App\Models\EventContact::where('application_id', $invoice->application_id)->first();
                if ($eventContact && $application) {
                    $billingDetail = (object) [
                        'billing_company' => $application->company_name ?? '',
                        'contact_name' => ($eventContact->salutation ?? '') . ' ' . ($eventContact->first_name ?? '') . ' ' . ($eventContact->last_name ?? ''),
                        'email' => $eventContact->email ?? $application->company_email ?? '',
                        'phone' => $eventContact->contact_number ?? $application->landline ?? '',
                        'address' => $application->address ?? '',
                        'country_id' => $application->country_id ?? null,
                        'state_id' => $application->state_id ?? null,
                        'postal_code' => $application->postal_code ?? '',
                        'state' => $application->state_id ? (object)['name' => optional(\App\Models\State::find($application->state_id))->name] : (object)['name' => ''],
                        'country' => $application->country_id ? (object)[
                            'name' => optional(\App\Models\Country::find($application->country_id))->name,
                            'states' => collect()
                        ] : (object)['name' => '', 'states' => collect()],
                        'gst' => $application->gst_no ?? null,
                        'pan_no' => $application->pan_no ?? null,
                        'city_id' => $application->city_id ?? null,
                    ];
                }
            }
        }
        
        // For non-startup-zone, use BillingDetail
        if (!$billingDetail) {
            $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();
        }

        $requirementsBilling = \DB::table('requirements_billings')
            ->where('invoice_id', $invoice->id)
            ->first();

            //'phone' => $requirementsBilling->billing_phone, in this 91-9801217815 pass only 9801217815





        //if billingDetail 
        if ($requirementsBilling) {
            $billingDetail = (object) [
                'billing_company' => $requirementsBilling->billing_company,
                'contact_name' => $requirementsBilling->billing_name,
                'email' => $requirementsBilling->billing_email,
                // 'phone' => $requirementsBilling->billing_phone,
                            'phone' => preg_replace('/^91-/', '', $requirementsBilling->billing_phone),

                'address' => $requirementsBilling->billing_address,
                'country_id' => $requirementsBilling->country_id,
                'state_id' => $requirementsBilling->state_id,
                'postal_code' => $requirementsBilling->zipcode,
                // Add a dummy state object to mimic $billingDetail->state->name
                'state' => (object)[
                    'name' => optional(\App\Models\State::find($requirementsBilling->state_id))->name
                ],
                // Add a dummy country object to mimic $billingDetail->country->name
                'country' => (object)[
                    'name' => optional(\App\Models\Country::find($requirementsBilling->country_id))->name,
                    'states' => ($requirementsBilling->country_id)
                        ? optional(\App\Models\Country::with('states')->find($requirementsBilling->country_id))->states ?? collect()
                        : collect(),
                ],
                'gst' => $requirementsBilling->gst_no ?? null,
                'pan_no' => $requirementsBilling->pan_no ?? null,
                'city_id' => $requirementsBilling->billing_city ?? $billingDetail->city_id,

            ];
        }


        // Generate order_id with TIN prefix format: {application_id}_{timestamp}
        // If application exists, use application_id (TIN), otherwise use invoice_no
        $tinPrefix = $application && $application->application_id 
            ? $application->application_id 
            : $orderID;
        $orderIdWithTimestamp = $tinPrefix . '_' . time();

        $data = [
            'merchant_id' => $this->merchantId,
            'order_id' => $orderIdWithTimestamp,
            'currency' => 'INR',
            'amount' => $invoice->total_final_price,
            'redirect_url' => $this->redirectUrl,
            'cancel_url' => $this->cancelUrl,
            'language' => 'EN',
            'billing_name' => $billingDetail->contact_name,
            'billing_address' => $billingDetail->address,
            'billing_city' => $billingDetail->city_id,
            'billing_state' => $billingDetail->state->name,
            'billing_zip' => $billingDetail->postal_code,
            'billing_country' => $billingDetail->country->name,
            'billing_tel' => preg_replace('/^.*-/', '', $billingDetail->phone),
            'billing_email' => $billingDetail->email,
        ];


        dd($data);

        $merchantData = json_encode($data);

        //insert into payment_gateway_response table
        \DB::table('payment_gateway_response')->insert([
            'merchant_data' => $merchantData,
            'order_id' => $data['order_id'],
            'amount' => $data['amount'],
            'status' => 'Pending',
            'gateway' => 'CCAvenue',
            'currency' => 'INR',
            'email' => $data['billing_email'],
            'user_id' => $application ? $application->user_id : null,
            'created_at' => now(),
        ]);

        // dd($data);

        $queryString = http_build_query($data);
        $encryptedData = $this->encrypt($queryString, $this->workingKey);

        session([
            'invoice_no' => $orderID,
            'payment_user_id' => auth()->check() ? auth()->id() : null,
        ]);

        return view('pgway.ccavenue', compact('encryptedData'));
    }

    //

    public function downloadInvoicePdf($invoiceId)
    {

        $service = new ExtraRequirementsMailService();
        $data = $service->prepareMailData($invoiceId);
        //$mail = new ExtraRequirementsMail($data);
        //render documents.extraOrder to HTML
        $pdf = Pdf::loadView('documents.extraOrder', $data)->setPaper('a3', 'portrait')->set_option('isRemoteEnabled', true);;

        // display the PDF in the browser or download it
        return $pdf->stream('invoice_' . $invoiceId . '.pdf');
        return $pdf->download('OrderConfirmation_' . $data['invoice_Id'] . '.pdf');

        // Or, to display in browser:
        // return $pdf->stream('invoice_' . $invoiceId . '.pdf');
    }


    public function ccAvenueSuccess(Request $request)
    {



        //dd($request->all());
        // Decrypt response
        $workingKey = env('CCAVENUE_WORKING_KEY');
        $encResponse = $request->input("encResp");


        $decryptedResponse = $this->decrypt($encResponse, $this->workingKey);
        parse_str($decryptedResponse, $responseArray);



        //dd($responseArray);
        if ($responseArray['order_status'] == "Success") {
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

            //update the invoice table with the status as paid
            if ($responseArray['order_status'] == "Success") {
                $invoice->update([
                    'payment_status' => 'paid',
                    'amount_paid' => $responseArray['mer_amount'],
                    'updated_at' => now(),
                    'pending_amount' => 0,
                    'currency' => 'INR',
                ]);
                
                // Check if this is a startup zone invoice
                $isStartupZone = false;
                $application = null;
                if ($invoice->application_id) {
                    $application = Application::find($invoice->application_id);
                    if ($application && $application->application_type === 'startup-zone') {
                        $isStartupZone = true;
                    }
                }
                
                if ($isStartupZone) {
                    // For startup zone, payment success is handled by webhook
                    // Just redirect to confirmation page
                    Log::info('Startup Zone CCAvenue Payment Success', [
                        'application_id' => $application->application_id,
                        'invoice_no' => $invoice->invoice_no,
                        'amount' => $responseArray['mer_amount']
                    ]);
                    
                    // Redirect to startup zone confirmation
                    return redirect()->route('startup-zone.confirmation', $application->application_id)
                        ->with('success', 'Payment successful!');
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
            if (!$isStartupZone) {
                //check if the invoices doesn't have co_exhibitorID 
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

                //put in session that paymeent is successful
                session(['payment_success' => true, 'invoice_no' => $order_id, 'payment_message' => 'Payment is successful.']);
                return redirect()->route('exhibitor.orders');
            } else {
                // Startup zone - already redirected above
                return redirect()->route('startup-zone.confirmation', $application->application_id)
                    ->with('success', 'Payment successful!');
            }
            return response()->json($responseArray);
            return redirect('/payment-success');
        } elseif (isset($responseArray)) {
            //update the table with failed payment details
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

            //order_id
            $order_id = explode('_', $responseArray['order_id'])[0];

            // Check if this is a startup zone invoice
            $isStartupZone = false;
            $application = null;
            if ($invoice->application_id) {
                $application = Application::find($invoice->application_id);
                if ($application && $application->application_type === 'startup-zone') {
                    $isStartupZone = true;
                }
            }
            
            if ($isStartupZone && $application) {
                return redirect()->route('startup-zone.payment', $application->application_id)
                    ->with('error', 'Payment failed. Please try again.');
            }
            
            return redirect('/payment/' . $order_id . '?status=failed');

            //return to /payment/{id} 
        } else {
            //update the table with failed payment details
            \DB::table('payment_gateway_response')
                ->where('order_id', $responseArray['order_id'])
                ->update([
                    'status' => 'Failed',
                    'updated_at' => now(),
                ]);
        }

        return redirect('/payment-failed');
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
        $email = "manish.sharma@interlinks.in";
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
                            'pg_response_json' => $webhookData,
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
                            'pg_response_json' => $webhookData,
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
                        'pg_response_json' => $webhookData,
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
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
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
}
