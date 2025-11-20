<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApiRelayController;
use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CoExhibitUser;
use App\Http\Controllers\CoExhibitorController;
use App\Http\Controllers\DocumentsContoller;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExhibitorController;
use App\Http\Controllers\ExhibitorInfoController;
use App\Http\Controllers\ExtraRequirementController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\GeoController;
use App\Http\Controllers\ImportData;
use App\Http\Controllers\EVisitorGuideController;
use App\Http\Controllers\IntegrationAPIController;
use Illuminate\Http\Request;
use App\Http\Controllers\InterlinxAPIController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MeetingRoomAdminController;
use App\Http\Controllers\MeetingRoomBookingController;
use App\Http\Controllers\MisController;
use App\Http\Controllers\NewMisController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\PassesController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\PaymentReceiptController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\SponsorshipController;
use App\Http\Middleware\Auth;
use App\Http\Middleware\CheckUser;
use App\Http\Middleware\CoExhibitorMiddleware;
use App\Http\Middleware\SharedMiddleware;
use App\Mail\AttendeeConfirmationMail;
use App\Mail\CoExhibitorInvoiceMail;
use App\Mail\DisclaimerMail;
use App\Mail\ExhibitorMail;
use App\Mail\InvoiceMailView;
use App\Mail\InviteMail;
use App\Mail\MeetingRoomInvoice;
use App\Mail\Onboarding;
use App\Mail\SendOtpMail;
use App\Mail\UpdateMailer;
use App\Models\Application;
use App\Models\Attendee;
use App\Models\ComplimentaryDelegate;
use App\Models\ExhibitorInfo;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Mews\Captcha\Facades\Captcha;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\EmailPreviewController;
use App\Http\Controllers\CompanyLookupController;
use App\Http\Controllers\FeedbackController;

Route::get('send-exhibitor-chkdin2', [AdminController::  class, 'sendAllData'])->name('send.exhibitor.chkdin')->middleware(Auth::class);
Route::get('get-users', [AdminController::class, 'getUsers'])->name('getUsers')->middleware(Auth::class);
Route::get('get-users2', [AdminController::class, 'getUsers'])->name('getUsers2')->middleware(Auth::class);

Route::get('/{event}/onboarding', [ApplicationController::class, 'showForm2'])->name('event.onboarding')->middleware(CheckUser::class);
Route::get('/{event}/onboarding', [ApplicationController::class, 'showForm2'])->name('new_form')->middleware(CheckUser::class);


//Route::get('/admin/exhibitor-info', function () {
//
//
//    return view('admin.exhibitor-info'  ,compact('exhibitorInfo', 'analytics'));
//})->name('home')->middleware(Auth::class);

// Admin Exhibitor Information Dashboard


//Exhibitor Controller
Route::get('dashboard', [DashboardController::class, 'exhibitorDashboard'])->name('user.dashboard')->middleware(CheckUser::class);

Route::get('send-participation-email', [ExhibitorController::class, 'attendeeEmailSent'])->name('send.participation.email')->middleware(Auth::class);

//get the complimentary delegates list
Route::get('/exhibitor/list/{type}', [ExhibitorController::class, 'list'])->name('exhibition.list')->middleware(CheckUser::class); //invite delegates to the event
Route::get('/exhibitor/list2/{type}', [ExhibitorController::class, 'list2'])->name('exhibition2.list')->middleware(CheckUser::class); //invite delegates to the event
Route::get('/exhibitor/export/complimentary', [ExhibitorController::class, 'exportComplimentary'])->name('exhibitor.export.complimentary')->middleware(CheckUser::class); //export complimentary delegates
Route::post('/invite', [ExhibitorController::class, 'invite'])->name('exhibition.invite')->middleware(SharedMiddleware::class);
Route::post('/accept-coex-terms', [ExhibitorController::class, 'acceptTerms'])->name('coex.acceptTerms')->middleware(CheckUser::class);
//get the invited delegates form the exhibitor controller
Route::get('/invited/{token}/', [ExhibitorController::class, 'invited'])->name('exhibition.invited');
Route::get('/invited/inaugural/{token}/', [ExhibitorController::class, 'invited_test'])->name('exhibition.invited.inaugural');
//invited submit inviteeSubmitted from exhibitor controller
Route::post('/invite/submit', [ExhibitorController::class, 'inviteeSubmitted'])->name('exhibition.invitee.submit');
Route::post('inaugural/invite/submit', [ExhibitorController::class, 'inauguralInviteeSubmitted'])->name('inaugural.invitee.submit');
Route::post('/add', [ExhibitorController::class, 'add'])->name('exhibition.add')->middleware(SharedMiddleware::class);
Route::get('/invited/inaugural/thank-you/{token}', [ExhibitorController::class, 'inauguralInviteeSubmittedThankYou'])->name('inaugural.invitee.thankyou');
Route::get('receipt', [ExhibitorController::class, 'invoices'])->name('exhibitor.invoices')->middleware(CheckUser::class);
Route::patch('fasciaUpdate', [DashboardController::class, 'updateFasciaName'])->name('user.fascia.update')->middleware(CheckUser::class);
Route::get('passes', [ExhibitorController::class, 'analytics'])->name('exhibitor.passes')->middleware(CheckUser::class);

// get the registration data from the exhibitor controller
Route::get('exhibitor/registration-data', [ExhibitorController::class, 'registrationData'])->name('exhibitor.registration.data')->middleware(CheckUser::class);
// return view from email.exhibitor.registrationEmail
Route::get('email/exhibitor/registration', function () {

    return view('emails.exhibitor.registrationEmail');
});

Route::get('exhibitor-directory', function (Request $request) {
    session(['allow_exhibitor_pdf' => true]);

    return view('e-visitor-guide.index');
});
Route::get('exhibitor-directory/pdf', [EVisitorGuideController::class, 'showPdf'])->name('exhibitor.directory.pdf');

Route::get('send-sample-data', [ApiRelayController::class, 'testSampleEnqueue'])->name('send.sample.data');
//api test 
Route::get('/send-data/{unique_id}', [ApiRelayController::class, 'sendDataToApi']);
Route::get('/send-data2/{unique_id}', [ApiRelayController::class, 'sendDataToApiNew']);
Route::get('bulk-send', [ApiRelayController::class, 'sendAllAttendeesToApi'])->name('bulk.send.api');
Route::get('bulk-send-exhibitor', [ApiRelayController::class, 'sendAllComplimentaryDelegatesToApi'])->name('bulk.send.api.exhibitor');
Route::get('bulk-send-stall', [ApiRelayController::class, 'sendAllStallManningsToApi2'])->name('bulk.send.api.stall');
Route::get('/relay/help-tool', [ApiRelayController::class, 'enqueueToHelpTool']);
Route::get('/relay/help-tool/{id}', [ApiRelayController::class, 'status']);



Route::middleware(['auth'])->group(function () {
    Route::get('/file-upload', [FileUploadController::class, 'show'])->name('file.upload.form');
    Route::post('/file-upload', [FileUploadController::class, 'upload'])->name('file.upload');
});

//get meeting room invoice with meeting_id as parameter
Route::get('/meeting-room-invoice/{meeting_id}', function ($meeting_id) {
    $invoiceMail = new MeetingRoomInvoice($meeting_id);
    return $invoiceMail->render();
})->name('meeting.room.invoice');


Route::get('/send-exhibitor-confirmation/{id}', function ($id) {

    // dd('id: ' . $id);
    // Fetch attendee data by $id (replace with your actual logic)
    $attendee = \App\Models\ComplimentaryDelegate::where('unique_id', $id)->first();

    if (!$attendee) {
        $attendee = \App\Models\StallManning::where('unique_id', $id)->first();
    }

    if (!$attendee) {
        // Optionally, redirect back with error or abort(404)
        return redirect()->back()->with('error', 'Attendee not found.');
    }


    $data = [
        'fullName' => trim($attendee->first_name . ' ' . ($attendee->middle_name ?? '') . ' ' . $attendee->last_name),

            'title' => $attendee->title ?? '',

            'first_name' => $attendee->first_name ?? '',

            'last_name' => $attendee->last_name ?? '',

            'middle_name' => $attendee->middle_name ?? '',

            'company_name' => $attendee->organisation_name ?? 'N/A',

            'email' => $attendee->email,

            'mobile' => $attendee->mobile,

            'qr_code_path' => $attendee->qr_code_path,



            'unique_id' => $attendee->unique_id,

            'pinNo' => $attendee->pinNo ?? 'N/A',
            'ticket_type' => $attendee->ticketType,

            'designation' => $attendee->designation ?? $attendee->job_title,

            'registration_date' => $attendee->created_at->format('Y-m-d'),

            'registration_type' => $attendee['registration_type'] === 'Online' ? 1 : 0,

            'id_card_number' => $attendee->id_card_number ?? $attendee->id_no,

            'id_card_type' => $attendee->id_card_type ?? $attendee->id_type,

            'dates' => is_array($attendee->event_days)

                ? implode(', ', $attendee->event_days)

                : implode(', ', json_decode($attendee->event_days, true) ?? []),

            'type' => $attendee->ticketType,
    ];

    // dd($data);

    // return view('mail.ExhibitorRegMail', ['data' => $data]);
    // exit;

    Mail::to($data['email'])
    ->bcc(['test.interlinks@gmail.com'])
    ->send(new ExhibitorMail($data));
    // echo "Exhibitor confirmation mail sent successfully to " . $data['email'];
    // exit;
    return redirect()->back();
})->name('mail.exhibitor_confirmation')->middleware(Auth::class);

Route::get('/send-invite-mail-custom', function (Request $request) {
    // $companyName = $request->query('company');
    // $inviteType = $request->query('invite_type');
    // $email = $request->query('email');
    // $token = $request->query('token');

    $email = "mai-takagi@ckd.co.jp";
    $token = "qOrTWFLHiwsHa3STODfeaBAaqhMK00iY";
    $inviteType = "Exhibitor";
    $companyName = "CKD Corporation";
    if (!$companyName || !$inviteType || !$email || !$token) {
        return response()->json(['status' => 'failed', 'message' => 'Missing required parameters.'], 400);
    }

    try {
        // Mail::to($email)->queue(new InviteMail($companyName, $inviteType, $token));
        return response()->json(['status' => 'success', 'message' => 'Invite mail queued.']);
    } catch (\Exception $e) {
        \Log::error('Failed to queue invite mail: ' . $e->getMessage());
        return response()->json(['status' => 'failed', 'message' => 'Failed to queue invite mail.'], 500);
    }
})->name('send.invite.mail-custom');

//return view with random otp in constructor 
Route::get('/send-otps', function () {
    $otp = rand(100000, 999999);
    $email = 'manishk_sharma@outlook.com';
    $sendOtpMail = new SendOtpMail($otp);
    //send the otp mail
    try {
        // Mail::to($email)->send($sendOtpMail);
        // Log the success message
        Log::info('OTP email sent successfully to ' . $email);
    } catch (\Exception $e) {
        // Log the error message
        Log::error('Failed to send OTP email: ' . $e->getMessage());
        return response()->json(['status' => 'failed', 'message' => 'Failed to send OTP email.'], 500);
    }
    //return the rendered email view
    return $sendOtpMail->render();
})->name('send.otps');

//use Onboarding Class to view the email using the route 
Route::get('/onboarding-email', function () {
    $email = 'user@example.com';
    $company = 'Interlinks';
    $onboardingEmail = new Onboarding($email, $company);

    return $onboardingEmail->render();
})->name('onboarding.email');

//send 
Route::get('/send-attendee-confirmation/{id}', function ($id) {
    // Fetch attendee data by $id (replace with your actual logic)
    $attendee = \App\Models\Attendee::where('unique_id', $id)->firstOrFail();

    $data = [
        'unique_id' => $attendee['unique_id'],
        'email' => $attendee['email'],
        'name' => $attendee['first_name'] . ' ' . $attendee['middle_name'] . ' ' . $attendee['last_name'],
        'ticket_type' => 'Visitor',
        'mobile' => $attendee['mobile'],
        'company_name' => $attendee['company'] ?? '-',
        'designation' => $attendee['designation'] ?? '-',
        'registration_date' => now()->format('Y-m-d'),
        'registration_type' => $attendee['registration_type'] === 'Online' ? 1 : 0,
        'id_card_number' => $attendee['id_card_number'] ?? 'N/A',
        'id_card_type' => $attendee['id_card_type'] ?? 'N/A',
        'dates' => is_array($attendee['event_days'])
            ? implode(', ', $attendee['event_days'])
            : implode(', ', json_decode($attendee['event_days'], true) ?? []),
    ];

    // Mail::bcc(['test.interlinks@gmail.com'])
    //     ->queue(new AttendeeConfirmationMail($data));

    return redirect()->back();
})->name('mail.attendee_confirmation')->middleware(Auth::class);



//give new InvoiceMail($application_id) to view the email using the route
Route::get('/receipt/{application_id}', function ($application_id) {
    $invoiceMail = new InvoiceMailView($application_id);
    return $invoiceMail->render();
})->name('invoice.mail.view');

Route::get('/receipt/coexh/{application_id}', function ($application_id) {
    $invoiceMail = new CoExhibitorInvoiceMail($application_id);
    return $invoiceMail->render();
})->name('co-invoice.mail.view');


/*
 * Visitor Controller
 * AttendeeController Routes
 * */
//test-visitor-email by id viewAttendeeDetails($id) from attendee controller
Route::get('/test-visitor-email/{id}', [AttendeeController::class, 'viewAttendeeDetails'])->name('test.visitor.email');
Route::get('/test-exhibitor-email/{id}', [AttendeeController::class, 'viewAttendeeDetailsExhibitor'])->name('test.exhibitor.email');
Route::get('/visitor-pdf/{id}', [AttendeeController::class, 'viewAttendeeDetailsPdf'])->name('visitor.pdf');
Route::get('/exhibitor-pdf/{id}', [AttendeeController::class, 'viewAttendeeDetailsPdfExhibitor'])->name('exhibitor.pdf');
Route::get('/visitor/registration', [AttendeeController::class, 'showForm'])->name('visitor.register.form');
Route::get('/visitor/registration2', [AttendeeController::class, 'showForm2'])->name('visitor.register.form2');
Route::post('/visitor/registration', [AttendeeController::class, 'visitor_reg'])->name('visitor_register');
Route::get('/thank-you', function () {
    return view('attendee.thank-you');
})->name('thank-you');

Route::get('/visitor/thankyou/{id}', [AttendeeController::class, 'thankyou'])->name('visitor_thankyou');

Route::get('admin_attendee_list', [AttendeeController::class, 'listAttendees'])->name('visitor.list')->middleware(Auth::class);
Route::get('exhibitor_list', [AttendeeController::class, 'listExhibitor'])->name('exhibitor.list')->middleware(Auth::class);
Route::get('export-exhibitor', [AttendeeController::class, 'exportExhibitor'])->name('export.exhibitor')->middleware(Auth::class);

/*
* Visitor Attendee Controller Admin Routes
*/
Route::get('export_stall_invoices', [ExportController::class, 'export_stall_invoices'])->name('export.stall_invoices')->middleware(Auth::class);

Route::get('registration-analytics', [AttendeeController::class, 'dashboard'])->name('registration.analytics')->middleware(Auth::class);
Route::get('registration-matrix', [AttendeeController::class, 'jobsMatrix'])->name('registration.matrix')->middleware(Auth::class);
Route::post('/attendees/mass-approve', [AttendeeController::class, 'massApprove'])->name('attendees.mass.approve')->middleware(Auth::class);
Route::post('/exhibitor/mass-approve', [AttendeeController::class, 'ExhibitormassApprove'])->name('exhibitor.mass.approve')->middleware(Auth::class);
Route::get('export-attendees', [AttendeeController::class, 'export'])->name('export.list')->middleware(Auth::class);
Route::post('approve-attendee', [AttendeeController::class, 'approveInauguralSession'])->name('approve.attendee')->middleware(Auth::class);
Route::get('viewAttendeeDetails/{id}', [AttendeeController::class, 'viewAttendee'])->name('view.attendee.details')->middleware(Auth::class);
Route::patch('/attendee/{unique_id}/update', [AttendeeController::class, 'update'])->name('attendee.update');

/*
* Visitor Controller Routes Ends
*/


/*
 * Application Controller Routes
 * */
Route::get('/onboard/{id}', [ApplicationController::class, 'OnboardingEmail'])->name('OnboardingEmail');
Route::patch('logo_link', [ApplicationController::class, 'saveLogoLink'])->name('user.logo.update')->middleware(SharedMiddleware::class);

//download application form
Route::get('/download-application-form', [ApplicationController::class, 'exportPDF'])->name('download.application.form')->middleware(CheckUser::class);
Route::match(['post', 'get'], '/application/exhibitor', [ApplicationController::class, 'showForm'])->name('application.exhibitor')->middleware(CheckUser::class);
//Route::match(['get'],'exhibitor/application', [ApplicationController::class, 'showForm'])->name('application.exhibitor')->middleware(CheckUser::class);
Route::post('/exhibitor/application', [ApplicationController::class, 'submitForm'])->name('application.exhibitor.submit')->middleware(CheckUser::class);
Route::get('apply', [ApplicationController::class, 'apply'])->name('application.show')->middleware(CheckUser::class);
Route::get('apply_new2', [ApplicationController::class, 'apply_spon'])->name('application.show2')->middleware(CheckUser::class);
Route::post('apply', [ApplicationController::class, 'apply_store'])->name('event-participation.store');

//terms and conditions page
Route::get('terms', [ApplicationController::class, 'terms'])->name('terms')->middleware(CheckUser::class);
//terms_store
Route::post('terms', [ApplicationController::class, 'terms_store'])->name('terms.store');

// get preview from preview function of application controller
Route::get('preview', [ApplicationController::class, 'preview'])->name('application.preview')->middleware(CheckUser::class);
// route to updated the submitted form with name final
Route::post('final', [ApplicationController::class, 'final'])->name('application.final');
// Route::match(['post', 'get'], '/proforma/{application_id}', [ApplicationController::class, 'invoice'])->name('invoice-details')->middleware(CheckUser::class); //Route::view('/users/list', 'admin.user')->name('users.list')->middleware(Auth::class);
Route::get('application-info', [ApplicationController::class, 'applicationInfo'])->name('application.info')->middleware(CheckUser::class);
Route::get('application/create', [ApplicationController::class, 'create'])->name('application.create')->middleware(Auth::class);
Route::post('application/store', [ApplicationController::class, 'store'])->name('application.store')->middleware(Auth::class);
Route::get('/{event}/onboarding', [ApplicationController::class, 'showForm2'])->name('new_form')->middleware(CheckUser::class);
Route::get('apply_new', [ApplicationController::class, 'apply_new'])->name('apply_new')->middleware(CheckUser::class);
Route::post('/get-sqm-options', [ApplicationController::class, 'getSQMOptions']);

//get country code from applicationController
Route::post('/get-country-code', [ApplicationController::class, 'getCountryCode']);
/*
 * Application Admin Routes
 * */
Route::get('/download-application-form-admin', [ApplicationController::class, 'exportPDF_admin'])
    ->name('download.application.form.admin')->middleware(Auth::class);
Route::get('submit_admin', [ApplicationController::class, 'final_admin'])->name('application.final_admin')->middleware(Auth::class);


/*
Application Controller Routes Ends
*/

/*
 * Meeting Room Controller Routes
 * */
// Add routes for MeetingRoomBookingController
Route::get('/meeting-rooms', [MeetingRoomBookingController::class, 'index'])->name('meeting_rooms.index')->middleware(CheckUser::class);
Route::get('/meeting-rooms/availability', [MeetingRoomBookingController::class, 'check'])->name('meeting-rooms.availability')->middleware(CheckUser::class);
Route::post('/meeting-rooms/book', [MeetingRoomBookingController::class, 'book'])->name('meeting_rooms.book')->middleware(CheckUser::class);
Route::get('/meeting-rooms/mybookings', [MeetingRoomBookingController::class, 'myBookings'])->name('meeting_rooms.mybook')->middleware(CheckUser::class);

/* Meeting room Admin Routes
*/
//rout get with index function MeetingRoomAdminController meeting-rooms/bookings with auth middleware
Route::get('/meeting-rooms/admin/bookings', [MeetingRoomAdminController::class, 'index'])->name('meeting_rooms.admin.index')->middleware(Auth::class);
//post method to mark a booking as paid
Route::post('/meeting-rooms/admin/mark-paid', [MeetingRoomAdminController::class, 'markAsPaid'])->name('meeting_rooms.admin.mark_paid')->middleware(Auth::class);


/*
Meeting Room Controller Routes Ends
*/

/*
 * Extra Requirement Controller Routes
 */
Route::post('/extra-requirements/upload-tax-invoice/{order}', [ExtraRequirementController::class, 'uploadTaxInvoice'])->name('extra_requirements.upload_tax_invoice')->middleware('auth');
Route::get('extra_requirements/list', [ExtraRequirementController::class, 'list'])->name('extra_requirements.list')->middleware(SharedMiddleware::class);
Route::post('extra_requirements', [ExtraRequirementController::class, 'store'])->name('extra_requirements.store')->middleware(SharedMiddleware::class);
Route::get('exhibitor/orders', [ExtraRequirementController::class, 'userOrders'])->name('exhibitor.orders')->middleware(SharedMiddleware::class);

Route::post('extraRequirements/billing', [ExtraRequirementController::class, 'updateBillingDetails'])->name('extra_requirements.billing');
Route::post('exhibitor/orders/delete', [ExtraRequirementController::class, 'deleteOrder'])
    ->name('exhibitor.orders.delete')->middleware(SharedMiddleware::class);
//get method for extra requirements from extra requirement controller with index function
Route::get('extra_requirements', [ExtraRequirementController::class, 'index'])->name('extra_requirements.index');

Route::post('/lead-retrieval/add-user-file', [ExtraRequirementController::class, 'addLeadRetrievalUserToFile'])
    ->name('lead-retrieval.add-user-file')->middleware(SharedMiddleware::class);
Route::get('/lead-retrieval/users-file/{orderId}', [ExtraRequirementController::class, 'getLeadRetrievalUsersFromFile'])
    ->name('lead-retrieval.users-file')->middleware(SharedMiddleware::class);



/* Extra Requirement Controller Admin Routes
*/
Route::get('extra_requirements/analytics', [ExtraRequirementController::class, 'analytics'])->name('extra.analytics')->middleware(Auth::class);
Route::get('requirements/order', [ExtraRequirementController::class, 'allOrders'])->name('extra_requirements.admin')->middleware(Auth::class);
Route::get('requirements/leadRetrieval', [ExtraRequirementController::class, 'allLeadRetrieval'])
    ->name('extra_requirements.admin.leadRetrieval')->middleware(Auth::class);
Route::post('/mark-as-delivered', [ExtraRequirementController::class, 'markAsDelivered'])->name('requirement.delivered')->middleware(Auth::class);
Route::get('/admin/extra_requirement', [ExtraRequirementController::class, 'showExtrarequirement'])
    ->name('extra_requirements.admin.show')->middleware(Auth::class);
Route::get('/download/extra-requirements/{invoice_id}', [PaymentGatewayController::class, 'downloadInvoicePdf'])->name('download.extra-requirements')->middleware(SharedMiddleware::class);
Route::get('/receipt/extra-requirements/{invoice_id}', [PaymentGatewayController::class, 'downloadInvoicePdf'])->name('receipt.extra-requirements')->middleware(Auth::class);

/* Extra Requirement Controller Routes Ends
*/


//give a test route to view the email from paymentGatewayController function as showInvoiceEmail with invoice_id as parameters and get method
Route::get('/extra-requirements/{invoice_id}', [PaymentGatewayController::class, 'showInvoiceEmail'])->name('extra-requirements.email');


Route::get('{event}/sponsorship', [SponsorshipController::class, 'new'])->name('sponsorship')->middleware(CheckUser::class);

// Route to reload the captcha image via AJAX
Route::get('/reload-captcha', function () {
    return response()->json(['captcha' => captcha_img()]);
})->name('captcha.reload');

Route::get('companies/{letter}', [CompanyLookupController::class, 'index']);
// CORS preflight for companies endpoint
Route::options('companies/{letter}', function () {
    $origin = request()->headers->get('Origin');
    $allowed = in_array($origin, [
        'https://bengalurutechsummit.com',
        'https://www.bengalurutechsummit.com',
    ]) ? $origin : '';
    return response('', 200)
        ->header('Access-Control-Allow-Origin', $allowed)
        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
        ->header('Access-Control-Allow-Credentials', 'true')
        ->header('Access-Control-Max-Age', '86400');
})->middleware('companies.cors');


/*
    * Passes Controller Admin Routes
    * */
Route::get('/passes-allocation', [PassesController::class, 'passesAllocation'])->name('passes.allocation')->middleware(Auth::class);
Route::post('/update-passes-allocation', [PassesController::class, 'updatePassesAllocation'])->name('passes.update-allocation')->middleware(Auth::class);
Route::post('/auto-allocate-passes', [PassesController::class, 'autoAllocatePasses'])->name('passes.auto-allocate')->middleware(Auth::class);
Route::get('/resend-invite-emails', [PassesController::class, 'resendInviteEmails'])->name('passes.resend-invites')->middleware(Auth::class);


Route::get('exhibitor/combine', [PassesController::class, 'CombinePasses'])->name('admin.passes.combine')->middleware(Auth::class);
Route::get('exhibitor/stallmanning', [PassesController::class, 'StallManning'])->name('admin.stall-manning')->middleware(Auth::class);
Route::get('exhibitor/complimentary', [PassesController::class, 'Complimentary'])->name('admin.complimentary.delegate')->middleware(Auth::class);


Route::get('exhibitor/passes', [PassesController::class, 'Complimentary'])->name('admin.inaugural')->middleware(Auth::class);
Route::get('exhibitor/remove/{id}', [PassesController::class, 'deleteVisitor'])->name('admin.remove')->middleware(Auth::class);
Route::get('visitor/remove/{id}', [PassesController::class, 'deleteVisitor2'])->name('admin.remove.attendee')->middleware(Auth::class);

Route::get('exhibitor/stallmanning/export', [PassesController::class, 'exportPasses'])->name('passes.export')->middleware(Auth::class);

/* Passes Controller Admin Routes Ends
*/
/*
    * MIS Controller Admin Routes
    * */

Route::post('export-logo', [MisController::class, 'exportFasciaAndLogo'])->name('export.fasciaLogo')->middleware(Auth::class);
Route::post('export-fascia', [MisController::class, 'exportFasciaName'])->name('export.fascia')->middleware(Auth::class);
Route::get('active-users-analytics', [MisController::class, 'activeUsersAnalytics'])->name('active.users.analytics')->middleware(Auth::class);
Route::get('/admin/analytics/export', [MisController::class, 'exportUsers'])->name('admin.analytics.export')->middleware(Auth::class);
Route::get('/import_states', [MisController::class, 'getCountryAndState']);
Route::post('/get-states', [MisController::class, 'getStates'])->name('get.states');

Route::get('active-users-analytics2', [NewMisController::class, 'activeUsersAnalytics'])->name('active.users.analytics2')->middleware(Auth::class);
Route::get('/admin/analytics/export2', [NewMisController::class, 'exportUsers'])->name('admin.analytics.export2')->middleware(Auth::class);
/* MIS Controller Admin Routes Ends
*/
Route::post('/add-tds-amount', [InvoicesController::class, 'addTdsAmount'])->name('invoices.add-tds')->middleware(Auth::class);
/*
 * Test Routes
 * */

Route::get('/pgway', function () {
    return view('pgway.create-order');
});


Route::match(['get', 'post'], '/ccavResponseHandler', function () {
    return request()->all();
});

/* Test Routes Ends
*/

Route::get('/send-invoice/{invoiceId}/{email}', function ($invoiceId, $email) {
    return app()->call('App\Http\Controllers\PaymentGatewayController@sendInvoice', [
        'invoiceId' => $invoiceId,
        'toEmail' => $email
    ]);
});


/* Payment Gateway Routes
* PayPalController Routes
*/

Route::get('/payment/{id}', [PayPalController::class, 'showPaymentForm'])->name('paypal.form');
// Route::get('/payment/{id}', [PayPalController::class, 'showPaymentForm2'])->name('paypal.form');
Route::get('/payment/{id}', [PayPalController::class, 'showPaymentForm'])->name('paypal.form');
Route::post('/paypal/create', [PayPalController::class, 'createOrder'])->name('paypal.create');
Route::post('/paypal/create-order', [PayPalController::class, 'createOrder']);
Route::post('/paypal/capture-order/{orderId}', [PayPalController::class, 'captureOrder']);
Route::get('/paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');
Route::get('/paypal/webhook', [PayPalController::class, 'webhook'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);


/* Payment Gateway CCAvenue Routes
*/
Route::post('/payment/ccavenue/{id}', [PaymentGatewayController::class, 'ccAvenuePayment']);
Route::post('/payment/ccavenue-success', [PaymentGatewayController::class, 'ccAvenueSuccess'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

/* Payment Gateway Routes Ends
*/


/* CoExhibitor Controller Routes
* */
//co exhibitor dashboard
Route::get('/co-exhibitor', [CoExhibitorController::class, 'user_list'])->name('co_exhibitor')->middleware(CheckUser::class);
Route::get('/co-exhibitor/dashboard', [CoExhibitUser::class, 'index'])->name('dashboard.co-exhibitor')->middleware(CoExhibitorMiddleware::class);
Route::get('/co-exhibitor/passes', [CoExhibitUser::class, 'passes'])->name('co-exhibitor.passes')->middleware(CoExhibitorMiddleware::class);
Route::get('/co-exhibitor/inaugural', [CoExhibitUser::class, 'inauguralPasses'])->name('co-exhibitor.inauguralPasses')->middleware(CoExhibitorMiddleware::class);
Route::get('/co-exhibitor/email-view', [CoExhibitorController::class, 'emailView'])->name('co_exhibitor.email_view');
//route get the exhibitor co-exhibitor list

Route::post('/co-exhibitor/store', [CoExhibitorController::class, 'store'])->name('co_exhibitor.store')->middleware(CheckUser::class);

/* CoExhibitor Controller Admin Routes
*/
Route::post('/co-exhibitor/approve/{id}', [CoExhibitorController::class, 'approve'])->name('co_exhibitor.approve')->middleware(Auth::class);

Route::post('/co-exhibitor/reject/{id}', [CoExhibitorController::class, 'reject'])->name('co_exhibitor.reject')->middleware(Auth::class);

Route::get('/co-exhibitors', [CoExhibitorController::class, 'index'])->name('co_exhibitors')->middleware(Auth::class);
Route::get('/co-exhibitors/uplo', [CoExhibitorController::class, 'createCoExhibitor'])->name('co_exhibitors.upload')->middleware(Auth::class);

/* CoExhibitor Controller Routes Ends
*/

/* Admin Controller Routes
* */
Route::get('/application-list/', [AdminController::class, 'index'])->name('application.lists')->middleware(Auth::class);;
Route::get('/copy-application/', [AdminController::class, 'copy'])->name('application.copy')->middleware(Auth::class);;
Route::get('/application-list/{status}', [AdminController::class, 'index'])->name('application.list')->middleware(Auth::class);
Route::get('/application-detail', [DashboardController::class, 'applicantDetails'])->name('application.show.admin')->middleware(Auth::class);
Route::get('/price', [AdminController::class, 'price'])->name('price')->middleware(Auth::class);
//approve application
Route::post('/approve/{id}', [AdminController::class, 'approve'])->name('approve')->middleware(Auth::class);
//route get invoice list from dashboard controller invoiceDetails function
Route::get('/invoice-list', [DashboardController::class, 'invoiceDetails'])->name('invoice.list')->middleware(Auth::class);
// Route::view('/users/list', 'admin.users')->name('users.list')->middleware(Auth::class);
Route::get('/users/list', [AdminController::class, 'usersList'])->name('users.list')->middleware(Auth::class);
Route::post('/users/send-credentials/{userId}', [AdminController::class, 'sendCredentials'])->name('users.send-credentials')->middleware(Auth::class);
Route::get('/admin/import-exhibitors', function () {
    return view('admin.import-exhibitors');
})->name('admin.import.exhibitors.view')->middleware(Auth::class);
Route::post('/admin/import-exhibitors-bulk', [App\Http\Controllers\ImportData::class, 'importExhibitorsBulk'])->name('admin.import.exhibitors')->middleware(Auth::class);
///post application/submit-endpoint to submit the application
Route::post('/application/submit', [AdminController::class, 'approve'])->name('approve.submit')->middleware(Auth::class);
Route::get('/application/submit/test', [AdminController::class, 'approve_test'])->name('approve.submit.test')->middleware(Auth::class);
Route::post('/sponsorship/submit', [SponsorController::class, 'approve'])->name('sponsorship.submit')->middleware(Auth::class);
Route::post('/application/reject', [AdminController::class, 'reject'])->name('reject.submit')->middleware(Auth::class);
Route::post('/application/submitback', [AdminController::class, 'submission_back'])->name('submit.back')->middleware(Auth::class);
Route::post('/sponsorship/reject', [AdminController::class, 'sponsorship_reject'])->name('sponsorship.reject')->middleware(Auth::class);
Route::get('/get-users', [AdminController::class, 'getUsers'])->middleware(Auth::class);
//Exhibitor Admin Routes
Route::get('applicationView', [AdminController::class, 'applicationView'])->name('application.view')->middleware(Auth::class);
Route::put('/application/update/{id}', [AdminController::class, 'applicationUpdate'])->name('application.update')->middleware(Auth::class);
//Admin Sponsorship Route
Route::get('/sponsorship-list/', [AdminController::class, 'sponsorApplicationList'])->name('sponsorship.lists')->middleware(Auth::class);;
Route::get('/sponsorship-list/{status}', [AdminController::class, 'sponsorApplicationList'])->name('sponsorship.list')->middleware(Auth::class);

// Declaration form routes
Route::get('/admin/declarations/list', [AdminController::class, 'declarationsList'])->name('admin.declarations.list')->middleware(Auth::class);
Route::get('/admin/declarations/export', [AdminController::class, 'exportDeclarations'])->name('admin.declarations.export')->middleware(Auth::class);
Route::get('/admin/declarations/view/{id}', [AdminController::class, 'viewDeclaration'])->name('admin.declarations.view')->middleware(Auth::class);
//verify the membership by admin /membership/verify
Route::post('membership/verify', [AdminController::class, 'verifyMembership'])->name('membership.verify')->middleware(Auth::class);
///membership/reject
Route::post('membership/reject', [AdminController::class, 'unverifyMembership'])->name('membership.reject')->middleware(Auth::class);
Route::get('onboarding-test', [AdminController::class, 'sendOnboardingEmail'])->name('onboarding.test');

Route::get('/send-user-credentials-email', [AdminController::class, 'sendUserCredentialsEmail'])->name('send.user.credentials.email')->middleware(Auth::class);

// Booth Management Routes
Route::get('/admin/booth-management', [AdminController::class, 'boothManagement'])->name('booth.management')->middleware(Auth::class);
Route::post('/admin/booth/update/{id}', [AdminController::class, 'updateBooth'])->name('booth.update')->middleware(Auth::class);
Route::post('/admin/booth/bulk-update', [AdminController::class, 'bulkUpdateBooths'])->name('booth.bulkUpdate')->middleware(Auth::class);
Route::get('/admin/booths/export-template', [AdminController::class, 'exportBoothTemplate'])->name('admin.booths.exportTemplate')->middleware(Auth::class);
Route::post('/admin/booths/import', [AdminController::class, 'importBoothUpdates'])->name('admin.booths.import')->middleware(Auth::class);
Route::get('/admin/booths/export-fascia', [AdminController::class, 'exportFasciaDetails'])->name('admin.booths.exportFascia')->middleware(Auth::class);
Route::get('/admin/export/missing-exhibitor-directory', [AdminController::class, 'exportMissingExhibitorDirectory'])->name('admin.export.missing-directory')->middleware(Auth::class);

/* Admin Controller Routes Ends
* */

/*
 * Sales Controller Routes
 * */
Route::get('/sales', [SalesController::class, 'index'])->name('sales.index')->middleware(Auth::class);


/*
  SponsorController Routes
*/
Route::get('/sponsor/create_new', [SponsorController::class, 'create'])->name('sponsor.create_new')->middleware(Auth::class);
Route::get('/sponsor/add', [SponsorController::class, 'add'])->name('sponsor.add')->middleware(Auth::class);
Route::get('/sponsor/{id}/update', [SponsorController::class, 'sponsor_update'])->name('sponsor.update')->middleware(Auth::class);
Route::post('/sponsor/store', [SponsorController::class, 'create'])->name('sponsor_item.store')->middleware(Auth::class);
Route::post('/sponsor-items/store', [SponsorController::class, 'item_store'])->name('sponsor_items.store')->middleware(Auth::class);
Route::put('/sponsor-items/{id}/update', [SponsorController::class, 'item_update'])->name('sponsor_items.update')->middleware(Auth::class);
//put to update the sponsor items to inactive
Route::put('/sponsor-items/{id}/inactive', [SponsorController::class, 'item_inactive'])->name('sponsor_items.inactive')->middleware(Auth::class);

/*
 * Document Controller Routes
 * */
Route::get('invitation-letter', [DocumentsContoller::class, 'invitation'])->name('invitation.letter')->middleware(SharedMiddleware::class);
Route::get('transport-letter', [DocumentsContoller::class, 'transport_letter'])->name('transport.letter')->middleware(SharedMiddleware::class);
Route::get('exhibitor-manual', [DocumentsContoller::class, 'exhibitor_manual'])->name('exhibitor_manual')->middleware(SharedMiddleware::class);
Route::get('portal-guide', [DocumentsContoller::class, 'exhibitor_guide'])->name('exhibitor_guide')->middleware(SharedMiddleware::class);
Route::get('faqs', [DocumentsContoller::class, 'faqs'])->name('faqs')->middleware(SharedMiddleware::class);
Route::get('promo-banner', [DocumentsContoller::class, 'promo_banner'])->name('promo.banner')->middleware(SharedMiddleware::class);
// Declaration form routes
Route::get('declaration-form', [DocumentsContoller::class, 'declaration_download'])->name('declaration.download')->middleware(SharedMiddleware::class);
Route::post('declaration/upload', [DocumentsContoller::class, 'declaration_upload'])->name('declaration.upload')->middleware(SharedMiddleware::class);
Route::get('declaration/view/{id}', [DocumentsContoller::class, 'declaration_view'])->name('declaration.view')->middleware(SharedMiddleware::class);
Route::get('participation-letter', [DashboardController::class, 'participantDetails'])->name('participation.letter')->middleware(CheckUser::class);

/* Document Controller Routes Ends
*/

/*
 * Payment Receipt Controller Routes
 * */
Route::post('upload-receipt', [PaymentReceiptController::class, 'uploadReceipt'])->name('upload.receipt')->middleware(Auth::class);
Route::get('upload-receipt_test', [PaymentReceiptController::class, 'uploadReceipt_test'])->name('upload.receipt.test')->middleware(Auth::class);
Route::post('upload-receipt-user', [PaymentReceiptController::class, 'uploadReceipt_user'])->name('upload.receipt_user')->middleware(CheckUser::class);
Route::post('upload-receipt-extra', [PaymentReceiptController::class, 'uploadReceipt_extra'])->name('upload.receipt_extra')->middleware(SharedMiddleware::class);

/* Payment Receipt Controller Routes Ends
*/

/*
 * Invoice Controller Routes
 * */

Route::get('/invoice', [InvoicesController::class, 'index'])->name('invoice.list')->middleware(Auth::class);
//get the invoice details from invoice controller with view function as get method

Route::get('/invoice/{id}', [InvoicesController::class, 'show'])->name('invoice.show')->middleware(Auth::class);


/*
* Sponsorship Controller
*/
Route::get('/{event}/sponsorship_test', [SponsorController::class, 'new_up'])->name('list_sponsorship_test')->middleware(CheckUser::class);
Route::get('/{event}/sponsorship_new', [SponsorshipController::class, 'listing'])->name('list_sponsorship_new')->middleware(CheckUser::class);
Route::get('/{event}/sponsorship_state', [SponsorshipController::class, 'listing_state'])->name('list_sponsorship_new')->middleware(CheckUser::class);
Route::post('/submit_sponsor', [SponsorshipController::class, 'store'])->name('sponsor.store')->middleware(CheckUser::class);
Route::get('/sponsor/preview', [SponsorshipController::class, 'confirmation'])->name('sponsor.review')->middleware(CheckUser::class);
//delete sponsor application with post method
Route::post('/sponsor/delete', [SponsorshipController::class, 'delete'])->name('sponsor.delete')->middleware(CheckUser::class);
//submit the application with post method
Route::post('/sponsor/submit', [SponsorController::class, 'submit'])->name('sponsor.submit')->middleware(CheckUser::class);
Route::get('review_sponsor', [SponsorController::class, 'review'])->name('review.sponsor')->middleware(CheckUser::class);


//Sponsorship Admin routes
Route::view('/sponsorship/list', 'sponsor.applications')->name('sponsor.list')->middleware(Auth::class);
Route::get('/sponsors_list', [SponsorController::class, 'get_applications'])->middleware(Auth::class);
//approve-sponsorship
Route::post('approve-sponsorship', [SponsorController::class, 'approve'])->name('approve.sponsorship')->middleware(Auth::class);

/*
 * Export Controller Routes
 * */

Route::get('export_users', [ExportController::class, 'export_users'])->name('export.users')->middleware(Auth::class);
Route::get('export_applications', [ExportController::class, 'export_applications'])->name('export.applications')->middleware(Auth::class);
Route::get('export_approved_applications', [ExportController::class, 'export_approved_applications'])->name('export.app.applications')->middleware(Auth::class);
Route::get('export_sponsorships', [ExportController::class, 'export_sponsorship_applications'])->name('export.sponsorships')->middleware(Auth::class);
Route::get('export_requirements', [ExportController::class, 'extra_requirements_export'])->name('export.requirements')->middleware(Auth::class);
Route::get('export_lead_retrieval', [ExportController::class, 'export_lead_retrieval'])->name('export.lead_retrieval')->middleware(Auth::class);
/* Export Controller Routes Ends
*/

/*
 * Mail Controller Routes
 * */
//Mail Controller
//return view with route mail test from MailController
Route::get('/mail-test', [MailController::class, 'reminderExhibitors'])->name('mail.test');
Route::get('/mail-test2', [MailController::class, 'reminderVenue'])->name('mail.test2');
Route::get('/send-email', [MailController::class, 'thankYouMail'])->name('send.email');
// inactive users reminder
Route::get('/inactive-users-reminder', [MailController::class, 'inactiveUsersReminder'])->name('inactive.users.reminder')->middleware(Auth::class);


/*
 * Exhibitor Info Routes
 * */
//Exhibitor Info Routes
Route::get('/exhibitor-info', [ExhibitorInfoController::class, 'showForm'])->name('exhibitor.info')->middleware(CheckUser::class);
//show the preview page
Route::get('/exhibitor-info-preview', [ExhibitorInfoController::class, 'showPreview'])->name('exhibitor.info.preview')->middleware(CheckUser::class);
//post the exhibitor info
Route::post('/exhibitor-info', [ExhibitorInfoController::class, 'storeExhibitor'])->name('exhibitor.info.submit')->middleware(CheckUser::class);
//submit final form
Route::post('/exhibitor-info-submit-final', [ExhibitorInfoController::class, 'submitFinalForm'])->name('exhibitor.info.submit.final')->middleware(CheckUser::class);
//generate PDF
Route::get('/exhibitor-info-pdf', [ExhibitorInfoController::class, 'generatePDF'])->name('exhibitor.info.pdf')->middleware(CheckUser::class);


//product-add route
Route::get('/product-add', [ExhibitorInfoController::class, 'showProductForm'])->name('product.add')->middleware(CheckUser::class);
Route::post('/product-add', [ExhibitorInfoController::class, 'productStore'])->name('product.store')->middleware(CheckUser::class);

/*Exhibitor Info Admin Routes
*/

Route::get('/exhibitor-info-list', [ExhibitorInfoController::class, 'listExhibitors'])->name('exhibitor.directory.list')->middleware(Auth::class);
Route::get('/exhibitor-info-export', [ExportController::class, 'export_exhibitor_info'])->name('exhibitor.directory.export')->middleware(Auth::class);
Route::get('/exhibitor-directory-reminder-send', [AdminController::class, 'testEmailSending'])->name('exhibitor.directory.reminder.send')->middleware(Auth::class);

// API endpoint for exhibitor details
Route::get('/api/exhibitor-details/{id}', [ExhibitorInfoController::class, 'getExhibitorDetails'])->name('api.exhibitor.details')->middleware(Auth::class);

// API endpoints for exhibitor editing
Route::get('/api/exhibitor-edit/{id}', [ExhibitorInfoController::class, 'getExhibitorForEdit'])->name('api.exhibitor.edit')->middleware(Auth::class);
Route::post('/api/exhibitor-update/{id}', [ExhibitorInfoController::class, 'updateExhibitor'])->name('api.exhibitor.update')->middleware(Auth::class);

/*Interlinx API Routes
*/
Route::post('/api/interlinx/register', [InterlinxAPIController::class, 'registerUserFromRequest'])->name('interlinx.register');
Route::get('/api/interlinx/test', [InterlinxAPIController::class, 'testConnection'])->name('interlinx.test')->middleware(Auth::class);
Route::get('/api/interlinx/check', [InterlinxAPIController::class, 'checkEndpoint'])->name('interlinx.check')->middleware(Auth::class);

//terms and conditions route
Route::get('/terms-conditions', function () {
    return view('applications.tc');
})->name('terms-conditions');


Route::get('/invoice/details', [InvoicesController::class, 'view'])->name('invoice.details');

Route::get('/', function () {
    return redirect()->route('login');
});


/*
 * Auth Routes
 * */
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register.form');
// Route::post('register', [AuthController::class, 'register'])->name('register');


Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1')->name('login.process');

//forget password

Route::get('forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('forgot.password');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('forgot.password.submit');
//reset password
Route::get('reset-password/{token}/{email}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password');
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('reset.password.submit');

//verify account with get method
Route::get('verify-account/{token}', [AuthController::class, 'verifyAccount'])->name('auth.verify');

/*
 * Auth Routes Ends
 * */

/*
 * Import data from the old system
 * */
Route::get('/import-users', [ImportData::class, 'importUsers'])->name('import.users')->middleware(Auth::class);

Route::middleware(['auth', 'role:sponsor'])->group(function () {
    Route::get('/sponsor/dashboard', fn() => view('sponsor.dashboard'))->name('dashboard.sponsor');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard_old', [DashboardController::class, 'exhibitorDashboard'])->name('dashboard.admin');
});
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'exhibitorDashboard_new'])->name('dashboard.admin');
});

//logout



Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/event-list', [AuthController::class, 'showEvents'])->name('event.list')->middleware(CheckUser::class);
//invoice details


//Payment routes
Route::match(['post', 'get'], '/payment', [PaymentController::class, 'showOrder'])->name('payment')->middleware(CheckUser::class);
Route::post('/payment/success', [PaymentController::class, 'completeOrder'])->name('payment_success')->middleware(Auth::class);
//partial amount payment
Route::post('/payment/partial', [PaymentController::class, 'partialPayment'])->name('payment.partial')->middleware(CheckUser::class);
Route::post('/payment/full', [PaymentController::class, 'fullPayment'])->name('payment.full')->middleware(CheckUser::class);
//payment verified from payment gateway
Route::match(['post', 'get'], '/payment/verify', [PaymentController::class, 'Successpayment'])->name('payment.verify')->middleware(CheckUser::class);



//exhibitor Dashboard

Route::get('/invited/', function () {
    return redirect('invited/not-found');
})->name('exhibition.invited2');
//get /invited/inaugural/thank-you/{token} from exhibitor controller












//store the sponsorship submission


Route::get('review_new', function () {
    return view('applications.preview_new');
});

//verify paymnent route with post method
Route::post('verify-payment', [PaymentController::class, 'verifyPayment'])->name('verify.payment')->middleware(Auth::class);
Route::post('verify-extra-payment', [PaymentController::class, 'verifyExtraPayment'])->name('verify.extra-payment')->middleware(Auth::class);

Route::get('/download-invoice', [InvoicesController::class, 'generatePDF'])->name('download.invoice');


Route::prefix('api')->group(function () {
    Route::get('/countries', [GeoController::class, 'countries'])->name('api.countries');
    Route::get('/states/{country}', [GeoController::class, 'states'])->name('api.states');
    Route::get('/cities/{country}/{state}', [GeoController::class, 'cities'])->name('api.cities');
    // CORS preflight for geo endpoints (allow all origins)
    Route::options('/countries', function () {
        return response('', 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Max-Age', '86400');
    });
    Route::options('/states/{country}', function () {
        return response('', 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Max-Age', '86400');
    });
    Route::options('/cities/{country}/{state}', function () {
        return response('', 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Max-Age', '86400');
    });
});
Route::post('/otp/send', [OTPController::class, 'sendOtp']);
Route::post('/otp/verify', [OTPController::class, 'verifyOtp']);

Route::get('/send-invite-mail-custom', function () {
    $coExhibitorco_exhibitor_id = "SI25-COEXH-4DFF7E"; // Replace with actual co-exhibitor ID
    Mail::bcc(['semiconindia@semi.org'])
        ->send(new CoExhibitorInvoiceMail($coExhibitorco_exhibitor_id));
});

Route::get('enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');

// Registration Count Dashboard
Route::get('/registration-count', [AttendeeController::class, 'registrationCount'])->name('registration.count')->middleware(Auth::class);
Route::get('/api/registration-count-data', [AttendeeController::class, 'getRegistrationCountData'])->name('api.registration.count')->middleware(Auth::class);

Route::get('/email-preview/credentials/{email}', [EmailPreviewController::class, 'showCredentialsEmail']);

// Exhibitor Directory PDF export (runs Python script) - Admin only
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/exhibitors/export-directory', [AdminController::class, 'showExhibitorDirectoryExportPage'])->name('admin.exhibitors.exportDirectory');
    Route::post('/admin/exhibitors/export-directory/run', [AdminController::class, 'runExhibitorDirectoryExport'])->name('admin.exhibitors.exportDirectory.run');
});

// Feedback Routes (Public Access - No Authentication Required)
Route::get('/feedback', [FeedbackController::class, 'show'])->name('feedback.show');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
Route::get('/feedback/thankyou', [FeedbackController::class, 'thankyou'])->name('feedback.thankyou');
Route::get('/feedback/reload-captcha', [FeedbackController::class, 'reloadCaptcha'])->name('feedback.reload.captcha');