<?php
require 'dbcon.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "
        SELECT 
            ep.id,
            a.allocated_sqm,
            ce.booth_number,
            ep.complimentary_delegate_count,
            CASE 
                WHEN a.company_name IS NOT NULL THEN 'exhibitor'
                WHEN ce.co_exhibitor_name IS NOT NULL THEN 'co-exhibitor'
            END as participant_type
        FROM exhibition_participants ep
        LEFT JOIN applications a ON ep.application_id = a.id
        LEFT JOIN co_exhibitors ce ON ep.coExhibitor_id = ce.id
        WHERE a.company_name IS NOT NULL OR ce.co_exhibitor_name IS NOT NULL
    ";

    $result = mysqli_query($link, $sql);
    if (!$result) {
        $_SESSION['message'] = "SQL Error: " . mysqli_error($link);
        header('Location: Mpasses_count.php');
        exit;
    }

    $updates = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $allowed_passes = 0;
        $sqm = 0;

        if ($row['participant_type'] === 'exhibitor' && !empty($row['allocated_sqm'])) {
            $sqm = (int)$row['allocated_sqm'];
        } elseif ($row['participant_type'] === 'co-exhibitor' && !empty($row['booth_number'])) {
            if (preg_match('/(\d+)$/', $row['booth_number'], $matches)) {
                $sqm = (int)$matches[1];
            }
        }

        if ($sqm >= 9 && $sqm < 36) {
            $allowed_passes = 2;
        } elseif ($sqm >= 36 && $sqm <= 100) {
            $allowed_passes = 5;
        } elseif ($sqm > 100) {
            $allowed_passes = 10;
        }

        if ((int)$row['complimentary_delegate_count'] < $allowed_passes) {
            $update_sql = "UPDATE exhibition_participants 
                         SET complimentary_delegate_count = ?,
                             updated_at = NOW() 
                         WHERE id = ?";
            
            $stmt = mysqli_prepare($link, $update_sql);
            if ($stmt) {

                // Bind parameters: allowed_passes and id
                echo "Updating ID: {$row['id']} with allowed passes: {$allowed_passes}\n";
                // Debugging output
                mysqli_stmt_bind_param($stmt, "ii", $allowed_passes, $row['id']);
                if (mysqli_stmt_execute($stmt)) {
                    $updates++;
                }
                mysqli_stmt_close($stmt);

            } else {
                $_SESSION['message'] = "Prepare failed: " . mysqli_error($link);

                //header('Location: Mpasses_count.php');
                exit;
            }
        }
    }

    $_SESSION['message'] = "{$updates} delegate count(s) have been updated to match their allowed limits.";
    header('Location: Mpasses_count.php');
    exit;
} else {
    $_SESSION['message'] = "Invalid request method.";
    header('Location: Mpasses_count.php');
    exit;
}
