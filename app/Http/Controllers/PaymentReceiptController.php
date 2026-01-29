<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Mail\Onboarding;
use App\Models\Application;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ExhibitionController;
use App\Models\CoExhibitor;
use App\Models\EventContact;
use App\Mail\CoExhibitorApprovalMail;
use Illuminate\Support\Facades\Hash;


class PaymentReceiptController extends Controller
{
    //
    public function uploadReceipt_old(Request $request)
    {
        try {
            Log::info('Payment receipt upload request', $request->all());

            $request->validate([
                'invoice_id' => 'required|exists:invoices,invoice_no',
                'user_id' => 'required|exists:users,id',
                'payment_method' => 'required|in:Bank Transfer,Credit Card,UPI,PayPal,Cheque,Cash',
                'amount_paid' => 'required|numeric|min:0',
                'currency' => 'nullable|string|max:10',
                'payment_date' => 'required|date_format:Y-m-d',
                'receipt_image' => 'required|image|mimes:jpg,png,jpeg|max:2048',  // 2MB max file size
                'transaction_no' => 'required|string|max:255',
            ]);
            #Storage::disk('public')->makeDirectory('receipts');
            $receiptPath = $request->file('receipt_image')->storeAs('receipts', $request->transaction_no . '.' . $request->file('receipt_image')->getClientOriginalExtension(), 'public');

            //get the invoice record
            $invoice = Invoice::where('invoice_no', $request->invoice_id)->first();
            if (!$invoice) {
                return response()->json(['message' => 'Invoice not found'], 404);
            }
            //get the invoice_id
            $request->invoice_id = $invoice->id;
            //create new payments record with the uploaded receipt
            $paymentReceipt = Payment::create([
                'invoice_id' => $request->invoice_id,
                'user_id' => $request->user_id,
                'payment_method' => $request->payment_method,
                'amount_paid' => $request->amount_paid,
                'currency' => $request->currency,
                'payment_date' => $request->payment_date,
                'receipt_image' => $receiptPath,
                'amount' => $invoice->amount,
                'transaction_id' => $request->transaction_no,
            ]);

            return response()->json(['message' => 'Payment receipt uploaded successfully', 'data' => $paymentReceipt]);
        } catch (\Exception $e) {
            Log::error('Error uploading payment receipt: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to upload payment receipt'], 500);
        }
    }

    public function uploadReceiptV2(Request $request)
    {
        try {
            Log::info('Payment receipt upload request', [
                'request' => $request->all(),
                'ip' => $request->ip(),
            ]);

            // Validate request inputs
            $validatedData = $request->validate([
                'app_id' => 'required_without:invoice_id|exists:applications,application_id',
                'invoice_id' => 'required_without:app_id|exists:invoices,invoice_no',
                'user_id' => 'required|exists:users,id',
                'payment_method' => 'required|in:Bank Transfer,Credit Card,UPI,PayPal,Cheque,Cash',
                'amount_paid' => 'required|numeric|min:0',
                'currency' => 'nullable|string|max:10',
                'payment_date' => 'required|date_format:Y-m-d',
                'receipt_image' => 'image|mimes:jpg,png,jpeg,pdf|max:2048',
                'transaction_no' => 'required|string|max:255',
            ]);


            // Store the receipt image
            //            $receiptPath = $request->file('receipt_image')->storeAs(
            //                'receipts',
            //                $request->transaction_no . '.' . $request->file('receipt_image')->getClientOriginalExtension(),
            //                'public'
            //            );

            //if app_id comes then take find the invoice id from using application_no
            if ($request->app_id) {
                $invoice = Invoice::where('application_no', $request->app_id)->first();

                if (!$invoice) {
                    return response()->json(['error' => ['app_id' => 'Application not found']], 404);
                }
            } else {
                $invoice = Invoice::where('invoice_no', $request->invoice_id)->first();
                if (!$invoice) {
                    return response()->json(['error' => ['invoice_id' => 'Invoice not found']], 404);
                }
            }

            // Fetch the invoice record


            // Create new payment record
            $paymentReceipt = Payment::create([
                'invoice_id' => $invoice->id,
                'user_id' => $request->user_id,
                'payment_method' => $request->payment_method,
                'amount_paid' => $request->amount_paid,
                'currency' => $request->currency,
                'payment_date' => $request->payment_date,
                //'receipt_image' => $receiptPath,
                'amount' => $invoice->amount,
                'transaction_id' => $request->transaction_no,
            ]);

            // get the payment_id after creating the payment record
            $payment_id = $paymentReceipt->id;

            $payment = Payment::findOrFail($payment_id);

            if ($payment->verification_status === 'Verified') {
                return response()->json(['message' => 'Payment already verified.'], 400);
            }

            $invoice = Invoice::where('id', $payment->invoice_id)->firstOrFail();
            Log::info('Invoice ID: ' . $invoice->id . ' Application ID: ' . $invoice->application_id);


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
                    'remarks' => 'Payment verified successfully',
                    'verified_by' => auth()->user()->name,
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

            $user_id = Application::where('id', $invoice->application_id)->first()->user_id;
            //get the user email from user_id
            $user_email = User::where('id', $user_id)->first()->email;
            //get the company name from application table
            $company_name = Application::where('id', $invoice->application_id)->first()->company_name;
            //get the contact email from application table
            $contact_email = EventContact::where('application_id', $invoice->application_id)->first()->email;


            //send use Onboarding email to the user with user->email and $company_name as parameter
            Mail::to($contact_email)
                ->cc($user_email)
                ->bcc('test.interlinks@gmail.com')
                ->queue(new Onboarding($user_email, $company_name));

            //return response()->json(['message' => 'Payment verified and processed successfully.'], 200);

            return response()->json([
                'message' => 'Payment uploaded successfully and Exhibitor Onboarded.',
                'data' => $paymentReceipt
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Catch validation errors and return error messages for each invalid field
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading payment receipt: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload payment receipt'], 500);
        }
    }


    public function uploadReceipt(Request $request)
    {
        try {

            

            Log::info('Payment receipt upload request', [
                'request' => $request->all(),
                'ip' => $request->ip(),
            ]);

            // Validate request inputs
            $validatedData = $request->validate([
                'app_id' => 'required_without:invoice_no|exists:applications,application_id',
                'invoice_no' => 'required_without:app_id|exists:invoices,invoice_no',
                'user_id' => 'required|exists:users,id',
                'payment_method' => 'required|in:Bank Transfer,Credit Card,UPI,PayPal,Cheque,Cash',
                'amount_paid' => 'required|numeric|min:0',
                'currency' => 'nullable|string|max:10',
                'payment_date' => 'required|date_format:Y-m-d',
                'receipt_image' => 'image|mimes:jpg,png,jpeg,pdf|max:2048',
                'transaction_no' => 'required|string|max:255',
            ]);


            // Store the receipt image
            //            $receiptPath = $request->file('receipt_image')->storeAs(
            //                'receipts',
            //                $request->transaction_no . '.' . $request->file('receipt_image')->getClientOriginalExtension(),
            //                'public'
            //            );

            $type = "Stall Booking";
            //get the invoice type by invoice_id
            if ($request->invoice_no) {
                $invoice = Invoice::where('invoice_no', $request->invoice_no)->first();
                Log::info('Invoice: ' . $invoice);

                if (!$invoice) {
                    return response()->json(['error' => ['invoice_id' => 'Invoice not found']], 404);
                }
                //get the invoice type by invoice->type
                if ($invoice->type === 'extra_requirement') {
                    return response()->json(['error' => ['invoice_id' => 'Invoice type is not valid for payment upload']], 400);
                }
                $type = $invoice->type;
            }

            // dd($type);

            Log::info('Invoice type: ' . $type);

            //if app_id comes then take find the invoice id from using application_no

            if ($type == "Stall Booking") {
                $invoice = Invoice::where('application_no', $request->app_id)->first();

                if (!$invoice) {
                    return response()->json(['error' => ['app_id' => 'Application not found']], 404);
                }
            } else {
                $invoice = Invoice::where('invoice_no', $request->invoice_no)->first();
                if (!$invoice) {
                    return response()->json(['error' => ['invoice_id' => 'Invoice not found']], 404);
                }
            }

            // Fetch the invoice record


            // Create new payment record
            $paymentReceipt = Payment::create([
                'invoice_id' => $invoice->id,
                'user_id' => $request->user_id,
                'payment_method' => $request->payment_method,
                'amount_paid' => $request->amount_paid,
                'currency' => $request->currency,
                'payment_date' => $request->payment_date,
                //'receipt_image' => $receiptPath,
                'amount' => $invoice->amount,
                'transaction_id' => $request->transaction_no,
            ]);

            // get the payment_id after creating the payment record
            $payment_id = $paymentReceipt->id;

            $payment = Payment::findOrFail($payment_id);

            if ($payment->verification_status === 'Verified') {
                return response()->json(['message' => 'Payment already verified.'], 400);
            }

            $invoice = Invoice::where('id', $payment->invoice_id)->firstOrFail();
            #Log::info('Invoice ID: ' . $invoice->id . ' Application ID: ' . $invoice->application_id);


            DB::transaction(function () use ($payment, $invoice, $request) {
                $amountPaid = $payment->amount_paid;
                $invoice->amount_paid += $amountPaid;
                $invoice->pending_amount = $invoice->amount - $payment->amount_paid;

                $invoice->save();
                $invoice->refresh();
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
                    'remarks' => 'Payment verified successfully',
                    'verified_by' => auth()->user()->name,
                    'verified_at' => now(),
                ]);

                if ($invoice->amount_paid == $invoice->total_final_price) {
                    $invoice->pending_amount = 0;
                }
            });

            //check whether the invoice amount atleast greater than 60% 
            // if ($invoice->amount_paid < ($invoice->amount * 0.6)) {
            //     // Payment is less than 60%, do not proceed with onboarding
            //     return response()->json([
            //         'message' => 'At least 60% payment is required to proceed with onboarding.',
            //         'data' => $paymentReceipt
            //     ], 200);
            // }

            if (
                $type === 'Stall Booking' ||
                (is_string($type) && stripos($type, 'sponsorship') !== false)
            ) {
                $application = Application::find($invoice->application_id);
                $user = User::find($application->user_id ?? null);
                $contact_email = EventContact::where('application_id', $invoice->application_id)->value('email');

                (new ExhibitionController())->handlePaymentSuccess($invoice->application_id);

                if ($user && $contact_email) {
                    Mail::to($contact_email)
                        ->cc($user->email)
                        ->bcc('test.interlinks@gmail.com')
                        ->queue(new Onboarding($user->email, $application->company_name));
                }

                return response()->json([
                    'message' => 'Payment uploaded successfully and Exhibitor Onboarded.',
                    'data' => $paymentReceipt
                ], 200);
            }

            if ($type === 'Co-Exhibitor') {
                $coExhibitor = CoExhibitor::where('co_exhibitor_id', $invoice->co_exhibitorID)->first();

                //dd($coExhibitor);
                if (!$coExhibitor) {
                    return response()->json(['error' => ['co_exhibitor_id' => 'Co-Exhibitor not found']], 404);
                }

                //get the user from the user_id from users table
                $user = User::find($coExhibitor->user_id);
                if (!$user) {
                    return response()->json(['error' => ['user_id' => 'User not found']], 404);
                }

                //gerate random password and save to the user use laravel hash
                $password = substr(md5(uniqid()), 0, 10);

                $user->password = Hash::make($password);
                $user->save();


                //dd($user);


                $exhibiting_under = Application::where('id', $coExhibitor->application_id)->value('company_name');

                Mail::to($coExhibitor->email)
                    ->bcc('test.interlinks@gmail.com')
                    ->send(new CoExhibitorApprovalMail(
                        $coExhibitor,
                        $password,
                        $exhibiting_under
                    ));

                return response()->json([
                    'message' => 'Payment uploaded successfully and Co-Exhibitor Onboarded.',
                    'data' => $paymentReceipt
                ], 200);
            }
            // Handle pass allocation
            return response()->json(['message' => 'Payment verified and processed successfully.'], 200);

            return response()->json([
                'message' => 'Payment uploaded successfully and Exhibitor Onboarded.',
                'data' => $paymentReceipt
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Catch validation errors and return error messages for each invalid field
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading payment receipt: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload payment receipt'], 500);
        }
    }


    public function uploadReceipt_user(Request $request)
    {
        Log::info('Payment receipt upload request', $request->all());
        try {
            Log::info('Payment receipt upload request', $request->all());

            // Validate request inputs
            $validatedData = $request->validate([
                'app_id' => 'required_without:invoice_id|exists:applications,application_id',
                'invoice_id' => 'required_without:app_id|exists:invoices,invoice_no',
                'user_id' => 'required|exists:users,id',
                'payment_method' => 'required|in:Bank Transfer,Credit Card,UPI,PayPal,Cheque,Cash',
                'amount_paid' => 'required|numeric|min:0',
                'currency' => 'nullable|string|max:10',
                'payment_date' => 'required|date_format:Y-m-d',
                'receipt_image' => 'required|mimes:jpg,png,jpeg,pdf|max:2048',
                'transaction_no' => 'required|string|max:255',
            ]);

            // Store the receipt image
            $receiptPath = $request->file('receipt_image')->storeAs(
                'receipts',
                $request->transaction_no . '.' . $request->file('receipt_image')->getClientOriginalExtension(),
                'public'
            );

            //if app_id comes then take find the invoice id from using application_no
            if ($request->app_id) {
                $invoice = Invoice::where('application_no', $request->app_id)->first();
                if (!$invoice) {
                    return response()->json(['error' => ['app_id' => 'Application not found']], 404);
                }
            } else {
                $invoice = Invoice::where('invoice_no', $request->invoice_id)->first();
                if (!$invoice) {
                    return response()->json(['error' => ['invoice_id' => 'Invoice not found']], 404);
                }
            }

            // Fetch the invoice record


            // Create new payment record
            $paymentReceipt = Payment::create([
                'invoice_id' => $invoice->id,
                'user_id' => $request->user_id,
                'payment_method' => $request->payment_method,
                'amount_paid' => $request->amount_paid,
                'currency' => $request->currency,
                'payment_date' => $request->payment_date,
                'receipt_image' => $receiptPath,
                'amount' => $invoice->amount,
                'transaction_id' => $request->transaction_no,
            ]);

            return response()->json([
                'message' => 'Payment receipt uploaded successfully',
                'data' => $paymentReceipt
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Catch validation errors and return error messages for each invalid field
            Log::info('Error uploading payment receipt: ' . $e->getMessage());
            Log::info('Payment receipt upload request', $request->all());
            return response()->json(['error' => $e->errors(), 'request' => $request->all()], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading payment receipt: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload payment receipt'], 500);
        }
    }

    public function uploadReceipt_extra(Request $request)
    {
        Log::info('Payment receipt upload request', $request->all());
        try {

            //find invoice by invoice_id
            $invoice = Invoice::where('invoice_no', $request->invoice_id)->first();
            if (!$invoice) {
                $error = ['invoice_id' => 'Invoice not found'];
                return $request->expectsJson()
                    ? response()->json(['error' => $error], 404)
                    : back()->withErrors($error)->withInput();
            }

            //if amount_paid < = 0 return error
            if ($request->amount_paid <= 0) {   
                $error = ['amount_paid' => 'Amount paid must be greater than 0'];
                return $request->expectsJson()
                    ? response()->json(['error' => $error], 422)
                    : back()->withErrors($error)->withInput();
            }

            //amount_paid merge from the invoice
            $request->merge(['amount_paid' => $request->amount_paid]);
            $request->merge(['user_id' => auth()->user()->id]);
            Log::info('Payment receipt upload request', $request->all());

            $validatedData = $request->validate([
                'app_id' => 'required_without:invoice_id|exists:applications,application_id',
                'invoice_id' => 'required_without:app_id|exists:invoices,invoice_no',
                'user_id' => 'required|exists:users,id',
                'payment_method' => 'required|in:Bank Transfer,Credit Card,UPI,PayPal,Cheque,Cash',
                'amount_paid' => 'required|numeric|min:0',
                'currency' => 'nullable|string|max:10',
                'payment_date' => 'required|date_format:Y-m-d',
                'receipt_image' => 'required|mimes:jpg,png,jpeg,pdf|max:2048',
                'transaction_no' => 'required|string|max:255',
            ]);

            $receiptPath = $request->file('receipt_image')->storeAs(
                'receipts',
                $request->transaction_no . '.' . $request->file('receipt_image')->getClientOriginalExtension(),
                'public'
            );

            if ($request->app_id) {
                $invoice = Invoice::where('application_no', $request->app_id)->first();
                if (!$invoice) {
                    $error = ['app_id' => 'Application not found'];
                    return $request->expectsJson()
                        ? response()->json(['error' => $error], 404)
                        : back()->withErrors($error)->withInput();
                }
            } else {
                $invoice = Invoice::where('invoice_no', $request->invoice_id)->first();
                if (!$invoice) {
                    $error = ['invoice_id' => 'Invoice not found'];
                    return $request->expectsJson()
                        ? response()->json(['error' => $error], 404)
                        : back()->withErrors($error)->withInput();
                }
            }

            $paymentReceipt = Payment::create([
                'invoice_id' => $invoice->id,
                'user_id' => $request->user_id,
                'payment_method' => $request->payment_method,
                'amount_paid' => $request->amount_paid,
                'currency' => $request->currency,
                'payment_date' => $request->payment_date,
                'receipt_image' => $receiptPath,
                'amount' => $invoice->amount,
                'transaction_id' => $request->transaction_no,
            ]);

            //if the co_exhibitorID is present in the invoice then get the companyNamee as id from the co_exhibitors coulumn co_exhibitor_name
            // else application_id from the invoices and get the company_name from the applications table
            $companyName = null;

            if ($invoice->co_exhibitorID) {
                $coExhibitor = CoExhibitor::find($invoice->co_exhibitorID);
                if (!$coExhibitor) {
                    $error = ['co_exhibitor_id' => 'Co-Exhibitor not found'];
                } else {
                    $companyName = $coExhibitor->co_exhibitor_name;
                }
            } else {
                $application = Application::find($invoice->application_id);
                if (!$application) {
                    $error = ['application_id' => 'Application not found'];
                } else {
                    $companyName = $application->company_name;
                }
            }

            if (isset($error)) {
                return $request->expectsJson()
                    ? response()->json(['error' => $error], 404)
                    : back()->withErrors($error)->withInput();
            }

            $html = '<p>Dear Admin,</p>';
            $html .= '<p>We have received a new offline payment transaction. Please find the details below and update them into the SEMICON Admin Panel.</p>';
            $html .= '<ul>';
            $html .= '<li><strong>Company Name:</strong> ' . $companyName . '</li>';
            $html .= '<li><strong>Invoice ID:</strong> ' . $invoice->invoice_no . '</li>';
            // $html .= '<li><strong>Application ID:</strong> ' . $invoice->application_no . '</li>';
            $html .= '<li><strong>Payment Method:</strong> ' . $request->payment_method . '</li>';
            $html .= '<li><strong>Amount Paid:</strong> ' . $request->amount_paid . '</li>';
            $html .= '<li><strong>Payment Date:</strong> ' . $request->payment_date . '</li>';
            $html .= '<li><strong>Transaction No:</strong> ' . $request->transaction_no . '</li>';
            $html .= '<li><strong>Receipt Image:</strong> <a href="' . env('APP_URL') . Storage::url($receiptPath) . '">View Receipt</a></li>';
            $html .= '<li><strong>Uploaded By:</strong> ' . auth()->user()->name . '</li>';
            $html .= '</ul>';


            // Send the email directly using the HTML content
            \Mail::send([], [], function ($message) use ($html) {
                $message
                    ->to(['nitin.chauhan@mmactiv.com', 'vijay.mashalkar@mmactiv.com'])
                    ->cc('test.interlinks@gmail.com')
                    ->subject('New Offline Payment Receipt Uploaded')
                    ->html($html);
            });


            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Payment receipt uploaded successfully',
                    'data' => $paymentReceipt
                ], 200);
            } else {
                //if user is logged in then redirect to route userOrders
                // if (auth()->check()) {
                if (auth()->check()) {
                    return redirect()->route('exhibitor.orders', ['uploadSuccess' => true])
                        ->with('receipt_success', 'Payment receipt uploaded successfully!');
                } else {
                    return redirect()->back()->with('receipt_success', 'Payment receipt uploaded successfully!');
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::info('Error uploading payment receipt: ' . $e->getMessage());
            Log::info('Payment receipt upload request', $request->all());
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->errors(), 'request' => $request->all()], 422);
            } else {
                return back()->withErrors($e->errors())->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Error uploading payment receipt: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to upload payment receipt'], 500);
            } else {
                return back()->with('error', 'Failed to upload payment receipt');
            }
        }
    }
}
