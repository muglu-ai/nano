<?php 
namespace App\Exports;

use App\Models\Attendee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class AttendeesExport implements FromCollection, WithHeadings
{

    // construct the export with a specific status
    public function __construct($status = null)
    {
        $this->status = $status;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    // Fetch the collection of attendees based on the status
    
    public function collection(): Collection
    {
        $query = Attendee::query();

        if ($this->status === 1 || $this->status === 0) {
            $query->where('inaugural_session', $this->status);
        }

        $attendees = $query->get();

        $i = 0;
        return $attendees->map(function ($attendee) use (&$i) {
            $i++;
            $purpose = $attendee->purpose;
            $products = $attendee->products;
            $business_nature = $attendee->business_nature;
            $event_days = $attendee->event_days;

            // Normalize purpose to comma-separated string
            if (is_string($purpose)) {
            $decoded = json_decode($purpose, true);
            $purpose = is_array($decoded) ? implode(', ', $decoded) : $purpose;
            } elseif (is_array($purpose)) {
            $purpose = implode(', ', $purpose);
            }

            // Normalize products to comma-separated string
            if (is_string($products)) {
            $decoded = json_decode($products, true);
            $products = is_array($decoded) ? implode(', ', $decoded) : $products;
            } elseif (is_array($products)) {
            $products = implode(', ', $products);
            }

            // Normalize business_nature to comma-separated string
            if (is_string($business_nature)) {
            $decoded = json_decode($business_nature, true);
            $business_nature = is_array($decoded) ? implode(', ', $decoded) : $business_nature;
            } elseif (is_array($business_nature)) {
            $business_nature = implode(', ', $business_nature);
            }

            // Normalize event_days to comma-separated string
            if (is_string($event_days)) {
            $decoded = json_decode($event_days, true);
            $event_days = is_array($decoded) ? implode(', ', $decoded) : $event_days;
            } elseif (is_array($event_days)) {
            $event_days = implode(', ', $event_days);
            }

            return [
            'id' => $i,
                'unique_id' => $attendee->unique_id,
                // 'status' => $attendee->status,
                'badge_category' => $attendee->badge_category,
                'title' => $attendee->title,
                'first_name' => $attendee->first_name,
                'last_name' => $attendee->last_name,
                'designation' => $attendee->designation,
                'company' => $attendee->company,
                'address' => $attendee->address,
                'country' => $attendee->countryRelation ? $attendee->countryRelation->name : null,
                'state' => $attendee->stateRelation ? $attendee->stateRelation->name : null,
                'city' => $attendee->city,
                'postal_code' => $attendee->postal_code,
                'mobile' => $attendee->mobile,
                'email' => $attendee->email,
                'purpose' => $purpose,
                'products' => $products,
                'business_nature' => $business_nature,
                // 'job_function' => $attendee->job_function,
                'job_category' => $attendee->job_category,
                'job_subcategory' => $attendee->job_subcategory,
                'profile_picture' => $attendee->profile_picture 
                ? '' . env('APP_URL') . '/' . ltrim($attendee->profile_picture, '/') . '' 
                : null,
                'id_card_type' => $attendee->id_card_type,
                'id_card_number' => $attendee->id_card_number,
                'consent' => $attendee->consent ? 'Yes' : 'No',
                'created_at' => $attendee->created_at,
                // 'updated_at' => $attendee->updated_at,
                // 'qr_code_path' => $attendee->qr_code_path,
                // 'source' => $attendee->source,
                'inaugural_session' => $attendee->inaugural_session ? 'Yes' : 'No',
                'registration_type' => $attendee->registration_type,
                'event_days' => $event_days,
                'other_job_category' => $attendee->other_job_category,
                'promotion_consent' => $attendee->promotion_consent ? 'Yes' : 'No',
                'startup' => $attendee->startup ? 'Yes' : 'No',
            ];
        });
        
    }

    public function headings(): array
    {
        return [
            'Sr No',
            'Unique ID',
            // 'Status',
            'Badge Category',
            'Title',
            'First Name',
            'Last Name',
            'Designation',
            'Company',
            'Address',
            'Country',
            'State',
            'City',
            'Postal Code',
            'Mobile',
            'Email',
            'Purpose',
            'Products',
            'Business Nature',
            // 'Job Function',
            'Job Category',
            'Job Subcategory',
            'Profile Picture',
            'ID Card Type',
            'ID Card Number',
            'Consent',
            'Created At',
            // 'Updated At',
            // 'QR Code Path',
            // 'Source',
            'Inaugural Session',
            'Registration Type',
            'Event Days',
            'Other Job Category',
            'Promotion Consent',
            'Startup',
        ];
    }
}
