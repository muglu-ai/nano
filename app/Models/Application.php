<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Application extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'user_id',
        'billing_country_id',
        'gst_compliance',
        'tan_compliance',
        'company_name',
        'address',
        'postal_code',
        'city_id',
        'state_id',
        'country_id',
        'landline',
        'company_email',
        'website',
        'main_product_category',
        'headquarters_country_id',
        'type_of_business',
        'participated_previous',
        'semi_member',
        'stall_category',
        'boothDescription',
        'booth_count',
        'payment_currency',
        'status',
        'certificate',
        'gst_no',
        'pan_no',
        'tan_no',
        'participant_type',
        'previous_participation',
        'interested_sqm',
        'product_groups',
        'terms_accepted',
        'cancellation_terms',
        'semi_memberID',
        'interested_sqm',
        'product_groups',
        'region',
        'submission_status',
        'application_id',
        'submission_date',
        'approved_date',
        'participation_type',
        'is_pavilion',
        'has_sponsorship',
        'allocated_sqm',
        'pavilion_id',
        'event_id',
        'sponsorship_item_id',
        'sponsorship_count',
        'application_type',
        'sector_id',
        'subSector',
        'rejection_reason',
        'rejected_date',
        'stallNumber',
        'country_name',
        'assoc_mem',
        'pref_location',
        'membership_verified',
        'spon_discount_eligible',
        'cart_data',
        'withdraw_title',
        'sponsor_only',
        'fascia_name',
        'logo_link', // New field for logo link
        'userActive',
        'companyYears',
        'salesPerson',
        'exhibitorType',
        'tag',
        'approved_by',
        'RegSource',
        'promocode', // NEW: Promocode column
        'zone',
        'declarationStatus',
        'hallNo',
        'pavilionName',
    ];

    /* Todo : Addedd the following new fields
participant_type
previous_participation
interested_sqm
product_groups
cancellation_terms
submission_status
*/
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function eventContact()
    {
        return $this->hasOne(EventContact::class, 'application_id', 'id');
    }

    public function secondaryEventContact()
    {
        return $this->hasOne(SecondaryEventContact::class, 'application_id', 'id');
    }


    //    public function billingDetail()
    //    {
    //        return $this->hasOne(BillingDetail::class);
    //    }

    public function billingDetail()
    {
        return $this->hasOne(BillingDetail::class);
    }
    public function coExhibitors()
    {
        return $this->hasMany(CoExhibitor::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    // public function invoice()
    // {
    //     return $this->hasOne(Invoice::class, 'application_id', 'application_id')
    //             ->where('application_no', $this->application_id)
    //             ->where('type', 'Stall Booking');
    // }

    //headquartersCountry
    public function headquartersCountry()
    {
        return $this->belongsTo(Country::class, 'headquarters_country_id');
    }

    public function products()
    {
        return $this->belongsToMany(ProductCategory::class, 'application_products');
    }

    public function sectors()
    {
        return $this->belongsToMany(Sector::class, 'application_sectors');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    //let's check whether use has a application to a event id or not
    public function scopeHasApplication($query, $user_id, $event_id)
    {
        return $query->where('user_id', $user_id)->where('event_id', $event_id);
    }
    //example for $hasApplication = Application::hasApplication($user_id, $event_id)->exists();

    //call event name from event table
    public function event()
    {
        return $this->belongsTo(Events::class);
    }

    public function exhibitionParticipant()
    {
        return $this->hasOne(ExhibitionParticipant::class);
    }

    public function stallManning()
    {
        return $this->hasManyThrough(StallManning::class, ExhibitionParticipant::class);
    }

    public function complimentaryDelegates()
    {
        return $this->hasManyThrough(ComplimentaryDelegate::class, ExhibitionParticipant::class);
    }

    //check application is there in sponsorships or not
    public function sponsorships()
    {
        return $this->hasOne(Sponsorship::class);
    }
    public function sponsorship()
    {
        return $this->hasMany(Sponsorship::class, 'application_id', 'id');
    }

    public function sponsorshipsIfSponsor()
    {
        return $this->hasMany(Sponsorship::class, 'application_id', 'id')
            ->whereHas('application', function ($query) {
                $query->where('has_sponsorship', 1);
            });
    }

    public function mainProductCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'main_product_category_id')->select('id', 'name');
    }

    public function requirementsOrders()
    {
        return $this->hasMany(RequirementsOrder::class);
    }

    //get the main category name by passing the id in the main_product_category_id
    public function mainProductCategoryName($id)
    {
        $category = ProductCategory::find($id);
        return $category ? $category->name : null;
    }

    //GET THE exhibitor prodducts   
    public function exhibitorProducts()
    {
        return $this->hasMany(ExhibitorProduct::class, 'application_id', 'id');
    }

    public function meetingRoomBookings(): HasMany
    {
        return $this->hasMany(MeetingRoomBooking::class);
    }



    //fetch the application with user, event and billing details
    //$application = Application::with(['user', 'event', 'billingDetail'])->find($id);

    /**
     * Accessor: Get allocated_sqm, fallback to interested_sqm if empty
     * 
     * @param mixed $value
     * @return string|null
     */
    public function getAllocatedSqmAttribute($value)
    {
        // If allocated_sqm is empty/null/0, use interested_sqm as fallback
        if (empty($value) || $value === null || $value === '0' || $value === 0 || trim($value) === '') {
            // Access raw attribute to avoid recursion
            $interestedSqm = $this->attributes['interested_sqm'] ?? null;
            return !empty($interestedSqm) ? $interestedSqm : $value;
        }
        return $value;
    }

}
