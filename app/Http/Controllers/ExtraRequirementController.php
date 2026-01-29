<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\ExtraRequirement;
use Illuminate\Support\Facades\Log;
use App\Models\RequirementOrderItem;
use App\Models\RequirementsOrder;
use Illuminate\Support\Facades\Validator;
use App\Models\BillingDetail;
//use coexhibitor model
use App\Models\CoExhibitor;
use App\Models\RequirementsBilling;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
// use App\Models\Invoice;

class ExtraRequirementController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        //check whether the user is logged in or not
        if (!auth()->check()) {
            //if user is not logged in, redirect to login page
            return redirect()->route('login');
        }
    }
    // Show all items
    public function index()
    {
        //if user is not logged in, redirect to login page
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        //if user is admin then return to extra_requirements.admin
        if (auth()->user()->role == 'admin') {
            return redirect()->route('extra_requirements.admin');
        }

        $coExhibitor = null;
        //if user->role is co-exhibitor then get the co-exhibitor details
        if (auth()->user()->role == 'co-exhibitor') {
            $coExhibitor = CoExhibitor::where('email', auth()->user()->email)->first();
            if ($coExhibitor) {
                $billingDetails = $coExhibitor->billingDetail;
            }
        }
        $items = ExtraRequirement::all();
        return view('extra_requirements.index', compact('items', 'coExhibitor'));
    }

      public function deleteOrder(Request $request)
{
    // Validate the request
    // dd($request->all());
    // $request->validate([
    //     'order_id' => 'required|integer|exists:requirements_orders,id',
    //     'invoice_id' => 'required|integer|exists:invoices,invoice_no',
    // ]);

    // if it is not validated give error message
    // if ($request->fails()) {
    //     return redirect()->back()->with('delete_error', 'Invalid order or invoice ID.');
    // }

    $user_id = auth()->id();

    // dd($user_id);

    // Check if the order belongs to the authenticated user
    $order = RequirementsOrder::where('id', $request->input('order_id'))
        ->where('user_id', $user_id)
        ->first();

    if (!$order) {
        return redirect()->back()->with('delete_error', 'Order not found or does not belong to the authenticated user.');
    }

    // Find the invoice
    $invoice = Invoice::where('invoice_no', $request->input('invoice_id'))->first();

    if (!$invoice) {
        return redirect()->back()->with('delete_error', 'Invoice not found.');
    }

    // Check if the invoice is already paid or partial
    if (in_array($invoice->payment_status, ['paid', 'partial'])) {
        return redirect()->back()->with('delete_error', 'Cannot delete order with paid or partially paid invoice.');
    }

    // Mark the order as deleted (soft delete)
    $order->delete = true;
    $order->save();

    return redirect()->route('exhibitor.orders')->with('success', 'Order deleted successfully!');
}

    // Show form to create new item
    public function create()
    {
        return view('extra_requirements.create');
    }

    //list of all requirements in json format
    public function list()
    {
        $items = ExtraRequirement::all();
        return response()->json($items);
    }

    // Store new item
    public function store0(Request $request)
    {
        //as data is in array format, we need to loop through each item and store it in database
        foreach ($request->all() as $item) {
            Log::info('items', $item);
            $validator = \Validator::make($item, [
                'item_id' => 'required|integer|exists:extra_requirements,id',
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            Log::info($item);
            //ExtraRequirement::create($item);
        }
        //return json response with  message data received successfully
        return response()->json(['message' => 'Data received successfully']);

        return redirect()->route('extra_requirements.index')->with('success', 'Item added successfully!');
    }

    public function store1(Request $request)
    {
        // Retrieve 'items' array from the request
        $items = $request->input('items', []);

        // Check if items exist and are in the expected format
        if (!is_array($items) || empty($items)) {
            return response()->json(['error' => 'Invalid input format'], 422);
        }

        foreach ($items as $item) {
            Log::info('Processing item:', $item);

            // Ensure $item is an array before validating
            if (!is_array($item)) {
                return response()->json(['error' => 'Invalid item format'], 422);
            }

            $validator = \Validator::make($item, [
                'item_id' => 'required|integer|exists:extra_requirements,id',
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }



            Log::info('Validated item:', $item);
            // ExtraRequirement::create($item);
        }

        return response()->json(['message' => 'Data received successfully']);
    }


    // Generate a unique invoice number 
    private function generateUniqueInvoiceNo()
    {
        $invoice_no = 'INV-SEMI25-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        if (Invoice::where('invoice_no', $invoice_no)->exists()) {
            return $this->generateUniqueInvoiceNo();
        }
        return $invoice_no;
    }


    // Handle tax invoice upload and send as email attachment
    public function uploadTaxInvoice(Request $request, $orderId)
    {

        // dd($orderId);

        // dd($request->all());
        $request->validate([
            'tax_invoice' => 'required|mimes:pdf|max:2048', // 2MB max
        ]);

        $order = RequirementsOrder::with(['user', 'invoice'])->findOrFail($orderId);

        $invoice = Invoice::where('id', $order->invoice_id)->first();

        $invoice_id = $invoice->invoice_no;

        $directory = storage_path('app/public/tax_invoices');
        if (!file_exists($directory)) {
            mkdir($directory, 0775, true);
        }
        // Store the file
        $path = $request->file('tax_invoice')->store('tax_invoices', 'public');

        //change the file name to invoice_no.pdf
        $fileName = $invoice_id . '.pdf';
        Storage::disk('public')->move($path, 'tax_invoices/' . $fileName);
        $path = 'tax_invoices/' . $fileName;


        $fullPath = '/storage/' . $path;

        //add public_path 
        $fullPath = public_path($fullPath);


        //update invoice in tax_invoice column as array 
        $existingTaxInvoice = $invoice->tax_invoice;

        // If already exists, make it an array and append the new path
        if ($existingTaxInvoice) {
            // If it's already an array, decode it; otherwise, make it an array
            $taxInvoices = is_array($existingTaxInvoice)
                ? $existingTaxInvoice
                : (is_json($existingTaxInvoice) ? json_decode($existingTaxInvoice, true) : [$existingTaxInvoice]);

            $taxInvoices[] = $path;
            $invoice->update(['tax_invoice' => json_encode($taxInvoices)]);
        } else {
            // If not exists, just store as array with one item
            $invoice->update(['tax_invoice' => json_encode([$path])]);
        }

        // Helper function to check if a string is JSON
        function is_json($string)
        {
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        }


        // dd($fullPath);



        // dd($invoice_id, $invoice->application->eventContact->email ?? 'No contact email found');


        if (!$invoice) {
            return back()->with('error', 'Invoice not found for this order.');
        }



        // Prepare recipient email (fallback to invoice email if user email not set)
        // $recipient = $order->user->email ?? ($order->invoice->billing_email ?? null);

        $recipient1 = $invoice->application->eventContact->email;
        $recipient2 = $invoice->application->user->email;
        $recipient3 = $invoice->application->billingDetail->email;  // Use event contact email if available, otherwise use user email

        //make array of recipient emails
        $recipients = array_unique(array_filter([$recipient1, $recipient2, $recipient3]));

        // If no recipients found, return error
        if (empty($recipients)) {
            return back()->with('error', 'No recipient email found for this order.');
        }

        // Always send to accounts and support as CC
        $cc = ['accounts@mmactiv.com', 'support.mmaportal@interlinks.in'];

        // Send email with attachment
        Mail::send([], [], function ($message) use ($order, $fullPath, $invoice_id, $recipients, $cc) {
            $message->to($recipients)
                ->cc($cc)
                ->subject('Tax Invoice for Order: ' . $invoice_id)
                ->html('Dear Customer,<br>Your tax invoice is attached.<br>Thank you.')
                ->attach($fullPath);
        });

        //dd($invoice);
        // dd($fullPath);
        //dd($order->invoice());

        return back()->with('success', 'Tax invoice uploaded and sent successfully!');
    }


    public function store4(Request $request)
    {
        // Validate the request format
        $items = $request->input('items', []);
        if (!is_array($items) || empty($items)) {
            return response()->json(['error' => 'Invalid input format'], 422);
        }

        //if user is admin then return to extra_requirements.admin
        //        if (auth()->user()->role == 'admin') {
        //            return redirect()->route('extra_requirements.admin');
        //        }
        //
        //

        // Ensure user and application exist
        //$application_id = $request->input('application_id');

        $user_id = auth()->id();

        //if the user role is co-exhibitor, get the application_id from co_exhibitors table and get the application_id and find the application from that id
        if (auth()->user()->role == 'co-exhibitor') {
            // Log::info('User is a co-exhibitor', ['user_id' => $user_id]);
            $email = auth()->user()->email;
            $CoExh = CoExhibitor::where('email', $email)->first();
            if (!$CoExh) {
                return response()->json(['error' => 'CoExhibitor record not found'], 404);
            }
            //Log::info('CoExhibitor record found', ['CoExh' => $CoExh]);
            $application_id = $CoExh->application->id;
            //Log::info('CoExhibitor application_id', ['application_id' => $application_id]);
        } else {
            //get the user application_id from the application table where user_id is equal to the user_id
            $application_id = Application::where('user_id', $user_id)->first()->id;
        }
        //get the user application_id from the application table where user_id is equal to the user_id
        //$application_id = Application::where('user_id', $user_id)->first()->id;

        if (!$application_id || !$user_id) {
            return response()->json(['error' => 'Application ID and User ID are required'], 422);
        }

        $country = BillingDetail::where('application_id', $application_id)->first()->country->name;
        //Log::info('Country:', $country);

        //log the application_id and user_id
        Log::info('Creating order for application_id:', ['application_id' => $application_id, 'user_id' => $user_id, 'country' => $country]);
        // Create an invoice for the order
        $invoice = Invoice::create([
            'application_id' => $application_id,
            'amount' => 0, // Will update after item calculations
            'currency' => 'INR',
            'payment_status' => 'unpaid',
            'payment_due_date' => now()->addDays(7),
            'discount_per' => 0,
            'discount' => 0,
            'gst' => 18, // GST 18%
            'price' => 0,
            'processing_charges' => 0,
            'total_final_price' => 0,
            'partial_payment_percentage' => 0,
            'pending_amount' => 0,
            'type' => 'extra_requirement',
            'invoice_no' => $this->generateUniqueInvoiceNo(),
            'amount_paid' => 0,
        ]);

        // Create the order`
        $order = RequirementsOrder::create([
            'application_id' => $application_id,
            'invoice_id' => $invoice->id,
            'user_id' => $user_id,
        ]);

        $total_price = 0;

        foreach ($items as $item) {
            //Log::info('Processing item:', $item);

            // Validate item data
            $validator = Validator::make($item, [
                'item_id' => 'required|integer|exists:extra_requirements,id',
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $requirement = ExtraRequirement::find($item['item_id']);
            $unit_price = $requirement->price_for_expo ?? 0; // Assume the requirement has a price field

            $subtotal = $unit_price * $item['quantity'];

            $total_price += $subtotal;

            // Store order items
            RequirementOrderItem::create([
                'requirements_order_id' => $order->id,
                'requirement_id' => $item['item_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $unit_price,
            ]);
        }

        $item_price = $total_price;
        $gst_amount = round(($total_price * 18) / 100);

        // Update the total price with GST
        $total_price += $gst_amount;
        //calculate the processing charges and add it to the total price
        $processing_charges = 0;
        //Log::info('Country: ' . $country);
        //if billing_country is not India then processing charges is 9% of total price else 2% of total price
        if ($country != 'India') {
            $processing_charges = round(($total_price * 9) / 100, 2);
        } else {
            $processing_charges = round(($total_price * 3) / 100, 2);
        }

        //round off the processing charges to 2 decimal places
        $processing_charges = round($processing_charges);

        // if ($total_price > 0) {
        //     $processing_charges = round(($total_price * 2) / 100, 2);
        // }

        $total_price += $processing_charges;




        // Update invoice amounts





        $final_total_price = round($total_price, 2);
        $final_total_price_usd = null; // Initialize USD price variable

        //3550.62 round off to 3551
        $final_total_price = round($final_total_price);

        //surcharge logic
        
        $orderDate = now();
        $paymentStatus = 'unpaid'; // For new orders, always unpaid

        $surChargepercentage = 0;

        // Define cutoff dates
        // Define cutoff dates
        $standardEnd   = now()->copy()->setDate(2025, 8, 9)->endOfDay();
        $thirtyStart   = now()->copy()->setDate(2025, 8, 10)->endOfDay();
        $thirtyEnd     = now()->copy()->setDate(2025, 8, 12)->endOfDay();
        $fiftyStart    = now()->copy()->setDate(2025, 8, 16)->startOfDay();
        $fiftyEnd      = now()->copy()->setDate(2025, 8, 25)->endOfDay();
        $onsiteStart   = now()->copy()->setDate(2025, 8, 26)->startOfDay();

        // 1. Onsite orders: From 26th August 2025
        if (now()->gte($onsiteStart)) {
            if (
                $orderDate->gte($onsiteStart) // new orders
                || (
                    $orderDate->lte($onsiteStart->copy()->subDay()) // orders placed on or before 25th Aug
                    && $paymentStatus != 'paid'
                )
            ) {
                $surChargepercentage = 75;
            }
        }
        // 2. 50% Surcharge: From 16th August 2025
        elseif (now()->gte($fiftyStart)) {
            if (
                $orderDate->gte($fiftyStart) // new orders
                || (
                    $orderDate->lte($fiftyStart->copy()->subDay()) // orders placed on or before 15th Aug
                    && $paymentStatus != 'paid'
                )
            ) {
                $surChargepercentage = 50;
            }
        }
        // 3. 30% Surcharge: From 10th August 2025
        elseif (now()->gte($thirtyStart)) {
            if (
                $orderDate->gte($thirtyStart) // new orders
                || (
                    $orderDate->lte($standardEnd) // orders placed before 10th Aug
                    && $paymentStatus != 'paid'
                    && now()->gte($thirtyEnd->copy()->addDay()) // after 12th Aug
                )
            ) {
                $surChargepercentage = 30;
            }
        }
        // 4. No surcharge for orders placed before 10th Aug and paid by 12th Aug
        else {
            $surChargepercentage = 0;
        }

        //dd($surChargepercentage);

        $totalWithoutSurcharge = $final_total_price;

        $surCharge = round(($final_total_price * $surChargepercentage) / 100);
        $final_total_price += $surCharge;

        // Log::info('Surcharge percentage:', [
        //     'order_date' => $orderDate->toDateString(),
        //     'payment_status' => $paymentStatus,
        //     'sur_charge_percentage' => $surChargepercentage,
        // ]);

        //dd($surChargepercentage);

        // $surCharge = round(($final_total_price * $surChargepercentage) / 100);
        // $final_total_price += $surCharge;

        if ($country != 'India') {
            // Path to store the last successful exchange rate
            $rate_file = "exchange_rate.json";

            // Function to get the last stored rate
            function get_last_stored_rate($rate_file)
            {
                if (file_exists($rate_file)) {
                    $stored_data = json_decode(file_get_contents($rate_file), true);
                    if (isset($stored_data["INR"])) {
                        return $stored_data["INR"];
                    }
                }
                return null; // Return null if no stored rate exists
            }

            // Fetch the latest exchange rate from API
            $api_url = "https://v6.exchangerate-api.com/v6/303f4de10b784cbb27e4a065/latest/USD";
            $response = @file_get_contents($api_url); // Suppress errors if API fails
            $data = $response ? json_decode($response, true) : null;

            // Check if API call was successful
            if ($data && isset($data["conversion_rates"]["INR"])) {
                $inr_to_usd_rate = $data["conversion_rates"]["INR"];

                // Save the latest rate to file
                file_put_contents($rate_file, json_encode(["INR" => $inr_to_usd_rate]));
            } else {
                // Use last stored rate if API fails
                $inr_to_usd_rate = get_last_stored_rate($rate_file);

                if (!$inr_to_usd_rate) {
                    Log::info("Error: Unable to fetch exchange rates, and no stored rate available.");
                }
            }

            // Convert INR to USD
            $final_total_price_usd = $final_total_price / $inr_to_usd_rate;
            $final_total_price_usd = round($final_total_price_usd, 2); // Round to 2 decimal places
        }

        // Log::info('Final total price:', [
        //     'total_price' => $total_price,
        //     'processing_charges' => $processing_charges,
        //     'gst_amount' => $gst_amount,
        //     'final_total_price' => $final_total_price,
        //     'final_total_price_usd' => $final_total_price_usd ?? null,
        //     'co_exhibitorID' => $CoExh->id ?? 'test',
        //     'usd_rate' => $inr_to_usd_rate ?? 'test',
        // ]);
        $invoice->update([
            'processing_charges' => $processing_charges,
            'price' => $item_price,
            'amount' => $final_total_price,
            'gst' => $gst_amount,
            'total_final_price' => $final_total_price,
            'int_amount_value' => $final_total_price_usd,
            'pending_amount' => $totalWithoutSurcharge,
            'co_exhibitorID' => $CoExh->id ?? null,
            'usd_rate' => $inr_to_usd_rate ?? null,
            'surChargepercentage' => $surChargepercentage,
            'surCharge' => $surCharge,
        ]);

        return response()->json([
            'message' => 'Order created successfully',
            'order_id' => $order->id,
            'invoice_id' => $invoice->id,
            'total_price' => $final_total_price,
        ]);
    }


    public function store(Request $request)
    {


        // Log::info('Request data:', $request->all());
        $items = $request->input('items', []);
        if (!is_array($items) || empty($items)) {
            return response()->json(['error' => 'Invalid input format'], 422);
        }

        //if user is admin then return to extra_requirements.admin
        //        if (auth()->user()->role == 'admin') {
        //            return redirect()->route('extra_requirements.admin');
        //        }
        //
        //

        // Ensure user and application exist
        //$application_id = $request->input('application_id');

        $user_id = auth()->id();

        //if the user role is co-exhibitor, get the application_id from co_exhibitors table and get the application_id and find the application from that id
        if (auth()->user()->role == 'co-exhibitor') {
            // Log::info('User is a co-exhibitor', ['user_id' => $user_id]);
            $email = auth()->user()->email;
            $CoExh = CoExhibitor::where('email', $email)->first();
            if (!$CoExh) {
                return response()->json(['error' => 'CoExhibitor record not found'], 404);
            }
            //Log::info('CoExhibitor record found', ['CoExh' => $CoExh]);
            $application_id = $CoExh->application->id;
            //Log::info('CoExhibitor application_id', ['application_id' => $application_id]);
        } else {
            //get the user application_id from the application table where user_id is equal to the user_id
            $application_id = Application::where('user_id', $user_id)->first()->id;
        }
        //get the user application_id from the application table where user_id is equal to the user_id
        //$application_id = Application::where('user_id', $user_id)->first()->id;

        if (!$application_id || !$user_id) {
            return response()->json(['error' => 'Application ID and User ID are required'], 422);
        }

        $country = BillingDetail::where('application_id', $application_id)->first()->country->name;
        //Log::info('Country:', $country);

        //log the application_id and user_id
        Log::info('Creating order for application_id:', ['application_id' => $application_id, 'user_id' => $user_id, 'country' => $country]);
        // Create an invoice for the order
        $invoice = Invoice::create([
            'application_id' => $application_id,
            'amount' => 0, // Will update after item calculations
            'currency' => 'INR',
            'payment_status' => 'unpaid',
            'payment_due_date' => now()->addDays(7),
            'discount_per' => 0,
            'discount' => 0,
            'gst' => 18, // GST 18%
            'price' => 0,
            'processing_charges' => 0,
            'total_final_price' => 0,
            'partial_payment_percentage' => 0,
            'pending_amount' => 0,
            'type' => 'extra_requirement',
            'invoice_no' => $this->generateUniqueInvoiceNo(),
            'amount_paid' => 0,

        ]);

        // Create the order`
        $order = RequirementsOrder::create([
            'application_id' => $application_id,
            'invoice_id' => $invoice->id,
            'user_id' => $user_id,
        ]);

        $total_price = 0;

        foreach ($items as $item) {
            //Log::info('Processing item:', $item);

            // Validate item data
            $validator = Validator::make($item, [
                'item_id' => 'required|integer|exists:extra_requirements,id',
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $requirement = ExtraRequirement::find($item['item_id']);
            $unit_price = $requirement->price_for_expo ?? 0; // Assume the requirement has a price field

            $subtotal = $unit_price * $item['quantity'];

            $total_price += $subtotal;

            // Store order items
            RequirementOrderItem::create([
                'requirements_order_id' => $order->id,
                'requirement_id' => $item['item_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $unit_price,
            ]);
        }

        $item_price = $total_price;

        $orderDate = now();
        $paymentStatus = 'unpaid'; // For new orders, always unpaid

        $surChargepercentage = 0;

        // Define cutoff dates
        $standardEnd   = now()->copy()->setDate(2025, 8, 10)->endOfDay();
        $thirtyStart   = now()->copy()->setDate(2025, 8, 11)->startOfDay();
        $thirtyEnd     = now()->copy()->setDate(2025, 8, 15)->endOfDay();
        $fiftyStart    = now()->copy()->setDate(2025, 8, 16)->startOfDay();
        $fiftyEnd      = now()->copy()->setDate(2025, 8, 25)->endOfDay();
        $onsiteStart   = now()->copy()->setDate(2025, 8, 26)->startOfDay();

        // 1. Onsite orders: From 26th August 2025
        if (now()->gte($onsiteStart)) {
            if (
                $orderDate->gte($onsiteStart) // new orders
                || (
                    $orderDate->lte($onsiteStart->copy()->subDay()) // orders placed on or before 25th Aug
                    && $paymentStatus != 'paid'
                )
            ) {
                $surChargepercentage = 75;
            }
        }
        // 2. 50% Surcharge: From 16th August 2025
        elseif (now()->gte($fiftyStart)) {
            if (
                $orderDate->gte($fiftyStart) // new orders
                || (
                    $orderDate->lte($fiftyStart->copy()->subDay()) // orders placed on or before 15th Aug
                    && $paymentStatus != 'paid'
                )
            ) {
                $surChargepercentage = 50;
            }
        }
        // 3. 30% Surcharge: From 10th August 2025
        elseif (now()->gte($thirtyStart)) {
            if (
                $orderDate->gte($thirtyStart) // new orders
                || (
                    $orderDate->lte($standardEnd) // orders placed before 10th Aug
                    && $paymentStatus != 'paid'
                    && now()->gte($thirtyEnd->copy()->addDay()) // after 12th Aug
                )
            ) {
                $surChargepercentage = 30;
            }
        }
        // 4. No surcharge for orders placed before 10th Aug and paid by 12th Aug
        else {
            $surChargepercentage = 0;
        }

        $surChargepercentage = 0;

        $surCharge = round(($item_price * $surChargepercentage) / 100);
        $total_price += $surCharge;

        $gst_amount = round(($total_price * 18) / 100);

        // Update the total price with GST
        $total_price += $gst_amount;
        //calculate the processing charges and add it to the total price
        $processing_charges = 0;
        //Log::info('Country: ' . $country);
        //if billing_country is not India then processing charges is 9% of total price else 2% of total price
        if ($country != 'India') {
            $processing_charges = round(($total_price * 9) / 100, 2);
        } else {
            $processing_charges = round(($total_price * 3) / 100, 2);
        }

        //round off the processing charges to 2 decimal places
        $processing_charges = round($processing_charges);

        // if ($total_price > 0) {
        //     $processing_charges = round(($total_price * 2) / 100, 2);
        // }

        $total_price += $processing_charges;




        // Update invoice amounts





        $final_total_price = round($total_price, 2);
        $final_total_price_usd = null; // Initialize USD price variable

        //3550.62 round off to 3551
        $final_total_price = round($final_total_price);




        //dd($surChargepercentage);

        // $surCharge = round(($final_total_price * $surChargepercentage) / 100);
        // $final_total_price += $surCharge;


        if ($country != 'India') {
            // Path to store the last successful exchange rate
            $rate_file = "exchange_rate.json";

            // Function to get the last stored rate
            function get_last_stored_rate($rate_file)
            {
                if (file_exists($rate_file)) {
                    $stored_data = json_decode(file_get_contents($rate_file), true);
                    if (isset($stored_data["INR"])) {
                        return $stored_data["INR"];
                    }
                }
                return null; // Return null if no stored rate exists
            }

            // Fetch the latest exchange rate from API
            $api_url = "https://v6.exchangerate-api.com/v6/303f4de10b784cbb27e4a065/latest/USD";
            $response = @file_get_contents($api_url); // Suppress errors if API fails
            $data = $response ? json_decode($response, true) : null;

            // Check if API call was successful
            if ($data && isset($data["conversion_rates"]["INR"])) {
                $inr_to_usd_rate = $data["conversion_rates"]["INR"];

                // Save the latest rate to file
                file_put_contents($rate_file, json_encode(["INR" => $inr_to_usd_rate]));
            } else {
                // Use last stored rate if API fails
                $inr_to_usd_rate = get_last_stored_rate($rate_file);

                if (!$inr_to_usd_rate) {
                    $inr_to_usd_rate = 90; // Fallback to a default rate if no stored rate exists
                    Log::info("Error: Unable to fetch exchange rates, and no stored rate available.");
                }
            }

            // Convert INR to USD
            $final_total_price_usd = $final_total_price / $inr_to_usd_rate;
            $final_total_price_usd = round($final_total_price_usd, 2); // Round to 2 decimal places
        }

       // dd($surChargepercentage, $surCharge, $final_total_price);

        // if today

        // Log::info('Final total price:', [
        //     'total_price' => $total_price,
        //     'processing_charges' => $processing_charges,
        //     'gst_amount' => $gst_amount,
        //     'final_total_price' => $final_total_price,
        //     'final_total_price_usd' => $final_total_price_usd ?? null,
        //     'co_exhibitorID' => $CoExh->id ?? 'test',
        //     'usd_rate' => $inr_to_usd_rate ?? 'test',
        // ]);
        $invoice->update([
            'processing_charges' => $processing_charges,
            'price' => $item_price,
            'amount' => $final_total_price,
            'gst' => $gst_amount,
            'total_final_price' => $final_total_price,
            'int_amount_value' => $final_total_price_usd,
            'pending_amount' => $final_total_price,
            'co_exhibitorID' => $CoExh->id ?? null,
            'usd_rate' => $inr_to_usd_rate ?? null,
            'surChargepercentage' => $surChargepercentage,
            'surCharge' => $surCharge,
        ]);

        return response()->json([
            'message' => 'Order created successfully',
            'order_id' => $order->id,
            'invoice_id' => $invoice->id,
            'total_price' => $final_total_price,
        ]);
    }

    public function userOrders(Request $request)
    {
        //if user is not logged in, redirect to login page
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user_id = auth()->id();

        // Fetch all orders placed by the user along with related data
        $orders = RequirementsOrder::where('user_id', $user_id)
            ->with(['invoice', 'orderItems.requirement'])
            ->orderBy('created_at', 'desc')
            ->get();
        // Eager load billing details with the application in a single query
        $application = Application::with('billingDetail')->where('user_id', $user_id)->first();
        $billingDetails = $application ? $application->billingDetail : null;

        $coExhibitor = null;
        //if user->role is co-exhibitor then get the co-exhibitor details
        if (auth()->user()->role == 'co-exhibitor') {
            $coExhibitor = CoExhibitor::where('email', auth()->user()->email)->first();
            if ($coExhibitor) {
                $billingDetails = $coExhibitor->billingDetail;
            }
        }




        return view('extra_requirements.orders', compact('orders', 'billingDetails', 'coExhibitor', 'application'));
        //        return response()->json($orders);
    }

    //mark order as delivered by admin only 
    public function markAsDelivered(Request $request)
    {
        // Validate the request
        $request->validate([
            'order_id' => 'required|integer|exists:requirements_orders,id',
            'remarks' => 'nullable|string|max:255',
        ]);

        // Find the order
        $order = RequirementsOrder::findOrFail($request->input('order_id'));

        // Update the order status and remarks
        $order->delivery_status = 'delivered';
        $order->remarks = $request->input('remarks', '');
        $order->save();

        // If the request expects JSON (AJAX), return JSON response
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => 'Order marked as delivered successfully!']);
        }

        return redirect()->route('extra_requirements.admin')->with('success', 'Order marked as delivered successfully!');
    }



    public function allOrders0()
    {



        //if user type is admin then route to extra_requirements.admin
        //        if (auth()->user()->role == 'admin') {
        //            return redirect()->route('extra_requirements.admin');
        //        }
        // Fetch all orders with related user, invoice, and order items
        if (!auth()->check() || auth()->user()->role != 'admin') {
            return redirect()->route('login');
        }

        //if requirements/order?status is paid or unpaid then filter the orders accordingly
        $status = request()->query('status', 'all');

        $orders = RequirementsOrder::with(['user', 'invoice', 'orderItems.requirement'])
            ->orderBy('created_at', 'desc')
            ->when($status !== 'all', function ($query) use ($status) {
                if ($status === 'paid') {
                    $query->whereHas('invoice', function ($q) {
                        $q->where('payment_status', 'paid');
                    });
                } elseif ($status === 'unpaid') {
                    $query->whereHas('invoice', function ($q) {
                        $q->where('payment_status', 'unpaid');
                    });
                }
            })
            ->get();
        foreach ($orders as $order) {
            $invoice = $order->invoice;
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

            $order->payment_method = $paymentMethod;
        }




        return view('extra_requirements.admin.list', compact('orders'));
    }
 function formatPaymentMethod($gateway)
        {
            return lcfirst(str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', strtolower($gateway)))));
        }
     public function allOrders()
    {

        //ini_set('max_execution_time', 300); // Increase max execution time to 5 minutes

        //if user type is admin then route to extra_requirements.admin
        //        if (auth()->user()->role == 'admin') {
        //            return redirect()->route('extra_requirements.admin');
        //        }
        // Fetch all orders with related user, invoice, and order items
        if (!auth()->check() || auth()->user()->role != 'admin') {
            return redirect()->route('login');
        }

        //if requirements/order?status is paid or unpaid then filter the orders accordingly
        $status = request()->query('status', 'all');



                $orders = RequirementsOrder::with(['user', 'invoice.payments', 'orderItems.requirement'])
                ->orderBy('created_at', 'desc')
                ->where('delete', 0)
                ->when($status !== 'all', function ($query) use ($status) {
                    $query->whereHas('invoice', function ($q) use ($status) {
                        $q->where('payment_status', $status);

                    });
                })
                // ->limit(120)
                ->get();

                foreach ($orders as $order) {
                    $paymentMethod = 'unknown';
                    $paymentVerificationStatus = 'N/A';

                    $invoice = $order->invoice;
                    if (!$invoice) {
                        $order->payment_method = $paymentMethod;
                        $order->payment_verification_status = $paymentVerificationStatus;
                        continue;
                    }

                    $invoiceNoPart = explode('_', $invoice->invoice_no)[0];

                    // Gateway payments (success first)
                    $gatewayPayment = \DB::table('payment_gateway_response')
                        ->whereRaw("LEFT(order_id, LENGTH(?)) = ?", [$invoiceNoPart, $invoiceNoPart])
                        ->orderByDesc('id')
                        ->get();

                    $successfulGateway = $gatewayPayment->first(fn($p) => in_array($p->status, ['Completed', 'Success']));
                    $anyGateway = $gatewayPayment->first();

                    if ($successfulGateway) {
                        $paymentMethod = $this->formatPaymentMethod($successfulGateway->gateway);
                    } else {
                        // Offline payment
                        $offlinePayment = $invoice->payments->first();
                        if ($offlinePayment) {
                            $paymentMethod = 'offline';

                            // Check verification status
                            $hasVerified = $invoice->payments->contains(fn($p) => strtolower($p->verification_status) === 'verified');
$hasRejected = $invoice->payments->contains(fn($p) => strtolower($p->verification_status) === 'rejected');

// Give priority to verified
if ($hasVerified) {
    $paymentVerificationStatus = 'verified';
} elseif ($hasRejected) {
    $paymentVerificationStatus = 'rejected';
} else {
    $paymentVerificationStatus = 'pending';
}
                        } elseif ($anyGateway) {
                            $paymentMethod = $this->formatPaymentMethod($anyGateway->gateway);
                        }
                    }

                    // Fallback to currency
                    if ($paymentMethod === 'unknown') {
                        $paymentMethod = $invoice->currency === 'USD' ? 'paypal' : 'ccavenue';
                    }

                    $order->payment_method = $paymentMethod;
                    $order->payment_verification_status = $paymentVerificationStatus;
                    // echo "Processing Order ID: {$order->id}, Payment Method: {$paymentMethod}, Verification Status: {$paymentVerificationStatus}\n";
                }





        return view('extra_requirements.admin.list', compact('orders'));
    }


    public function allLeadRetrieval()
    {

        ini_set('max_execution_time', 300); // Increase max execution time to 5 minutes

        //if user type is admin then route to extra_requirements.admin
        //        if (auth()->user()->role == 'admin') {
        //            return redirect()->route('extra_requirements.admin');
        //        }
        // Fetch all orders with related user, invoice, and order items
        if (!auth()->check() || auth()->user()->role != 'admin') {
            return redirect()->route('login');
        }

        //if requirements/order?status is paid or unpaid then filter the orders accordingly
        $status = request()->query('status', 'Paid');



                $orders = RequirementsOrder::with(['user', 'invoice.payments', 'orderItems.requirement'])
                ->orderBy('created_at', 'desc')
                     ->whereHas('orderItems.requirement', function ($query) {
                        $query->where('id', 60);
                     })
                ->when($status !== 'all', function ($query) use ($status) {
                    $query->whereHas('invoice', function ($q) use ($status) {
                        $q->whereIn('payment_status', ['paid', 'partial']);
                    })
                    ;
                })
                // ->limit(2)
                ->get();

                // foreach ($orders as $order) {
                //     $paymentMethod = 'unknown';
                //     $paymentVerificationStatus = 'N/A';

                //     $invoice = $order->invoice;
                //     if (!$invoice) {
                //         $order->payment_method = $paymentMethod;
                //         $order->payment_verification_status = $paymentVerificationStatus;
                //         continue;
                //     }

                //     $invoiceNoPart = explode('_', $invoice->invoice_no)[0];

                    // Gateway payments (success first)
                    // $gatewayPayment = \DB::table('payment_gateway_response')
                    //     ->whereRaw("LEFT(order_id, LENGTH(?)) = ?", [$invoiceNoPart, $invoiceNoPart])
                    //     ->orderByDesc('id')
                    //     ->get();

                    // $successfulGateway = $gatewayPayment->first(fn($p) => in_array($p->status, ['Completed', 'Success']));
                    // $anyGateway = $gatewayPayment->first();

                    // if ($successfulGateway) {
                    //     $paymentMethod = $this->formatPaymentMethod($successfulGateway->gateway);
                    // } else {
                        // Offline payment
                        // $offlinePayment = $invoice->payments->first();
                        // if ($offlinePayment) {
                        //     $paymentMethod = 'offline';

                            // Check verification status
                          // $hasVerified = $invoice->payments->contains(fn($p) => strtolower($p->verification_status) === 'verified');

                            // Treat verified as rejected
                    //         $paymentVerificationStatus = ($hasRejected || $hasVerified) ? 'rejected' : 'pending';
                    //     } elseif ($anyGateway) {
                    //         $paymentMethod = $this->formatPaymentMethod($anyGateway->gateway);
                    //     }
                    // }

                    // Fallback to currency
                    // if ($paymentMethod === 'unknown') {
                    //     $paymentMethod = $invoice->currency === 'USD' ? 'paypal' : 'ccavenue';
                    // }

                    // $order->payment_method = $paymentMethod;
                    // $order->payment_verification_status = $paymentVerificationStatus;
                    // echo "Processing Order ID: {$order->id}, Payment Method: {$paymentMethod}, Verification Status: {$paymentVerificationStatus}\n";
                // }

// exit;



        return view('extra_requirements.admin.leadRetrieval', compact('orders'));
    }








    // Show a single item
    public function show(ExtraRequirement $extraRequirement)
    {
        return view('extra_requirements.show', compact('extraRequirement'));
    }

    // Show form to edit item
    public function edit(ExtraRequirement $extraRequirement)
    {
        return view('extra_requirements.edit', compact('extraRequirement'));
    }

    // Update item details
    public function update(Request $request, ExtraRequirement $extraRequirement)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'days' => 'required|integer|min:1',
            'price_for_expo' => 'required|numeric|min:0',
            'image_quantity' => 'nullable|integer|min:0',
            'available_quantity' => 'nullable|integer|min:0',
            'status' => 'required|in:available,out_of_stock',
        ]);

        $extraRequirement->update($request->all());
        return redirect()->route('extra_requirements.index')->with('success', 'Item updated successfully!');
    }

    // Delete item
    public function destroy(ExtraRequirement $extraRequirement)
    {
        $extraRequirement->delete();
        return redirect()->route('extra_requirements.index')->with('success', 'Item deleted successfully!');
    }
    // Show admin dashboard for extra requirements
    public function showExtrarequirement()
    {
        $extraRequirements = ExtraRequirement::all();
        return view('extra_requirements.admin.show', compact('extraRequirements'));
    }

    // Get the analytics for the extra requirements which includes the total number of orders, total amount, and total items sold only for admin
    public function analytics()
    {
        //if user is not logged in, redirect to login page
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        //if user is not admin then redirect to home page
        if (auth()->user()->role != 'admin') {
            return redirect()->route('home');
        }

        // Fetch all orders with related invoice and order items
        $orders = RequirementsOrder::whereHas('invoice', function ($query) {
            $query->where('payment_status', 'paid');
        })
            ->with(['invoice', 'orderItems.requirement'])
            ->get();

        // we have to get the items name as well 
        $items = [];

        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $item_code = $item->requirement->item_code;
                $name = $item->requirement->item_name;
                $price = $item->requirement->price_for_expo;

                // dd($item->requirement, $item_code, $name, $price);

                // dd($item->requirement);
                $quantity = $item->quantity;
                $price = $item->requirement->price_for_expo;
                $unit_price = $item->requirement->price_for_expo;
                // Assuming price per item is stored in orderItems
                $revenue = $quantity * $price;



                if (!isset($items[$name])) {
                    $items[$name] = [
                        'name' => $name,
                        'quantity' => 0,
                        'revenue' => 0,
                        'item_code' => $item_code,
                        'unit_price' => $unit_price,
                    ];
                }
                $items[$name]['quantity'] += $quantity;
                $items[$name]['revenue'] += $revenue;
            }
        }



        $totalOrders = $orders->count();
        $totalAmount = $orders->sum('invoice.total_final_price');
        $totalItemsSold = $orders->sum(function ($order) {
            return $order->orderItems->sum('quantity');
        });

        return view('extra_requirements.admin.analytics', compact('totalOrders', 'totalAmount', 'totalItemsSold', 'items'));
    }

    //update extraRequirement Billing Details
    public function updateBillingDetails(Request $request)
    {

        //  dd($request->all());



        #Log::info('Update Billing Details Request:', $request->all());
        # return response()->json(['message' => 'Update Billing Details Request received'], 200);
        $validator = Validator::make($request->all(), [
            'invoice_id'       => 'required|exists:invoices,invoice_no',
            'billing_company'  => 'required|string|max:255',
            'contact_name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'phone'    => 'required|string|max:20',
            'gst'           => 'nullable|string|max:20',
            'pan_no'           => 'nullable|string|max:20',
            'address'  => 'required|string',
            'city'   => 'required|string|max:255',
            'country_id'       => 'required|exists:countries,id',
            'state_id'         => 'required|exists:states,id',
            'postal_code'          => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'errors'  => $validator->errors()
            ], 422);
        }


        $invoice = Invoice::where('invoice_no', $request->input('invoice_id'))->first();


        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        //if invoice is paid then return error
        if ($invoice->payment_status == 'paid') {
            return response()->json(['error' => 'Billing details cannot be updated for a paid invoice'], 422);
        }

        // store invoice_id in a varibalee 
        $invoice_id = $invoice->id;


        // Map validated request data to the expected database fields using validated data
        $validated = $validator->validated();
        // dd($validated);
        $data = [
            'invoice_id'      => $invoice_id,
            'invoice_no'      => $validated['invoice_id'],
            'billing_company' => $validated['billing_company'] ?? null,
            'billing_name'    => $validated['contact_name'],
            'billing_email'   => $validated['email'],
            'billing_phone'   => $validated['phone'],
            'gst_no'          => $validated['gst'] ?? null,
            'pan_no'          => $validated['pan_no'] ?? null,
            'billing_address' => $validated['address'],
            'country_id'      => $validated['country_id'],
            'state_id'        => $validated['state_id'],
            'billing_city'    => $validated['city'],
            'zipcode'         => $validated['postal_code'],
        ];

        //dd($data);

        $billing = RequirementsBilling::updateOrCreate(
            [
                'invoice_id' => $invoice_id
            ],
            $data
        );

        return response()->json([
            'status' => true,
            'message' => 'Billing information saved successfully.',
            'data' => $billing
        ]);
    }

// Store Lead Retrieval user in DB
     public function addLeadRetrievalUserToFile(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'mobile' => 'required|string|max:20',
            'designation' => 'nullable|string|max:100',
        ]);

        // Store in DB (unique by email)
        $users = \App\Models\LeadRetrievalUser::updateOrCreate(
            ['email' => $request->email],
            [
                'user_id' => $request->user_id,
                'name' => $request->name,
                'mobile' => $request->mobile,
                'designation' => $request->designation,
                'company_name' => $request->company_name,
            ]
        );

       // Get only users for the logged-in user
    $users = \App\Models\LeadRetrievalUser::where('user_id', \Auth::id())->get();

        return response()->json([
            'success' => true,
            'users' => $users,
            'count' => $users->count(),
        ]);
    }

    // Get Lead Retrieval users from JSON file
    public function getLeadRetrievalUsersFromFile($orderId)
    {
    // Fetch only users for the logged-in user
    $users = \App\Models\LeadRetrievalUser::where('user_id', \Auth::id())->get();
    return response()->json(['users' => $users]);
    }
}
