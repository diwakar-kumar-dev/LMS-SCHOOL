<?php
// config.php
$host = "localhost";
$user = "root";
$pass = "";           // XAMPP/WAMP default empty; else set it
$db   = "school_portal";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
