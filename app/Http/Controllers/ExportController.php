<?php

namespace App\Http\Controllers;

use App\Exports\ApplicationExport;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoicesExport;
use App\Exports\LeadRetrievalExport;
use App\Exports\ApprovedApplicationExport;
use App\Exports\StallInvoiceExport;
use App\Exports\ExhibitorInfoExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class ExportController extends Controller
{
    //
    public function export_users()
    {
        $name = 'users_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new UsersExport, $name);
    }

    //export all applications to excel
    public function export_applications()
    {

        // dd(request()->all());
        // accepted status from the request

        $statuses = array('all', 'in progress', 'initiated',  'submitted', 'approved', 'rejected',);

        //if the status is approved the redirect to route export.app.applications 
        if (request()->status == 'approved') {
            return redirect()->route('export.app.applications');
        }



        //let's validate the request status that it should be in the accepted status
        //if not then return with error message
        $validated = request()->validate([
            'status' => 'nullable|in:' . implode(',', $statuses)
        ]);


        //dd(request()->all());
        //get status from the request
        $status = request()->status ?? 'all';
        if ($status == 'initiated') {
            $status = 'in progress';
        }
        $name = 'applications_' . $status . '_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new ApplicationExport($status), $name);
    }

    // export approved applications 
    public function export_approved_applications()
    {
        //accepted status from the request
        $statuses = array('all', 'in progress', 'initiated',  'submitted', 'approved', 'rejected',);
        $validated = request()->validate([
            'status' => 'nullable|in:' . implode(',', $statuses)
        ]);

        $status = request()->status ?? 'all';
        if ($status == 'initiated') {
            $status = 'in progress';
        }
        $status = 'approved';
        $name = 'approved_applications_' . $status . '_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new ApprovedApplicationExport($status), $name);
    }

    //export all sponsorship applications to excel
    public function export_sponsorship_applications()
    {
        //        dd(request()->all());
        //accepted status from the request
        $statuses = array('all', 'in progress', 'initiated', 'submitted', 'approved', 'rejected',);
        $validated = request()->validate([
            'status' => 'nullable|in:' . implode(',', $statuses)
        ]);

        $status = request()->status ?? 'all';
        $name = 'sponsorship_applications_' . $status . '_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new SponsorshipApplicationExport($status), $name);
    }


    public function extra_requirements_export()
    {
        //send status such as paid or unpaid
        $statuses = array('all', 'paid', 'unpaid');
        $validated = request()->validate([
            'payment_status' => 'nullable|in:' . implode(',', $statuses)
        ]);
        $paymentStatus = request()->payment_status ?? 'all';

        // dd($paymentStatus);
        $filename = 'extra_requirements_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new InvoicesExport($paymentStatus), $filename);
    }

     // Export lead retrieval data to Excel
    public function export_lead_retrieval()
    {
        $statuses = array('all', 'paid', 'unpaid');
        $validated = request()->validate([
            'payment_status' => 'nullable|in:' . implode(',', $statuses)
        ]);
        $paymentStatus = request()->payment_status ?? 'all';

        $filename = 'lead_retrieval_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new LeadRetrievalExport($paymentStatus), $filename);
    }


    // Export stall invoice data to Excel
    public function export_stall_invoices()
    {
        Log::info('User ' . (Auth::user() ? Auth::user()->id : 'guest') . ' (IP: ' . request()->ip() . ') is exporting stall invoice data.');
        $filename = 'stall_invoices_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new StallInvoiceExport, $filename);
    }

    // Export exhibitor info to Excel
    public function export_exhibitor_info()
    {
        // Check if user is admin
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        if (!in_array(Auth::user()->role, ['admin', 'super-admin'])) {
            return redirect()->route('user.dashboard')->with('error', 'You are not authorized to access this page.');
        }

        $filename = 'exhibitor-info-' . now()->format('Y-m-d-His') . '.xlsx';
        return Excel::download(new ExhibitorInfoExport, $filename);
    }
}
