<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['school_id'])) { header("Location: school_login.php"); exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>School Dashboard - LMS School</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="logo">
      <img src="logo.png" alt="logo" onerror="this.style.visibility='hidden'">
      <div>
        <div class="logo">
      <img src="school img_logo.jpg" alt="School Logo" onerror="this.style.visibility='hidden'">
        <h2>LMS School</h2>
</div>
        <div class="small">Hello, <?= htmlspecialchars($_SESSION['school_name']) ?></div>
      </div>
    </div>
    <nav class="nav">
      <a href="school_dashboard.php">Home</a>
      <!-- <a href="library.php">Library</a> -->
      <a href="department.php">Department</a>
      <a href="students_record.php">Student Record</a>
      <a href="result.php">Result</a>
      <a href="logout.php">Logout</a>
    </nav>
  </aside>

  <main>
    <div class="header">
      <strong>School Dashboard</strong>
    </div>
    <div class="content">
      <div class="card">
        <h2>Welcome School</h2>
        <p class="small">Use the sidebar to manage library, students, and results.</p>
      </div>
    </div>
  </main>
</div>
</body>
</html>
