<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\RequirementOrderItem;
use App\Models\RequirementsOrder;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\BillingDetail;
use App\Models\CoExhibitor;
use Illuminate\Support\Facades\Log;



class ExtraRequirementsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $orderItems;
    public $subtotal;
    public $processingCharge;
    public $orderTotal;
    public $discount;
    public $gst;
    public $finalTotalPrice;
    public $pendingAmount;
    public $total_received;

    public $billingCompany;
    public $billingEmail;
    public $invoice_Id;

    public $order_date;
    public $currency;

    public $usdTotal;

    public $gst_number;
    public $gst_applicable;
    public $billingContactName;
    public $billingPhone;
    public $billingAddress;
    public $coExhibitorName;
    public $coExhibitorEmail;
    public $coExhibitorContactName;
    public $paymentStatus;
    public $payment_method;
    public $payment_reference;
    public $payment_Date;
    public $exhibitor_name;
    public $booth_no;
    public $stall_size;
    public $surcharge;
    public $surcharge_percentage;




    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {

        // dd($data);
        // Log::info("message data: " . json_encode($data));
        $this->invoice = $data['invoice'];
        $this->orderItems = $data['orderItems'];
        $this->subtotal = $data['subtotal'];
        $this->processingCharge = $data['processingCharge'];
        $this->discount = $data['discount'];
        $this->gst = $data['gst'];
        $this->finalTotalPrice = $data['finalTotalPrice'];
        $this->pendingAmount = $data['pendingAmount'];
        $this->total_received = $data['total_received'];
        $this->billingCompany = $data['billingCompany'];
        $this->billingEmail = $data['billingEmail'];
        $this->invoice_Id = $data['invoice_Id'];
        $this->order_date = $data['order_date'];
        $this->currency = $data['currency'];
        $this->usdTotal = $data['usdTotal'];
        $this->gst_number = $data['gst_number'];
        $this->gst_applicable = $data['gst_applicable'] ?? false;
        $this->billingContactName = $data['billingContactName'] ?? 'N/A';
        $this->billingPhone = $data['billingPhone'] ?? 'N/A';
        $this->billingAddress = $data['billingAddress'] ?? 'N/A';
        $this->paymentStatus = $data['paymentStatus'] ?? 'unpaid';
        $this->payment_method = $data['payment_method'] ?? null;
        $this->payment_reference = $data['payment_reference'] ?? null;
        $this->payment_Date = $data['payment_Date'] ?? null;
        $this->surcharge = $data['surcharge'] ?? 0;
        $this->surcharge_percentage = $data['surcharge_percentage'] ?? 0;

        // If co-exhibitor details are provided, set them
        if (isset($data['coExhibitor'])) {
            $this->coExhibitorName = $data['coExhibitor']['name'] ?? 'N/A';
            $this->coExhibitorEmail = $data['coExhibitor']['email'] ?? 'N/A';
            $this->coExhibitorContactName = $data['coExhibitor']['contact_name'] ?? 'N/A';
        } else {
            $this->coExhibitorName = 'N/A';
            $this->coExhibitorEmail = 'N/A';
            $this->coExhibitorContactName = 'N/A';
        }
        $this->exhibitor_name = $data['exhibitor_name'] ?? 'N/A';
        $this->booth_no = $data['booth_no'] ?? 'N/A';
        $this->stall_size = $data['stall_size'] ?? 'N/A';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Extra Requirements Order  - SEMICON India 2025',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.extra_requirements_mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
