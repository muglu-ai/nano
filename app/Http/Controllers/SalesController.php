<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesController extends Controller
{

    //+55% since last month
    //can we show the sales of the last month





    // function to display index page of sales
    public function index()
    {
        //2025-02-03 10:22:56
        $startDate = Carbon::parse('2025-02-03 00:00:00');
        $endDate = Carbon::parse(config(constant('EVENT_DATE_END')))->endOfDay();
        $invoices = Invoice::get();

        //get the currency
        $currency = 'INR';


        $totalRevenue = $invoices->sum('total_final_price');


        $totalPaid = $invoices->where('payment_status', 'paid')->sum('total_final_price');
        $totalUnpaid = $invoices->where('payment_status', 'unpaid')->sum('total_final_price');
        $totalOverdue = $invoices->where('payment_status', 'overdue')->sum('total_final_price');
        $totalPartial = $invoices->where('payment_status', 'partial')->sum('total_final_price');

        return view('sales.index', compact( 'startDate', 'endDate',
            'invoices', 'totalRevenue', 'totalPaid', 'totalUnpaid', 'totalOverdue', 'totalPartial', 'currency'
        ));
    }
}
