<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\Ticket\TicketRegistration;
use Illuminate\Support\Facades\Log;

class TicketRegistrationsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = TicketRegistration::with([
            'event',
            'contact',
            'order.items.ticketType',
            'order.payments',
            'delegates',
            'registrationCategory'
        ]);

        // Apply filters
        if (!empty($this->filters['event_id'])) {
            $query->where('event_id', $this->filters['event_id']);
        }

        if (!empty($this->filters['nationality'])) {
            $query->where('nationality', $this->filters['nationality']);
        }

        if (!empty($this->filters['status'])) {
            $query->whereHas('order', function($orderQuery) {
                $orderQuery->where('status', $this->filters['status']);
            });
        }

        if (!empty($this->filters['gateway'])) {
            $query->whereHas('order', function($orderQuery) {
                $orderQuery->whereHas('payments', function($paymentQuery) {
                    $paymentQuery->where('gateway_name', $this->filters['gateway']);
                });
            });
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('company_phone', 'like', "%{$search}%")
                  ->orWhere('gstin', 'like', "%{$search}%")
                  ->orWhereHas('contact', function($contactQuery) use ($search) {
                      $contactQuery->where('email', 'like', "%{$search}%")
                                    ->orWhere('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order', function($orderQuery) use ($search) {
                      $orderQuery->where('order_no', 'like', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function map($registration): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $order = $registration->order;
        $contact = $registration->contact;
        $event = $registration->event;
        
        // Get payment info
        $payment = null;
        $gatewayName = '';
        $transactionId = '';
        $paymentDate = '';
        
        if ($order) {
            $payment = \App\Models\Ticket\TicketPayment::whereJsonContains('order_ids_json', $order->id)
                ->where('status', 'completed')
                ->orderBy('paid_at', 'desc')
                ->first();
            
            if ($payment) {
                $gatewayName = $payment->gateway_name ?? '';
                $transactionId = $payment->gateway_txn_id ?? '';
                $paymentDate = $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : '';
            }
        }

        // Determine currency
        $currency = $registration->nationality === 'International' ? 'USD' : 'INR';
        $currencySymbol = $currency === 'USD' ? '$' : '₹';

        // Get ticket types and quantities from order items
        $ticketTypes = [];
        $quantities = [];
        if ($order && $order->items) {
            foreach ($order->items as $item) {
                $ticketTypeName = $item->ticketType ? $item->ticketType->name : 'N/A';
                $ticketTypes[] = $ticketTypeName;
                $quantities[] = $item->quantity;
            }
        }

        // Get delegate details
        $delegateNames = [];
        $delegateEmails = [];
        $delegatePhones = [];
        
        foreach ($registration->delegates as $delegate) {
            $delegateNames[] = trim("{$delegate->salutation} {$delegate->first_name} {$delegate->last_name}");
            $delegateEmails[] = $delegate->email ?? '';
            $delegatePhones[] = $delegate->phone ?? '';
        }

        return [
            $rowNumber,
            $order ? $order->order_no : '',
            $registration->created_at ? $registration->created_at->format('Y-m-d H:i:s') : '',
            $registration->company_name ?? '',
            $contact ? $contact->name : '',
            $contact ? $contact->email : '',
            $contact ? $contact->phone : '',
            $registration->company_country ?? '',
            $registration->company_state ?? '',
            $registration->company_city ?? '',
            $registration->company_phone ?? '',
            $registration->nationality ?? '',
            $currency,
            implode(', ', $ticketTypes),
            implode(', ', $quantities),
            $order ? number_format($order->subtotal, 2) : '0.00',
            $order ? number_format($order->gst_total, 2) : '0.00',
            $order ? number_format($order->processing_charge_total, 2) : '0.00',
            $order ? number_format($order->discount_amount, 2) : '0.00',
            $order ? number_format($order->total, 2) : '0.00',
            $order ? $order->status : '',
            $gatewayName,
            $transactionId,
            $paymentDate,
            implode('; ', $delegateNames),
            implode('; ', $delegateEmails),
            implode('; ', $delegatePhones),
            $registration->gstin ?? '',
            $registration->gst_legal_name ?? '',
            $registration->registrationCategory ? $registration->registrationCategory->name : '',
            $event ? $event->event_name : '',
            $event ? $event->event_year : '',
        ];
    }

    public function headings(): array
    {
        return [
            'Sr No',
            'Order Number (TIN)',
            'Registration Date',
            'Company Name',
            'Contact Name',
            'Contact Email',
            'Contact Phone',
            'Company Country',
            'Company State',
            'Company City',
            'Company Phone',
            'Nationality',
            'Currency',
            'Ticket Types',
            'Quantities',
            'Subtotal',
            'GST Total',
            'Processing Charge',
            'Discount Amount',
            'Total Amount',
            'Payment Status',
            'Payment Gateway',
            'Transaction ID',
            'Payment Date',
            'Delegate Names',
            'Delegate Emails',
            'Delegate Phones',
            'GSTIN',
            'GST Legal Name',
            'Registration Category',
            'Event Name',
            'Event Year',
        ];
    }
}
