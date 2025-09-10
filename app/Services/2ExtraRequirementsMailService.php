<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\RequirementOrderItem;
use App\Models\RequirementsOrder;
use App\Models\BillingDetail;
use App\Models\CoExhibitor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Application;
use App\Models\PaymentGatewayResponse;
use App\Models\RequirementsBilling;

class ExtraRequirementsMailService
{
    public function prepareMailData(string $invoiceId): array
    {
        $invoice = Invoice::where('invoice_no', $invoiceId)->first();

        if (!$invoice) {
            return [
                'invoice' => null,
                'orderItems' => collect(),
                'subtotal' => 0,
                'processingCharge' => 0,
                'discount' => 0,
                'gst' => 0,
                'finalTotalPrice' => 0,
                'pendingAmount' => 0,
                'total_received' => 0,
                'billingCompany' => 'N/A',
                'billingEmail' => 'N/A',
                'invoice_Id' => 'N/A',
                'order_date' => 'N/A',
                'currency' => 'INR',
                'usdTotal' => 0,
                'surcharge' => 0,
                'surcharge_percentage' => 0,
            ];
        }

        $billingCompany = 'N/A';
        $billingEmail = 'N/A';

        $apps = Application::where('id', $invoice->application_id);
        $application = $apps->first();
        $exhibitor_name = $application->company_name;

        if (!empty($invoice->co_exhibitorID)) {
            $coExhibitor = CoExhibitor::where('id', $invoice->co_exhibitorID)->first();
            if ($coExhibitor) {
                $billingCompany = $coExhibitor->co_exhibitor_name;
                $billingEmail = $coExhibitor->email;
                $billingContactName = $coExhibitor->contact_person ?? 'N/A';
                $billingPhone = $coExhibitor->phone ?? 'N/A';
                $billingAddress = $coExhibitor->address1 ?? 'N/A';
                $exhibitor_name = $coExhibitor->co_exhibitor_name;
            }
        } else {
            $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();
            if ($billingDetail) {
                $billingCompany = $billingDetail->billing_company;
                $billingEmail = $billingDetail->email;
                $billingContactName = $billingDetail->contact_name ?? 'N/A';
                $billingPhone = $billingDetail->phone ?? 'N/A';
                $billingAddress = $billingDetail->address ?? 'N/A';
            }
        }

        $gst_number = 'N/A';

        // if there is requirements billing, with invoice_id, then use that billing details



        $gst_applicable = $application && $application->gst_compliance == 1;


        if ($gst_applicable) {
            // If GST compliance is true, get gst_number from application
            $gst_number = $application->gst_no ?? 'N/A';
        } else {
            $gst_number = 'N/A';
        }


        $requirementsBilling = RequirementsBilling::where('invoice_id', $invoice->id)->first();

        //dd($requirementsBilling);
        if ($requirementsBilling) {
            $billingCompany = $requirementsBilling->billing_company ?? $billingCompany;
            $billingEmail = $requirementsBilling->billing_email ?? $billingEmail;
            $billingContactName = $requirementsBilling->billing_name ?? $billingContactName;
            $billingPhone = $requirementsBilling->billing_phone ?? $billingPhone;
            $billingAddress = $requirementsBilling->billing_address ?? $billingAddress;
            $gst_number = $requirementsBilling->gst_no;
        }

        // dd($billingCompany, $billingEmail, $billingContactName, $billingPhone, $billingAddress, $gst_number);

        // if gst_number is null or empty string  then gst_applicable is 0
        if (empty($gst_number)) {
            $gst_applicable = 0;
        } else {
            $gst_applicable = 1;
        }

        $amountPay = $invoice->total_final_price;

        // To match invoice_no like 'INV-SEMI25-D6CDF2%' (e.g., INV-SEMI25-D6CDF2_1749126047)
        $payment = PaymentGatewayResponse::where('order_id', 'like', $invoice->invoice_no . '%')
            ->where('status', 'Success')
            ->where('amount', $amountPay)
            ->first();
        // $sql = "SELECT * FROM payment_gateway_response WHERE invoice_id LIKE ? AND status = ? LIMIT 1";
        // $bindings = [$invoice->invoice_no . '%', 'Success'];


        // $payment = DB::select($sql, $bindings);
        // $payment = $payment ? (object) $payment[0] : null;
        // dd($sql, $bindings, $payment);



        $order = RequirementsOrder::where('invoice_id', $invoice->id)->first();
        //dd($order);
        $orderItems = collect();
        $subtotal = 0;

        if ($order) {
            $orderItems = RequirementOrderItem::where('requirements_order_id', $order->id)
                ->join('extra_requirements', 'requirement_order_items.requirement_id', '=', 'extra_requirements.id')
                ->select(
                    'extra_requirements.item_name',
                    'requirement_order_items.unit_price',
                    'requirement_order_items.quantity',
                    DB::raw('(requirement_order_items.unit_price * requirement_order_items.quantity) as total_price')
                )
                ->get();

            $subtotal = $orderItems->sum('total_price');
        }

        $orderItemsArray = $orderItems->map(function ($item) {
            return [
                'item_name' => $item->item_name,
                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
                'total_price' => $item->total_price,
            ];
        })->toArray();

        $invoiceArrray = $invoice->toArray();

        $data = [
            'invoice' => $invoice,
            'orderItems' => $orderItemsArray,
            'subtotal' => $subtotal,
            'processingCharge' => $invoice->processing_charges,
            'discount' => $invoice->discount,
            'gst' => $invoice->gst,
            'finalTotalPrice' => $invoice->amount,
            'pendingAmount' => $invoice->pending_amount,
            'total_received' => $invoice->amount_paid,
            'billingCompany' => $billingCompany,
            'billingEmail' => $billingEmail,
            'invoice_Id' => $invoice->invoice_no,
            'order_date' => $invoice->updated_at,
            'currency' => $invoice->currency,
            'usdTotal' => $invoice->int_amount_value,
            'gst_number' => $gst_number ?? 'N/A',
            'gst_applicable' => $gst_applicable,
            'billingContactName' => $billingContactName ?? 'N/A',
            'billingPhone' => $billingPhone ?? 'N/A',
            'billingAddress' => $billingAddress ?? 'N/A',
            'paymentStatus' => $invoice->payment_status ?? 'Unpaid',
            'payment_method' => $payment ? $payment->payment_method : null,
            'payment_reference' => $payment ? $payment->transaction_id : null,
            'payment_Date' => $payment ? $payment->trans_date : null,
            'exhibitor_name' => $exhibitor_name ?? 'N/A',
            'booth_no' => $application->stallNumber ?? 'N/A',
            'stall_size' => $application->allocated_sqm ?? 'N/A',
            'surcharge' => $invoice->surCharge ?? 0,
            'surcharge_percentage' => $invoice->surChargepercentage ?? 0,


        ];

        // dd($data);

        return $data;
    }
}
