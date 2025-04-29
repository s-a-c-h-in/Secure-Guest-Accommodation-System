<?php
session_start();
if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Guest Accommodation</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Welcome to Secure Guest Accommodation</h1>
    <a href="register.php">Register</a> | <a href="login.php">Login</a>
</body>
</html>