<?php

namespace App\Exports;

use App\Models\Application;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Log;
use App\Models\ExhibitorInfo;
use App\Models\Sector;
use Illuminate\Support\Facades\Auth;

class ApplicationExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $status;

    //construct function to get the data with status parameter
    public function     __construct($status)
    {
        //log the user->id , date and ip and date of the export
        Log::info('ApplicationExport initiated', [
            'user_id' => Auth::id(),
            'date' => now(),
            'ip' => request()->ip(),
            'status' => $status,
        ]);
        $this->status = $status;
    }



    /**
     * Fetch data from the database.
     */

    public function collection()
    {
        if ($this->status !== 'all') {
            return Application::with(['billingDetail', 'eventContact'])
                ->where('submission_status', $this->status)
                ->where('company_name', '!=', 'SCI Knowledge Interlinks Pvt. Ltd.')
                ->get();
        }
        return Application::with(['billingDetail', 'eventContact'])->get();
    }

    /**
     * Define column headers.
     */
    public function headings(): array
    {
        return [
            //            'Application ID',
            'Application Number',
            'Company Name',

            'Stall Category',
            'Booth size requested', // New Column
            // 'Preferred Location',
            // 'SEMI status',
            // 'Country',
            'HQ Country',
            'Payment Currency',

            // Contact Person 1
            //            'Contact Salutation',
            'Contact Name',
            'Job Title',
            'Contact Email',
            'Contact Number',

            //second contact person details
            //            'Sec-Contact Salutation',
            // 'Sec-Contact Name',
            // 'Sec-Job Title',
            // 'Sec-Contact Email',
            // 'Sec-Contact Number',



            'Company Email',
            'Website',
            // 'Main Product Category',
            // 'Type of Business',
            // 'Participated Previously',
            'Fascia Name', // New Column

            //            'Booth Count',
            'GST No',
            'PAN No',
            'TAN No',
            // 'Region',
            // 'Submission Status',
            // 'Submission Date',
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


            //            'Secondary Email',

            // 'Terms and Conditions',
        ];
    }

    /**
     * Map data to match headings.
     */
    public function map($application): array
    {
        // Resolve sector names from relation or fallback to sector_id JSON
        $sectorDisplay = 'N/A';
        if (!empty($application->sectors) && $application->sectors->count() > 0) {
            $sectorDisplay = implode(', ', $application->sectors->pluck('name')->toArray());
        } else {
            $sectorIds = [];
            if (!empty($application->sector_id)) {
                if (is_string($application->sector_id)) {
                    $decoded = json_decode($application->sector_id, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $sectorIds = $decoded;
                    } elseif (is_numeric($application->sector_id)) {
                        $sectorIds = [(int) $application->sector_id];
                    }
                } elseif (is_numeric($application->sector_id)) {
                    $sectorIds = [(int) $application->sector_id];
                }
            }
            if (!empty($sectorIds)) {
                $sectorNames = Sector::whereIn('id', $sectorIds)->pluck('name')->toArray();
                if (!empty($sectorNames)) {
                    $sectorDisplay = implode(', ', $sectorNames);
                }
            }
        }

        return [
            // General Information
            $application->application_id ?? 'N/A',
            $application->company_name ?? 'N/A',
            optional($application)->stall_category ?? 'N/A',
            optional($application)->interested_sqm . ' SQM' ?? 'N/A',
            // optional($application)->pref_location ?? 'N/A',
            // optional($application)->semi_member == 1 ? 'Yes / ' . optional($application)->semi_memberID : 'No',
            // optional(optional($application->billingDetail)->country)->name ?? 'N/A',
            optional($application->headquartersCountry)->name ?? 'N/A',
            $application->payment_currency === 'USD' ? 'INR' : ($application->payment_currency ?? 'N/A'),

            // Contact Person Details (Using optional() for safety)
            optional($application->eventContact)->salutation . ' ' . optional($application->eventContact)->first_name . ' ' . optional($application->eventContact)->last_name ?? 'N/A',
            optional($application->eventContact)->job_title ?? 'N/A',
            optional($application->eventContact)->email ?? 'N/A',
            optional($application->eventContact)->contact_number ?? 'N/A',

            // Second Contact Person Details (Using optional() for safety) secondaryEventContact
            // optional($application->secondaryEventContact)->salutation . ' ' . optional($application->secondaryEventContact)->first_name . ' ' . optional($application->secondaryEventContact)->last_name ?? 'N/A',
            // optional($application->secondaryEventContact)->job_title ?? 'N/A',
            // optional($application->secondaryEventContact)->email ?? 'N/A',
            // optional($application->secondaryEventContact)->contact_number ?? 'N/A',


            $application->company_email ?? 'N/A',
            $application->website ?? 'N/A',

            // Main Product Category
            // $application->mainProductCategoryName($application->main_product_category) ?? 'N/A',

            // $application->type_of_business ?? 'N/A',
            // $application->participated_previous == 1 ? 'Yes' : 'No',
            //$application->fascia_name ?? 'N/A',

            //here we have to get the fascia name from the exhibitor_info table
            // $fasciaName = ExhibitorInfo::where('application_id', $application->id)->first()->fascia_name ?? 'N/A',

            $application->fascia_name = ExhibitorInfo::where('application_id', $application->id)->first()->fascia_name ?? 'N/A',

            //            $application->booth_count ?? 0,
            //            $application->payment_currency ?? 'N/A',
            $application->gst_no ?? 'N/A',
            $application->pan_no ?? 'N/A',
            $application->tan_no ?? 'N/A',
            // $application->region ?? 'N/A',
            // $application->submission_status ?? 'N/A',
            // $application->submission_date ?? 'N/A',
            $application->approved_date ?? 'N/A',
            $application->allocated_sqm ?? 0,

            // Sector(s)
            $sectorDisplay,

            // Product Groups Handling (Checking null and decoding JSON safely)
            !empty($application->product_groups) && is_string($application->product_groups)
                ? implode(', ', json_decode($application->product_groups, true) ?? [])
                : 'N/A',

            $application->address ?? 'N/A',
            $application->city_id ?? 'N/A',
            optional($application->state)->name ?? 'N/A',
            optional($application->country)->name ?? 'N/A',

            // Billing Details (Using optional() to prevent errors)
            optional($application->billingDetail)->billing_company ?? 'N/A',
            optional($application->billingDetail)->contact_name ?? 'N/A',
            optional($application->billingDetail)->email ?? 'N/A',
            optional($application->billingDetail)->phone ?? 'N/A',
            optional($application->billingDetail)->address ?? 'N/A',
            optional($application->billingDetail)->city_id ?? 'N/A',
            optional(optional($application->billingDetail)->state)->name ?? 'N/A',
            optional(optional($application->billingDetail)->country)->name ?? 'N/A',
            optional($application->billingDetail)->postal_code ?? 'N/A',


            //            optional($application->eventContact)->secondary_email ?? 'N/A',

            // Terms Acceptance Status
            // $application->terms_accepted == 1 ? 'Accepted' : 'Not Accepted',
        ];
    }
}
