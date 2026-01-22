<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poster extends Model
{
    protected $fillable = [

        'tin_no',
        'pin_no',
        'draft_token',

        'sector',
        'nationality',
        'title',

        'lead_name',
        'lead_email',
        'lead_org',

        'lead_ccode',
        'lead_phone',

        'lead_addr',
        'lead_city',
        'lead_state',
        'lead_country',
        'lead_zip',

        // 'lead_presenter_same',

        'pp_name',
        'pp_email',
        'pp_org',
        'pp_website',

        'pp_ccode',
        'pp_phone',

        'pp_addr',
        'pp_city',
        'pp_state',
        'pp_country',
        'pp_zip',

        'co_auth_name_1',
        'co_auth_name_2',
        'co_auth_name_3',
        'co_auth_name_4',

        // 'co_auth_same',

        'acc_co_auth_name_1',
        'acc_co_auth_name_2',
        'acc_co_auth_name_3',
        'acc_co_auth_name_4',

        'theme',
        'abstract_text',

        'sess_abstract_path',
        'sess_abstract_original_name',
        'sess_abstract_size',
        'sess_abstract_mime',

        'lead_auth_cv_path',
        'lead_auth_cv_original_name',
        'lead_auth_cv_size',
        'lead_auth_cv_mime',

        'paymode',
        'currency',
        'base_amount',
        'discount_code',
        'discount_amount',
        'gst_amount',
        'processing_fee',
        'total_amount',

        'acc_count',
        'acc_unit_cost',
        'additional_charge',

        'status',
        'payment_status',

    ];

    // protected $casts = [
    //     'lead_presenter_same' => 'boolean',
    //     'co_auth_same' => 'boolean',
    // ];
}
