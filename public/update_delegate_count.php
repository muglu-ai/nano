<?php
require 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // print_r($_POST);
    // exit;
    // Get values from POST
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $count = isset($_POST['complimentary_delegate_count']) ? (int)$_POST['complimentary_delegate_count'] : 0;

    // Validate input
    if ($id <= 0) {
        die('Invalid ID');
    }

    if ($count < 0) {
        $count = 0;
    }


    // Update the record
    $sql = "UPDATE exhibition_participants 
            SET complimentary_delegate_count = ?, 
                updated_at = NOW() 
            WHERE id = ?";

            // print_r($sql);
            // exit;

    $stmt = mysqli_prepare($link, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $count, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if (!$success) {
            die('Update failed: ' . mysqli_error($link));
        }
    } else {
        die('Prepare failed: ' . mysqli_error($link));
    }
}

// Redirect back to the previous page
header('Location: Mpasses_count.php');
exit;
?>
