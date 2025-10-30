<?php 

require_once 'config.php';

// Auto-increment email alias helper
function get_next_alias_email(string $baseEmail): string {
    $parts = explode('@', $baseEmail, 2);
    if (count($parts) !== 2) return $baseEmail; // fallback

    [$local, $domain] = $parts;

    // If local already has +suffix, strip it to get the root local part
    $rootLocal = preg_replace('/\+.*/', '', $local);

    $storePath = __DIR__ . DIRECTORY_SEPARATOR . 'email_counters.json';

    // Initialize store
    if (!file_exists($storePath)) {
        file_put_contents($storePath, json_encode([], JSON_PRETTY_PRINT));
    }

    // Read with shared lock, then upgrade to exclusive for write
    $fp = fopen($storePath, 'c+');
    if ($fp === false) {
        // If file operations fail, just default to +1
        return $rootLocal . '+1@' . $domain;
    }

    // Acquire exclusive lock for safe R/W across concurrent calls
    if (flock($fp, LOCK_EX)) {
        $size = filesize($storePath);
        $raw = $size > 0 ? fread($fp, $size) : '';
        $data = json_decode($raw ?: '[]', true);
        if (!is_array($data)) $data = [];

        $key = $rootLocal . '@' . $domain;
        $next = isset($data[$key]) && is_int($data[$key]) ? $data[$key] + 1 : 1;

        $data[$key] = $next;

        // Truncate and rewrite
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        return $rootLocal . '+' . $next . '@' . $domain;
    } else {
        fclose($fp);
        // Lock failed; fallback
        return $rootLocal . '+1@' . $domain;
    }
}

//call the send_guest_data function
$data = [
    'name' => 'Test Name',
    'category_id' => 3516,
    // Base email; will be converted into alias form like local+N@domain
    'email' => 'manish.sharma@interlinks.in',
    'country_code' => '+91',
    'mobile' => '9876543210',
    'company' => 'Test Company',
    'qsn_933' => 'VVIP',
    'qsn_934' => 'All Days',
    'qsn_935' => '',
];

// Generate next alias email automatically
$data['email'] = get_next_alias_email($data['email']);

$response = send_guest_data(
    $data['name'],
    $data['category_id'],
    $data['email'],
    $data['country_code'],
    $data['mobile'],
    $data['company'],
    $data['qsn_933'],
    $data['qsn_934'],
    $data['qsn_935'],
    $data['qsn_936']
);

print_r($response);

