<?php 
$server_timezone = date_default_timezone_get();
echo "Server Timezone: " . $server_timezone . "<br><br>";

// Get current server time
$server_time = date('Y-m-d H:i:s');
echo "Server Time: " . $server_time . "<br><br>";

// Check if server timezone is not IST
if ($server_timezone !== 'Asia/Kolkata') {
    // Convert to IST
    date_default_timezone_set('Asia/Kolkata');
    $ist_time = date('Y-m-d H:i:s');
    echo "IST Time: " . $ist_time;
}


