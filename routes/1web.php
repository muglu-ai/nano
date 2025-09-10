<?php

use App\Http\Controllers\CoExhibitorController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExhibitorController;
use App\Http\Controllers\MisController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentReceiptController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SponsorController;
use App\Http\Middleware\Auth;
use App\Http\Middleware\CheckUser;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\ExtraRequirementController;


Route::get('/invoice/details', [InvoicesController::class, 'view'])->name('invoice.details');
Route::get('extra_requirements/list', [ExtraRequirementController::class, 'list'])->name('extra_requirements.list');

//Route::get('/', function () {
//    return view('auth.login_new');
//})->name('login');
//Route::get('login', function () {
//    return view('auth.login_new');
//})->name('login');
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

Route::get('/sponsorship-test', function () {
    return view('sponsor.page');
});
//co exhibitor dashboard
Route::get('/co-exhibitor/dashboard', function () {
    return view('co_exhibitor.dashboard');
})->name('dashboard.co-exhibitor');

Route::post('extra_requirements', [ExtraRequirementController::class, 'store'])->name('extra_requirements.store')->middleware(CheckUser::class);
Route::get('exhibitor/orders', [ExtraRequirementController::class, 'userOrders'])->name('exhibitor.orders')->middleware(CheckUser::class);
//route get the exhibitor co-exhibitor list
Route::get('/co-exhibitor', [CoExhibitorController::class, 'user_list'])->name('co_exhibitor')->middleware(CheckUser::class);
Route::post('/co-exhibitor/store', [CoExhibitorController::class, 'store'])->name('co_exhibitor.store')->middleware(CheckUser::class);
Route::post('/co-exhibitor/approve/{id}', [CoExhibitorController::class, 'approve'])->name('co_exhibitor.approve')->middleware(Auth::class);

Route::post('/co-exhibitor/reject/{id}', [CoExhibitorController::class, 'reject'])->name('co_exhibitor.reject')->middleware(Auth::class);
Route::get('/co-exhibitors', [CoExhibitorController::class, 'index'])->name('co_exhibitors')->middleware(Auth::class);
Route::get('/{event}/onboarding', [ApplicationController::class, 'showForm2'])->name('new_form');

////
//Route::get('{role}/register', [AuthController::class, 'showRegistrationForm'])->name('register.form');
//Route::post('{role}/register', [AuthController::class, 'register'])->name('register');
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register.form');
Route::post('register', [AuthController::class, 'register'])->name('register');


Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('upload-receipt-user', [PaymentReceiptController::class, 'uploadReceipt_user'])->name('upload.receipt_user')->middleware(CheckUser::class);


//forget password

Route::get('forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('forgot.password');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('forgot.password.submit');
//reset password
Route::get('reset-password/{token}/{email}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password');
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('reset.password.submit');

//verify account with get method
Route::get('verify-account/{token}', [AuthController::class, 'verifyAccount'])->name('auth.verify');


//review the filled information by the user
Route::get('/exhibitor/application/review', [ApplicationController::class, 'review'])->name('application.review');

// Route for Exhibitor Application
//Route::middleware(['auth', 'role:exhibitor'])->group(function () {
//    Route::get('/exhibitor/application', function () {
//        return app(ApplicationController::class)->showForm('exhibitor');
//    })->name('application.exhibitor');
//
//    Route::post('/exhibitor/application', [ApplicationController::class, 'submitForm'])->name('application.exhibitor.submit');
//});
//group middleware for checkuser
//Route::middleware(['auth', 'role:exhibitor'])->group(function () {

Route::match(['post', 'get'],'/application/exhibitor', [ApplicationController::class, 'showForm'])->name('application.exhibitor')->middleware(CheckUser::class);
//Route::match(['get'],'exhibitor/application', [ApplicationController::class, 'showForm'])->name('application.exhibitor')->middleware(CheckUser::class);
Route::post('/exhibitor/application', [ApplicationController::class, 'submitForm'])->name('application.exhibitor.submit')->middleware(CheckUser::class);
Route::view('/about', 'pages.home')->name('about');
Route::view('/application-from', 'pages.form')->name('application-form');
//second step of form

Route::get('apply', [ApplicationController::class, 'apply'])->name('application.show')->middleware(CheckUser::class);
Route::post('apply', [ApplicationController::class, 'apply_store'])->name('event-participation.store');

//terms and conditions page
Route::get('terms', [ApplicationController::class, 'terms'])->name('terms')->middleware(CheckUser::class);
//terms_store
Route::post('terms', [ApplicationController::class, 'terms_store'])->name('terms.store');

// get preview from preview function of application controller
Route::get('preview', [ApplicationController::class, 'preview'])->name('application.preview')->middleware(CheckUser::class);
// route to updated the submitted form with name final
Route::post('final', [ApplicationController::class, 'final'])->name('application.final');
//sponsor product list page with index function
//Route::get('sponsor', [SponsorController::class, 'index'])->name('sponsor.index');

Route::get('/sponsor-items', [SponsorController::class, 'index']);


// Route for Sponsor Application
Route::middleware(['auth', 'role:sponsor'])->group(function () {
    Route::get('/sponsor/application', function () {
        return app(ApplicationController::class)->showForm('sponsor');
    })->name('application.sponsor');

    Route::post('/sponsor/application', [ApplicationController::class, 'submitForm'])->name('application.sponsor.submit');
});

//Route::middleware(['auth', 'role:exhibitor'])->group(function () {
//    Route::get('dashboard', [DashboardController::class, 'exhibitorDashboard'])->name('user.dashboard');
//});
Route::get('dashboard', [DashboardController::class, 'exhibitorDashboard'])->name('user.dashboard')->middleware(CheckUser::class);
Route::middleware(['auth', 'role:sponsor'])->group(function () {
    Route::get('/sponsor/dashboard', fn() => view('sponsor.dashboard'))->name('dashboard.sponsor');

});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'exhibitorDashboard'])->name('dashboard.admin');
});

//logout


Route::get('/import_states', [MisController::class, 'getCountryAndState']);
Route::post('/get-states', [MisController::class, 'getStates'])->name('get.states');
//Route::get('bill', function () {
//    return view('bills.invoice');
//})->name('bill');
//Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
//test-layout
Route::get('/test', [AdminController::class, 'test'])->name('test');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/application-list/', [AdminController::class, 'index'])->name('application.lists')->middleware(Auth::class);;
Route::get('/application-list/{status}', [AdminController::class, 'index'])->name('application.list')->middleware(Auth::class);
Route::get('/application-detail', [DashboardController::class, 'applicantDetails'])->name('application.show.admin')->middleware(Auth::class);
Route::get('/price', [AdminController::class, 'price'])->name('price')->middleware(Auth::class);
//approve application
Route::post('/approve/{id}', [AdminController::class, 'approve'])->name('approve')->middleware(Auth::class);
//route get invoice list from dashboard controller invoiceDetails function
Route::get('/invoice-list', [DashboardController::class, 'invoiceDetails'])->name('invoice.list')->middleware(Auth::class);
//route get event list only for registered user
Route::get('/event-list', [AuthController::class, 'showEvents'])->name('event.list')->middleware(CheckUser::class);
//invoice details
Route::match(['post', 'get'], '/proforma/{application_id}', [ApplicationController::class, 'invoice'])->name('invoice-details')->middleware(CheckUser::class);//Route::view('/users/list', 'admin.user')->name('users.list')->middleware(Auth::class);
Route::view('/users/list', 'admin.users')->name('users.list')->middleware(Auth::class);
Route::get('/get-users', [AdminController::class, 'getUsers'])->middleware(Auth::class);
///post application/submit-endpoint to submit the application
Route::post('/application/submit', [AdminController::class, 'approve'])->name('approve.submit')->middleware(Auth::class);
Route::get('/application/submit/test', [AdminController::class, 'approve_test'])->name('approve.submit.test')->middleware(Auth::class);
Route::post('/sponsorship/submit', [SponsorController::class, 'approve'])->name('sponsorship.submit')->middleware(Auth::class);
Route::post('/application/reject', [AdminController::class, 'reject'])->name('reject.submit')->middleware(Auth::class);
Route::post('/sponsorship/reject', [AdminController::class, 'sponsorship_reject'])->name('sponsorship.reject')->middleware(Auth::class);

//Payment routes
Route::match(['post', 'get'],'/payment', [PaymentController::class, 'showOrder'])->name('payment')->middleware(CheckUser::class);
Route::post('/payment/success', [PaymentController::class, 'completeOrder'])->name('payment_success')->middleware(Auth::class);
//partial amount payment
Route::post('/payment/partial', [PaymentController::class, 'partialPayment'])->name('payment.partial')->middleware(CheckUser::class);
Route::post('/payment/full', [PaymentController::class, 'fullPayment'])->name('payment.full')->middleware(CheckUser::class);
//payment verified from payment gateway
Route::match(['post', 'get'],'/payment/verify', [PaymentController::class, 'Successpayment'])->name('payment.verify')->middleware(CheckUser::class);



//exhibitor Dashboard
//get the complimentary delegates list
Route::get('/exhibitor/list/{type}', [ExhibitorController::class, 'list'])->name('exhibition.list')->middleware(CheckUser::class);//invite delegates to the event
Route::post('/invite', [ExhibitorController::class, 'invite'])->name('exhibition.invite')->middleware(CheckUser::class);
Route::get('/get-users', [AdminController::class, 'getUsers'])->middleware(Auth::class);
Route::post('/add', [ExhibitorController::class, 'add'])->name('exhibition.invite')->middleware(CheckUser::class);
//application_info
Route::get('application-info', [ApplicationController::class, 'applicationInfo'])->name('application.info')->middleware(CheckUser::class);
//invoices list
Route::get('invoices', [ExhibitorController::class, 'invoices'])->name('exhibitor.invoices')->middleware(CheckUser::class);
//Upload payemnt receipt
Route::post('upload-receipt', [PaymentReceiptController::class, 'uploadReceipt'])->name('upload.receipt')->middleware(Auth::class);
//get the application info

//dynamic event application
Route::get('/{event}/onboarding', [ApplicationController::class, 'showForm2'])->name('new_form')->middleware(CheckUser::class);
Route::get('/{event}/sponsorship', [SponsorController::class, 'new'])->name('sponsorship')->middleware(CheckUser::class);
Route::post('/submit_sponsor', [SponsorController::class, 'store'])->name('sponsor.store')->middleware(CheckUser::class);
Route::get('/sponsor/preview', [SponsorController::class, 'preview'])->name('sponsor.review')->middleware(CheckUser::class);
//delete sponsor application with post method
Route::post('/sponsor/delete', [SponsorController::class, 'delete'])->name('sponsor.delete')->middleware(CheckUser::class);
//submit the application with post method
Route::post('/sponsor/submit', [SponsorController::class, 'submit'])->name('sponsor.submit')->middleware(CheckUser::class);



Route::get('apply_new', [ApplicationController::class, 'apply_new'])->name('apply_new')->middleware(CheckUser::class);
//store the sponsorship submission


Route::get('review_new', function() {
    return view('applications.preview_new');
});
//Exhibitor Admin Routes
Route::get('applicationView', [AdminController::class, 'applicationView'])->name('application.view')->middleware(Auth::class);
//verify paymnent route with post method
Route::post('verify-payment', [PaymentController::class, 'verifyPayment'])->name('verify.payment')->middleware(Auth::class);
Route::post('verify-extra-payment', [PaymentController::class, 'verifyExtraPayment'])->name('verify.extra-payment')->middleware(Auth::class);



//Sponsorship Admin routes
Route::view('/sponsorship/list', 'sponsor.applications')->name('users.list')->middleware(Auth::class);
Route::get('/sponsors_list', [SponsorController::class, 'get_applications'])->middleware(Auth::class);
//approve-sponsorship
Route::post('approve-sponsorship', [SponsorController::class, 'approve'])->name('approve.sponsorship')->middleware(Auth::class);
//review_sponsor with class name review from SponsorController
Route::get('review_sponsor', [SponsorController::class, 'review'])->name('review.sponsor')->middleware(CheckUser::class);


//Admin Sponsorship Route
Route::get('/sponsorship-list/', [AdminController::class, 'sponsorApplicationList'])->name('sponsorship.lists')->middleware(Auth::class);;
Route::get('/sponsorship-list/{status}', [AdminController::class, 'sponsorApplicationList'])->name('sponsorship.list')->middleware(Auth::class);

//Invoices and Payments  routes

Route::get('/invoice', [InvoicesController::class, 'index'])->name('invoice.list')->middleware(Auth::class);
Route::get('/invoice/{id}', [InvoicesController::class, 'show'])->name('invoice.show')->middleware(Auth::class);
//get the invoice details from invoice controller with view function as get method




//Mail Controller
//return view with route mail test from MailController
Route::get('/mail-test', [MailController::class, 'mailTest'])->name('mail.test');
Route::post('/send-email', [MailController::class, 'sendEmail'])->name('send.email');



//sales controller
Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');


//Extra Requirement Controller

//get method for extra requirements from extra requirement controller with index function
Route::get('extra_requirements', [ExtraRequirementController::class, 'index'])->name('extra_requirements.index');
Route::get('requirements/order', [ExtraRequirementController::class, 'allOrders'])->name('extra_requirements.admin');

//route to export data
Route::get('export_users', [ExportController::class, 'export_users'])->name('export.users')->middleware(Auth::class);
Route::get('export_applications', [ExportController::class, 'export_applications'])->name('export.applications')->middleware(Auth::class);
Route::get('export_sponsorships', [ExportController::class, 'export_sponsorship_applications'])->name('export.sponsorships')->middleware(Auth::class);

//verify the membership by admin /membership/verify
Route::post('membership/verify', [AdminController::class, 'verifyMembership'])->name('membership.verify')->middleware(Auth::class);
///membership/reject
Route::post('membership/reject', [AdminController::class, 'unverifyMembership'])->name('membership.reject')->middleware(Auth::class);






Route::get('/download-invoice', [InvoicesController::class, 'generatePDF'])->name('download.invoice');



Route::post('/get-sqm-options', [ApplicationController::class, 'getSQMOptions']);

//get country code from applicationController
Route::post('/get-country-code', [ApplicationController::class, 'getCountryCode']);















