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
            $query->where('payment_status', $this->paymentStatus);
        }

        $invoices = $query->get();
        $data = [];

        foreach ($invoices as $invoice) {
            $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();
            $application = $invoice->application;
            $stallNumber = $application->stallNumber ?? 'N/A';

            if ($invoice->co_exhibitorID) {
                $co_exhibitor = CoExhibitor::where('id', $invoice->co_exhibitorID)->first();
                $billingDetail->billing_company = $co_exhibitor->co_exhibitor_name;
            }

            $orders = RequirementsOrder::where('invoice_id', $invoice->id)
                ->with(['orderItems.requirement'])
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($orders as $order) {
                $orderItems = $order->orderItems->values();
                $totalItems = count($orderItems);

                foreach ($orderItems as $index => $item) {
                    $requirement = $item->requirement;

                    if ($requirement) {
                        $isLastItem = ($index === $totalItems - 1);
                        
                        $data[] = [
                            'Date' => $order->created_at->format('Y-m-d'),
                            'Invoice No' => $invoice->invoice_no,
                            'Company' => $billingDetail->billing_company ?? 'N/A',
                            'GST Number' => $application->gst_no ?? 'N/A',
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
                            'Amount Paid' => $invoice->amount_paid,
                        ];
                    }
                }
            }
        }

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
            'Amount Paid',

        ];
    }
}
