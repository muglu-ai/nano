<?php
require 'dbcon.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exhibition Participants</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center py-8">    <div class="w-full max-w-5xl bg-white rounded-lg shadow-lg p-8">        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-blue-700">Exhibition Participants</h1>
            <form method="post" action="bulk_update_delegates.php" class="flex items-center gap-2">
                <button type="submit" name="update_all" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Update All Mismatching Counts
                </button>
            </form>
        </div>
        <?php
        
        if (isset($_SESSION['message'])) {
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">' . htmlspecialchars($_SESSION['message']) . '</span>
                  </div>';
            unset($_SESSION['message']);
        }
        ?>
        <?php
        // Updated SQL to get allocated_sqm and booth_number
        $sql = "
            SELECT 
                ep.id,
                a.application_id AS app_id,
                a.company_name AS exhibitor_name,
                a.allocated_sqm,
                ce.co_exhibitor_name,
                ce.booth_number,
                ep.stall_manning_count,
                ep.complimentary_delegate_count
            FROM exhibition_participants ep
            LEFT JOIN applications a ON ep.application_id = a.id
            LEFT JOIN co_exhibitors ce ON ep.coExhibitor_id = ce.id
        ";

        $result = mysqli_query($link, $sql);

        if (!$result) {
            echo '<div class="text-red-600 font-semibold">Query Error: ' . htmlspecialchars(mysqli_error($link)) . '</div>';
        } else {
            echo '<div class="overflow-x-auto">';
            echo '<table class="min-w-full border border-gray-300 rounded-lg">';
            echo '<thead>
                    <tr class="bg-blue-100 text-blue-900">
                        <th class="px-4 py-2 border">ID</th>
                        <th class="px-4 py-2 border">Application ID</th>
                        <th class="px-4 py-2 border">Exhibitor Name</th>
                        <th class="px-4 py-2 border">Co-Exhibitor Name</th>
                        <th class="px-4 py-2 border">Allocated SQM</th>
                        <th class="px-4 py-2 border">Booth Number</th>
                        <th class="px-4 py-2 border">Stall Manning Count</th>
                        <th class="px-4 py-2 border">Complimentary Delegate Count</th>
                        <th class="px-4 py-2 border">Allowed Passes</th>
                        <th class="px-4 py-2 border">Action</th>
                    </tr>
                  </thead><tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                $exhibitor = $row['exhibitor_name'] ?? '';
                $coexhibitor = $row['co_exhibitor_name'] ?? '';
                $exhibitor_display = (!empty($exhibitor)) ? htmlspecialchars($exhibitor) : '';
                $coexhibitor_display = (!empty($coexhibitor)) ? htmlspecialchars($coexhibitor) : '';
                $allocated_sqm = $row['allocated_sqm'] ?? '';
                $booth_number = $row['booth_number'] ?? '';
                $complimentary_delegate_count = (int)$row['complimentary_delegate_count'];

                // Skip row if both exhibitor and co-exhibitor are empty
                if (empty($exhibitor_display) && empty($coexhibitor_display)) {
                    continue;
                }

                // Determine allowed passes
                $allowed_passes = '';
                if (!empty($exhibitor_display) && !empty($allocated_sqm)) {
                    $sqm = (int)$allocated_sqm;
                    if ($sqm >= 9 && $sqm < 36) {
                        $allowed_passes = 2;
                    } elseif ($sqm >= 36 && $sqm <= 100) {
                        $allowed_passes = 5;
                    } elseif ($sqm > 100) {
                        $allowed_passes = 10;
                    } else {
                        $allowed_passes = 0;
                    }
                } elseif (!empty($coexhibitor_display) && !empty($booth_number)) {
                    // For co-exhibitor, you may want to fetch the allocated_sqm based on booth_number if available
                    // For now, let's assume booth_number can be mapped to a size (you may need to adjust this logic)
                    // Example: booth_number format "B12-36" where 36 is sqm
                    if (preg_match('/(\d+)$/', $booth_number, $matches)) {
                        $sqm = (int)$matches[1];
                        if ($sqm >= 9 && $sqm < 36) {
                            $allowed_passes = 2;
                        } elseif ($sqm >= 36 && $sqm <= 100) {
                            $allowed_passes = 5;
                        } elseif ($sqm > 100) {
                            $allowed_passes = 10;
                        } else {
                            $allowed_passes = 0;
                        }
                    } else {
                        $allowed_passes = 0;
                    }
                } else {
                    $allowed_passes = 0;
                }

                // Highlight if count exceeds allowed
                $count_class = ($complimentary_delegate_count > $allowed_passes) ? 'bg-red-100 text-red-700 font-bold' : '';

                echo "<tr class='hover:bg-blue-50'>
                        <td class='px-4 py-2 border text-center'>{$row['id']}</td>
                        <td class='px-4 py-2 border text-center'>{$row['app_id']}</td>
                        <td class='px-4 py-2 border text-center'>{$exhibitor_display}</td>
                        <td class='px-4 py-2 border text-center'>{$coexhibitor_display}</td>
                        <td class='px-4 py-2 border text-center'>{$allocated_sqm}</td>
                        <td class='px-4 py-2 border text-center'>{$booth_number}</td>
                        <td class='px-4 py-2 border text-center'>{$row['stall_manning_count']}</td>
                        <td class='px-4 py-2 border text-center $count_class'>{$complimentary_delegate_count}</td>
                        <td class='px-4 py-2 border text-center'>{$allowed_passes}</td>
                        <td class='px-4 py-2 border text-center'>
                            <form method='post' action='update_delegate_count.php' class='flex items-center gap-2'>
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <input type='number' name='complimentary_delegate_count' value='{$allowed_passes}' min='0' class='w-16 border rounded px-1 py-0.5'>
                                <button type='submit' class='bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600'>Update</button>
                            </form>
                        </td>
                      </tr>";
            }
            echo '</tbody></table></div>';
        }

        // Show total counts
        $sql_total = "
            SELECT 
                SUM(stall_manning_count) AS total_stall_manning,
                SUM(complimentary_delegate_count) AS total_complimentary_delegate
            FROM exhibition_participants
        ";
        $total_result = mysqli_query($link, $sql_total);
        if ($total = mysqli_fetch_assoc($total_result)) {
            echo '<div class="mt-6 flex flex-col md:flex-row gap-4 justify-center">';
            echo '<div class="bg-blue-50 text-blue-900 px-6 py-4 rounded shadow text-lg font-semibold">';
            echo "Total Stall Manning Count: <span class='font-bold'>{$total['total_stall_manning']}</span>";
            echo '</div>';
            echo '<div class="bg-green-50 text-green-900 px-6 py-4 rounded shadow text-lg font-semibold">';
            echo "Total Complimentary Delegate Count: <span class='font-bold'>{$total['total_complimentary_delegate']}</span>";
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
