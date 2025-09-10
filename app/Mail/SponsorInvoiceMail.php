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
use App\Models\Sponsorship; // Assuming you have a Sponsorship model
use App\Models\SponsorItem; // Assuming you have a SponsorItem model
use Illuminate\Support\Facades\Log;

class SponsorInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($application_id)
    {

        Log::info('SponsorInvoiceMail constructor called with application_id: ' . $application_id);
        // Fetch application data
        $application = Application::where('application_id', $application_id)
            ->with(['billingDetail', 'invoice', 'sectors', 'sponsorship'])
            ->first();

        $id = $application->id;
        $billing = $application->billingDetail;
        $invoice = $application->invoice;
        $sectors = $application->sectors;
        $productCategories = ProductCategory::select('id', 'name')->get();

        $sectorIds = json_decode($application->sector_id, true); // Decode JSON if stored as a string
        if (!is_array($sectorIds)) {
            $sectorIds = [$sectorIds]; // Ensure it's an array
        }

        $selectedSectors = Sector::whereIn('id', $sectorIds)->pluck('name')->implode(', ');

        $registrationType = $application->application_type;
        $business_type = $billing->business_type;

        // find the has_sponsorship is true or false
        $hasSponsorship = $application->has_sponsorship ?? false;

        // if has_sponsorship is true then get the sponsorship details
        $sponsorshipItems = 'N/A'; // Default value
        if ($hasSponsorship) {
            $sponsorshipItems = Sponsorship::where('application_id', $application->id)
                ->where('status', 'approved') // Add condition to filter by approved status
                ->get()
                ->map(function ($sponsorship) {
                    return [
                        'sponsorship_name' => $sponsorship->sponsorship_item ?? 'N/A',
                        'quantity' => $sponsorship->sponsorship_item_count ?? 'N/A',
                        'price' => $sponsorship->price ?? 'N/A',
                    ];
                });
        }

        //dd($sponsorshipItems); // Debugging line

        //get all the invoices from the invoice table where id from sponsorships table  and sponsorship_id matches with the invoice table

        $sponsorships = Sponsorship::where('application_id', $application->id)->pluck('id'); 

        // Fetch all invoices where sponsorship_id matches the sponsorship IDs
        $invoices = Invoice::whereIn('sponsorship_id', $sponsorships)->get();
        $gst_amount = 0;
        $sub_total = 0;
        $total_amount = 0;

        foreach ($invoices as $invoice) {
            $gst_amount += $invoice->gst ?? 0;
            $sub_total += $invoice->price ?? 0;
            $total_amount += $invoice->price + $invoice->gst ?? 0;
        }




        //price calculation for sponsorship items


       // dd($sponsorshipItems); // Debugging line

        // Add sponsorship details to the data array
        //$this->data['sponsorshipDetails'] = $sponsorshipItems;

        $this->data = [
            'applicationID' => $application->application_id ?? 'N/A',
            'exhibitor_name' => $application->company_name ?? 'N/A',
            'approval_date' => $application->approved_date ?? 'N/A',
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
            'products' => $productCategories->filter(function($product) use ($application) {
                return isset($application) && in_array($product->id, (array) json_decode($application->main_product_category, true));
            })->pluck('name')->implode(', ') ?? 'N/A',
            'stall_size' => ($application->allocated_sqm) .' SQM'  ?? 'N/A',
            'stall_type' => $application->stall_category ?? 'N/A',
            'booth_no' => $application->stallNumber ?? 'N/A',
            'price' => $sub_total ,
            'gst' => $gst_amount ,
            'GST' => $application->gst_no ?? 'N/A',
            'total_amount' => $total_amount ,
            'stallNumber' => $application->stallNumber ?? 'N/A',
            'pref_location' => $application->pref_location ?? 'N/A',
            'sponsorshipDetails' => $sponsorshipItems, // Add sponsorship details to the data array

        ];
       // dd($this->data);
    }

    public function build()
    {
        return $this->subject('Your Sponsor Application is approved at SEMICON 2025')
            ->view('emails.sponsor_invoice')
            ->with('data', $this->data);
    }
}
