<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\BillingDetail;
use App\Models\RequirementsOrder;
use App\Mail\ExtraRequirementsMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\ExtraRequirementsMailService;
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
        $this->accessCode  = 'AVAX60MC26BE01XAEB';
        $this->workingKey  = 'DBBE266B02508AF7118D4A2598763D69';
        $this->redirectUrl = 'https://portal.semiconindia.org/payment/ccavenue-success';
        $this->cancelUrl   = 'https://portal.semiconindia.org/payment/ccavenue-success';
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

        //fetch the BillingDetail details from the model BillingDetail where application_id = $invoice->application_id
        $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();

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


        $data = [
            'merchant_id' => $this->merchantId,
            'order_id' => $orderID . '_' . time(),
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
                //
                $service = new ExtraRequirementsMailService();
                $data = $service->prepareMailData($order_id);
                $email = $data['billingEmail'];

                Mail::to($email)
                    ->bcc(['semiconindia@mmactiv.com', 'test.interlinks@gmail.com', 'amit.upadhyay@mmactiv.com', 'nitin.chauhan@mmactiv.com'])
                    ->send(new ExtraRequirementsMail($data));
            }

            // check the application_id from the invoice and theen from the application use user_id to authenticate the user

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


            // route to Route::get('/payment/{id}', [PayPalController::class, 'showPaymentForm'])->name('paypal.form'); with invoice_no as $order_id
            //return to /payment/{id}
            //return to /payment/{id} with status=success
            //  return redirect('/payment/' . $order_id . '?status=success');
            //reutn to route exhibitor.orders

            //put in session that paymeent is successful
            session(['payment_success' => true, 'invoice_no' => $order_id, 'payment_message' => 'Payment is successful.']);
            return redirect()->route('exhibitor.orders');
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
}
