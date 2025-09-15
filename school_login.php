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
        $stmt = $conn->prepare("SELECT id, school_id, username, email, password_hash FROM schools WHERE school_id=? OR username=? OR email=? LIMIT 1");
        $stmt->bind_param("sss", $login_user, $login_user, $login_user);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            if (password_verify($login_pass, $row['password_hash'])) {
                $_SESSION['school_id'] = $row['id'];
                $_SESSION['school_name'] = $row['username'];
                header("Location: school_dashboard.php");
                exit;
            }
        }
        $err = "Invalid School ID/Username/Email or Password.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>School Login - LMS</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="logo">
      <img src="school img_logo.jpg" alt="School Logo" onerror="this.style.visibility='hidden'">
    <h1>School Login</h1>
</div>
    <?php if($err): ?><div class="msg error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

    <form method="post">
      <div class="form-grid">
      <input name="login_user" placeholder="School ID / Username / Email" required>
      <input name="login_pass" type="password" placeholder="Password" required>
      <button type="submit" class="btn-primary">Login</button>
      <a href="school_signup.php" class="btn-ghost">Create account</a>
    </div>
    <div class="extra-actions">
        <a class="btn-link" href="forgot_password.php">Forgot Password?</a>
      </div>

    </form>
  </div>
</div>
</body>
</html>
