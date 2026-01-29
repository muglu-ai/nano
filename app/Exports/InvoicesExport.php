<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\BillingDetail;
use App\Models\RequirementsOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\RequirementOrderItem;
use Illuminate\Support\Facades\Log;
use App\Models\CoExhibitor;
use App\Models\RequirementsBilling;

class InvoicesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $paymentStatus;

    /**
     * Constructor to accept payment status.
     */
    public function __construct($paymentStatus = null)
    {
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * Fetch invoices of type "extra_requirement" and process the details for export.
     */
    public function collection()
    {
        $query = Invoice::where('type', 'extra_requirement');

        
        if ($this->paymentStatus && in_array($this->paymentStatus, ['paid', 'unpaid'])) {
            // if paid is passed then fetch the paid and partial both 
            if ($this->paymentStatus == 'paid') {
                $query->whereIn('payment_status', ['paid', 'partial']);
            } else {
                $query->where('payment_status', 'unpaid');
            }
            // $query->where('payment_status', $this->paymentStatus);
        }

        $invoices = $query->get();
        $data = [];

        foreach ($invoices as $invoice) {
            $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();
            $application = $invoice->application;
            $stallNumber = $application->stallNumber ?? 'N/A';
            $companyName = $application->company_name ?? 'N/A';

            if ($invoice->co_exhibitorID) {
                $co_exhibitor = CoExhibitor::where('id', $invoice->co_exhibitorID)->first();
                // or if the the co_exhibitor is not found then search where co_exhibitor_id is $invoice->co_exhibitorID
                if (!$co_exhibitor) {
                    $co_exhibitor = CoExhibitor::where('co_exhibitor_id', $invoice->co_exhibitorID)->first();
                }
                $billingDetail->billing_company = $co_exhibitor->co_exhibitor_name;
                $companyName = $co_exhibitor->co_exhibitor_name;
                $billingDetail->email = $co_exhibitor->email;
                $billingDetail->phone = $co_exhibitor->phone;
                $billingDetail->address = $co_exhibitor->address1;
                $billingDetail->city_id = $co_exhibitor->city;
                $billingDetail->state->name = $co_exhibitor->state;
                $billingDetail->postal_code = $co_exhibitor->zip;
                $billingDetail->country->name = $co_exhibitor->country;


            }

            $companyName =  $companyName;
            $gst_no = $application->gst_no ?? 'N/A';



            // dd($billingDetail->toArray(), $companyName); // Debugging line to check the billing detail structure

            // if the invoice_id is found in RequirementsBilling, fetch the billing details
            $requirementsBilling = RequirementsBilling::where('invoice_id', $invoice->id)->first();
            if ($requirementsBilling) {

                $billingDetail->billing_company = $requirementsBilling->billing_company;
                $billingDetail->address = $requirementsBilling->address;
                $billingDetail->city_id = $requirementsBilling->billing_city;
                $billingDetail->state_id = $requirementsBilling->state_id;
                $billingDetail->postal_code = $requirementsBilling->zipcode;
                $billingDetail->country_id = $requirementsBilling->country_id;
                $billingDetail->email = $requirementsBilling->billing_email;
                $billingDetail->phone = $requirementsBilling->billing_phone;
                $billingDetail->gst_no = $requirementsBilling->gst_no;
                $gst_no = $requirementsBilling->gst_no;
            }



            $billingDetail->companyName = $companyName;

            $orders = RequirementsOrder::where('invoice_id', $invoice->id)
                ->with(['orderItems.requirement'])
                ->where('delete', 0)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($orders as $order) {
                $orderItems = $order->orderItems->values();
                $totalItems = count($orderItems);

                foreach ($orderItems as $index => $item) {
                    $requirement = $item->requirement;

                    if ($requirement) {
                        $isLastItem = ($index === $totalItems - 1);

                        // --- Payment Method Logic Start ---
                        $paymentMethod = 'unknown';
                        if ($invoice) {
                            $invoiceNoPart = explode('_', $invoice->invoice_no)[0];

                            // 1. Prefer successful online payment if exists
                            $gatewayPayment = \DB::table('payment_gateway_response')
                                ->whereRaw("LEFT(order_id, LENGTH(?)) = ?", [$invoiceNoPart, $invoiceNoPart])
                                ->whereIn('status', ['Completed', 'Success'])
                                ->orderByDesc('id')
                                ->first();

                            if ($gatewayPayment) {
                                $paymentMethod = lcfirst(str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', strtolower($gatewayPayment->gateway)))));
                            } else {
                                // 2. Only if no successful online payment, check for offline
                                $offlinePayment = \DB::table('payments')->where('invoice_id', $invoice->id)->first();
                                if ($offlinePayment) {
                                    $paymentMethod = 'offline';
                                } else {
                                    // 3. If neither, check for any gateway record (even if not successful)
                                    $gatewayPaymentAny = \DB::table('payment_gateway_response')
                                        ->whereRaw("LEFT(order_id, LENGTH(?)) = ?", [$invoiceNoPart, $invoiceNoPart])
                                        ->orderByDesc('id')
                                        ->first();
                                    if ($gatewayPaymentAny) {
                                        $paymentMethod = lcfirst(str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', strtolower($gatewayPaymentAny->gateway)))));
                                    }
                                }
                            }

                            // Fallback to currency if still unknown
                            if ($paymentMethod == 'unknown') {
                                $paymentMethod = $invoice->currency == 'USD' ? 'paypal' : 'ccavenue';
                            }
                        }

                        $data[] = [
                            'Date' => $order->created_at->format('Y-m-d'),
                            'Invoice No' => $invoice->invoice_no,
                            'Company' => $billingDetail->companyName ?? 'N/A',
                            'Billing Company' => $billingDetail->billing_company ?? 'N/A',
                            'GST Number' => $gst_no ?? 'N/A',
                            'Address' => $billingDetail->address . ' ' . $billingDetail->city_id ?? 'N/A',
                            'State' => $billingDetail->state->name ?? 'N/A',
                            'PinCode' => $billingDetail->postal_code ?? 'N/A',
                            'Country' => $billingDetail->country->name ?? 'N/A',
                            'Email' => $billingDetail->email ?? 'N/A',
                            'Phone' => $billingDetail->phone ?? 'N/A',
                            'Stall Number' => $stallNumber,
                            'Requirement Name' => $requirement->item_name,
                            'Quantity' => $item->quantity,
                            'Unit Price' => $item->unit_price,
                            'Sub Total' => $item->quantity * $item->unit_price,
                            'Processing Fee' => $isLastItem ? $invoice->processing_charges : '',
                            'GST Amount' => $isLastItem ? $invoice->gst : '',
                            'Total Invoice Amount' => $isLastItem ? $invoice->amount : '',
                            'Pay Status' => $invoice->payment_status,
                            'Currency' => ($invoice->currency == 'USD')
                                ? ($invoice->currency . '@' . ($invoice->usd_rate ?? ''))
                                : ($invoice->currency ?? 'INR'),
                            'Amount Paid' => $isLastItem ? $invoice->amount_paid : '',
                            'Payment Method' => $paymentMethod,
                            'Updated Date' => $invoice->updated_at,

                        ];
                    }
                }
            }
        }

        // dd($data); // Debugging line to check the data structure

        return collect($data);
    }

    /**
     * Set the headings for the Excel export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Date',
            'Invoice No',
            'Company',
            'Billing Company',
            'GST Number',
            'Address',
            'State',
            'PinCode',
            'Country',
            'Email',
            'Phone',
            'Stall Number',
            'Requirement Name',
            'Quantity',
            'Unit Price',
            'SubTotal',
            'Processing Fee',
            'GST Amount',
            'Total Invoice Amount',

            'Payment Status',
            'Currency',
            'Amount Paid',
            'Payment Method',
            'Update Date',

        ];
    }
}
