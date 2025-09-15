<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - LMS School</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="logo">
      <img src="school img_logo.jpg" alt="School Logo" onerror="this.style.visibility='hidden'">
      <div>
        <h2>LMS School</h2>
        <div class="small">Hello, <?= htmlspecialchars($_SESSION['admin_name']) ?></div>
      </div>
    </div>
    <nav class="nav">
      <a href="admin_dashboard.php">Home</a>
      <!-- <a href="school_record.php">School Record</a> -->
      <a href="student_record.php">Student Record</a>
      <a href="upload_result.php">Upload Result</a>
      <a href="upload_video.php">Upload Video</a>
      <a href="logout.php">Logout</a>
    </nav>
  </aside>

  <main>
    <div class="header">
      <strong>Admin Dashboard</strong>
    </div>
    <div class="content">
      <div class="card">
        <h2>Welcome Admin</h2>
        <p class="small"></p>
      </div>
    </div>
  </main>
</div>
</body>
</html>
