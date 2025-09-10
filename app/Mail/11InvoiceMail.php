<?php
namespace App\Mail;

use App\Models\Sector;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use App\Models\BillingDetail;
use App\Models\Invoice;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($application_id)
    {



        // Fetch application data
        $application = Application::where('application_id', $application_id)
            ->with(['billingDetail', 'invoice', 'sectors'])
            ->first();

        $id = $application->id;
        $billing = $application->billingDetail;
        $invoice = $application->invoice;
        $sectors = $application->sectors;

        $sectorIds = json_decode($application->sector_id, true); // Decode JSON if stored as a string
        if (!is_array($sectorIds)) {
            $sectorIds = [$sectorIds]; // Ensure it's an array
        }

        $selectedSectors = Sector::whereIn('id', $sectorIds)->pluck('name')->implode(', ');

        $registrationType = $application->application_type;
        $business_type = $billing->business_type;

        $this->data = [
            'registrationType' => $registrationType ?? 'N/A',
            'BillingName' => $billing->contact_name ?? 'N/A',
            'invoiceID' => $invoice->invoice_no ?? 'N/A',
            'GSTIN' => $application->gst_no ?? 'N/A',
            'amount' => $invoice->amount ?? 'N/A',
            'dueDate' => $invoice->payment_due_date ?? 'N/A',
            'paymentStatus' => $invoice->payment_status ?? 'N/A',
            'billingDate' => $invoice->created_at->format('Y-m-d') ?? 'N/A',
            'DueDate' => $invoice->payment_due_date ?? 'N/A',
            'currency' => $application->payment_currency ?? 'N/A',
            'BillingCompanyName' => $billing->billing_company ?? 'N/A',
            'BillingAddress' => $billing->address ?? 'N/A',
            'BillingCity' => $billing->city_id ?? 'N/A',
            'BillingState' => $billing->state->name ?? 'N/A',
            'BillingCountry' => $billing->country->name ?? 'N/A',
            'BillingZip' => $billing->postal_code ?? 'N/A',
            'BillingPhone' => $billing->phone ?? 'N/A',
            'BillingEmail' => $billing->email ?? 'N/A',
            'business_type' => $application->type_of_business ?? 'N/A',
            'sectors' => $selectedSectors ?? 'N/A',
            'products' => is_array($application->product_groups) ? implode(', ', $application->product_groups) : $application->product_groups ?? 'N/A',
            'stall_size' => ($application->allocated_sqm) .' SQM'  ?? 'N/A',
            'stall_type' => $application->stall_category ?? 'N/A',
            'booth_no' => $application->stallNumber ?? 'N/A',
            'price' => $invoice->price ?? 'N/A',
            'gst' => $invoice->gst ?? 'N/A',
            'GST' => $application->gst_no ?? 'N/A',
            'total_amount' => $invoice->amount ?? 'N/A',
        ];
        //dd($this->data);
    }

    public function build()
    {
        return $this->subject('Your Application is approved at SEMICON 2025')
            ->view('emails.invoice')
            ->with('data', $this->data);
    }
}
