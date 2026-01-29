<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\ExtraRequirement;

class AdminOrderController extends Controller {
    public function index() {
        // Fetch all orders grouped by item and calculate total quantity & revenue
        $orderSummary = Order::selectRaw('extra_requirement_id, SUM(quantity) as total_quantity, SUM(total_price) as total_revenue')
            ->groupBy('extra_requirement_id')
            ->with('extraRequirement')
            ->get();

        // Fetch all individual orders
        $orders = Order::with(['user', 'extraRequirement'])->get();

        return view('admin.orders.index', compact('orderSummary', 'orders'));
    }
}

