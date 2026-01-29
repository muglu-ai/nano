<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Application;
use App\Models\Invoice;
use App\Models\EventContact;
use App\Models\Sector;
use App\Models\Ticket\TicketOrder;
use App\Models\Events;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCredentialsMail;
use App\Mail\ExhibitorRegistrationMail;
use App\Mail\StartupZoneMail;

class EmailPreviewController extends Controller
{
    public function showCredentialsEmail(Request $request, $email)
    {

    //    dd($email);
        // Use query params or defaults for preview
        $user = User::where('email', $email)->first();
        if (!$user) {
            abort(404, 'User not found');
        }
        $name = $user->name;
        $setupProfileUrl = config('constants.APP_URL') . '/login';
        $username = $user->email;
        $password = $user->simplePass;

        Mail::to('manish.sharma@interlinks.in')
            ->bcc('test.interlinks@gmail.com')
            ->queue(new UserCredentialsMail($name, $setupProfileUrl, $username, $password));
        //send this emails emails.credentials to user and test.interlinks@gmail.com
//        Mail::send('emails.credentials', ['name' => $name, 'setupProfileUrl' => $setupProfileUrl, 'username' => $username, 'password' => $password], function ($message) use ($user) {
//                    $message->to($user->email)
////                        ->cc('manish.sharma@interlinks.in')
//                        ->bcc('vivek@interlinks.in')
//                        ->subject(config('constants.EVENT_NAME') . ' Exhibitor Login Credentials');
//                });

        return view('emails.credentials', compact('name', 'setupProfileUrl', 'username', 'password'));
    }

    /**
     * Preview exhibitor registration email by application_id (TIN number)
     */
    public function showExhibitorRegistrationEmail($applicationId)
    {
        // Find application by application_id (TIN number)
        $application = Application::where('application_id', $applicationId)
            ->where('application_type', 'startup-zone')
            ->first();

        if (!$application) {
            abort(404, 'Application not found with TIN: ' . $applicationId);
        }

        // Load relationships
        $application->load(['country', 'state', 'eventContact']);

        // Get sector name if sector_id exists
        $sectorName = null;
        if ($application->sector_id) {
            $sector = Sector::find($application->sector_id);
            $sectorName = $sector ? $sector->name : null;
        }

        // Get invoice
        $invoice = Invoice::where('application_id', $application->id)->first();

        if (!$invoice) {
            abort(404, 'Invoice not found for application: ' . $applicationId);
        }

        // Get contact
        $contact = EventContact::where('application_id', $application->id)->first();

        // Generate payment URL
        $paymentUrl = route('startup-zone.payment', $application->application_id);

        // Return the email view
        return view('emails.exhibitor-registration', compact('application', 'invoice', 'contact', 'paymentUrl', 'sectorName'));
    }

    /**
     * List all startup zone emails for admin preview
     */
    public function startupZoneEmailsList()
    {
        // Check admin access
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return redirect('/login');
        }

        // Get a sample startup zone application for preview
        $sampleApplication = Application::where('application_type', 'startup-zone')
            ->with(['country', 'state', 'eventContact'])
            ->first();

        // If no application exists, create a mock one for preview
        if (!$sampleApplication) {
            $sampleApplication = new Application();
            $sampleApplication->application_id = 'BTS-XXXXXX';
            $sampleApplication->company_name = 'Sample Startup Company';
            $sampleApplication->company_email = 'sample@example.com';
            $sampleApplication->address = '123 Sample Street';
            $sampleApplication->city_id = 'Sample City';
            $sampleApplication->postal_code = '123456';
            $sampleApplication->landline = '+91-1234567890';
            $sampleApplication->website = 'https://www.example.com';
            $sampleApplication->submission_status = 'submitted';
            $sampleApplication->submission_date = now();
            $sampleApplication->RegSource = 'Sample Association';
            $sampleApplication->application_type = 'startup-zone';
        }

        $sampleInvoice = null;
        $sampleContact = null;
        $sampleBillingDetail = null;

        if ($sampleApplication->id) {
            $sampleInvoice = Invoice::where('application_id', $sampleApplication->id)->first();
            $sampleContact = EventContact::where('application_id', $sampleApplication->id)->first();
            $sampleBillingDetail = \App\Models\BillingDetail::where('application_id', $sampleApplication->id)->first();
        }

        // Create mock data if needed
        if (!$sampleInvoice) {
            $sampleInvoice = new Invoice();
            $sampleInvoice->invoice_no = $sampleApplication->application_id;
            $sampleInvoice->price = 10000;
            $sampleInvoice->gst = 1800;
            $sampleInvoice->processing_charges = 300;
            $sampleInvoice->total_final_price = 12100;
            $sampleInvoice->currency = 'INR';
            $sampleInvoice->payment_status = 'unpaid';
            $sampleInvoice->payment_due_date = now()->addDays(5);
        }

        if (!$sampleContact) {
            $sampleContact = new EventContact();
            $sampleContact->first_name = 'John';
            $sampleContact->last_name = 'Doe';
            $sampleContact->email = 'john.doe@example.com';
            $sampleContact->job_title = 'CEO';
            $sampleContact->contact_number = '+91-9876543210';
        }

        if (!$sampleBillingDetail) {
            $sampleBillingDetail = new \App\Models\BillingDetail();
            $sampleBillingDetail->billing_company = 'Sample Billing Company';
            $sampleBillingDetail->contact_name = 'Jane Doe';
            $sampleBillingDetail->email = 'billing@example.com';
            $sampleBillingDetail->phone = '+91-9876543210';
            $sampleBillingDetail->address = '456 Billing Street';
            $sampleBillingDetail->city_id = 'Billing City';
            $sampleBillingDetail->postal_code = '654321';
        }

        return view('admin.startup-zone-emails-list', compact('sampleApplication', 'sampleInvoice', 'sampleContact', 'sampleBillingDetail'));
    }

    /**
     * Preview startup zone admin notification email
     */
    public function previewStartupZoneAdminNotification($applicationId = null)
    {
        // Check admin access
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return redirect('/login');
        }

        if ($applicationId && $applicationId !== 'sample') {
            $application = Application::where('application_id', $applicationId)
                ->where('application_type', 'startup-zone')
                ->with(['country', 'state', 'eventContact'])
                ->firstOrFail();
            
            $contact = EventContact::where('application_id', $application->id)->first();
        } else {
            // Use sample data
            $application = Application::where('application_type', 'startup-zone')
                ->with(['country', 'state', 'eventContact'])
                ->first();
            
            if (!$application) {
                $application = new Application();
                $application->application_id = 'BTS-XXXXXX';
                $application->company_name = 'Sample Startup Company';
                $application->submission_status = 'submitted';
                $application->submission_date = now();
            }
            
            $contact = EventContact::where('application_id', $application->id ?? null)->first();
        }

        $mail = new StartupZoneMail($application, 'admin_notification', null, $contact);
        $content = $mail->content();
        return view($content->view, $content->with);
    }

    /**
     * Preview startup zone approval email
     */
    public function previewStartupZoneApproval($applicationId = null)
    {
        // Check admin access
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return redirect('/login');
        }

        if ($applicationId && $applicationId !== 'sample') {
            $application = Application::where('application_id', $applicationId)
                ->where('application_type', 'startup-zone')
                ->with(['country', 'state', 'eventContact'])
                ->firstOrFail();
            
            $invoice = Invoice::where('application_id', $application->id)->firstOrFail();
            $contact = EventContact::where('application_id', $application->id)->first();
        } else {
            // Use sample data
            $application = Application::where('application_type', 'startup-zone')
                ->with(['country', 'state', 'eventContact'])
                ->first();
            
            if (!$application) {
                $application = new Application();
                $application->application_id = 'BTS-XXXXXX';
                $application->company_name = 'Sample Startup Company';
            }
            
            $invoice = Invoice::where('application_id', $application->id ?? null)->first();
            if (!$invoice) {
                $invoice = new Invoice();
                $invoice->invoice_no = $application->application_id;
                $invoice->price = 10000;
                $invoice->gst = 1800;
                $invoice->processing_charges = 300;
                $invoice->total_final_price = 12100;
                $invoice->currency = 'INR';
                $invoice->payment_due_date = now()->addDays(5);
            }
            
            $contact = EventContact::where('application_id', $application->id ?? null)->first();
        }

        $mail = new StartupZoneMail($application, 'approval', $invoice, $contact);
        return view($mail->content()->view, $mail->content()->with);
    }

    /**
     * Preview startup zone payment thank you email
     */
    public function previewStartupZonePaymentThankYou($applicationId = null)
    {
        // Check admin access
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return redirect('/login');
        }

        if ($applicationId && $applicationId !== 'sample') {
            $application = Application::where('application_id', $applicationId)
                ->where('application_type', 'startup-zone')
                ->with(['country', 'state', 'eventContact'])
                ->firstOrFail();
            
            $invoice = Invoice::where('application_id', $application->id)->firstOrFail();
            $contact = EventContact::where('application_id', $application->id)->first();
            
            $paymentDetails = [
                'transaction_id' => 'TXN' . rand(100000, 999999),
                'payment_method' => 'PayPal',
                'amount' => $invoice->total_final_price,
                'currency' => $invoice->currency,
            ];
        } else {
            // Use sample data
            $application = Application::where('application_type', 'startup-zone')
                ->with(['country', 'state', 'eventContact'])
                ->first();
            
            if (!$application) {
                $application = new Application();
                $application->application_id = 'BTS-XXXXXX';
                $application->company_name = 'Sample Startup Company';
            }
            
            $invoice = Invoice::where('application_id', $application->id ?? null)->first();
            if (!$invoice) {
                $invoice = new Invoice();
                $invoice->invoice_no = $application->application_id;
                $invoice->price = 10000;
                $invoice->gst = 1800;
                $invoice->processing_charges = 300;
                $invoice->total_final_price = 12100;
                $invoice->currency = 'INR';
                $invoice->payment_status = 'paid';
            }
            
            $contact = EventContact::where('application_id', $application->id ?? null)->first();
            
            $paymentDetails = [
                'transaction_id' => 'TXN' . rand(100000, 999999),
                'payment_method' => 'PayPal',
                'amount' => $invoice->total_final_price,
                'currency' => $invoice->currency,
            ];
        }

        $mail = new StartupZoneMail($application, 'payment_thank_you', $invoice, $contact, $paymentDetails);
        return view($mail->content()->view, $mail->content()->with);
    }

    /**
     * Preview ticket registration email by TIN (order number) or secure token
     */
    public function showTicketRegistrationEmail($identifier)
    {
        // Try to find by secure token first, then by TIN (order_no)
        $order = TicketOrder::where('secure_token', $identifier)
            ->orWhere('order_no', $identifier)
            ->with(['registration.contact', 'items.ticketType', 'registration.delegates', 'registration.registrationCategory'])
            ->first();

        if (!$order) {
            abort(404, 'Order not found with identifier: ' . $identifier);
        }

        // Get event
        $event = Events::find($order->registration->event_id);
        
        if (!$event) {
            abort(404, 'Event not found for order: ' . $identifier);
        }

        // Return the email view
        return view('emails.tickets.registration', compact('order', 'event'));
    }
}

