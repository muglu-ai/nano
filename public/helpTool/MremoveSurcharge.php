<?php
require_once 'dbcon.php';

// Handle remove surcharge action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_invoice'])) {
    $invoiceNo = $_POST['remove_invoice'];

    //store all the requested in json format 
    $jsonData = json_encode(['invoice_no' => $invoiceNo]);
    // Log the JSON data to a file
    // Ensure the logs directory exists
    if (!is_dir('logs')) {
      mkdir('logs', 0777, true);
    }
    file_put_contents('logs/remove_surcharge.log', $jsonData . PHP_EOL, FILE_APPEND);
    // Update the invoice to remove the surcharge
    $updateSql = "UPDATE invoices SET surChargeRemove = 1 WHERE invoice_no = ?";
    $stmt = $link->prepare($updateSql);
    $stmt->bind_param("s", $invoiceNo);
    $stmt->execute();
    $stmt->close();
    // Optional: Add a success message or redirect
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Surcharges - Admin Panel</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
  <?php include 'components/header.php'; ?>
  <div class="flex">
    <?php include 'components/sidebar.php'; ?>
    <main class="flex-1 p-6">
      <h1 class="text-2xl font-bold mb-4">Surcharges</h1>
      <?php
      // Get all the surcharges stored in the database
      $sql = "SELECT * FROM invoices WHERE surCharge IS NOT NULL and surCharge > 0 and type = 'extra_requirement'";
      $result = $link->query($sql);
      $rows = [];
      if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
              $companyName = '';
              if (!empty($row["application_id"])) {
                  $appSql = "SELECT company_name FROM applications WHERE id = " . $row["application_id"];
                  $appResult = $link->query($appSql);
                  if ($appResult->num_rows > 0) {
                      $appRow = $appResult->fetch_assoc();
                      $companyName = $appRow["company_name"];
                  } else {
                      $companyName = 'Unknown Company';
                  }
              }
              $removeBtn = '<form method="POST" style="display:inline;">
                  <input type="hidden" name="remove_invoice" value="'.htmlspecialchars($row["invoice_no"]).'">
                  <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Remove Surcharge</button>
                </form>';
              $rows[] = [
                $row["invoice_no"],
                $row["surCharge"],
                $companyName,
                $removeBtn
              ];
          }
      }
      $headers = ["Invoice ID", "Surcharge", "Company Name", "Action"];
      // Custom table rendering to allow HTML in Action column
      echo '<table class="min-w-full bg-white border border-gray-200 rounded shadow-sm">';
      echo '<thead><tr>';
      foreach ($headers as $header) {
          echo '<th class="px-4 py-2 border-b font-semibold text-left">'.htmlspecialchars($header).'</th>';
      }
      echo '</tr></thead><tbody>';
      foreach ($rows as $row) {
          echo '<tr>';
          foreach ($row as $i => $cell) {
              echo '<td class="px-4 py-2 border-b align-top">';
              if ($i === count($row) - 1) {
                  echo $cell; // Action column (raw HTML)
              } else {
                  echo htmlspecialchars($cell);
              }
              echo '</td>';
          }
          echo '</tr>';
      }
      echo '</tbody></table>';
      ?>
    </main>
  </div>
</body>
</html>