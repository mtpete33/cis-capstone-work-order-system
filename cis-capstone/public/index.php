<?php 
require_once __DIR__ . '/../config/session.php';
requireLogin();
$user = currentUser();
?>

<!DOCTYPE html>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Work Order System</title>

</head>

<body>
  <h1>Work Order System</h1>
  <p>Logged in as <strong><?= htmlspecialchars($user['email'] ?? '') ?> </strong></p>
  <p><a href="/api/auth/logout.php">Logout</a></p>
  <hr>
  <p>Dashboard Placeholder</p>

</body>

</html>


   