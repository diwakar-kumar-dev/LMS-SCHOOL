<?php
require_once "config.php";

$err = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $login_user = trim($_POST['login_user'] ?? ''); // can be username or email
  $login_pass = $_POST['login_pass'] ?? '';

  if (strlen($login_user) < 3 || strlen($login_pass) < 6) {
    $err = "Please enter valid credentials.";
  } else {
    $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username=? OR email=? LIMIT 1");
    $stmt->bind_param("ss", $login_user, $login_user);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
      if (password_verify($login_pass, $row['password_hash'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        header("Location: dashboard.php");
        exit;
      }
    }
    $err = "Invalid username/email or password.";
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login - Diwakar Public School</title>
  <!-- <link rel="style.css" href="style.css"> -->
   <link rel="stylesheet" href="style.css">
  <script defer src="validate.js"></script>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="logo">
      <img src="school img_Logo.jpg" alt="logo" onerror="this.style.visibility='hidden'">
      <div>
        <h1>LMS School</h1>
        <div class="small">Welcome back</div>
      </div>
    </div>

    <?php if($err): ?><div class="msg error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

    <form id="loginForm" method="post" onsubmit="return validateLogin()">
      <div class="form-grid">
        <div class="full"><input id="login_user" name="login_user" placeholder="Username or Email" required></div>
        <div class="full"><input id="login_pass" name="login_pass" type="password" placeholder="Password" required></div>
      </div>
      <div class="actions">
        <button class="btn-primary" type="submit">Login</button>
        <a class="btn-ghost" href="signup.php">Create account</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
