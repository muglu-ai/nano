<?php

namespace App\Exports;

use App\Models\Application;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\BillingDetail;
use App\Models\EventContact;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StallInvoiceExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $applications = Application::where('submission_status', 'approved')
            ->where('allocated_sqm', '>', 0)
            ->get();

        $rows = [];
        foreach ($applications as $app) {
            $billing = BillingDetail::where('application_id', $app->id)->first();
            $contact = EventContact::where('application_id', $app->id)->first();
            $invoice = Invoice::where('application_id', $app->id)
                ->where('type', 'stall booking')->first();
            $payment = $invoice ? Payment::where('invoice_id', $invoice->id)->first() : null;

            // dd($app->id, $billing, $contact, $invoice, $payment);

            //if the invoice payment_status is unpaid skip the row
            if ($invoice && $invoice->payment_status == 'unpaid') {
                continue;
            }

            $rows[] = [
                'Company Name' => $app->company_name ?? '',
                'Stall Number' => $app->stallNumber ?? '',
                'Contact Person' => $contact ? ($contact->salutation . ' ' . $contact->first_name . ' ' . $contact->last_name) : '',
                'Contact Email' => $contact->email ?? '',
                'Contact Number' => $contact->contact_number ?? '',
                'Billing Name' => $billing->billing_company ?? '',
                // 'Billing GST' => $billing->gst ?? '',
                'Billing Address' => $billing->address ?? '',
                'Invoice Number' => $invoice->invoice_no ?? '',
                'Invoice Amount' => $invoice->amount ?? '',
                'Invoice Date' => $invoice->created_at ? $invoice->created_at->format('Y-m-d') : '',
                'Payment Ref' => $payment->transaction_id ?? '',
                'Amount Paid' => $payment->amount_paid ?? '',
                'Payment Date' => $payment && $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') : '',
                'Payment Mode' => $payment->payment_method ?? '',
            ];

            // dd($rows);
            // print_r($rows); // Debugging line to check the rows being collected
        }

        //dd($rows);
        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Company Name',
            'Stall Number',
            'Contact Person',
            'Contact Email',
            'Contact Number',
            'Billing Name',
            // 'Billing GST',
            'Billing Address',
            'Invoice Number',
            'Invoice Amount',
            'Invoice Date',
            'Payment Ref',
            'Amount Paid',
            'Payment Date',
            'Payment Mode',
        ];
    }
}
