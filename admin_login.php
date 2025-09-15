<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$err = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $login_user = trim($_POST['login_user'] ?? '');
  $login_pass = $_POST['login_pass'] ?? '';

  if (strlen($login_user) < 3 || strlen($login_pass) < 6) {
    $err = "Please enter valid credentials.";
  } else {
    $stmt = $conn->prepare("SELECT admin_id, username, password_hash
                             FROM admins
                             WHERE admin_id=? OR username=? OR email=? LIMIT 1");
    $stmt->bind_param("sss", $login_user, $login_user, $login_user);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
      if (password_verify($login_pass, $row['password_hash'])) {
        $_SESSION['admin_id'] = $row['admin_id'];
        $_SESSION['admin_name'] = $row['username'];
        header("Location: admin_dashboard.php");
        exit;
      }
    }
    $err = "Invalid Admin ID/Username/Email or Password.";
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login - LMS School</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="logo">
      <img src="school img_logo.jpg" alt="School Logo" onerror="this.style.visibility='hidden'">
    
    <h1>Admin Login</h1>
</div>
    <?php if($err): ?><div class="msg error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

    <form method="post">
      <div class="form-grid">
        <div class="full">
          <input name="login_user" placeholder="Admin ID / Username / Email" required>
        </div>
        <div class="full">
          <input name="login_pass" type="password" placeholder="Password" required>
        </div>
      </div>
      <div class="actions">
        <button class="btn-primary" type="submit">Login</button>
        <a class="btn-ghost" href="admin_signup.php">Create account</a>
      </div>
      <div class="extra-actions">
        <a class="btn-link" href="forgot_password.php">Forgot Password?</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
