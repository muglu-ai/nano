<?php 

require_once 'dbcon.php';

//get all the surcharges stored in the database
$sql = "SELECT * FROM invoices WHERE surCharge IS NOT NULL and surCharge > 0 and type = 'extra_requirement'";
$result = $link->query($sql);
$companyName = '';

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        if ($row["surCharge"] > 0) {
            // if there is application_id in the row, display it
            if (!empty($row["application_id"])) {
                //get the company name from the applications table
                $appSql = "SELECT company_name FROM applications WHERE id = " . $row["application_id"];
                $appResult = $link->query($appSql);
                if ($appResult->num_rows > 0) {
                    $appRow = $appResult->fetch_assoc();
                    $companyName = $appRow["company_name"];
                } else {
                    $companyName = 'Unknown Company';
                }

            }
            echo "Invoice ID: " . $row["id"]. " - Surcharge: " . $row["surCharge"]. " - Company Name: " . $companyName . "<br>";
        }
    }
} else {
    echo "0 results";
}