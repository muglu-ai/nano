<?php
require_once 'dbcon.php';

// Tailwind CDN for quick styling
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Users - Admin Panel</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
  <?php include 'components/header.php'; ?>
  <div class="flex">
    <?php include 'components/sidebar.php'; ?>
   <main class="flex-1 p-6">
  <h1 class="text-2xl font-bold mb-4">Users</h1>
  <?php
  $conn = $link;
  // LEFT JOIN to get company_name if application exists
  $sql = "SELECT users.*, applications.company_name 
          FROM users 
          LEFT JOIN applications ON users.id = applications.user_id";
  $result = $conn->query($sql);
  $rows = [];
  if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
          $rows[] = [
            $row["id"],
            $row["name"],
            $row["email"],
            $row["company_name"] ?? 'N/A',
            // decrypt($row["password"])
          ];
      }
  }
  $headers = ["ID", "Name", "Email", "Company Name"];
  include 'components/table.php';
  $conn->close();
  function decrypt($data) {
      return 'Original password cannot be retrieved from Laravel hash';
  }
  ?>
</main>
  </div>
</body>
</html>
