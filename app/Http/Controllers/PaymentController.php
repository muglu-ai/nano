<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\EventContact;
use App\Models\RequirementsOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\PaymentVerifiedMailer;
use Illuminate\Support\Facades\Mail;
use App\Mail\Onboarding;
use App\Models\Application;
use App\Mail\PaymentRejectionNotice;
use App\Mail\ExtraRequirementPaymentUpdate;


class PaymentController extends Controller
{
    // display order that need to passed on to payment gateway
    public function showOrder(Request $request)
    {
        $request->validate([
            'application_no' => 'required|exists:invoices,application_no',
        ]);
        $application_no = $request->application_no;
        // get order details from Invoice model
        $order = Invoice::where('application_no', $application_no)->firstOrFail();
        $invoice_id = $order->id;

        $billing = $order->billingDetails;

        $amount = $order['total_final_price'];
        $description =  $order['type'];
        $billing_cust_name = $order->billingDetails->contact_name;
        $billing_cust_email = $order->billingDetails->email;
        $billing_cust_tel = $order->billingDetails->phone;
        $billing_cust_address = $order->billingDetails->address;
        $application_id = $order->application->application_id;


        $payment = Payment::where('id', $invoice_id)->latest()->first();
        //check if the payment is partial or full payment
        // if payment is partial and order is created and payment status is not successful  then fetch the latest payment id
        //else create new payment id



        if ($payment && $payment->status !== 'successful' && $order->payment_status === 'partial') {
            // Fetch the latest payment id
            $payment_id = $payment->id;
        } else {
            // Create new payment id
            $payment = new Payment();
            $payment->invoice_id = $invoice_id;
            $payment->amount = $amount;
            $payment->payment_method = 'online';
            $payment->transaction_id = "SEMI2025" . rand(1000, 9999);
            $payment->status = 'pending';
            $payment->payment_date = null;

            $payment->save();
            $payment_id = $payment->id;
        }

        //store that transaction id in invoice table
        //$order->transaction_id = $payment->transaction_id;
        $amount = $payment->amount;

        //return create_order view
        return view('pay.create_order', compact('amount', 'description', 'billing_cust_name', 'billing_cust_email', 'billing_cust_tel', 'billing_cust_address', 'application_id'));
        // return order details
        return response()->json(['order' => $order]);
    }

    //complete order after payment
    public function Successpayment(Request $request)
    {

        //        dd(
        //            $request->all()
        //        );
        // get order details from request
        //dd($request->all());
        $order = $request->all();
        $request->validate([
            'application_no' => 'required|exists:invoices,application_no',
        ]);
        $application_no = $request->application_no;

        // update order status
        $order = Invoice::where('application_no', $application_no)->firstOrFail();

        //        dd($order);
        //$order->status = 'paid';

        //$order->save();

        //update payments table with payment status as successful
        //amount_received as total_final_price
        // status as successful
        // update_at as now
        $payment = Payment::where('invoice_id', $order->id)->latest()->first();
        $payment->amount_received = $order->total_final_price;
        $payment->status = 'successful';
        $payment->payment_date = now();
        $payment->save();




        $applicationId = $order->application->application_id;


        $exhibitionController = new ExhibitionController();
        $exhibitionController->handlePaymentSuccess($applicationId);

        //send mail that thank you for payment and registration is successful you can now login to your dashboard
        //handle email sending here



        // redirect to exhibitor dashboard with route user.dashboard
        return redirect()->route('user.dashboard');


        return response()->json(['message' => 'Payment successful']);

        // return order details
        return response()->json(['order' => $order]);
    }



    //partial payment update
    //if partial payment is requested then update the payment status to partial
    // update partial payment percentage and total_final_price in invoice table 40% of total price
    public function partialPayment(Request $request)
    {

        // get order details from request
        $order = $request->all();



        //validate whether the invoice id exist in Invoice table from the request
        $request->validate([
            'application_no' => 'required|exists:invoices,application_no',
        ]);
        $application_no = $request->application_no;

        // update order status
        $order = Invoice::where('application_no', $application_no)->firstOrFail();

        // Verify whether the invoice id is present in the application table and the current user is the owner of the invoice
        $application = $order->application;
        if (!$application || $application->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized or invalid invoice'], 403);
        }
        $order->payment_status = 'partial';
        $order->partial_payment_percentage = 40; // Indicates 40% payment
        $order->total_final_price = $order->amount * 0.4; // Calculate 40% of the total amount
        $pending_amount = $order->amount - $order->total_final_price; // Remaining amount after the partial payment
        $order->pending_amount = $pending_amount;
        //update payment_due_date to 30 days from now
        $order->payment_due_date = now()->addDays(30);
        // dd($order->amount, $pending_amount, $order->total_final_price, $order->status, $order->partial_payment);
        $order->save();

        //        dd($invoice_id);
        //call redirectToPayment function with invoice id
        return $this->redirectToPayment($application_no);
        //route to payment with invoice id to make partial payment as post method
        return redirect()->route('payment', ['invoice_id' => $invoice_id])->withMethod('POST');


        // take to payment route
        return redirect()->route('payment');

        // return order details
        return response()->json(['order' => $order]);
    }

    public function fullPayment(Request $request)
    {

        // get order details from request
        $order = $request->all();



        //validate whether the invoice id exist in Invoice table from the request
        $request->validate([
            'application_no' => 'required|exists:invoices,application_no',
        ]);
        $application_no = $request->application_no;

        // update order status
        $order = Invoice::where('application_no', $application_no)->firstOrFail();

        // Verify whether the invoice id is present in the application table and the current user is the owner of the invoice
        $application = $order->application;
        if (!$application || $application->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized or invalid invoice'], 403);
        }
        $order->payment_status = 'unpaid';
        $order->partial_payment_percentage = 0;
        $order->total_final_price = $order->amount; // Calculate 40% of the total amount
        $pending_amount = $order->amount - $order->total_final_price; // Remaining amount after the partial payment
        $order->pending_amount = $pending_amount;
        //update payment_due_date to 30 days from now
        $order->payment_due_date = now()->addDays(30);
        // dd($order->amount, $pending_amount, $order->total_final_price, $order->status, $order->partial_payment);
        $order->save();

        //        dd($invoice_id);
        //call redirectToPayment function with invoice id
        return $this->redirectToPayment($application_no);
        //route to payment with invoice id to make partial payment as post method
        return redirect()->route('payment', ['invoice_id' => $invoice_id])->withMethod('POST');


        // take to payment route
        return redirect()->route('payment');

        // return order details
        return response()->json(['order' => $order]);
    }


    public function redirectToPayment($application_no)
    {
        return response()->make("
        <form id='payment-form' action='" . route('payment', ['application_no' => $application_no]) . "' method='POST'>
            <input type='hidden' name='_token' value='" . csrf_token() . "'>
        </form>
        <script>
            document.getElementById('payment-form').submit();
        </script>
    ", 200, ['Content-Type' => 'text/html']);
    }



     public function updateInvoiceSurcharge($surchargePercentage, $invoiceId)
    {
        // Validate surcharge percentage
        if (!is_numeric($surchargePercentage) || $surchargePercentage < 0) {
            return response()->json(['error' => 'Invalid surcharge percentage'], 400);
        }

        // Find the invoice by ID
        $invoice = Invoice::findOrFail($invoiceId);

        // Update the surcharge percentage
        $invoice->surChargepercentage = $surchargePercentage;

        // Calculate the surcharge amount based on the percentage
        $surchargeAmount = ($invoice->price * $surchargePercentage) / 100;
        $invoice->surCharge = round($surchargeAmount);

        // calculate the gst on amount + surcharge amount
        $gstAmount = ($invoice->price + $surchargeAmount) * 0.18; // Assuming GST is 18%
        $invoice->gst = round($gstAmount);

        // $invoice->currency is INR then 3% processing fee else 9% processing fee
        if ($invoice->currency === 'INR') {
            $processingFee = ($invoice->price + $surchargeAmount + $gstAmount) * 0.03; // 3% processing fee
        } else {
            $processingFee = ($invoice->price + $surchargeAmount + $gstAmount) * 0.09; // 9% processing fee
        }
        $invoice->processing_charges = round($processingFee);

        // Update the total amount after adding surcharge, GST, and processing fee
        $invoice->amount = round($invoice->price + $surchargeAmount + $gstAmount + $processingFee);

        // Save the updated invoice

        // if invoice->currency is USD then convert the amount to INR
        if ($invoice->currency === 'USD') {
            $usdRate = $invoice->usd_rate ?: 88;

            $invoice->int_amount_value = $invoice->amount / $usdRate; // Convert to INR
        }

        $invoice->total_final_price = $invoice->amount; // Update total final price

        $invoice->surChargeReason = $request->surcharge_change_reason ?? null; // Store the reason for surcharge change if provided

        $invoice->save();

    }


    //verify payment receipt from admin dashboard
    // as admin view the payment receipt and verify or reject the payment receipt
    //update that payment status in payment table as verified or rejected
    //on click it is sending following data
    // Verifying payment ID: 36 Status: reject Remarks:  User: Manish
    // ID is from payment table
    // Status is either verified or rejected
    // Remarks is the reason for rejection
    // User is the user who verified or rejected the payment
    public function verifyPayment(Request $request)
    {
        //        dd($request->all());
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'amount_paid' => 'required|numeric|min:0',
            'status' => 'required|in:verified,rejected',
            'remarks' => 'required|string',
            'user' => 'required|string',
            'surcharge_slab' => 'integer|nullable',
            'surcharge_change_reason' => 'nullable|required_if:surcharge_slab,!=,null|string'
        ]);

        Log::info('Verifying payment ID: ' . $request->payment_id . ' Status: ' . $request->status . ' Remarks: ' . $request->remarks . ' User: ' . $request->user);
        //dd($request->all());

        $payment = Payment::findOrFail($request->payment_id);

        if ($payment->verification_status === 'Verified') {
            return response()->json(['message' => 'Payment already verified.'], 400);
        }

        $invoice = Invoice::where('id', $payment->invoice_id)->firstOrFail();


        if ($request->status === 'rejected') {
            $payment->update([
                'status' => 'failed',
                'verification_status' => 'Rejected',
                'remarks' => $request->remarks,
                'verified_by' => $request->user,
                'verified_at' => now(),
            ]);

            if ($invoice->type == 'extra_requirement') {
                $application = Application::where('id', $invoice->application_id)->firstOrFail();

                $eventContact = $application->eventContact ?? 'N/A';

                $eventContactEmail = $eventContact->email ?? 'N/A';
                // Log::info('Invoice ID: ' . $invoice->id . ' Application ID: ' . $invoice->application_id);

                $receiptId = $invoice->invoice_no ?? 'N/A';

                if ($request->status === 'rejected') {
                    $payment->update([
                        'status' => 'failed',
                        'verification_status' => 'Rejected',
                        'remarks' => $request->remarks,
                        'verified_by' => $request->user,
                        'verified_at' => now(),
                    ]);


                    $remarks = htmlspecialchars($request->remarks ?? 'No remarks provided');
                    $htmlContent =
                    <<<HTML
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Payment Update Notification</title>
                    </head>
                    <body style="margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; background-color:#f7f7f7;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f7f7f7;">
                            <tr>
                                <td align="center">
                                    <table width="600" cellpadding="0" cellspacing="0" border="0"
                                        style="background:#fff; margin:30px auto; border-radius:8px; box-shadow:0 2px 5px rgba(60,60,60,0.08);">
                                        <tr>
                                            <td bgcolor="#d01028"
                                                style="color:#fff; padding:24px 32px; border-radius:8px 8px 0 0; text-align:center;">
                                                <h2 style="margin:0; font-size:28px; letter-spacing:1px;">Payment Rejection Notice</h2>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:32px;">
                                                <p style="font-size:18px; color:#222; margin-top:0;">Dear Customer,</p>
                                                <p style="font-size:16px; color:#333;">
                                                    We regret to inform you that your recent payment verification has been <span
                                                        style="color:#d01028; font-weight:bold;">rejected</span>.
                                                </p>
                                                <table cellspacing="0" cellpadding="0" border="0" width="100%"
                                                    style="margin:24px 0 32px 0;">
                                                    <tr>
                                                        <td style="color:#888; padding:6px 0; width:160px;">Receipt ID:</td>
                                                        <td style="color:#d01028; font-weight:600; padding:6px 0;"> $receiptId </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color:#888; padding:6px 0; width:160px;">Status:</td>
                                                        <td style="color:#d01028; font-weight:600; padding:6px 0;">Failed</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color:#888; padding:6px 0;">Verification Status:</td>
                                                        <td style="color:#d01028; font-weight:600; padding:6px 0;">Rejected</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color:#888; padding:6px 0;">Remarks:</td>
                                                        <td style="color:#333; padding:6px 0;">$remarks</td>
                                                    </tr>
                                                </table>
                                                <p style="font-size:16px; color:#333; margin-bottom:5px;">
                                                    Please review the remarks above for more details. If you have any questions or believe
                                                    this was a mistake, contact us for further assistance.
                                                </p>
                                                <p style="font-size:16px; color:#333; margin-bottom:5px;">
                                                    Kindly upload your payment receipt again through the portal to proceed with the verification process.
                                                </p>

                                                <p style="font-size:16px; color:#333; margin-bottom:0;">
                                                    <strong>' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . '.</strong>
                                                    <br>
                                                    <span style="color:#888;">For any queries, please reach out to us at:</span>
                                                </p>
                                                <p style="font-size:16px; color:#333; margin-top:8px; margin-bottom:0;">
                                                    <strong>Email:</strong> <b>' . ORGANIZER_EMAIL . '</b>
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>
                    HTML;

                    // $email = "manish.sharma@interlinks.in";
                    // Send email using Laravel Mail::html with HTML content
                    $subject = "Extra Requirement Payment Update - " . config('constants.EVENT_NAME') . " " . config('constants.EVENT_YEAR');
                    Mail::to($eventContactEmail)
                        ->cc(['support.mmaportal@interlinks.in'])
                        ->bcc(['test.interlinks@gmail.com'])
                        ->queue(new ExtraRequirementPaymentUpdate($htmlContent, $subject));
                }
            }


            return response()->json(['message' => 'Payment rejected successfully.'], 200);
        }


        if ($invoice->type == 'extra_requirement') {
            $surchargePercentage = $request->surcharge_slab;
            $surchargeChangeReason = $request->surcharge_change_reason;

            $invoice->surChargeReason = $surchargeChangeReason ?? null; // Store the reason for surcharge change if provided

            // Update the invoice surcharge
            // if the surcharge percentage doesn't match the current surcharge percentage then update the surcharge percentage
            if ($surchargePercentage !== null && $surchargePercentage != $invoice->surChargepercentage) {
                $this->updateInvoiceSurcharge($surchargePercentage, $invoice->id);
            }

            // Log the surcharge change reason if provided
            if ($surchargeChangeReason) {
                Log::info('Surcharge change reason: ' . $surchargeChangeReason);
            }
        }

        //refrresh the invoice
        $invoice->refresh();

        $payment->amount_paid = $request->amount_paid;

        DB::transaction(function () use ($payment, $invoice, $request) {
            $amountPaid = $payment->amount_paid;
            $invoice->amount_paid += $amountPaid;
            $invoice->pending_amount = $invoice->amount - $payment->amount_paid;


            if ($invoice->pending_amount <= 0) {
                $invoice->payment_status = 'paid';
                $invoice->pending_amount = 0;
            } elseif ($invoice->amount_paid > 0 && $invoice->pending_amount > 0) {
                $invoice->payment_status = 'partial';
                $invoice->pending_amount = $invoice->amount - $invoice->amount_paid;
                if ($invoice->pending_amount < 0) {
                    $invoice->pending_amount = 0;
                }
                $invoice->payment_due_date = now()->addDays(30);
            }

            $invoice->save();

            //check if the amount and amount_paid is equal then set pending amount to 0
            if ($invoice->amount_paid == $invoice->amount) {
                $invoice->pending_amount = 0;
                $invoice->payment_status = 'paid';
                $invoice->save();
            }

            $payment->update([
                'status' => 'successful',
                'verification_status' => 'Verified',
                'remarks' => $request->remarks,
                'verified_by' => $request->user,
                'verified_at' => now(),
            ]);

            if ($invoice->amount_paid == $invoice->total_final_price) {
                $invoice->pending_amount = 0;
            }
        });
        //Log::info('Payment verified successfully. Payment ID: ' . $invoice->application_id);
         if ($invoice->type == 'extra_requirement') {


            if ($invoice->type == 'extra_requirement') {

             if ($request->status === 'verified') {

                $remarks = htmlspecialchars($request->remarks ?? 'No remarks provided');

                $receiptId = $invoice->invoice_no ?? 'N/A';

                    // $payment->update([
                    //     'status' => 'successful',
                    //     'verification_status' => 'Verified',
                    //     'remarks' => $request->remarks,
                    //     'verified_by' => $request->user,
                    //     'verified_at' => now(),
                    // ]);

                    //send email to the user
                    $htmlContent = <<<HTML
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>Payment Verification Successful</title>
                        </head>
                        <body style="margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; background-color:#f7f7f7;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f7f7f7;">
                                <tr>
                                    <td align="center">
                                        <table width="600" cellpadding="0" cellspacing="0" border="0"
                                            style="background:#fff; margin:30px auto; border-radius:8px; box-shadow:0 2px 5px rgba(60,60,60,0.08);">
                                            <tr>
                                                <td bgcolor="#d01028"
                                                    style="color:#fff; padding:24px 32px; border-radius:8px 8px 0 0; text-align:center;">
                                                    <h2 style="margin:0; font-size:28px; letter-spacing:1px;">Payment Verification Successful</h2>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:32px;">
                                                    <p style="font-size:18px; color:#222; margin-top:0;">Dear Customer,</p>
                                                    <p style="font-size:16px; color:#333;">
                                                        We are pleased to inform you that your payment for Extra Requirement has been successfully verified.
                                                    </p>
                                                    <table cellspacing="0" cellpadding="0" border="0" width="100%"
                                                        style="margin:24px 0 32px 0;">
                                                        <tr>
                                                            <td style="color:#888; padding:6px 0; width:160px;">Receipt ID:</td>
                                                            <td style="color:#d01028; font-weight:600; padding:6px 0;"> $receiptId </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="color:#888; padding:6px 0; width:160px;">Status:</td>
                                                            <td style="color:#d01028; font-weight:600; padding:6px 0;">Successful</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="color:#888; padding:6px 0;">Verification Status:</td>
                                                            <td style="color:#d01028; font-weight:600; padding:6px 0;">Verified</td>
                                                        </tr>

                                                    </table>
                                                    <p style="font-size:16px; color:#333; margin-bottom:5px;">
                                                        Your payment has been successfully processed, and your order is now confirmed.
                                                    </p>
                                                    <p style="font-size:16px; color:#333; margin-bottom:5px;">
                                                        You can now log in to your dashboard to view your order details and manage your account.
                                                    </p>
                                                    <p style="font-size:16px; color:#333; margin-bottom:0;">
                                                        <strong>' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . '.</strong>
                                                        <br>
                                                        <span style="color:#888;">For any queries, please reach out to us at:</span>
                                                    </p>
                                                    <p style="font-size:16px; color:#333; margin-top:8px; margin-bottom:0;">
                                                        <strong>Email:</strong> <b>' . ORGANIZER_EMAIL . '</b>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </body>
                        </html>
                        HTML;

                        $subject = "Extra Requirement Payment Update - " . config('constants.EVENT_NAME') . " " . config('constants.EVENT_YEAR');

                        $eventContact = EventContact::where('application_id', $invoice->application_id)->first();
                        $eventContactEmail = $eventContact->email;

                         Mail::to($eventContactEmail)
                        ->cc(['support.mmaportal@interlinks.in'])
                        ->bcc(['test.interlinks@gmail.com'])
                        ->queue(new ExtraRequirementPaymentUpdate($htmlContent, $subject));
            }
            return response()->json(['message' => 'Payment verified and processed successfully.'], 200);

        }
        }
        // Handle pass allocation
        $exhibitionController = new ExhibitionController();
        $exhibitionController->handlePaymentSuccess($invoice->application_id);

        Log::info('Payment verified successfully. Payment ID: ' . $invoice->application_id);
        // $contact_email = EventContact::where('application_id', $invoice->application_id)->first()->email;

        //get the application->id from invoice and search for user_id from the application table
        /*
        $user_id = Application::where('id', $invoice->application_id)->first()->user_id;
        //get the user email from user_id
        $user_email = User::where('id', $user_id)->first()->email;
        //get the company name from application table
        $company_name = Application::where('application_id', $invoice->application_id)->first()->company_name;


        //send use Onboarding email to the user with user->email and $company_name as parameter
        Mail::to($contact_email)
            ->cc($user_email)
            ->bcc('test.interlinks@gmail.com')
            ->send(new Onboarding($user_email, $company_name));

        */



        // if ($invoice->type === 'Stall Booking' && $request->status != 'rejected') {
        //     Mail::to($contact_email)->send(new PaymentVerifiedMailer($invoice));
        //     //Log::info('Payment verification email sent to: ' . $contact_email);
        // }

        return response()->json(['message' => 'Payment verified and processed successfully.'], 200);
    }

    public function verifyExtraPayment(Request $request)
    {
        //        dd($request->all());
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'status' => 'required|in:verified,rejected',
            'remarks' => 'required|string',
            'user' => 'required|string',
        ]);

        Log::info('Verifying payment ID: ' . $request->payment_id . ' Status: ' . $request->status . ' Remarks: ' . $request->remarks . ' User: ' . $request->user);
        //dd($request->all());

        $payment = Payment::findOrFail($request->payment_id);

        if ($payment->verification_status === 'Verified') {
            return response()->json(['message' => 'Payment already verified.'], 400);
        }

        $invoice = Invoice::where('id', $payment->invoice_id)->firstOrFail();
        Log::info('Invoice ID: ' . $invoice->id . ' Application ID: ' . $invoice->application_id);

        if ($request->status === 'rejected') {
            $payment->update([
                'status' => 'failed',
                'verification_status' => 'Rejected',
                'remarks' => $request->remarks,
                'verified_by' => $request->user,
                'verified_at' => now(),
            ]);


            return response()->json(['message' => 'Payment rejected successfully.'], 200);
        }

        if ($invoice->amount_paid > $invoice->amount) {
            $invoice->amount_paid = $invoice->amount;
            return response()->json(['error' => 'Amount paid exceeds the total amount.'], 400);
        }

        DB::transaction(function () use ($payment, $invoice, $request) {
            $amountPaid = $payment->amount_paid;
            $invoice->amount_paid += $amountPaid;
            if ($invoice->amount_paid > $invoice->amount) {
                $invoice->amount_paid = $invoice->amount;
            }
            $invoice->pending_amount = $invoice->amount - $payment->amount_paid;


            if ($invoice->pending_amount <= 0) {
                $invoice->payment_status = 'paid';
                $invoice->pending_amount = 0;
            } elseif ($invoice->amount_paid > 0 && $invoice->pending_amount > 0) {
                $invoice->payment_status = 'partial';
                $invoice->pending_amount = $invoice->amount - $invoice->amount_paid;
                if ($invoice->pending_amount < 0) {
                    $invoice->pending_amount = 0;
                }
                $invoice->payment_due_date = now()->addDays(30);
            }

            $invoice->save();

            //check if the amount and amount_paid is equal then set pending amount to 0
            if ($invoice->amount_paid == $invoice->amount) {
                $invoice->pending_amount = 0;
                $invoice->payment_status = 'paid';
                $invoice->save();
            }

            $payment->update([
                'status' => 'successful',
                'verification_status' => 'Verified',
                'remarks' => $request->remarks,
                'verified_by' => $request->user,
                'verified_at' => now(),
            ]);

            if ($invoice->amount_paid == $invoice->total_final_price) {
                $invoice->pending_amount = 0;
            }
        });
        Log::info('Payment verified successfully. Payment ID: ' . $invoice->application_id);

        // Handle the RequirementsOrder payment with marking as Confirmed.

        $requirementsOrder = RequirementsOrder::where('invoice_id', $invoice->id)->first();
        Log::info('Requirements Order: ' . $requirementsOrder);
        if ($requirementsOrder) {
            $requirementsOrder->order_status = 'Confirmed';
            $requirementsOrder->save();
        }


        return response()->json(['message' => 'Payment verified and processed successfully.'], 200);
    }

    public function verifyPaymentnew(Request $request)
    {
        //        dd($request->all());
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'status' => 'required|in:verified,rejected',
            'remarks' => 'required|string',
            'user' => 'required|string',
        ]);

        Log::info('Verifying payment ID: ' . $request->payment_id . ' Status: ' . $request->status . ' Remarks: ' . $request->remarks . ' User: ' . $request->user);
        //dd($request->all());

        $payment = Payment::findOrFail($request->payment_id);
        Log::info('Payments' . $payment);
        //check if the same payment id is present in payment table and is mark as verified
        if ($payment->verification_status === 'Verified') {
            return response()->json(['error' => 'Payment already verified.'], 400);
        }
        $query = Invoice::where('id', $payment->paid);
        Log::info('SQL Query: ' . $query->toSql());
        $invoice = $query->firstOrFail();

        Log::info('Invoice: ' . $invoice);

        if ($request->status === 'rejected') {
            $payment->update([
                'status' => 'failed',
                'verification_status' => 'Rejected',
                'remarks' => $request->remarks,
                'verified_by' => $request->user,
                'verified_at' => now(),
            ]);
            return response()->json(['message' => 'Payment rejected successfully.'], 200);
        }


        DB::transaction(function () use ($payment, $invoice, $request) {
            $amountPaid = $payment->amount_paid ?? $payment->amount;
            $invoice->amount_paid += $amountPaid;
            $invoice->pending_amount = $invoice->amount - $invoice->amount_paid;
            $invoice->payment_due_date = now()->addDays(30);

            if ($invoice->pending_amount <= 0) {
                $invoice->payment_status = 'paid';
                $invoice->pending_amount = 0;
            } elseif ($invoice->amount_paid > 0 && $invoice->pending_amount > 0) {
                $invoice->payment_status = 'partial';
            }

            $invoice->save();

            $payment->update([
                'status' => 'successful',
                'verification_status' => 'Verified',
                'remarks' => $request->remarks,
                'verified_by' => $request->user,
                'verified_at' => now(),
            ]);

            if ($invoice->amount_paid == $invoice->total_final_price) {
                $invoice->pending_amount = 0;
            }
        });
        Log::info('Payment verified successfully. Payment ID: ' . $invoice->application_id);

        // Handle pass allocation
        $exhibitionController = new ExhibitionController();
        $exhibitionController->handlePaymentSuccess($invoice->application_id);

        return response()->json(['message' => 'Payment verified and processed successfully.'], 200);
    }
}
