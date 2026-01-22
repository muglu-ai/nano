<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use App\Models\Invoice;
use App\Models\EventContact;
use App\Models\Sector;

class ExhibitorRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $invoice;
    public $contact;
    public $billingDetail;
    public $paymentUrl;
    public $sectorName;
    public $emailType;
    public $isApprovalEmail;

    /**
     * Create a new message instance.
     * 
     * @param Application $application
     * @param Invoice $invoice
     * @param EventContact|null $contact
     * @param string $emailType 'registration' | 'approval'
     */
    public function __construct(Application $application, Invoice $invoice, $contact = null, string $emailType = 'registration')
    {
        $this->application = $application;
        $this->invoice = $invoice;
        $this->contact = $contact;
        $this->emailType = $emailType;
        $this->isApprovalEmail = ($emailType === 'approval');
        $this->billingDetail = \App\Models\BillingDetail::where('application_id', $application->id)->first();
        
        // Determine payment URL based on application type
        if ($application->application_type === 'exhibitor-registration') {
            $this->paymentUrl = route('exhibitor-registration.payment', $application->application_id);
        } else {
            $this->paymentUrl = route('startup-zone.payment', $application->application_id);
        }
        
        // Get sector name if sector_id exists
        $this->sectorName = null;
        if ($application->sector_id) {
            $sector = Sector::find($application->sector_id);
            $this->sectorName = $sector ? $sector->name : null;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Set subject based on email type and payment status
        $isPaid = $this->invoice->payment_status === 'paid';
        $isExhibitorRegistration = $this->application->application_type === 'exhibitor-registration';
        
        if ($this->isApprovalEmail) {
            // Approval email subject
            $subject = config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' - Application Approved & Payment Link';
        } else {
            // Regular registration email subjects
            if ($isExhibitorRegistration) {
                // Exhibitor Registration emails
                if ($isPaid) {
                    $subject = config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' - Exhibitor Registration Confirmation & Payment';
                } else {
                    $subject = config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' - Exhibitor Registration Initiated & Payment Link';
                }
            } else {
                // Startup Zone emails
                if ($isPaid) {
                    $subject = config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' - Startup Exhibitor Registration Confirmation & Payment';
                } else {
                    $subject = config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' - Startup Exhibitor Registration Initiated & Payment Link';
                }
            }
        }
        
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Use approval template if this is an approval email
        $viewName = $this->isApprovalEmail 
            ? 'emails.exhibitor-registration-approval' 
            : 'emails.exhibitor-registration';
        
        return new Content(
            view: $viewName,
            with: [
                'application' => $this->application,
                'invoice' => $this->invoice,
                'contact' => $this->contact,
                'billingDetail' => $this->billingDetail,
                'paymentUrl' => $this->paymentUrl,
                'sectorName' => $this->sectorName,
                'isApprovalEmail' => $this->isApprovalEmail,
            ],
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
