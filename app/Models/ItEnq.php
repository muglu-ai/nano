<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItEnq extends Model
{
    protected $table = 'it_enq_table';
    protected $primaryKey = 'del_srno';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'srno',
        'title',
        'fname',
        'lname',
        'name',
        'org',
        'desig',
        'addr',
        'city',
        'state',
        'country',
        'zip',
        'cellno',
        'fone',
        'fax',
        'email',
        'website',
        'enq1',
        'enq2',
        'enq3',
        'enq4',
        'enq5',
        'enq6',
        'enq7',
        'enq8',
        'enq9',
        'comments',
        'task_assigned_to1',
        'task_assigned_to2',
        'task_assigned_from',
        'sp_msg',
        'know_from',
        'ddate',
        'ttime',
        'src_from',
        'reg_id',
        'prospect',
        'status',
        'status_comment',
        'event_name',
        'event_year',
        'followup_fs_id',
        'followup_user_id',
        'followup_user_name',
        'followup_prospect',
        'followup_status',
        'followup_status_comment',
        'followup_ddate',
        'followup_dtime',
        'del_admin_name',
        'del_date',
        'del_time',
        'sector',
    ];
}

