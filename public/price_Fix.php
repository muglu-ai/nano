<html>

<head>
    <title>Approved Applications</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>

    <?php
    // --- DATABASE CONNECTION AND HELPER FUNCTIONS ---
    require 'dbcon.php'; // This should setup your $link variable

    function calculate_price($currency, $membership, $stall_category, $booth_type, $submission_date, $sqm)
    {
        $prices = [
            'INR' => [
                'SEMI' => [
                    'Standard' => ['Regular' => ['Bare Space' => 17730, 'Shell Scheme' => 19770], 'Early Bird' => ['Bare Space' => 14700, 'Shell Scheme' => 16140]],
                    'Premium' => ['Regular' => ['Bare Space' => 18600, 'Shell Scheme' => 20760], 'Early Bird' => ['Bare Space' => 15450, 'Shell Scheme' => 16950]],
                ],
                'Non-SEMI' => [
                    'Standard' => ['Regular' => ['Bare Space' => 23340, 'Shell Scheme' => 26010], 'Early Bird' => ['Bare Space' => 19350, 'Shell Scheme' => 21240]],
                    'Premium' => ['Regular' => ['Bare Space' => 24480, 'Shell Scheme' => 27330], 'Early Bird' => ['Bare Space' => 20340, 'Shell Scheme' => 22290]],
                ],
            ],
            'EUR' => [
                'SEMI' => [
                    'Standard' => ['Regular' => ['Bare Space' => 26600, 'Shell Scheme' => 30130], 'Early Bird' => ['Bare Space' => 23180, 'Shell Scheme' => 26510]],
                    'Premium' => ['Regular' => ['Bare Space' => 27900, 'Shell Scheme' => 31610], 'Early Bird' => ['Bare Space' => 24290, 'Shell Scheme' => 27810]],
                ],
                'Non-SEMI' => [
                    'Standard' => ['Regular' => ['Bare Space' => 34860, 'Shell Scheme' => 39490], 'Early Bird' => ['Bare Space' => 30410, 'Shell Scheme' => 34760]],
                    'Premium' => ['Regular' => ['Bare Space' => 36520, 'Shell Scheme' => 41440], 'Early Bird' => ['Bare Space' => 31800, 'Shell Scheme' => 36430]],
                ],
            ]
        ];
        $early_bird_cutoff = '2026-03-31';
        $rate_type = (strtotime($submission_date) <= strtotime($early_bird_cutoff)) ? 'Early Bird' : 'Regular';

        // print_r([$currency, $membership, $stall_category, $booth_type, $rate_type, $sqm]); // Debugging line to check the parameters
        // echo "<br>"; // New line for better readability in debug output
        //die;// Debugging line to check the structure of prices
        if (isset($prices[$currency][$membership][$booth_type][$rate_type][$stall_category]) && is_numeric($sqm)) {

            return $prices[$currency][$membership][$booth_type][$rate_type][$stall_category] * (int)$sqm;
        }
        return null;
    }

    // --- SINGLE INVOICE UPDATE LOGIC ---
    // Handle the POST request from an individual "Update" button
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_single_invoice'])) {
        $application_id_to_update = (int)$_POST['application_id_to_update'];
        $update_message = '';

        // echo $application_id_to_update . "<br>"; // Debugging line to check the application ID being processed

        if ($application_id_to_update > 0) {
            // Fetch all necessary data for this specific application
            $appQuery = "SELECT * FROM applications WHERE id = ? AND submission_status = 'approved'";
            $stmt = mysqli_prepare($link, $appQuery);
            mysqli_stmt_bind_param($stmt, "i", $application_id_to_update);
            mysqli_stmt_execute($stmt);
            $appResult = mysqli_stmt_get_result($stmt);
            $application = mysqli_fetch_assoc($appResult);
            mysqli_stmt_close($stmt);

            if ($application) {
                // Fetch related data
                $invoiceQuery = "SELECT * FROM invoices WHERE application_id = ? AND type = 'Stall Booking'";
                $stmt = mysqli_prepare($link, $invoiceQuery);
                mysqli_stmt_bind_param($stmt, "i", $application_id_to_update);
                mysqli_stmt_execute($stmt);
                $invoice = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);

                $billingQuery = "SELECT * FROM billing_details WHERE application_id = ?";
                $stmt = mysqli_prepare($link, $billingQuery);
                mysqli_stmt_bind_param($stmt, "i", $application_id_to_update);
                mysqli_stmt_execute($stmt);
                $billingDetails = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);
                // Store relevant application data in a single array
                $data = [
                    'membership_verified' => $application['membership_verified'],
                    'stall_category'      => $application['stall_category'],
                    'pref_location'       => $application['pref_location'],
                    'submission_date'     => $application['submission_date'],
                    'allocated_sqm'       => $application['allocated_sqm']
                ];

                // Recalculate the price
                $currency = (isset($billingDetails['country_id']) && $billingDetails['country_id'] == 351) ? 'INR' : 'EUR';
                $calculated_price = calculate_price(
                    $currency,
                    $application['membership_verified'] == '1' ? 'SEMI' : 'Non-SEMI',
                    $application['stall_category'],
                    $application['pref_location'],
                    $application['submission_date'],
                    $application['allocated_sqm']
                );


                //die; // Debugging line to check the calculated price

                // give the old price and new price for debugging
                // echo "Old Price: " . $invoice['price'] . "<br>";
                // echo "New Price: " . $calculated_price . "<br>";
                // die;

                // if old price and new price match, no need to update
                if (!$application) {
                    $update_message = "<div class='alert alert-danger'>Could not find Application ID {$application_id_to_update}.</div>";
                } elseif (!$invoice) {
                    $update_message = "<div class='alert alert-danger'>Could not find Invoice for Application ID {$application_id_to_update}.</div>";
                } elseif ($calculated_price === null) {
                    $update_message = "<div class='alert alert-warning'>Could not calculate a new price for Application ID {$application_id_to_update}. No update was performed.</div>";
                } elseif ($invoice['price'] == $calculated_price) {
                    $update_message = "<div class='alert alert-info'>No update needed for Application ID {$application_id_to_update}. The invoice price is already correct.</div>";
                } else {
                    // Prepare new values
                    $new_price = $calculated_price;
                    $new_gst = round($new_price * 0.18, 2);
                    $new_total = $new_price + $new_gst;
                    $new_amount = $new_total;
                    $allocated_sqm = (int)$application['allocated_sqm'];
                    $new_rate = ($allocated_sqm > 0) ? ($new_price / $allocated_sqm) : 0;
                    $remarks = null;

                    // Update the invoice in the database
                    $updateQuery = "UPDATE invoices SET price = ?, gst = ?, total_final_price = ?, amount = ?, rate = ?, remarks = ? WHERE id = ?";
                    $updateStmt = mysqli_prepare($link, $updateQuery);
                    mysqli_stmt_bind_param($updateStmt, "dddddsi", $new_price, $new_gst, $new_total, $new_amount, $new_rate, $remarks, $invoice['id']);

                    if (mysqli_stmt_execute($updateStmt)) {
                        $update_message = "<div class='alert alert-success'>Successfully updated Invoice for Application ID {$application_id_to_update}. The page has been reloaded.</div>";
                    } else {
                        $update_message = "<div class='alert alert-danger'>Error updating Invoice ID {$invoice['id']}: " . mysqli_stmt_error($updateStmt) . "</div>";
                    }
                    mysqli_stmt_close($updateStmt);

                    //clear post data to prevent resubmission
                    unset($_POST['application_id_to_update']);
                }


        }
    }
    }
    ?>

    <div class="container mt-5">
        <h2>Approved Applications with Price Mismatches</h2>
        <p>This table shows approved applications where the invoice price doesn't match the system-calculated price. Click "Update" to correct a specific invoice.</p>

        <?php
        // Display any update messages from the POST request
        if (!empty($update_message)) {
            echo $update_message;
        }
        ?>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Submission Date</th>
                    <th>Stall Size (Sqm)</th>
                    <th>Stall Type</th>
                    <th>Booth Type</th>
                    <th>Country</th>
                    <th>Rate</th>
                    <th>New Rate </th>
                    <th>Current Price</th>
                    <th>Correct Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // --- DISPLAY LOGIC ---
                $query = "SELECT * FROM applications WHERE submission_status = 'approved' ORDER BY approved_date";
                $result = mysqli_query($link, $query);
                $approvedApplications = mysqli_fetch_all($result, MYSQLI_ASSOC);
                
                $i = 1;
                $mismatch_found = false;

                foreach ($approvedApplications as $application) {
                    // Skip specific IDs
                    if (in_array($application['id'], [325, 324, 213])) {
                        continue;
                    }
                    // Fetch related data for display
                    $invoiceQuery = "SELECT * FROM invoices WHERE application_id = ? AND type ='Stall Booking'";
                    $stmt = mysqli_prepare($link, $invoiceQuery);
                    mysqli_stmt_bind_param($stmt, "i", $application['id']);
                    mysqli_stmt_execute($stmt);
                    $invoice = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                    mysqli_stmt_close($stmt);

                    $billingQuery = "SELECT * FROM billing_details WHERE application_id = ?";
                    $stmt = mysqli_prepare($link, $billingQuery);
                    mysqli_stmt_bind_param($stmt, "i", $application['id']);
                    mysqli_stmt_execute($stmt);
                    $billingDetails = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                    mysqli_stmt_close($stmt);

                    // Calculate price
                    $currency = (isset($billingDetails['country_id']) && $billingDetails['country_id'] == 351) ? 'INR' : 'EUR';
                    $calculated_price = calculate_price(
                        $currency,
                        $application['membership_verified'] == '1' ? 'SEMI' : 'Non-SEMI',
                        $application['stall_category'],
                        $application['pref_location'],
                        $application['submission_date'],
                        $application['allocated_sqm']
                    );

                    // Only show rows where there is a mismatch
                    if ($invoice && $calculated_price !== null && $invoice['price'] != $calculated_price) {
                        $mismatch_found = true;
                ?>
                        <tr>
                            <td><?php echo $application['id']; ?></td>
                            <td><?php echo htmlspecialchars($application['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($application['submission_date']); ?></td>
                            <td><?php echo htmlspecialchars($application['allocated_sqm']); ?></td>
                            <td><?php echo htmlspecialchars($application['stall_category']); ?></td>
                            <td><?php echo htmlspecialchars($application['pref_location']); ?></td>
                            <td>
                                <?php
                                echo (isset($billingDetails['country_id']) && $billingDetails['country_id'] == 351) ? 'India (INR)' : 'International (EUR)';
                                ?>
                            </td>
                            <td>
                                <?php
                                $allocated_sqm = (int)$application['allocated_sqm'];
                                $rate = ($allocated_sqm > 0) ? ($invoice['price'] / $allocated_sqm) : 0;
                                echo number_format($rate);
                                ?>
                            </td>
                            <td>
                                <?php
                                $allocated_sqm = (int)$application['allocated_sqm'];
                                $rate = ($allocated_sqm > 0) ? ($calculated_price / $allocated_sqm) : 0;
                                echo number_format($rate);
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($invoice['price']); ?></td>
                            <td><strong class="text-success"><?php echo htmlspecialchars($calculated_price); ?></strong></td>
                            <td>
                                <!-- Individual Update Form per row -->
                                <form method="POST" action="">
                                    <input type="hidden" name="application_id_to_update" value="<?php echo htmlspecialchars($application['id']); ?>">
                                    <button type="submit" name="update_single_invoice" class="btn btn-sm btn-primary">Update</button>
                                </form>
                            </td>
                        </tr>
                <?php
                    } // end if mismatch
                } // end foreach

                if (!$mismatch_found) {
                    echo "<tr><td colspan='10' class='text-center alert alert-info'>No mismatched invoices found. All records are correct.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <?php
        // Close the final database connection
        mysqli_close($link);
        ?>
    </div>
</body>

</html>