<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Invoice;
use App\Models\Sponsorship;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicesController extends Controller
{
    //
    //list all invoices for admin
    public function index()
    {
        //check user is logged in or not
        if (!auth()->check()) {
            return redirect('/login');
        }
        // Get only applications that have invoices
        $applications = Application::has('invoices')->with(['invoices' => function($query) {
            $query->where('type', '!=', 'extra_requirement');
        }, 'billingDetail'])->get();

         // if application is null then pass null to view
         if ($applications->isEmpty()) {
            $applications = null;
        }
        return view('invoices.index', compact('applications', 'applications'));
    }

    //show invoice details for individual invoice
    public function show($id)
    {
        //check user is logged in or not
        if (!auth()->check()) {
            return redirect('/login');
        }
        $invoice = Invoice::where('invoice_no', $id)->first();
       // dd($invoice);
        // Get only applications that have invoices


        //dd($invoice->application_id);
        // Get the payments related to the invoice
        $payments = $invoice->payments;

         $invoiceType = $invoice->type;

        $surcharge = $invoice->surChargepercentage ?? 0;


        // Find the application with the same application_no in both invoices and applications tables
        $applications = Application::where('id', $invoice->application_id)->first();
//dd($applications);


        return view('invoices.invoice_details', compact('applications', 'invoice', 'payments', 'invoiceType', 'surcharge'));
    }

    //print invoice
    public function view(Request $request)
    {

        // Ensure the user is logged in (uncomment if authentication is required)
         if (!auth()->check()) {
             return redirect('/login');
         }

        // Retrieve invoice based on invoice number from the request
        $invoice_no = $request->no;
        $invoice = Invoice::where('invoice_no', $invoice_no)->firstOrFail(); // Fail gracefully if not found

        // Retrieve the related application
        $applications = Application::where('application_id', $invoice->application_no)->firstOrFail();

        // Retrieve sponsorship details if available
        $sponsor = Sponsorship::where('application_id', $applications->id)->first();

        // Get billing details
        $billing = $applications->billingDetail;

        // Determine product details based on sponsorship presence
        $products = $sponsor ? [
            'item' => $sponsor->sponsorship_item,
            'price' => $sponsor->price,
            'gst' => $invoice->gst,
            'quantity' => 1,
            'total' => $invoice->amount,
            'due' => $invoice->amount - $invoice->payments->sum('amount'),
        ] : [
            'item' => $applications->stall_category . ' Stall',
            'price' => $invoice->amount,
            'quantity' => $applications->allocated_sqm . ' (sqm)',
            'gst' => $invoice->gst,
            'total' => $invoice->amount,
            'due' => $invoice->amount - $invoice->payments->sum('amount'),
        ];

        // Return the view with necessary data
        return view('bills.invoice', compact('applications', 'invoice', 'billing', 'sponsor', 'products'));
    }


     public function addTdsAmount(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'invoice_id' => 'required|integer|exists:invoices,id',
                'tds_amount' => 'required|numeric|min:0',
                'tds_reason' => 'nullable|string|max:500',
            ]);

            // Find the invoice
            $invoice = Invoice::findOrFail($request->invoice_id);

            // Check if this is a Stall Booking invoice
            // if ($invoice->type !== 'Stall Booking') {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'TDS can only be added to Stall Booking invoices.'
            //     ], 400);
            // }

            // Check if TDS already exists
            if ($invoice->tds_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'TDS amount already exists for this invoice. You can only add TDS once.'
                ], 400);
            }

            // Add TDS amount to the invoice
            $invoice->tds_amount = $request->tds_amount;
            $invoice->save();

            // Check if total_final_price = amount_paid + tds_amount to mark as paid
            $totalPaid = $invoice->amount_paid ?? 0;
            $tdsAmount = $invoice->tds_amount ?? 0;
            $totalFinalPrice = $invoice->total_final_price ?? 0;

            if (($totalPaid + $tdsAmount) >= $totalFinalPrice) {
                $invoice->payment_status = 'paid';
                $invoice->tdsReason = $request->tds_reason;
                $invoice->pending_amount = 0;
                $invoice->save();
            }

            // Log the TDS addition
            \Log::info('TDS amount added to invoice', [
                'invoice_id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no,
                'tds_amount' => $request->tds_amount,
                'tds_reason' => $request->tds_reason,
                'added_by' => auth()->id(),
                'added_at' => now(),
                'payment_status' => $invoice->payment_status,
                'total_paid' => $totalPaid,
                'total_final_price' => $totalFinalPrice
            ]);

            return response()->json([
                'success' => true,
                'message' => 'TDS amount added successfully. Payment status updated based on amount paid + TDS.',
                'data' => [
                    'tds_amount' => $request->tds_amount,
                    'payment_status' => $invoice->payment_status,
                    'total_paid' => $totalPaid,
                    'total_final_price' => $totalFinalPrice
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found.'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error adding TDS amount to invoice', [
                'invoice_id' => $request->invoice_id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding TDS amount. Please try again.'
            ], 500);
        }
    }



    public function generatePDF(Request $request)
    {
        // Get the invoice number from the request
        $invoice_no = $request->no;

        // Find the invoice
        $invoice = Invoice::where('invoice_no', $invoice_no)->firstOrFail();

        // Get application details
        $applications = Application::where('application_id', $invoice->application_no)->firstOrFail();

        // Get sponsor details
        $sponsor = Sponsorship::where('application_id', $applications->id)->first();



        // Billing details
        $billing = $applications->billingDetail;

        // Determine product details based on sponsorship availability
        $products = $sponsor ? [
            'item' => $sponsor->sponsorship_item,
            'price' => $sponsor->price,
            'gst' => $invoice->gst,
            'quantity' => 1,
            'total' => $invoice->amount,
            'due' => $invoice->amount - $invoice->payments->sum('amount'),
        ] : [
            'item' => $applications->stall_category . ' Stall',
            'price' => $invoice->amount,
            'quantity' => $applications->allocated_sqm . ' (sqm)',
            'gst' => $invoice->gst,
            'total' => $invoice->amount,
            'due' => $invoice->amount - $invoice->payments->sum('amount'),
        ];

        // Load the PDF view
       $pdf = Pdf::loadView('bills.invoice', compact('applications', 'invoice', 'billing', 'sponsor', 'products'))->setPaper('a4');

        // Set the filename dynamically based on invoice number
        $filename = "Invoice_{$invoice_no}.pdf";

        // Return the PDF for download
        return $pdf->download($filename);
    }


}
