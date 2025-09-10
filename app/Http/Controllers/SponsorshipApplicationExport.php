<?php

namespace App\Http\Controllers;
use App\Models\Application;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class SponsorshipApplicationExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    protected $status;

    /**
     * Optionally filter by status.
     *
     * @param string|null $status
     */
    public function __construct($status = null)
    {
        $this->status = $status;
    }

    /**
     * Retrieve and flatten the applications so that each sponsorship
     * appears on its own row with the associated application data.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->status !== 'all') {
            $status = ($this->status == 'in progress') ? 'initiated' : $this->status;
//            dd($applications = Application::with('eventContact', 'sponsorship')
//                ->whereHas('sponsorship', function ($query) use ($status) {
//                    $query->where('sponsorships.status', $status);
//                })->toRawSql());
            $applications = Application::with('eventContact', 'sponsorship')
                ->whereHas('sponsorship', function ($query) use ($status) {
                    $query->where('sponsorships.status', $status);
                })->get();
        } else {
            $applications = Application::with('eventContact', 'sponsorship')
                ->whereHas('sponsorship')->get();
        }

        // Flatten each sponsorship into its own row.
        $rows = collect();
        foreach ($applications as $application) {
            foreach ($application->sponsorship as $sponsorship) {
                // If a status filter is applied and this sponsorship doesn't match, skip it.
                if ($this->status !== 'all' && $sponsorship->status !== $status) {
                    continue;
                }
                $rows->push([
                    'application' => $application,
                    'sponsorship' => $sponsorship,
                ]);
            }
        }

        return $rows;
    }

    /**
     * Define the column headings for the Excel export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Application Number',
            'Sponsorship Number',
            'Sponsorship Item',
            'Company Name',
            'Company Email',
            'Website',
            'Main Product Category',
            'Type of Business',
            'Participated Previously',
            'Stall Category',
            'Booth Count',
            'Payment Currency',
            'GST / Tax Compliance',
            'GST/Tax No',
            'PAN No',
            'TAN No',
            'Region',
            'Submission Status',
            'Submission Date',
            'Approved / Rejected Date',
            'Allocated SQM',
            'Sector',
            'Product Groups',
            'Address',
            'City',
            'State',
            'Country',
            // Billing Details
            'Billing Company',
            'Billing Contact Name',
            'Billing Email',
            'Billing Phone',
            'Billing Address',
            'Billing City',
            'Billing State',
            'Billing Country',
            'Billing Postal Code',
            // Contact Person
            'Contact Salutation',
            'Contact First Name',
            'Contact Last Name',
            'Job Title',
            'Contact Email',
            'Contact Number'
        ];
    }

    /**
     * Map each row into the specified columns.
     *
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $application = $row['application'];
        $sponsorship = $row['sponsorship'];

        // For "Approved / Rejected Date", choose the approved_date if available; otherwise, the rejected_date.
        $approvedOrRejectedDate = $sponsorship->approval_date ?: $sponsorship->approval_date;

        return [
            // Application details
            $application->application_id,                  // Application Number
            $sponsorship->sponsorship_id,                   // Sponsorship Number
            $sponsorship->sponsorship_item,                 // Sponsorship Item
            $application->company_name,                     // Company Name
            $application->company_email,                    // Company Email
            $application->website,                          // Website
            $application->mainProductCategoryName($application->main_product_category),           // Main Product Category
            $application->type_of_business,                 // Type of Business
            $application->participated_previous,            // Participated Previously
            $application->stall_category,                   // Stall Category
            $application->booth_count,                      // Booth Count
            $application->payment_currency,                 // Payment Currency
            $application->gst_compliance,                   // GST / Tax Compliance
            $application->gst_no ?? 'N/A',                           // GST/Tax No
            $application->pan_no ?? 'N/A',                           // PAN No
            $application->tan_no  ?? 'N/A',                           // TAN No
            $application->region,                           // Region
            $sponsorship->status,                // Submission Status
            $sponsorship->submitted_date,                  // Submission Date
            $approvedOrRejectedDate,                        // Approved / Rejected Date
            $application->allocated_sqm,                    // Allocated SQM
            implode(', ', $application->sectors->pluck('name')->toArray()),                    // Sector
            implode(', ', json_decode($application->product_groups, true)),                // Product Groups
            $application->address,                          // Address
            $application->city_id,                          // City
            optional($application->state)->name,
            optional($application->country)->name,                     // Country

            // Billing Details (if these attributes exist, otherwise return an empty string)
            optional($application->billingDetail)->billing_company,
            optional($application->billingDetail)->contact_name,
            optional($application->billingDetail)->email,
            optional($application->billingDetail)->phone,
            optional($application->billingDetail)->address,
            optional($application->billingDetail)->city_id,
            optional(optional($application->billingDetail)->state)->name,
            optional(optional($application->billingDetail)->country)->name,
            optional($application->billingDetail)->postal_code,

            // Contact Person (from the eventContact relationship)
            optional($application->eventContact)->salutation,
            optional($application->eventContact)->first_name,
            optional($application->eventContact)->last_name,
            optional($application->eventContact)->job_title,
            optional($application->eventContact)->email,
            optional($application->eventContact)->contact_number,
            optional($application->eventContact)->secondary_email,
        ];
    }
}
