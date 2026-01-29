<?php 
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Attendee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class AttendeesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{

    
    protected $status;

    public function __construct($status = null)
    {
        ini_set('memory_limit', '-1'); // Set memory limit here
        //time limit set to unlimited
         set_time_limit(0);
        $this->status = $status;
    }

    public function query()
    {
        $query = Attendee::query();
        Log::info("message" . $this->status);

        if ($this->status == 1 || $this->status == 0) {
            $query->where('inaugural_session', $this->status);
        }

        return $query;
    }

    public function map($attendee): array
    {
        $purpose = $this->normalize($attendee->purpose);
        $products = $this->normalize($attendee->products);
        $business_nature = $this->normalize($attendee->business_nature);
        $event_days = $this->normalize($attendee->event_days);

        static $i = 0;
        $i++;

        return [
            $i,
            $attendee->unique_id ?: '',
            $attendee->badge_category ?: '',
            $attendee->title ?: '',
            $attendee->first_name ?: '',
            $attendee->last_name ?: '',
            $attendee->designation ?: '',
            $attendee->company ?: '',
            $attendee->address ?: '',
            optional($attendee->countryRelation)->name ?: '',
            optional($attendee->stateRelation)->name ?: '',
            $attendee->city ?: '',
            $attendee->postal_code ?: '',
            $attendee->mobile ?: '',
            $attendee->email ?: '',
            $purpose ?: '',
            $products ?: '',
            $business_nature ?: '',
            $attendee->job_category ?: '',
            $attendee->job_subcategory ?: '',
            $attendee->profile_picture ? (env('APP_URL') . '/' . ltrim($attendee->profile_picture, '/')) : '',
            $attendee->id_card_type ?: '',
            $attendee->id_card_number ?: '',
            $attendee->consent ? 'Yes' : 'No',
            $attendee->created_at ?: '',
            $attendee->inaugural_session ? 'Yes' : 'No',
            $attendee->registration_type ?: '',
            $event_days ?: '',
            $attendee->other_job_category ?: '',
            $attendee->promotion_consent ? 'Yes' : 'No',
            $attendee->startup ? 'Yes' : 'No',
        ];
    }

    protected function normalize($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? implode(', ', $decoded) : $value;
        } elseif (is_array($value)) {
            return implode(', ', $value);
        }
        return $value;
    }

    public function headings(): array
    {
        return [
            'Sr No',
            'Unique ID',
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
            'Job Category',
            'Job Subcategory',
            'Profile Picture',
            'ID Card Type',
            'ID Card Number',
            'Consent',
            'Created At',
            'Inaugural Session',
            'Registration Type',
            'Event Days',
            'Other Job Category',
            'Promotion Consent',
            'Startup',
        ];
    }
}
