<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\PaypalServerSdkClient;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use App\Models\Invoice;
use App\Models\BillingDetail;
use App\Models\RequirementsOrder;
use App\Models\Payment;
use App\Models\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Mail\ExtraRequirementsMail;
use Illuminate\Support\Facades\Mail;
use App\Services\ExtraRequirementsMailService;
use Illuminate\Support\Facades\Response;
use App\Models\Country;
use Carbon\Carbon;
use App\Models\State;

use App\Models\PaymentGatewayResponse;

class PayPalController extends Controller
{


       public function showPaymentForm($id)
{
    if (!$id) {
        return redirect()->route('payment.lookup')
            ->with('error', 'Invoice ID is required');
    }

    $invoice = Invoice::where('invoice_no', $id)->first();
    if (!$invoice) {
        return redirect()->route('payment.lookup')
            ->with('error', 'Invoice not found')
            ->with('invoice_hint', $id);
    }
    
    // Check if this is a startup zone invoice
    $isStartupZone = false;
    $application = null;
    if ($invoice->application_id) {
        $application = \App\Models\Application::find($invoice->application_id);
        if ($application && $application->application_type === 'startup-zone') {
            $isStartupZone = true;
        }
    }
    
    // For startup-zone, check if already paid and redirect to confirmation
    if ($isStartupZone && $application) {
        if ($invoice->payment_status === 'paid') {
            return redirect()->route('startup-zone.confirmation', $application->application_id)
                ->with('info', 'Payment already completed');
        }
        
        // Check if application is approved - payment only allowed after approval
        if ($application->submission_status !== 'approved') {
            return redirect()->route('startup-zone.payment', $application->application_id)
                ->with('error', 'Your profile is not approved yet for payment. Please wait for admin approval.');
        }
    }
    
    // For non-startup-zone invoices, check type
    if (!$isStartupZone && $invoice->type != 'extra_requirement') {
        return redirect()->route('exhibitor.orders')
            ->with('error', 'Invalid invoice type');
    }

    // Fetch billing detail - handle startup zone differently
    $billingDetail = null;
    
    if ($isStartupZone) {
        // For startup zone, get billing from EventContact
        $eventContact = \App\Models\EventContact::where('application_id', $invoice->application_id)->first();
        if ($eventContact) {
            $billingDetail = $this->formatBillingFromEventContact($eventContact, $invoice->application_id);
        }
    } else {
        // For other types, use BillingDetail
        $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();

        // co-exhibitor override
        if ($invoice->co_exhibitorID) {
            $coExhibitor = \App\Models\CoExhibitor::find($invoice->co_exhibitorID);
            if ($coExhibitor) {
                $billingDetail = $this->formatBillingFromCoExhibitor($coExhibitor);
            }
        }

        // requirements billing override
        $requirementsBilling = \DB::table('requirements_billings')
            ->where('invoice_id', $invoice->id)
            ->first();
        if ($requirementsBilling) {
            $billingDetail = $this->formatBillingFromRequirements($requirementsBilling);
        }
    }
    
    if (!$billingDetail) {
        if ($isStartupZone && $application) {
            return redirect()->route('startup-zone.payment', $application->application_id)
                ->with('error', 'Billing details not found. Please contact support.');
        }
        return redirect()->route('exhibitor.orders')
            ->with('error', 'Billing details not found');
    }

    // determine timezone to compute "today" correctly for the buyer
    $timezone = $this->detectTimezoneForBilling($billingDetail);


    // For startup zone, skip surcharge logic
    if ($isStartupZone) {
        // Startup zone invoices are already calculated, just use the invoice amount
        // Currency is already set in the invoice
    } elseif($invoice->payment_status == 'unpaid') {



       

    if($id != 'INV-SEMI25-B8F4E1' && $id != 'INV-SEMI25-CB988F'){

    // apply surcharge logic (returns array with amount_after_surcharge, surcharge_amount, surcharge_pct)

    
    $surchargeResult = $this->applySurcharge($invoice, $timezone);

    // update invoice with surcharge fields and update invoice amount (INR)
    $invoice->surCharge = $surchargeResult['surcharge_amount']; // numeric INR amount
    $invoice->surChargepercentage = $surchargeResult['surcharge_pct']; // integer percent e.g. 30
    //apply 18% GST on amount after surcharge
    $invoice->gst = round($surchargeResult['amount_after_surcharge'] * 0.18); // 18% GST on amount after surcharge

    // if country is india then apply 3% processing charge else 9% for other countries
    $processingChargeRate = (strtolower($billingDetail->country->name) === 'india') ? 3 : 9;
    if($invoice->removeProcessing == 1){
        $processingChargeRate = 0;
    }
    // now calculate processing charge on amount after surcharge + gst
    $processingCharge = round(($surchargeResult['amount_after_surcharge'] + $invoice->gst) * ($processingChargeRate / 100), 2);

    $invoice->processing_charges = round($processingCharge); // numeric INR amount
    $invoice->amount = round($surchargeResult['amount_after_surcharge'] + $invoice->gst + $processingCharge); // total amount after surcharge, gst and processing charge (INR)




    // $invoice->amount = $surchargeResult['amount_after_surcharge']; // overwrite amount to include surcharge (INR)
    $invoice->total_final_price = $invoice->amount; // final total price after surcharge (INR)
    // $invoice->amount = $surchargeResult['amount_after_surcharge']; // overwrite amount to include surcharge (INR)
    // $invoice->total_final_price = $surchargeResult['amount_after_surcharge']; // final total price after surcharge (INR)
    $invoice->currency = 'INR';

    // if billing country is not India, convert INR total to USD (store int_amount_value & usd_rate)
    $billingCountryName = optional($billingDetail->country)->name ?? null;
    if ($billingCountryName && strtolower($billingCountryName) !== 'india') {
        [$usdAmount, $rate] = $this->convertInrToUsd($invoice->amount);
        $invoice->int_amount_value = $usdAmount;
        $invoice->usd_rate = $rate;
        $invoice->currency = 'USD';
    } else {
        // for Indian billing keep int_amount_value as null or set it equal to INR if you prefer
        $invoice->int_amount_value = null;
        $invoice->usd_rate = null;
    }

    $invoice->save();
    
}
    }
    

    // fetch order items for view
    $orders = RequirementsOrder::where('invoice_id', $invoice->id)
        ->with(['invoice', 'orderItems.requirement'])
        ->orderBy('created_at', 'desc')
        ->get();

    $countries = Country::all(['id', 'name']);

    return view('paypal.payment-form', compact('invoice', 'billingDetail', 'orders', 'countries'));
}

/**
 * Apply surcharge and return detailed result.
 * Returns ['amount_after_surcharge' => float, 'surcharge_amount' => float, 'surcharge_pct' => int]
 */
private function applySurcharge(Invoice $invoice, string $timezone = null): array
{
    $tz = $timezone ?: 'Asia/Kolkata';

    $today = Carbon::now($tz)->startOfDay();
    $orderDate = Carbon::parse($invoice->created_at)->startOfDay();
    $isPaid = in_array($invoice->payment_status, ['paid', 'partial']);

    $baseAmount = floatval($invoice->price);

    //define today as 7th Aug 2025 for testing
    // $today = Carbon::create(2025, 8, 10, 0, 0, 1, $tz);

    // define period cutoffs as Carbon in same timezone
    $cut10 = Carbon::create(2025, 8, 10, 0, 0, 0, $tz)->endOfDay();
    $cut12 = Carbon::create(2025, 8, 12, 0, 0, 0, $tz)->endOfDay();
    $cut16 = Carbon::create(2025, 8, 16, 0, 0, 0, $tz)->startOfDay();
    $cut26 = Carbon::create(2025, 8, 26, 0, 0, 0, $tz)->startOfDay();
    $cut15 = Carbon::create(2025, 8, 15, 0, 0, 0, $tz)->startOfDay();
    $cut25 = Carbon::create(2025, 8, 25, 0, 0, 0, $tz)->startOfDay();

    // echo "Today: {$today}, Order Date: {$orderDate}, Paid: " . ($isPaid ? 'Yes' : 'No') . "\n";
    // echo "<br>";

    // echo "Cutoffs: 10th Aug: {$cut10}, 12th Aug: {$cut12}, 16th Aug: {$cut16}, 25th Aug: {$cut25}, 26th Aug: {$cut26}\n";
    // echo "<br>";

    $surchargePct = 0;

    if ($today->gte($cut26)) {
        // From 26 Aug: 75% for new orders (>=26) OR unpaid orders placed on/before 25th Aug
        if ($orderDate->gte($cut26) || (!$isPaid && $orderDate->lte($cut25))) {
            $surchargePct = 75;
        }
    } elseif ($today->gte($cut16)) {
        // From 16 Aug to 25 Aug: 50% for new orders (>=16) OR unpaid orders placed on/before 15th Aug
        if ($orderDate->gte($cut16) || (!$isPaid && $orderDate->lte($cut15))) {
            $surchargePct = 50;
        }
    } elseif ($today->gte($cut10)) {
        // From 10 Aug to 15 Aug: 30% for new orders (>=10)
        if ($orderDate->gte($cut10)) {
            $surchargePct = 30;
        } else {
            // orders placed before 10th Aug, if unpaid after 12th Aug => 30%
            if (!$isPaid && $today->gt($cut12)) {
                $surchargePct = 30;
            }
        }
    } else {
        // Today is before 10 Aug
        // Orders placed before 10th Aug and paid by 12th Aug get 0% (handled by not setting surcharge)
        // If unpaid after 12th Aug (when $today > 12th) we will apply 30% (handled above once today passes 12th)
        $surchargePct = 0;
    }

    if($invoice->surChargeRemove == 1) {
        $surchargePct = 0; // if surChargeRemove is set, no surcharge
    }

    //if surChargeLock = 1 then surChargepercentage is locked and cannot be changed
    if($invoice->surChargeLock == 1) {
        $surchargePct = $invoice->surChargepercentage; // use locked percentage
    }

    // echo "Applying surcharge: $surchargePct% for invoice {$invoice->invoice_no} on date {$today->toDateString()}";
    // exit;

    $surchargeAmount = round(($baseAmount * $surchargePct) / 100);
    $amountAfter = round($baseAmount + $surchargeAmount);

    return [
        'amount_after_surcharge' => $amountAfter,
        'surcharge_amount' => $surchargeAmount,
        'surcharge_pct' => intval($surchargePct),
    ];
}

/**
 * Convert INR -> USD using external API, stores/returns USD amount & used rate.
 * Returns [usd_amount (float), used_rate (float)]
 */
private function convertInrToUsd(float $inrAmount): array
{
    $rateFile = storage_path('app/exchange_rate.json'); // persisted inside storage
    $apiUrl   = "https://v6.exchangerate-api.com/v6/303f4de10b784cbb27e4a065/latest/USD";

    $inrRate = null;
    $today   = now()->toDateString();

    // 1. Check if today's rate is already stored
    if (file_exists($rateFile)) {
        try {
            $stored = json_decode(file_get_contents($rateFile), true);
            if (!empty($stored['INR']) && !empty($stored['date']) && $stored['date'] === $today) {
                $inrRate = floatval($stored['INR']);
            }
        } catch (\Throwable $e) {
            Log::warning("Could not read rate file: " . $e->getMessage());
        }
    }

    // 2. If no rate for today, fetch from API
    if (!$inrRate) {
        try {
            $resp = Http::timeout(5)->get($apiUrl);
            if ($resp->ok()) {
                $data = $resp->json();
                if (isset($data['conversion_rates']['INR'])) {
                    $inrRate = floatval($data['conversion_rates']['INR']);
                    // Store with today's date
                    try {
                        file_put_contents($rateFile, json_encode([
                            'INR'  => $inrRate,
                            'date' => $today
                        ]));
                    } catch (\Throwable $e) {
                        Log::warning("Could not write rate file: " . $e->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Exchange API request failed: " . $e->getMessage());
        }
    }

    // 3. If still no rate, fallback to last stored rate (even if from older date)
    if (!$inrRate && file_exists($rateFile)) {
        try {
            $stored = json_decode(file_get_contents($rateFile), true);
            if (!empty($stored['INR'])) {
                $inrRate = floatval($stored['INR']);
            }
        } catch (\Throwable $e) {
            $inrRate = null;
        }
    }

    // 4. Absolute fallback
    if (!$inrRate) {
        $inrRate = 88.0;
    }

    // Convert INR to USD
    $usd = round($inrAmount / $inrRate, 2);

    return [$usd, $inrRate];
}


/**
 * Try to detect timezone for billing.
 * Priority: auth()->user()->timezone -> country iso_code -> config('app.timezone') -> UTC
 */
private function detectTimezoneForBilling($billingDetail): string
{
    if (auth()->check() && !empty(auth()->user()->timezone)) {
        return auth()->user()->timezone;
    }

    // If billingDetail has country_id use it
    $countryIso = null;
    if (!empty($billingDetail->country_id)) {
        $country = Country::find($billingDetail->country_id);
        if ($country) {
            // try common iso_code fields
            $countryIso = $country->iso_code ?? $country->iso ?? $country->code ?? null;
        }
    }

    if ($countryIso) {
        try {
            $zones = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, strtoupper($countryIso));
            if (!empty($zones)) {
                return $zones[0];
            }
        } catch (\Throwable $e) {
            // ignore and fallback
            //set default timezone to asia/Kolkata
            return 'Asia/Kolkata';
        }
    }

    return config('app.timezone', 'UTC');
}

/**
 * Format billing detail object from co-exhibitor
 */
private function formatBillingFromCoExhibitor($coExhibitor)
{
    return (object) [
        'billing_company' => $coExhibitor->co_exhibitor_name,
        'contact_name'    => $coExhibitor->contact_person,
        'email'           => $coExhibitor->email,
        'phone'           => $coExhibitor->phone,
        'address'         => $coExhibitor->address1,
        'country_id'      => $coExhibitor->country,
        'state_id'        => $coExhibitor->state,
        'postal_code'     => $coExhibitor->zip,
        'state'           => (object)['name' => $coExhibitor->state],
        'country'         => (object)[
            'name'   => $coExhibitor->country,
            'states' => optional(Country::with('states')->find($coExhibitor->country))->states ?? collect(),
        ],
        'gst'             => null,
        'pan_no'          => null,
        'city_id'         => $coExhibitor->city,
    ];
}

/**
 * Format billing detail object from requirements billing record
 */
private function formatBillingFromRequirements($billing)
{
    return (object) [
        'billing_company' => $billing->billing_company,
        'contact_name'    => $billing->billing_name,
        'email'           => $billing->billing_email,
        'phone'           => $billing->billing_phone,
        'address'         => $billing->billing_address,
        'country_id'      => $billing->country_id,
        'state_id'        => $billing->state_id,
        'postal_code'     => $billing->zipcode,
        'state'           => (object)['name' => optional(State::find($billing->state_id))->name],
        'country'         => (object)[
            'name'   => optional(Country::find($billing->country_id))->name,
            'states' => optional(Country::with('states')->find($billing->country_id))->states ?? collect(),
        ],
        'gst'             => $billing->gst_no ?? null,
        'pan_no'          => $billing->pan_no ?? null,
        'city_id'         => $billing->billing_city ?? null,
    ];
}

/**
 * Format billing detail object from EventContact for startup zone
 */
private function formatBillingFromEventContact($eventContact, $applicationId)
{
    $application = Application::find($applicationId);
    if (!$application) {
        return null;
    }

    // Build contact name properly (trim extra spaces)
    $contactName = trim(($eventContact->salutation ?? '') . ' ' . ($eventContact->first_name ?? '') . ' ' . ($eventContact->last_name ?? ''));
    if (empty($contactName)) {
        $contactName = $application->company_name ?? '';
    }

    // Get phone number and strip country code if present (format: 91-9801217815 -> 9801217815)
    $phone = $eventContact->contact_number ?? $application->landline ?? '';
    $phone = preg_replace('/^.*-/', '', $phone); // Remove country code prefix

    // Get city name from city_id
    $cityName = '';
    if ($application->city_id) {
        $city = \DB::table('cities')->where('id', $application->city_id)->first();
        $cityName = $city->name ?? '';
    }

    // Get state and country names
    $stateName = '';
    if ($application->state_id) {
        $state = State::find($application->state_id);
        $stateName = $state->name ?? '';
    }

    $countryName = '';
    if ($application->country_id) {
        $country = Country::find($application->country_id);
        $countryName = $country->name ?? '';
    }

    return (object) [
        'billing_company' => $application->company_name ?? '',
        'contact_name'    => $contactName,
        'email'           => $eventContact->email ?? $application->company_email ?? '',
        'phone'           => $phone,
        'address'         => $application->address ?? '',
        'country_id'      => $application->country_id ?? null,
        'state_id'        => $application->state_id ?? null,
        'postal_code'     => $application->postal_code ?? '',
        'state'           => (object)['name' => $stateName],
        'country'         => (object)[
            'name'   => $countryName,
            'states' => optional(Country::with('states')->find($application->country_id))->states ?? collect(),
        ],
        'gst'             => $application->gst_no ?? null,
        'pan_no'          => $application->pan_no ?? null,
        'city_id'         => $application->city_id ?? null,
        'city_name'       => $cityName,
    ];
}




    // Step 1: Show Payment Form
    public function showPaymentForm3($id)
    {
        //if not id then redirect to route exhibitor.orders
        if (!$id) {
            return redirect()->route('exhibitor.orders');
        }

        // get the invoice details from the Invoice model where invoice_no = $id
        $invoice = Invoice::where('invoice_no', $id)->first();



        //if invoice not found then redirect to route exhibitor.orders
        if (!$invoice) {
            return redirect()->route('exhibitor.orders');
        }

        if ($invoice->type != 'extra_requirement') {
            return response()->json(['error' => 'Invalid invoice type'], 400);
        }

        //if invoice is already paid then redirect to route exhibitor.orders
        // if ($invoice->payment_status == 'paid') {
        //     return redirect()->route('exhibitor.orders');
        // }

        //fetch the BillingDetail details from the model BillingDetail where application_id = $invoice->application_id
        $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();


        //if invoice->co_exhibitorID 
        if ($invoice->co_exhibitorID) {
            //if co_exhibitor_id is not null then get the co-exhibitor details from the CoExhibitor model
            $coExhibitor = \App\Models\CoExhibitor::where('id', $invoice->co_exhibitorID)->first();
            //if coExhibitor is not null then get the billing details from the coExhibitor
            if ($coExhibitor) {
                $billingDetail = (object) [
                    'billing_company' => $coExhibitor->co_exhibitor_name,
                    'contact_name' => $coExhibitor->contact_person,
                    'email' => $coExhibitor->email,
                    'phone' => $coExhibitor->phone,
                    'address' => $coExhibitor->address1,
                    'country_id' => $coExhibitor->country,
                    'state_id' => $coExhibitor->state,
                    'postal_code' => $coExhibitor->zip,
                    // Add a dummy state object to mimic $billingDetail->state->name
                    // Add a dummy state object to mimic $billingDetail->state->name
                    'state' => (object)[
                        'name' => $coExhibitor->state
                    ],
                    // Add a dummy country object to mimic $billingDetail->country->name
                    'country' => (object)[
                        'name' => $coExhibitor->country,
                        'states' => ($coExhibitor->country)
                            ? optional(\App\Models\Country::with('states')->find($coExhibitor->country))->states ?? collect()
                            : collect(),
                    ],
                    'gst' => null, // Assuming co-exhibitors do not have GST
                    'pan_no' => null, // Assuming co-exhibitors do not have PAN
                    'city_id' => $coExhibitor->city, // Assuming co-exhibitors do not have city_id
                ];
            }
        }

        //if the requirements_billings table  has same invoice_id frominvoice->id then get the billing details then fetch that details
        $requirementsBilling = \DB::table('requirements_billings')
            ->where('invoice_id', $invoice->id)
            ->first();

        //if billingDetail 
        if ($requirementsBilling) {
            $billingDetail = (object) [
                'billing_company' => $requirementsBilling->billing_company,
                'contact_name' => $requirementsBilling->billing_name,
                'email' => $requirementsBilling->billing_email,
                'phone' => $requirementsBilling->billing_phone,
                'address' => $requirementsBilling->billing_address,
                'country_id' => $requirementsBilling->country_id,
                'state_id' => $requirementsBilling->state_id,
                'postal_code' => $requirementsBilling->zipcode,
                // Add a dummy state object to mimic $billingDetail->state->name
                'state' => (object)[
                    'name' => optional(\App\Models\State::find($requirementsBilling->state_id))->name
                ],
                // Add a dummy country object to mimic $billingDetail->country->name
                'country' => (object)[
                    'name' => optional(\App\Models\Country::find($requirementsBilling->country_id))->name,
                    'states' => ($requirementsBilling->country_id)
                        ? optional(\App\Models\Country::with('states')->find($requirementsBilling->country_id))->states ?? collect()
                        : collect(),
                ],
                'gst' => $requirementsBilling->gst_no ?? null,
                'pan_no' => $requirementsBilling->pan_no ?? null,
                'city_id' => $requirementsBilling->billing_city ?? null,

            ];
        }

        //if billingDetail->country->name != 'India'
        //then call 
        if ($billingDetail->country->name != 'India') {



            $final_total_price = $invoice->amount; // Use the invoice amount directly
            // Path to store the last successful exchange rate
            $rate_file = "exchange_rate.json";

            // Function to get the last stored rate
            function get_last_stored_rate($rate_file)
            {
                // if (file_exists($rate_file)) {
                //     $stored_data = json_decode(file_get_contents($rate_file), true);
                //     if (isset($stored_data["INR"])) {
                //         return $stored_data["INR"];
                //     }
                // }
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

            //update invoice total_final_price to final_total_price_usd
            $invoice->int_amount_value = $final_total_price_usd;
            $invoice->usd_rate = $inr_to_usd_rate; // Store the exchange rate used for conversion
            $invoice->currency = 'USD'; // Update the currency to USD
            $invoice->save(); // Save the updated invoice with the new amount in USD
        }




        //find the orderItems from the 
        $orders = RequirementsOrder::where('invoice_id', $invoice->id)
            ->with(['invoice', 'orderItems.requirement'])
            ->orderBy('created_at', 'desc')
            ->get();

        //pass counties to the view from country model id, name
        $countries = Country::all(['id', 'name']);




        //if invoice is not paid then show the payment form
        return view('paypal.payment-form', compact('invoice', 'billingDetail', 'orders', 'countries'));
    }
    public function showPaymentForm2($id)
    {
        //if not id then redirect to route exhibitor.orders
        if (!$id) {
            return redirect()->route('exhibitor.orders');
        }

        // get the invoice details from the Invoice model where invoice_no = $id
        $invoice = Invoice::where('invoice_no', $id)->first();



        //if invoice not found then redirect to route exhibitor.orders
        if (!$invoice) {
            return redirect()->route('exhibitor.orders');
        }

        if ($invoice->type != 'extra_requirement') {
            return response()->json(['error' => 'Invalid invoice type'], 400);
        }

        //if invoice is already paid then redirect to route exhibitor.orders
        // if ($invoice->payment_status == 'paid') {
        //     return redirect()->route('exhibitor.orders');
        // }

        //fetch the BillingDetail details from the model BillingDetail where application_id = $invoice->application_id
        $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();


        //if invoice->co_exhibitorID 
        if ($invoice->co_exhibitorID) {
            //if co_exhibitor_id is not null then get the co-exhibitor details from the CoExhibitor model
            $coExhibitor = \App\Models\CoExhibitor::where('id', $invoice->co_exhibitorID)->first();
            //if coExhibitor is not null then get the billing details from the coExhibitor
            if ($coExhibitor) {
                $billingDetail = (object) [
                    'billing_company' => $coExhibitor->co_exhibitor_name,
                    'contact_name' => $coExhibitor->contact_person,
                    'email' => $coExhibitor->email,
                    'phone' => $coExhibitor->phone,
                    'address' => $coExhibitor->address1,
                    'country_id' => $coExhibitor->country,
                    'state_id' => $coExhibitor->state,
                    'postal_code' => $coExhibitor->zip,
                    // Add a dummy state object to mimic $billingDetail->state->name
                    // Add a dummy state object to mimic $billingDetail->state->name
                    'state' => (object)[
                        'name' => $coExhibitor->state
                    ],
                    // Add a dummy country object to mimic $billingDetail->country->name
                    'country' => (object)[
                        'name' => $coExhibitor->country,
                        'states' => ($coExhibitor->country)
                            ? optional(\App\Models\Country::with('states')->find($coExhibitor->country))->states ?? collect()
                            : collect(),
                    ],
                    'gst' => null, // Assuming co-exhibitors do not have GST
                    'pan_no' => null, // Assuming co-exhibitors do not have PAN
                    'city_id' => $coExhibitor->city, // Assuming co-exhibitors do not have city_id
                ];
            }
        }

        //if the requirements_billings table  has same invoice_id frominvoice->id then get the billing details then fetch that details
        $requirementsBilling = \DB::table('requirements_billings')
            ->where('invoice_id', $invoice->id)
            ->first();

        //if billingDetail 
        if ($requirementsBilling) {
            $billingDetail = (object) [
                'billing_company' => $requirementsBilling->billing_company,
                'contact_name' => $requirementsBilling->billing_name,
                'email' => $requirementsBilling->billing_email,
                'phone' => $requirementsBilling->billing_phone,
                'address' => $requirementsBilling->billing_address,
                'country_id' => $requirementsBilling->country_id,
                'state_id' => $requirementsBilling->state_id,
                'postal_code' => $requirementsBilling->zipcode,
                // Add a dummy state object to mimic $billingDetail->state->name
                'state' => (object)[
                    'name' => optional(\App\Models\State::find($requirementsBilling->state_id))->name
                ],
                // Add a dummy country object to mimic $billingDetail->country->name
                'country' => (object)[
                    'name' => optional(\App\Models\Country::find($requirementsBilling->country_id))->name,
                    'states' => ($requirementsBilling->country_id)
                        ? optional(\App\Models\Country::with('states')->find($requirementsBilling->country_id))->states ?? collect()
                        : collect(),
                ],
                'gst' => $requirementsBilling->gst_no ?? null,
                'pan_no' => $requirementsBilling->pan_no ?? null,
                'city_id' => $requirementsBilling->billing_city ?? null,

            ];
        }

        //if billingDetail->country->name != 'India'
        //then call 
        if ($billingDetail->country->name != 'India') {



            $final_total_price = $invoice->amount; // Use the invoice amount directly
            // Path to store the last successful exchange rate
            $rate_file = "exchange_rate.json";

            // Function to get the last stored rate
            function get_last_stored_rate($rate_file)
            {
                // if (file_exists($rate_file)) {
                //     $stored_data = json_decode(file_get_contents($rate_file), true);
                //     if (isset($stored_data["INR"])) {
                //         return $stored_data["INR"];
                //     }
                // }
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

            //update invoice total_final_price to final_total_price_usd
            $invoice->int_amount_value = $final_total_price_usd;
            $invoice->usd_rate = $inr_to_usd_rate; // Store the exchange rate used for conversion
            $invoice->currency = 'USD'; // Update the currency to USD
            $invoice->save(); // Save the updated invoice with the new amount in USD
        }




        //find the orderItems from the 
        $orders = RequirementsOrder::where('invoice_id', $invoice->id)
            ->with(['invoice', 'orderItems.requirement'])
            ->orderBy('created_at', 'desc')
            ->get();

        //pass counties to the view from country model id, name
        $countries = Country::all(['id', 'name']);




        //if invoice is not paid then show the payment form
        return view('paypal.payment-form2', compact('invoice', 'billingDetail', 'orders', 'countries'));
    }



    private $client;

    public function __construct()
    {
        $this->client = PaypalServerSDKClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init(
                    "Af98MdWNTOZO-rKE9MdjRJE50vr3Rp9DOYfr3TwidA9kzexdt2NGYAfXP9DfjK_5PTmTzxsxtoufZCyT",
                    "EPdptPZ_JJ5vFhlO4Cf4dJH9m6RIS7exO7xbGgy68pjGE42y2Cv2txd6Sh8g3l775b28SVX6gb7arBoQ"
                )
            )
            ->environment("Production")

            ->build();
    }

    public function checkoutForm()
    {
        return view('paypal.payment-form');
    }

    public function createOrder(Request $request)
    {

        Log::info($request->all());
        // Validate the request
        $request->validate([
            'invoice' => 'required|string|exists:invoices,invoice_no',
        ]);

        // Check if the invoice number exists in the Invoice model
        $invoice = Invoice::where('invoice_no', $request->invoice)->first();

        Log::info('Invoice: ' . json_encode($invoice));
        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        //if invoice->currency is not USD then return error
        // if ($invoice->currency != 'USD') {
        //     return response()->json(['error' => 'Invalid currency'], 400);
        // }





        // Check if this is a startup zone invoice
        $isStartupZone = false;
        $application = null;
        if ($invoice->application_id) {
            $application = \App\Models\Application::find($invoice->application_id);
            if ($application && $application->application_type === 'startup-zone') {
                $isStartupZone = true;
            }
        }

        //if invoice  type not extra_requirement and not startup-zone then return error
        if (!$isStartupZone && $invoice->type != 'extra_requirement') {
            return response()->json(['error' => 'Invalid invoice type'], 400);
        }
        
        // For startup-zone, check if application is approved - payment only allowed after approval
        if ($isStartupZone && $application) {
            if ($application->submission_status !== 'approved') {
                return response()->json(['error' => 'Your profile is not approved yet for payment. Please wait for admin approval.'], 403);
            }
        }

        Log::info('Invoice Type: ' . $invoice->type);
        Log::info('Is Startup Zone: ' . ($isStartupZone ? 'Yes' : 'No'));

        // Fetch billing detail - handle startup zone differently
        $billingDetail = null;
        
        if ($isStartupZone) {
            // For startup zone, get billing from EventContact
            $eventContact = \App\Models\EventContact::where('application_id', $invoice->application_id)->first();
            if ($eventContact && $application) {
                $billingDetail = $this->formatBillingFromEventContact($eventContact, $invoice->application_id);
            }
        } else {
            // For other types, use BillingDetail
            $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();
        }
        Log::info('Billing Detail: ' . json_encode($billingDetail));
        if (!$billingDetail) {
            return response()->json(['error' => 'Billing details not found'], 404);
        }

        $order_ID = $invoice->invoice_no . '_' . substr(uniqid(), -5);

        Log::info('Order ID: ' . $order_ID);
        $order = $order_ID;
        $amount = $invoice->int_amount_value ?? $invoice->amount;

        $email = $billingDetail->email;
        $company = $billingDetail->billing_company ?? ($application ? $application->company_name : '');

        // Only check co-exhibitor for non-startup-zone invoices
        if (!$isStartupZone && $invoice->co_exhibitorID) {
            //if co_exhibitor_id is not null then get the co-exhibitor details from the CoExhibitor model
            $coExhibitor = \App\Models\CoExhibitor::where('id', $invoice->co_exhibitorID)->first();
            //if coExhibitor is not null then get the billing details from the coExhibitor
            if ($coExhibitor) {
                $billingDetail = (object) [
                    'billing_company' => $coExhibitor->co_exhibitor_name,
                    'contact_name' => $coExhibitor->contact_person,
                    'email' => $coExhibitor->email,
                    'phone' => $coExhibitor->phone,
                    'address' => $coExhibitor->address1,
                    'country_id' => $coExhibitor->country,
                    'state_id' => $coExhibitor->state,
                    'postal_code' => $coExhibitor->zip,
                    // Add a dummy state object to mimic $billingDetail->state->name
                    // Add a dummy state object to mimic $billingDetail->state->name
                    'state' => (object)[
                        'name' => $coExhibitor->state
                    ],
                    // Add a dummy country object to mimic $billingDetail->country->name
                    'country' => (object)[
                        'name' => $coExhibitor->country,
                        'states' => ($coExhibitor->country)
                            ? optional(\App\Models\Country::with('states')->find($coExhibitor->country))->states ?? collect()
                            : collect(),
                    ],
                    'gst' => null, // Assuming co-exhibitors do not have GST
                    'pan_no' => null, // Assuming co-exhibitors do not have PAN
                    'city_id' => $coExhibitor->city, // Assuming co-exhibitors do not have city_id
                ];
            }
        }


        $requirementsBilling = \DB::table('requirements_billings')
            ->where('invoice_id', $invoice->id)
            ->first();

        //if billingDetail 
        if ($requirementsBilling) {
            $billingDetail = (object) [
                'billing_company' => $requirementsBilling->billing_company,
                'contact_name' => $requirementsBilling->billing_name,
                'email' => $requirementsBilling->billing_email,
                'phone' => $requirementsBilling->billing_phone,
                'address' => $requirementsBilling->billing_address,
                'country_id' => $requirementsBilling->country_id,
                'state_id' => $requirementsBilling->state_id,
                'postal_code' => $requirementsBilling->zipcode,
                // Add a dummy state object to mimic $billingDetail->state->name
                'state' => (object)[
                    'name' => optional(\App\Models\State::find($requirementsBilling->state_id))->name
                ],
                // Add a dummy country object to mimic $billingDetail->country->name
                'country' => (object)[
                    'name' => optional(\App\Models\Country::find($requirementsBilling->country_id))->name,
                    'states' => ($requirementsBilling->country_id)
                        ? optional(\App\Models\Country::with('states')->find($requirementsBilling->country_id))->states ?? collect()
                        : collect(),
                ],
                'gst' => $requirementsBilling->gst_no ?? null,
                'pan_no' => $requirementsBilling->pan_no ?? null,
                'city_id' => $requirementsBilling->billing_city ?? null,

            ];
        }



        if (!$billingDetail) {
            return response()->json(['error' => 'Billing details not found'], 404);
        }

        Log::info('Billing Detail: ' . json_encode($billingDetail));
        
        // Determine amount - for startup zone use int_amount_value, for others use int_amount_value
        $amount = $invoice->int_amount_value ?? $invoice->amount;
        $email = $billingDetail->email;
        $company = $billingDetail->billing_company ?? ($application ? $application->company_name : '');
        
        $purchaseUnit = PurchaseUnitRequestBuilder::init(
            AmountWithBreakdownBuilder::init('USD', $amount)->build()
        )
            ->description($isStartupZone ? 'Startup Zone Registration for ' . $company : 'Extra Requirements for ' . $company)   // Optional description
            ->invoiceId($order_ID)            // PayPal invoice tracking
            ->build();


        $orderBody = [
            'body' => OrderRequestBuilder::init(
                CheckoutPaymentIntent::CAPTURE,
                [$purchaseUnit]
            )->build()
        ];

        try {
            $apiResponse = $this->client->getOrdersController()->createOrder($orderBody);
            //get the id from the response
            $order_ID = $apiResponse->getResult()->getId();
            //insert into payment response table
            $data = [
                'merchant_id' => null,
                'payment_id' => $order_ID,
                'order_id' => $order,
                'currency' => 'USD',
                'amount' => $invoice->int_amount_value,
                'redirect_url' => null,
                'cancel_url' => null,
                'language' => 'EN',
                'billing_name' => $billingDetail->contact_name,
                'billing_address' => $billingDetail->address,
                'billing_city' => $billingDetail->city_id,
                'billing_state' => $billingDetail->state->name,
                'billing_zip' => $billingDetail->postal_code,
                'billing_country' => $billingDetail->country->name,
                'billing_tel' => $billingDetail->phone,
                'billing_email' => $billingDetail->email,
            ];
            Log::info('Data to be inserted: ' . json_encode($data));
            $merchantData = json_encode($data);

            \DB::table('payment_gateway_response')->insert([
                'merchant_data' => $merchantData,
                'order_id' => $data['order_id'],
                'payment_id' => $data['payment_id'],
                'amount' => $data['amount'],
                'status' => 'Pending',
                'gateway' => 'Paypal',
                'currency' => 'USD',
                'email' => $data['billing_email'],
                'created_at' => now(),
            ]);

            // Note: Payment record will be created only after payment is completed
            // (in captureOrder method when we have transaction_id and payment status)

            return response()->json($apiResponse->getResult());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function captureOrder($orderId)
    {
        try {
            $captureBody = ['id' => $orderId];
            $apiResponse = $this->client->getOrdersController()->captureOrder($captureBody);
            //store the response in the payment_gateway_response table with insert 
            \DB::table('payment_gateway_response')
                ->where('payment_id', $orderId)
                ->update([
                    'status' => 'Completed',
                    'response_json' => json_encode($apiResponse->getResult()),
                    'updated_at' => now(),
                ]);

            $apiEncodedResponse = json_encode($apiResponse->getResult());
            //decode the response
            //get the amount from the response
            $apiDecodedResponse = json_decode($apiEncodedResponse, true);
            $amountPaid = $apiDecodedResponse['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            //get the order_id from the response
            $pg_status = $apiDecodedResponse['status'];
            //if status is completed then mark it paid else mark it as failed
            if ($pg_status == 'COMPLETED') {
                $conf_status = 'paid';
            } else {
                $conf_status = 'failed';
            }



            //store the amount from the above json encode value

            $orderData = \DB::table('payment_gateway_response')
                ->where('payment_id', $orderId)
                ->select('order_id')
                ->first();

            // explode the orderData after _ and get the values 
            $orderIDParts = explode('_', $orderData->order_id);
            $invoiceNo = $orderIDParts[0]; // Get first part (invoice_no)
            $invoice = Invoice::where('invoice_no', $invoiceNo)->first();
            
            if (!$invoice) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }
            
            // Check if this is a startup zone invoice
            $isStartupZone = false;
            $application = null;
            if ($invoice->application_id) {
                $application = \App\Models\Application::find($invoice->application_id);
                if ($application && $application->application_type === 'startup-zone') {
                    $isStartupZone = true;
                }
            }



            //update the invoice table with the status as paid
            if ($conf_status == 'paid') {
                // Generate PIN number if not already set
                if (!$invoice->pin_no) {
                    $pinNo = $this->generatePinNo();
                    $invoice->pin_no = $pinNo;
                }
                
                $invoice->update([
                    'payment_status' => $conf_status,
                    'amount_paid' => $amountPaid,
                    'updated_at' => now(),
                    'pending_amount' => 0,
                    'currency' => 'USD',
                ]);
                
                // Check if this is a startup zone invoice
                $isStartupZone = false;
                $application = null;
                if ($invoice->application_id) {
                    $application = \App\Models\Application::find($invoice->application_id);
                    if ($application && $application->application_type === 'startup-zone') {
                        $isStartupZone = true;
                    }
                }
                
                if ($isStartupZone && $application) {
                    // Check if payment record already exists (from previous attempt)
                    $payment = Payment::where('order_id', $orderData->order_id)
                        ->where('invoice_id', $invoice->id)
                        ->first();
                    
                    if ($payment) {
                        // Update existing payment record
                        $payment->update([
                            'payment_method' => 'PayPal',
                            'amount' => $amountPaid,
                            'amount_paid' => $amountPaid,
                            'amount_received' => $amountPaid,
                            'transaction_id' => $apiDecodedResponse['purchase_units'][0]['payments']['captures'][0]['id'] ?? null,
                            'pg_result' => $pg_status,
                            'track_id' => $orderId,
                            'pg_response_json' => json_encode($apiDecodedResponse),
                            'payment_date' => now(),
                            'status' => 'successful',
                        ]);
                    } else {
                        // Create new payment record (payment records are only created after payment completion)
                        Payment::create([
                            'invoice_id' => $invoice->id,
                            'payment_method' => 'PayPal',
                            'amount' => $amountPaid,
                            'amount_paid' => $amountPaid,
                            'amount_received' => $amountPaid,
                            'transaction_id' => $apiDecodedResponse['purchase_units'][0]['payments']['captures'][0]['id'] ?? null,
                            'pg_result' => $pg_status,
                            'track_id' => $orderId,
                            'pg_response_json' => json_encode($apiDecodedResponse),
                            'payment_date' => now(),
                            'currency' => 'USD',
                            'status' => 'successful',
                            'order_id' => $orderData->order_id,
                            'user_id' => $application->user_id ?? null,
                        ]);
                    }
                    
                    Log::info('Startup Zone PayPal Payment Captured', [
                        'application_id' => $application->application_id,
                        'invoice_no' => $invoice->invoice_no,
                        'amount' => $amountPaid
                    ]);
                    
                    // Send thank you email after payment confirmation
                    try {
                        $contact = \App\Models\EventContact::where('application_id', $application->id)->first();
                        $application->load(['country', 'state', 'eventContact']);
                        
                        $userEmail = $contact && $contact->email ? $contact->email : $application->company_email;
                        
                        if ($userEmail) {
                            $paymentDetails = [
                                'transaction_id' => $apiDecodedResponse['purchase_units'][0]['payments']['captures'][0]['id'] ?? null,
                                'payment_method' => 'PayPal',
                                'amount' => $amountPaid,
                                'currency' => 'USD',
                            ];
                            
                            Mail::to($userEmail)->send(new \App\Mail\StartupZoneMail($application, 'payment_thank_you', $invoice, $contact, $paymentDetails));
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to send payment thank you email', [
                            'application_id' => $application->application_id,
                            'email' => $userEmail ?? 'unknown',
                            'error' => $e->getMessage()
                        ]);
                        // Don't fail the payment if email fails
                    }
                    
                    
                    // Return JSON with redirect URL for startup zone
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Payment successful',
                        'redirect' => route('startup-zone.confirmation', $application->application_id)
                    ]);
                } else {
                    // For other invoice types, send extra requirements mail
                    $service = new ExtraRequirementsMailService();
                    $data = $service->prepareMailData($orderID[0]);
                    $email = $data['billingEmail'];

                    Mail::to($email)
                        
                        ->bcc(['test.interlinks@gmail.com'])
                        ->queue(new ExtraRequirementsMail($data));
                }
            }





            //find the invoice from the payment_gateway_response table where payment_id = $orderId

            // For startup zone, return JSON with redirect URL (already handled above)
            // For other types, return the API response
            return response()->json($apiResponse->getResult());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    //webhoook insert into payment_gateway_response table
    public function webhook(Request $request)
    {
        Log::info('Paypal Webhook');
        $data = $request->all();
        Log::info('data ' . $data);
        try {
            \DB::table('payment_gateway_response')->insert([
                'merchant_data' => json_encode($data),
                'order_id' => $data['resource']['supplementary_data']['related_ids']['order_id'] ?? 'test',
                'amount' => $data['resource']['amount']['value'] ?? '0.00',
                'status' => $data['resource']['status'] ?? 'test',
                'gateway' => 'Paypal',
                'currency' => $data['resource']['amount']['currency_code'] ?? 'USD',
                'email' => $data['resource']['payer']['email_address'] ?? 'unknown@example.com',
                'response_json' => json_encode($data),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error inserting payment gateway response: ' . $e->getMessage());
        }
    }
    
    /**
     * Handle PayPal success redirect
     */
    public function success(Request $request)
    {
        $token = $request->query('token');
        
        if ($token) {
            // Find invoice by token/order_id
            $paymentResponse = \DB::table('payment_gateway_response')
                ->where('payment_id', $token)
                ->orWhere('order_id', 'like', '%' . $token . '%')
                ->first();
            
            if ($paymentResponse) {
                // Extract invoice_no from order_id
                $orderIdParts = explode('_', $paymentResponse->order_id);
                $invoiceNo = $orderIdParts[0];
                
                $invoice = Invoice::where('invoice_no', $invoiceNo)->first();
                
                if ($invoice && $invoice->application_id) {
                    $application = Application::find($invoice->application_id);
                    
                    // Check if this is a startup zone invoice
                    if ($application && $application->application_type === 'startup-zone') {
                        return redirect()->route('startup-zone.confirmation', $application->application_id)
                            ->with('success', 'Payment successful!');
                    }
                }
            }
        }
        
        // Default redirect for non-startup-zone
        return redirect()->route('exhibitor.orders')
            ->with('success', 'Payment successful!');
    }
    
    /**
     * Handle PayPal cancel redirect
     */
    public function cancel(Request $request)
    {
        $token = $request->query('token');
        
        if ($token) {
            // Find invoice by token/order_id
            $paymentResponse = \DB::table('payment_gateway_response')
                ->where('payment_id', $token)
                ->orWhere('order_id', 'like', '%' . $token . '%')
                ->first();
            
            if ($paymentResponse) {
                // Extract invoice_no from order_id
                $orderIdParts = explode('_', $paymentResponse->order_id);
                $invoiceNo = $orderIdParts[0];
                
                $invoice = Invoice::where('invoice_no', $invoiceNo)->first();
                
                if ($invoice && $invoice->application_id) {
                    $application = Application::find($invoice->application_id);
                    
                    // Check if this is a startup zone invoice
                    if ($application && $application->application_type === 'startup-zone') {
                        return redirect()->route('startup-zone.payment', $application->application_id)
                            ->with('error', 'Payment was cancelled. Please try again.');
                    }
                }
            }
        }
        
        // Default redirect for non-startup-zone
        return redirect()->route('exhibitor.orders')
            ->with('error', 'Payment was cancelled.');
    }

    /**
     * Generate unique PIN number using PIN_NO_PREFIX
     * Format: PRN-BTS-2026-EXHP-XXXXXX (6-digit random number)
     */
    private function generatePinNo()
    {
        $prefix = config('constants.PIN_NO_PREFIX');
        $maxAttempts = 100; // Prevent infinite loop
        $attempts = 0;
        
        while ($attempts < $maxAttempts) {
            // Generate 6-digit random number
            $randomNumber = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $pinNo = $prefix . $randomNumber;
            $attempts++;
            
            // Check if it already exists in invoices table
            if (!Invoice::where('pin_no', $pinNo)->exists()) {
                return $pinNo;
            }
        }
        
        // If we've tried too many times, use timestamp-based fallback
        $timestamp = substr(time(), -6); // Last 6 digits of timestamp
        $pinNo = $prefix . $timestamp;
        if (!Invoice::where('pin_no', $pinNo)->exists()) {
            return $pinNo;
        }
        
        // Last resort: use microtime
        $microtime = substr(str_replace('.', '', microtime(true)), -6);
        return $prefix . $microtime;
    }
}
