<?php 




require_once 'exhibitorSetup.php';

// echo $host;
// echo $user;
// echo $pass;
// echo $db;

$db2 = 'btsblnl265_asd1d_portal';

$dbConnection = new mysqli($host, $user, $pass, $db2);

/*
VIP Pass - 3548
Premium Pass - 3549
Standard Pass - 3550
FMC Premium - 3551
FMC GO - 3552
VIP Pass - 3553
Premium Pass - 3554
Standard Pass - 3555
FMC Premium - 3556
FMC GO - 3557
Exhibitor - 3558
BUSINESS VISITOR - 3564
*/ 
//database stored ticketType
/*
"ticketType"
"Premium"
"Exhibitor"
"Service Pass"
"Business Visitor Pass"
"VIP Pass"
"FMC GO"
"Standard"
"GIA PARTNER"
"VIP GIA PARTNER"
*/


$category_id_map = array(
    'Sponsor VIP Pass'         => array('category_id' => 3548, 'name' => 'VIP PASS'),
    'Sponsor Premium'          => array('category_id' => 3549, 'name' => 'PREMIUM PASS'),
    'Sponsor Standard'         => array('category_id' => 3550, 'name' => 'STANDARD PASS'),
    'Sponsor FMC Premium'      => array('category_id' => 3551, 'name' => 'FMC Premium'),
    'FMC GO'                   => array('category_id' => 3552, 'name' => 'FMC GO'),
    'VIP Pass'                 => array('category_id' => 3553, 'name' => 'VIP PASS'),
    'Premium Pass'             => array('category_id' => 3554, 'name' => 'PREMIUM PASS'),
    'Standard Pass'            => array('category_id' => 3555, 'name' => 'STANDARD PASS'),
    'Exhibitor FMC Premium'    => array('category_id' => 3556, 'name' => 'FMC Premium'),
    'Exhibitor FMC GO'         => array('category_id' => 3557, 'name' => 'FMC GO'),
    'Exhibitor'                => array('category_id' => 3558, 'name' => 'EXHIBITOR'),
    'BUSINESS VISITOR'         => array('category_id' => 3564, 'name' => 'BUSINESS VISITOR PASS'),
);

//event days as except for FMC GO and FMC Premium
$event_days = [
    '18 Nov',
    '19 Nov',
    '20 Nov',
];

// get the category_id from the category_id_map
$category_id = $category_id_map[$ticketType]['category_id'];

// get the name from the category_id_map
$name = $category_id_map[$ticketType]['name'];



// 


// select all the from the from the complimenary_delegates table map the ticketType to the category_id in above list and send the data to the api

// where first_name is not null and first_name is not empty
// First select all records from complimenary_delegates where first_name is present
$sql = "SELECT * FROM complimentary_delegates WHERE first_name IS NOT NULL AND TRIM(first_name) != ''";

$result = $dbConnection->query($sql);
$complimentaryRows = [];
if ($result && $result->num_rows > 0) {
    // For each row, fetch the application_id from ExhibitionParticipant, then get category from application table
    while ($row = $result->fetch_assoc()) {
        $exhibition_participant_id = $row['exhibition_participant_id']; // You may need to adjust field name


        
        // Get application_id from ExhibitionParticipant
        $appIdSql = "SELECT application_id FROM exhibition_participants WHERE id = ?";
        $stmt = $dbConnection->prepare($appIdSql);
        $stmt->bind_param("i", $exhibition_participant_id);
        $stmt->execute();
        $stmt->bind_result($application_id);
        $stmt->fetch();
        $stmt->close();

        // Get category from application table using application_id
        $category = null;
        if ($application_id) {

            //get the 
            $catSql = "SELECT application_type FROM applications WHERE id = ?";
            $stmt2 = $dbConnection->prepare($catSql);
            $stmt2->bind_param("i", $application_id);
            $stmt2->execute();
            $stmt2->bind_result($category);
            $stmt2->fetch();
            $stmt2->close();
        }

        // Add the category info to the current row for later use
        $row['application_id'] = $application_id;
        $row['application_type'] = $category;

        $complimentaryRows[] = $row;
    }
}
$result = $dbConnection->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ticketType = $row['ticketType'];
        $category_id = $ticketType;
        
        $mobile = $row['mobile'];

        // +91-7905080871
        //separate out the country code and mobile number alos remove the + from the country code
        $mobile_arr = explode('-', $mobile);
        $country_code = str_replace('+', '', $mobile_arr[0]);
        $mobile = $mobile_arr[1];

        // get the event days from the row['sessionDay']
        $event_days = '18, 19, 20 Nov';

        //if fmc go or fmc premium then set the event days to 20 Nov
        if ($ticketType == 'FMC GO' || $ticketType == 'FMC Premium') {
            $event_days = '20 Nov';
        }


        $data = [
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'mobile' => $mobile,
            'country_code' => $country_code,
            'event_days' => $event_days,
            'category_id' => $category_id,
            'name' => $name,

        ];

        print_r($data);
        exit;
    }
}

$dbConnection->close();