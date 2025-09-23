<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use mysqli;
use DB;
use App\Models\User;
//use application model
use App\Models\Application;
use Illuminate\Support\Facades\Hash;
use App\Models\EventContact;
use App\Models\BillingDetail;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Sector;


class ImportData extends Controller
{

    //create a db connection function
    public function dbConnection()
    {
        // Database connection logic here
        $host = "95.216.2.164";
        $username = "btsblnl265_asd1d_bengaluruite";
        $password = "Disl#vhfj#Af#DhW65";
        $database = "btsblnl265_asd1d_bengaluruite";

        $connection = new mysqli($host, $username, $password, $database);
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }
        return $connection;
    }

    //
    public function importUsers(Request $request)
    {


        // run a query to fetch data from the remote database
        $connection = $this->dbConnection();

        //select * from the it_2025_exhibitors_dir_payment_tbl where payment_status is 'Paid'
        $query = "SELECT * FROM it_2025_exhibitors_dir_payment_tbl";
        $result = $connection->query
        ($query);
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }



        $connection->close();

        //create a user for each row in the data array
        foreach ($data as $row) {

            $name = $row['cp_title']. ' '. $row['cp_fname'] . ' ' . $row['cp_lname'];
            $email = $row['cp_email'];
            $mobile = $row['cp_mobile'];
            //get the country code out of the mobile number 91-9573600744
            $countryCode = explode('-', $mobile)[0];
            $mobile = explode('-', $mobile)[1];
            //remove the country code from the mobile number
            //check if user already exists
            //clean the company name from &amp; to &
            $row['exhibitor_name'] = str_replace('&amp;', '&', $row['exhibitor_name']);
            //clean Chadha &amp;amp;amp; Chadha IP Law firm to Chadha & Chadha IP Law firm
            $row['exhibitor_name'] = str_replace('amp;', '&', $row['exhibitor_name']);
            //clean website from &amp; to &
            $row['website'] = str_replace('&amp;', '&', $row['website']);
            //clean   from the company name
            $row['exhibitor_name'] = str_replace(' ', ' ', $row['exhibitor_name']);

            //clean addr1 from &amp; to &
            $row['addr1'] = str_replace('&amp;', '&', $row['addr1']);
            //clean cp_desig from &amp; to &
            $row['cp_desig'] = str_replace('&amp;', '&', $row['cp_desig']);

            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                //skip that row and continue
                continue;
            }

            else {


                $user = new User();
                //name as cp_fname + cp_lname
                $row ['contact_person'] = $row['cp_fname'] . ' ' . $row['cp_lname'];

                $user->name = $row['contact_person'];
                $user->email = $email;
                //generate a random password
                $randomPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

                $user->password = Hash::make($randomPassword);
                $user->simplePass = $randomPassword;
                $user->role = 'exhibitor';
                $user->phone = $mobile;
                $user->email_verified_at = now();
                $user->save();

                //create an application for the user
                $application = new Application();
                $application->user_id = $user->id;
                $application->company_name = $row['exhibitor_name'];
                //if website does not start with http then add http:// to the website
                if (!str_starts_with($row['website'], 'http')) {
                    $row['website'] = 'https://' . $row['website'];
                }
                $application->website = $row['website'];
                $application->address = $row['addr1'];
                $application->city_id = $row['city'];
                $application->companyYears = !empty($row['company_years']) ? $row['company_years'] : null;
                $application->certificate = $row['ci_certf'];

                //get the sector id from the sectors table where name = sector
                $sector = Sector::where('name', $row['sector'])->first();
                if ($sector) {
                    $application->sector_id = json_encode($sector->id);
                }
//                $application->sector_id = $row['sector'];
                $application->subSector = $row['subsector'];
                $application->event_id = 1;

                //if booth_area contains Shell then stall_type is Shell
                // if booth_area contains Raw then stall_type is Raw
                // if Booth / POD then stall_type is Startup
                if (str_contains($row['booth_area'], 'Shell')) {
                    $application->stall_category = 'Shell Scheme';
                } elseif (str_contains($row['booth_area'], 'Raw')) {
                    $application->stall_category = 'Raw Space';
                } else {
                    $application->stall_category = 'Startup Booth';
                }
                $application->exhibitorType = $row['promocode'];

                $application->application_id = $row['tin_no'];

                // interested_sqm as Booth / POD value
                $application->interested_sqm = $row['booth_size'];
                $application->allocated_sqm = $row['booth_size'];



                //find the state name from the state code
                $state = strtolower($row['state']);
                $stateName = DB::table('states')->where('name', $row['state'])->value('id');
                $row['state'] = $stateName ? $stateName : $row['state'];

                $application->state_id = $row['state'];
                //country id from countries table
                $country = strtolower($row['country']);
                $countryId = DB::table('countries')->where('name', $row['country'])->value('id');
                $row['country'] = $countryId ? $countryId : $row['country'];

                //dd($countryId, $stateName, $row['country'], $row['state']);
                $application->country_id = $row['country'];
                $application->headquarters_country_id = $row['country'];
                $application->postal_code = $row['zip'];
                $application->gst_no = $row['gst_number'];
                $application->pan_no = $row['pan_number'];
                $application->company_email = $email;

                //if gst_number is not null then gst_compliance = 1
                if ($row['gst_number'] != null) {
                    $application->gst_compliance = 1;
                } else {
                    $application->gst_compliance = 0;
                }
                //set application status to approved
                // if row approval_status Approved then status is approved else pending
                if ($row['approval_status'] == 'Approved') {
                    $application->submission_status = 'approved';
                    //get the approved date from the reg_date field
                    $application->approved_date = $row['reg_date'];
                } else {
                    $application->submission_status = 'pending';
                }

                // if pay_status is paid then submission_status is approved
                if ($row['pay_status'] == 'Paid') {
                    $application->submission_status = 'approved';
                    //get the approved date from the reg_date field
                    $application->approved_date = $row['reg_date'];
                }
                //assoc_mem from user_type
                $application->assoc_mem = $row['user_type'];

                //
                $application->boothDescription = $row['booth_area'];

                $application->participation_type = 'Onsite';
                //if country is india then region is india else international

                $application->region = $countryId == 101 ? 'India' : 'International';

                $application->approved_by = $row['approved_by'];
                $application->save();

                //create a billing detail for the user
                $billingDetail = new BillingDetail();
                $billingDetail->application_id = $application->id;
                $billingDetail->billing_company = $row['exhibitor_name'];
                $billingDetail->contact_name = $row['contact_person'];
                $billingDetail->email = $email;
                $billingDetail->phone = $mobile;
                $billingDetail->address = $row['addr1'];
                $billingDetail->city_id = $row['city'];
                $billingDetail->state_id = $stateName;
                $billingDetail->country_id = $countryId;
                $billingDetail->postal_code = $row['zip'];
                $billingDetail->save();

                //create an event contact for the user
                $eventContact = new EventContact();
                $eventContact->application_id = $application->id;
                //salutation
                $eventContact->salutation = $row['cp_title'];
                $eventContact->first_name = $row['cp_fname'];
                $eventContact->last_name = $row['cp_lname'];
                $eventContact->email = $email;
                $eventContact->contact_number = $row['cp_mobile'];
                //can we clean CEO &amp;amp; Founder to CEO & Founder
                $row['cp_desig'] = str_replace('&amp;', '&', $row['cp_desig']);
                $eventContact->job_title = $row['cp_desig'];
                $eventContact->save();


                //create invoice entry in the invoices table
                $invoice = new Invoice();
//                $invoice->user_id = $user->id;
                $invoice->application_id = $application->id;
                $invoice->payment_due_date = date('Y-m-d', strtotime($row['reg_date']. ' + 30 days'));
                $invoice->amount = $row['total'];
                // if curr is Indian then currency is INR else USD
                if ($row['curr'] == 'Indian') {
                    $invoice->currency = 'INR';
                } else {
                    $invoice->currency = 'USD';
                }


                //if not paid then unpaid
                $payStatus = $row['pay_status'];
                if ($payStatus == 'Paid') {
                    $invoice->payment_status = 'paid';
                } else {
                    $invoice->payment_status = 'unpaid';
                }
                //type as exhibition
                $invoice->type = 'Stall Booking';
                //rate as selection_amt
                $invoice->rate = $row['selection_amt'];
                //gst as gst_amt
                $invoice->gst = $row['tax'];
                $invoice->processing_chargesRate = $row['processing_charge_per'];
                $invoice->processing_charges = $row['processing_charge'];
                $invoice->total_final_price = $row['total'];
                $invoice->amount_paid = !empty($row['total_amt_received']) ? $row['total_amt_received'] : 0;
                $invoice->invoice_no = $row['tin_no'];
                $invoice->pin_no = $row['pin_no'];
                $invoice->save();

                //if payment status is paid then create a payment entry
                if ($payStatus == 'Paid') {
                    //create a payment entry in the payments table
                    $payment = new Payment();
                    $payment->invoice_id = $invoice->id;
                    $payment->order_id = $row['pg_paymentid'] ?? null;
                    $payment->payment_method = 'CCAvenue';
                    $payment->amount = $row['total_amt_received'] ?? null;
                    $payment->amount_paid = $row['total_amt_received'] ?? null;
                    $payment->transaction_id = $row['pg_trackid'] ?? null;
                    //payment_date from pg_postdate field
                    $payment->payment_date = date('Y-m-d H:i:s', strtotime($row['pg_postdate']));
                    $payment->currency = $invoice->currency;
                    $payment->status = 'successful';
                    $payment->user_id = $user->id;
                    $payment->save();
                }










            }
        }


        // Return the fetched data as a JSON response
         return response()->json($data);



        // Handle the import logic here
        return response()->json(['message' => 'Data imported successfully']);
    }
}
