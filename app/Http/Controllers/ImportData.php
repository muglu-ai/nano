<?php

namespace App\Http\Controllers;

use App\Mail\UserCredentialsMail;
use App\Models\ExhibitionParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use mysqli;
use Illuminate\Support\Facades\DB as DBFacade;
use App\Models\User;
use App\Models\Application;
use Illuminate\Support\Facades\Hash;
use App\Models\EventContact;
use App\Models\BillingDetail;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Sector;
use App\Models\State;
use App\Models\Country;

class ImportData extends Controller
{
    // Remote DB connection
    public function dbConnection()
    {
        $host = "localhost";
        $username = "btsblnl265_asd1d_bengaluruite";
        $password = "Disl#vhfj#Af#DhW65";
        $database = "btsblnl265_asd1d_bengaluruite";

        $connection = new mysqli($host, $username, $password, $database);
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }
        return $connection;
    }

    public function importUsers(Request $request)
    {
        // echo "Importing users...\n";
        // exit;
        $connection = $this->dbConnection();

        // This query selects all columns from the exhibitors payments table (it_2025_exhibitors_dir_payment_tbl) where the pay_status is 'PAID'.
        // It retrieves a maximum of 50 records, skipping the very first (OFFSET 1).
        $query = "SELECT * FROM it_2025_exhibitors_dir_payment_tbl WHERE pay_status = 'PAID'";
        $result = $connection->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $connection->close();

//        dd(count($data));
//        print_r($data);
        foreach ($data as $row) {

//            dd($row);
            $importSuccess = false; // Track import status for this user

            /** ---------------------------
             * Step 1: Normalize & Prepare $command
             * ----------------------------*/

            // make and variable that say import is success or not
            // once the variable value is changed to success then send an email to the user with their credentials

            $command = [];

            $command['contact_person'] = trim($row['cp_fname'] . ' ' . $row['cp_lname']);
            $command['email'] = $row['cp_email'];

            // Mobile split (91-9573600744)
            $mobileSplit = explode('-', $row['cp_mobile']);
            $command['country_code'] = $mobileSplit[0] ?? null;
            $command['phone'] = $mobileSplit[1] ?? $row['cp_mobile'];

            // Clean company name & fields
            $command['company'] = $this->cleanString($row['exhibitor_name']);
            $command['website'] = $this->cleanString($row['website']);
            $command['address'] = $this->cleanString($row['addr1']);
            $command['designation'] = $this->cleanString($row['cp_desig']);

            //dd($command['designation']);

            // Ensure website starts with https
            if (!str_starts_with($command['website'], 'http')) {
                $command['website'] = 'https://' . $command['website'];
            }

            // Lookup foreign keys
            $command['sector_id'] = Sector::where('name', $row['sector'])->value('id');
            $command['state_id']  = State::where('name', $row['state'])->value('id');
            $command['country_id'] = Country::where('name', $row['country'])->value('id');

            // Random password
            $command['password_plain'] = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
            $command['password_hashed'] = Hash::make($command['password_plain']);

            // Stall category
            if (str_contains($row['booth_area'], 'Shell')) {
                $command['stall_category'] = 'Shell Scheme';
            } elseif (str_contains($row['booth_area'], 'Raw')) {
                $command['stall_category'] = 'Raw Space';
            } else {
                $command['stall_category'] = 'Startup Booth';
            }

            $command['row'] = $row; // keep raw row for fallback

            /** ---------------------------
             * Step 2: Check existing user
             * ----------------------------*/
            $user = User::where('email', $command['email'])->first();

            if ($user) {
                echo "User with email {$command['email']} already exists. Skipping...\n";

                continue;
            } else {
                /** ---------------------------
                 * Step 3: Create User
                 * ----------------------------*/
                $user = User::create([
                    'name' => $command['contact_person'],
                    'email' => $command['email'],
                    'password' => $command['password_hashed'],
                    'simplePass' => $command['password_plain'],
                    'role' => 'exhibitor',
                    'phone' => $command['phone'],
                    'email_verified_at' => now(),
                ]);
                echo "Created user: {$user->email} with password: {$command['password_plain']}\n";
            }


            /** ---------------------------
             * Step 4: Create Application
             * ----------------------------*/
            $application = Application::updateOrCreate(
                ['user_id' => $user->id],
                [
                'user_id' => $user->id,
                'company_name' => $command['company'],
                'website' => $command['website'],
                'address' => $command['address'],
                'city_id' => $row['city'],
                'companyYears' => intval($row['company_years']) ?? null,
                'certificate' => $row['ci_certf'],
                'sector_id' => $command['sector_id'],
                'subSector' => $row['subsector'],
                'event_id' => 1,
                'stall_category' => $command['stall_category'],
                'exhibitorType' => $row['promocode'],
                'application_id' => $row['tin_no'],
                'interested_sqm' => $row['booth_size'],
                'allocated_sqm' => $row['booth_size'],
                'state_id' => $command['state_id'] ?? $row['state'],
                'country_id' => $command['country_id'] ?? $row['country'],
                'headquarters_country_id' => $command['country_id'] ?? $row['country'],
                'postal_code' => $row['zip'],
                'gst_no' => $row['gst_number'],
                'pan_no' => $row['pan_number'],
                'company_email' => $command['email'],
                'gst_compliance' => !empty($row['gst_number']) ? 1 : 0,
                'submission_status' => ($row['approval_status'] == 'Approved' || $row['pay_status'] == 'Paid') ? 'approved' : 'pending',
                'approved_date' => ($row['approval_status'] == 'Approved' || $row['pay_status'] == 'Paid') ? $row['reg_date'] : null,
                'assoc_mem' => $row['user_type'],
                'boothDescription' => $row['booth_area'],
                'participation_type' => 'Onsite',
                'region' => ($command['country_id'] == 101 ? 'India' : 'International'),
                'approved_by' => $row['approved_by'],
                'tag' => $row['promocode'],
            ]);
            $application->save();

            echo "Created application for user: {$user->email}\n";
            /** ---------------------------
             * Step 5: Billing Details
             * ----------------------------*/
            BillingDetail::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'billing_company' => $command['company'],
                    'contact_name' => $command['contact_person'],
                    'email' => $command['email'],
                    'phone' => $command['phone'],
                    'address' => $command['address'],
                    'city_id' => $row['city'],
                    'state_id' => $command['state_id'],
                    'country_id' => $command['country_id'],
                    'postal_code' => $row['zip'],
                ]
            );

            echo "Created billing details for application ID: {$application->id}\n";

            /** ---------------------------
             * Step 6: Event Contact
             * ----------------------------*/
            EventContact::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'salutation' => $row['cp_title'],
                    'first_name' => $row['cp_fname'],
                    'last_name' => $row['cp_lname'],
                    'email' => $command['email'],
                    'contact_number' => $row['cp_mobile'],
                    'job_title' => $command['designation'],
                ]
            );

            echo "Created event contact for application ID: {$application->id}\n";

            /** ---------------------------
             * Step 7: Invoice
             * ----------------------------*/
            $invoice = Invoice::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'payment_due_date' => date('Y-m-d', strtotime($row['reg_date'] . ' +30 days')),
                    'amount' => $row['total'],
                    'currency' => ($row['curr'] == 'Indian' ? 'INR' : 'USD'),
                    'payment_status' => ($row['pay_status'] == 'Paid' ? 'paid' : 'unpaid'),
                    'type' => 'Stall Booking',
                    'rate' => $row['selection_amt'],
                    'gst' => $row['tax'],
                    'processing_chargesRate' => $row['processing_charge_per'],
                    'processing_charges' => $row['processing_charge'],
                    'total_final_price' => $row['total'],
                    'amount_paid' => $row['total_amt_received'] ?? 0,
                    'invoice_no' => $row['tin_no'],
                    'pin_no' => $row['pin_no'],
                ]
            );

            echo "Created invoice ID: {$invoice->id} for application ID: {$application->id}\n";

            /** ---------------------------
             * Step 8: Payment (if paid)
             * ----------------------------*/
            if ($row['pay_status'] == 'Paid') {
                Payment::create([
                    'invoice_id' => $invoice->id,
                    'order_id' => $row['pg_paymentid'] ?? null,
                    'payment_method' => 'CCAvenue',
                    'amount' => $row['total_amt_received'] ?? null,
                    'amount_paid' => $row['total_amt_received'] ?? null,
                    'transaction_id' => $row['pg_trackid'] ?? null,
                    'payment_date' => date('Y-m-d H:i:s', strtotime($row['pg_postdate'])),
                    'currency' => $invoice->currency,
                    'status' => 'successful',
                    'user_id' => $user->id,
                ]);



                /*
                 * If Exhibitor Type is 'Startup Booth' then stall manning count = 2 and ticket allocation = '{"2": 1 }'
                 * If booth size is 9sqm then stall manning count = 2 and ticket allocation = '{"2": 1 }'
                 * If booth size is 18sqm then stall manning count = 4 and ticket allocation = '{"2": 2 }'
                 * If booth size is 36sqm then stall manning count = 8 and ticket allocation = '{"2": 4 }'
                 * */
                if ($command['stall_category'] === 'Startup Booth' || (int)$row['booth_size'] <= 9) {
                    $stallManningCount = 0;
                    $ticketAllocation = '{"2": 1, "11":2 }';
                } elseif ((int)$row['booth_size'] > 9 && (int)$row['booth_size'] <= 18) {
                    $stallManningCount = 0;
                    $ticketAllocation = '{"2": 2, "11":4 }';
                } elseif ((int)$row['booth_size'] > 18 && (int)$row['booth_size'] <= 36) {
                    $stallManningCount = 0;
                    $ticketAllocation = '{"2": 4, "11":8 }';
                } else if($command['stall_category'] === 'Startup Booth' || (int)$row['booth_size'] <= 9 && (int)$row['promocode'] == 'TIESB' || (int)$row['promocode'] == 'TIESNB') {
                    $stallManningCount = 0;
                    $ticketAllocation = '{"2": 1, "11":1 }';
                
                }
                
                else {
                    $stallManningCount = 0;
                    $ticketAllocation = '{"2": 1, "11":2 }';
                }


                //create a ExhibitionParticipation entry
                ExhibitionParticipant::updateOrCreate(
                    ['application_id' => $application->id],
                    [
                        'stall_manning_count' => $stallManningCount,
                        'ticketAllocation' => $ticketAllocation,
                    ]
                );



                echo "Created payment for application ID: {$application->id}\n";
                $importSuccess = true;
                $name = $user->name;
                $setupProfileUrl = config('app.url');
                $username = $user->email;
                $password = $user->simplePass;
                $company = $command['company'];
                // Send email with credentials
                // $email='manish.sharma@interlinks.in';
                $email = $username;
                if ($importSuccess) {
                    // Send email to $command['email'] with credentials
                   Mail::to($email)
                       ->bcc('test.interlinks@gmail.com')
                       ->send(new UserCredentialsMail($name, $setupProfileUrl, $username, $password));
                    // Mail::to($command['email'])->send(new UserCredentialsMail($user, $command['password_plain'], $company, $setupProfileUrl));
                }
            }
        }

        return response()->json(['message' => 'Data imported successfully']);
    }

    private function cleanString($value)
    {
        if (!$value) return $value;
        $value = str_replace(['&amp;', 'amp;'], '&', $value);
        //if continous two & appears remove one
        $value = preg_replace('/&{2,}/', '&', $value);
        $value = str_replace(' ', ' ', $value); // invisible space
        return trim($value);
    }

    //generate a tin_no with 
    // TIN-BTS2025-EXHST-{random 5 characters with numbers only}
    public function generateTinNo()
    {
        return 'TIN-BTS2025-EXHST-' . rand(10000, 99999);
    }
    public function generatePinNo()
    {
        return 'PIN-BTS2025-EXHST-' . rand(10000, 99999);
    }

    /**
     * Import exhibitor data from CSV
     */
    public function importExhibitorsBulk(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        // Read CSV file
        $data = [];
        if (($handle = fopen($path, 'r')) !== FALSE) {
            $headers = fgetcsv($handle); // Get headers
            while (($row = fgetcsv($handle)) !== FALSE) {
                if (count($row) == count($headers)) {
                    $data[] = array_combine($headers, $row);
                }
            }
            fclose($handle);
        }

        // Step 1: Validate ALL rows first before inserting anything
        $errors = [];
        $validatedData = [];

        foreach ($data as $index => $row) {
            $rowErrors = [];
            
            // Validate required fields
            if (empty($row['Organisation (Exhibitor Name)'])) {
                $rowErrors[] = "Missing 'Organisation (Exhibitor Name)'";
            }
            if (empty($row['Exhibitor Contact Person Email *'])) {
                $rowErrors[] = "Missing 'Exhibitor Contact Person Email'";
            }
            if (empty($row['Exhibitor Contact Person Mobile *'])) {
                $rowErrors[] = "Missing 'Exhibitor Contact Person Mobile'";
            }

            // Check for duplicate email in CSV
            $email = trim($row['Exhibitor Contact Person Email *'] ?? '');
            if (!empty($email)) {
                foreach ($data as $prevIndex => $prevRow) {
                    if ($prevIndex < $index && trim($prevRow['Exhibitor Contact Person Email *']) == $email) {
                        $rowErrors[] = "Duplicate email '{$email}' already exists in row " . ($prevIndex + 2);
                        break;
                    }
                }
            }

            // Check if user already exists in database
            if (!empty($email)) {
                $existingUser = User::where('email', $email)->first();
                if ($existingUser) {
                    $rowErrors[] = "User with email '{$email}' already exists in database";
                }
            }

            if (count($rowErrors) > 0) {
                $errors[] = [
                    'row' => $index + 2,
                    'errors' => $rowErrors
                ];
            } else {
                // This row is valid, add to validated data
                $validatedData[] = $row;
            }
        }

        // If there are ANY validation errors, don't insert anything
        if (count($errors) > 0) {
            return response()->json([
                'success' => false,
                'message' => "Validation failed. Please fix the errors and try again.",
                'total_rows' => count($data),
                'valid_rows' => count($validatedData),
                'error_rows' => count($errors),
                'errors' => $errors
            ], 400);
        }

        // Step 2: All validations passed, now insert all records in a transaction
        DBFacade::beginTransaction();
        
        try {
            $successCount = 0;
            $insertedRecords = [];

            foreach ($validatedData as $index => $row) {
                // Extract data (all validations already passed)
                $organisation = $this->cleanString($row['Organisation (Exhibitor Name)']);
                $entityType = $this->cleanString($row['Entity is Sponsor/ Exhibitor / Startup?']) ?? 'Exhibitor';
                $boothSize = intval($row['Exhibition booth Size: in SQM'] ?? 9);
                $spaceType = $this->cleanString($row['Exhibitions Space Type (Raw / Shell)']) ?? 'Shell Scheme';
                $focusSectors = $this->cleanString($row['Focus Sectors (if any)'] ?? '');
                $onboardingStatus = $this->cleanString($row['Onboarding Status (From TechTeam)'] ?? '');
                $contactName = $this->cleanString($row['Exhibitor Contact Person Name']);
                $countryCode = $this->cleanString($row['Exhibitor Contact Mobile Country Code *']) ?? '+91';
                $mobile = $this->cleanString($row['Exhibitor Contact Person Mobile *']);
                $email = trim($row['Exhibitor Contact Person Email *']);
                
                // Pass requirements
                $startupBooths = intval($row['Entitled Startup booths Requirements (default : 0)'] ?? 0);
                $vipPasses = intval($row['VIP Pass Requirement (Default: 0)'] ?? 0);
                $premiumPasses = intval($row['Premium delegate Pass Requirement (Default: 0)'] ?? 0);
                $standardPasses = intval($row['Standard delegate Pass Requirement (Default: 0)'] ?? 0);
                $fmcPremiumPasses = intval($row['FMC PREMIUM Delegate Pass Requirement (Default: 0)'] ?? 0);
                $exhibitorPasses = intval($row['Exhibitor Pass Requirement (Default: 0)'] ?? 0);
                $servicePasses = intval($row['Service Pass Requirement (Default: 0)'] ?? 0);
                $businessVisitorPasses = intval($row['Business Visitor Pass Requirement (Default: 50)'] ?? 50);
                
                $salesPerson = $this->cleanString($row['Whose is Handling Client from BTS/MMA Team (Name Email Mobile)'] ?? '');
                $callingStatus = $this->cleanString($row['Calling Status (from Telecalling team)'] ?? '');

                // Generate random password
                $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
                $passwordHashed = Hash::make($password);

                // Create user
                $user = User::create([
                    'name' => $contactName,
                    'email' => $email,
                    'password' => $passwordHashed,
                    'simplePass' => $password,
                    'role' => 'exhibitor',
                    'phone' => $mobile,
                    'email_verified_at' => now(),
                ]);

                // Determine stall category
                if (str_contains($spaceType, 'Raw')) {
                    $stallCategory = 'Raw Space';
                } elseif (str_contains($spaceType, 'Shell')) {
                    $stallCategory = 'Shell Scheme';
                } else {
                    $stallCategory = 'Startup Booth';
                }

                // Determine exhibitor type
                if (str_contains($entityType, 'Startup')) {
                    $exhibitorType = 'Startup';
                } elseif (str_contains($entityType, 'Sponsor')) {
                    $exhibitorType = 'Sponsor';
                } else {
                    $exhibitorType = 'Exhibitor';
                }

                // Create application
                $application = Application::create([
                    'user_id' => $user->id,
                    'application_id' => $this->generateTinNo(),
                    'pin_no' => $this->generatePinNo(),
                    'company_name' => $organisation,
                    'company_email' => $email,
                    'event_id' => 1,
                    'stall_category' => $stallCategory,
                    'exhibitorType' => $exhibitorType,
                    'interested_sqm' => $boothSize,
                    'allocated_sqm' => $boothSize,
                    'submission_status' => 'approved',
                    'subSector' => $focusSectors,
                    'salesPerson' => $salesPerson,
                    'gst_compliance' => 0,
                    'boothDescription' => $spaceType,
                    'participation_type' => 'Onsite',
                    'approved_by' => 'Admin -Imported',
                    'tag' => $entityType,
                ]);

                // Create event contact
                EventContact::create([
                    'application_id' => $application->id,
                    'salutation' => '',
                    'first_name' => explode(' ', $contactName)[0] ?? $contactName,
                    'last_name' => implode(' ', array_slice(explode(' ', $contactName), 1)) ?? '',
                    'email' => $email,
                    'contact_number' => $countryCode . '-' . $mobile,
                    'job_title' => '',
                ]);

                // Create billing detail
                // BillingDetail::create([
                //     'application_id' => $application->id,
                //     'billing_company' => $organisation,
                //     'contact_name' => $contactName,
                //     'email' => $email,
                //     'phone' => $mobile,
                //     'address' => '',
                // ]);

                // Calculate ticket allocation based on booth size and passes
                $ticketAllocation = $this->calculateTicketAllocation($boothSize, $entityType, $standardPasses, $premiumPasses, $vipPasses, $fmcPremiumPasses, $exhibitorPasses, $servicePasses, $businessVisitorPasses);

                // Create exhibition participant
                ExhibitionParticipant::create([
                    'application_id' => $application->id,
                    'stall_manning_count' => 0,
                    'ticketAllocation' => $ticketAllocation,
                ]);

                // Send email with credentials
                Mail::to($email)
                    ->bcc('test.interlinks@gmail.com')
                    ->queue(new UserCredentialsMail($contactName, config('app.url'), $email, $password));

                $insertedRecords[] = [
                    'email' => $email,
                    'organisation' => $organisation,
                    'contact_name' => $contactName
                ];

                $successCount++;
            }

            // All records inserted successfully, commit transaction
            DBFacade::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$successCount} exhibitors",
                'success_count' => $successCount,
                'error_count' => 0,
                'inserted_records' => $insertedRecords
            ]);

        } catch (\Exception $e) {
            // Rollback transaction on any error
            DBFacade::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => "Import failed: " . $e->getMessage(),
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Calculate ticket allocation based on requirements
     */
    private function calculateTicketAllocation($boothSize, $entityType, $standardPasses, $premiumPasses, $vipPasses, $fmcPremiumPasses, $exhibitorPasses, $servicePasses, $businessVisitorPasses)
    {
        // This is a simplified calculation. Adjust based on your actual ticket type IDs
        $allocation = [];
        
        if ($vipPasses > 0) {
            $allocation['2'] = $vipPasses; // Assuming ticket type ID 2 is VIP
        }
        if ($premiumPasses > 0) {
            $allocation['11'] = $premiumPasses; // Assuming ticket type ID 11 is Premium
        }
        if ($standardPasses > 0) {
            $allocation['10'] = $standardPasses; // Assuming ticket type ID 10 is Standard
        }
        
        // Add default based on booth size
        if ($boothSize <= 9) {
            $allocation['2'] = ($allocation['2'] ?? 0) + 1;
            $allocation['11'] = ($allocation['11'] ?? 0) + 2;
        } elseif ($boothSize > 9 && $boothSize <= 18) {
            $allocation['2'] = ($allocation['2'] ?? 0) + 2;
            $allocation['11'] = ($allocation['11'] ?? 0) + 4;
        } elseif ($boothSize > 18 && $boothSize <= 36) {
            $allocation['2'] = ($allocation['2'] ?? 0) + 4;
            $allocation['11'] = ($allocation['11'] ?? 0) + 8;
        }

        return json_encode($allocation);
    }
}
