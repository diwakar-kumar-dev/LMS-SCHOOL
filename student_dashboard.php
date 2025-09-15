<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['student_id'])) { header("Location: student_login.php"); exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Student Dashboard - LMS School</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="logo">
      <img src="school img_logo.jpg" alt="School Logo" onerror="this.style.visibility='hidden'">
      <div>
        <h2>LMS School</h2>
        <div class="small">Hello, <?= htmlspecialchars($_SESSION['student_name']) ?></div>
      </div>
    </div>
    <nav class="nav">
      <a href="student_dashboard.php">Home</a>
      <a href="Admissions_forms.php">Admission Form</a>
      <a href="Results.php">Result</a>
      <!-- <a href="class.php">Class</a> -->
      <a href="video.php">Video</a>
      <a href="logout.php">Logout</a>
    </nav>
  </aside>

  <main>
    <div class="header">
      
      <strong>Student Dashboard</strong>
    </div>
    <div class="content">
      <div class="card">
        <h2>Welcome Student</h2>
        <p class="small">Use the sidebar to access admission, classes, results, and videos.</p>
      </div>
    </div>
  </main>
</div>
</body>
</html>
