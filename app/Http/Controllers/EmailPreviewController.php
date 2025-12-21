<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Application;
use App\Models\Invoice;
use App\Models\EventContact;
use App\Models\Sector;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCredentialsMail;
use App\Mail\ExhibitorRegistrationMail;

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
        $setupProfileUrl = config('app.url');
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
}

