<?php
require_once "config.php";
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard - Diwakar Public School</title>
  <!-- <link rel="stylesheet" href="assets/style.css"> -->
   <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="logo">
      <img src="school img_Logo.jpg" alt="logo" onerror="this.style.visibility='hidden'">
      <div>
        <h2>Diwakar Public School</h2>
        <div class="small">Hello, <?= htmlspecialchars($_SESSION['username']) ?></div>
      </div>
    </div>
    <nav class="nav">
      <a href="dashboard.php">Home</a>
      <a href="admission_form.php">Admission Form</a>
      <a href="#">Library</a>
      <a href="#">Department</a>
      <a href="#">Students</a>
      <a href="#">Teachers</a>
      <a href="#">Result</a>
      <a href="logout.php">Logout</a>
    </nav>
  </aside>

  <main>
    <div class="header">
      <div><strong>Dashboard</strong></div>
      <div class="small">Secure area</div>
    </div>
    <div class="content">
      <div class="card">
        <h2>Welcome to Diwakar Public School</h2>
        <p class="small">Use the left menu to navigate. Click <strong>Admission Form</strong> to open the student admission page.</p>
      </div>
    </div>
  </main>
</div>
</body>
</html>
