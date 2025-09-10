<?php

namespace App\Mail;

use App\Models\ProductCategory;
use App\Models\Sector;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use App\Models\BillingDetail;
use App\Models\Invoice;
use App\Models\CoExhibitor;

class CoExhibitorInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($application_id)
    {

        $coExhibitor = CoExhibitor::where('co_exhibitor_id', $application_id)->first();

        // dd($coExhibitor);

        $applicationID = $coExhibitor->application_id;


        //if not found, throw an exception
        if (!$applicationID) {
            throw new \Exception('Application not found for the given co-exhibitor ID.');
        }
        // Fetch application data
        $application = Application::where('id', $applicationID)
            ->with(['billingDetail', 'invoice', 'sectors'])
            ->first();
        //dd($application);
        $id = $application->id;
        $billing = $application->billingDetail;
        $invoice = Invoice::where('application_id', $id)
            ->where('co_exhibitorID', $application_id)
            ->first();
        // dd($invoice);
        $sectors = $application->sectors;
        $productCategories = ProductCategory::select('id', 'name')->get();

        $sectorIds = json_decode($application->sector_id, true); // Decode JSON if stored as a string
        if (!is_array($sectorIds)) {
            $sectorIds = [$sectorIds]; // Ensure it's an array
        }

        $selectedSectors = Sector::whereIn('id', $sectorIds)->pluck('name')->implode(', ');

        $registrationType = $application->application_type;
        $business_type = $billing->business_type;



        $this->data = [
            'applicationID' => $application->application_id ?? 'N/A',
            'exhibitor_name' => $application->company_name ?? 'N/A',
            'coExhibitorName' => $coExhibitor->co_exhibitor_name ?? 'N/A',
            'approval_date' => $coExhibitor->approved_At ?? 'N/A',
            'registrationType' => $registrationType ?? 'N/A',
            'coExhibitorPerson' => $coExhibitor->contact_person ?? 'N/A',
            'coExhibitorEmail' => $coExhibitor->email ?? 'N/A',
            'coExhibitorPhone' => $coExhibitor->phone ?? 'N/A',
            'coExhibitorJobTitle' => $coExhibitor->job_title ?? 'N/A',
            'coExhibitorID' => $coExhibitor->co_exhibitor_id ?? 'N/A',
            'coExhibitorStatus' => $coExhibitor->status ?? 'N/A',

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
            'products' => $productCategories->filter(function ($product) use ($application) {
                return isset($application) && in_array($product->id, (array) json_decode($application->main_product_category, true));
            })->pluck('name')->implode(', ') ?? 'N/A',
            'stall_size' => ($application->allocated_sqm) . ' SQM'  ?? 'N/A',
            'stall_type' => $application->stall_category ?? 'N/A',
            'booth_no' => $application->stallNumber ?? 'N/A',
            'price' => $invoice->price ?? 'N/A',
            'gst' => $invoice->gst ?? 'N/A',
            'GST' => $application->gst_no ?? 'N/A',
            'total_amount' => $invoice->amount ?? 'N/A',
            'stallNumber' => $application->stallNumber ?? 'N/A',
            'pref_location' => $application->pref_location ?? 'N/A',
            'amount_paid' => $invoice->amount_paid ?? 'N/A',
            'paystatus' => $invoice->payment_status ?? 'N/A',


        ];
        // dd($this->data);
    }

    public function build()
    {
        return $this->subject('Co-Exhibitor Application for ' . $this->data['coExhibitorName'] . ' is approved at SEMICON 2025')
            ->view('emails.Co-invoice')
            ->with('data', $this->data);
    }
}
