<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\Invoice;
use App\Models\EventContact;
use App\Models\BillingDetail;
use App\Models\Sector;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StartupZoneMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $invoice;
    public $contact;
    public $billingDetail;
    public $sectorName;
    public $exhibitorData;
    public $paymentDetails;
    
    // Email type flags
    public $isAdminNotification;
    public $isApprovalEmail;
    public $isPaymentThankYou;
    
    // Section visibility flags
    public $showPaymentDetails;
    public $showPaymentLink;
    public $showPaymentConfirmation;
    public $showPaymentBreakdown;
    public $showActionRequired;
    public $showOrganizerFooter;

    /**
     * Create a new message instance.
     * 
     * @param Application $application
     * @param string $emailType 'admin_notification' | 'approval' | 'payment_thank_you'
     * @param Invoice|null $invoice
     * @param EventContact|null $contact
     * @param array $paymentDetails
     */
    public function __construct(
        Application $application, 
        string $emailType = 'admin_notification',
        Invoice $invoice = null, 
        EventContact $contact = null, 
        array $paymentDetails = []
    ) {
        // Load relationships if not already loaded
        if (!$application->relationLoaded('country')) {
            $application->load('country');
        }
        if (!$application->relationLoaded('state')) {
            $application->load('state');
        }
        
        $this->application = $application;
        $this->invoice = $invoice;
        $this->contact = $contact;
        $this->billingDetail = BillingDetail::where('application_id', $application->id)->first();
        $this->paymentDetails = $paymentDetails;
        
        // Set email type flags
        $this->isAdminNotification = ($emailType === 'admin_notification');
        $this->isApprovalEmail = ($emailType === 'approval');
        $this->isPaymentThankYou = ($emailType === 'payment_thank_you');
        
        // Get sector name if sector_id exists
        $this->sectorName = null;
        if ($application->sector_id) {
            $sector = Sector::find($application->sector_id);
            $this->sectorName = $sector ? $sector->name : null;
        }
        
        // Prepare exhibitor data from application
        $this->exhibitorData = [
            'name' => $application->company_name,
            'email' => $application->company_email,
            'address' => $application->address,
            'city' => $application->city_id,
            'state' => $application->state ? $application->state->name : null,
            'country' => $application->country ? $application->country->name : null,
            'postal_code' => $application->postal_code,
            'telephone' => $application->landline,
            'website' => $application->website,
        ];
        
        // Set section visibility based on email type
        $this->showPaymentDetails = $this->isApprovalEmail || $this->isPaymentThankYou;
        $this->showPaymentLink = $this->isApprovalEmail;
        $this->showPaymentConfirmation = $this->isPaymentThankYou;
        $this->showPaymentBreakdown = $this->isPaymentThankYou;
        $this->showActionRequired = $this->isAdminNotification;
        $this->showOrganizerFooter = $this->isApprovalEmail || $this->isPaymentThankYou;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = '';
        
        if ($this->isAdminNotification) {
            $subject = config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' - New Startup Zone Application Submitted';
        } elseif ($this->isApprovalEmail) {
            $subject = config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' - Startup Zone Application Approved & Payment Link';
        } elseif ($this->isPaymentThankYou) {
            $subject = 'Thank You for Your Payment - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' Startup Exhibition';
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
        return new Content(
            view: 'emails.startup-zone',
            with: [
                'application' => $this->application,
                'invoice' => $this->invoice,
                'contact' => $this->contact,
                'billingDetail' => $this->billingDetail,
                'sectorName' => $this->sectorName,
                'exhibitorData' => $this->exhibitorData,
                'paymentDetails' => $this->paymentDetails,
                'isAdminNotification' => $this->isAdminNotification,
                'isApprovalEmail' => $this->isApprovalEmail,
                'isPaymentThankYou' => $this->isPaymentThankYou,
                'showPaymentDetails' => $this->showPaymentDetails,
                'showPaymentLink' => $this->showPaymentLink,
                'showPaymentConfirmation' => $this->showPaymentConfirmation,
                'showPaymentBreakdown' => $this->showPaymentBreakdown,
                'showActionRequired' => $this->showActionRequired,
                'showOrganizerFooter' => $this->showOrganizerFooter,
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

