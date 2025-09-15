<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>School Record</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
<?include "includes/sidebar_admin.php";?>


  <main>
    <div class="header"><strong>School Record</strong></div>
    <div class="content">
      <div class="card">
        <h2>All School Records</h2>
        <p class="small">Yahaan aap school ke records list/update kar sakte ho.</p>
      </div>
    </div>
  </main>
</div>
</body>
</html>
