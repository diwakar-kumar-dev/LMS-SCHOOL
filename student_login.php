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
    // NOTE: fetch all required columns (class, roll_no also)
    $stmt = $conn->prepare("
        SELECT id, student_id, username, email, password_hash, class, roll_no 
        FROM students 
        WHERE student_id=? OR username=? OR email=? 
        LIMIT 1
    ");
    $stmt->bind_param("sss", $login_user, $login_user, $login_user);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
      if (password_verify($login_pass, $row['password_hash'])) {
        // ✅ Set session variables
        $_SESSION['student_id']    = $row['student_id'];
        $_SESSION['student_name']  = $row['username'];
        $_SESSION['student_class'] = $row['class'];
        $_SESSION['student_roll']  = $row['roll_no'];

        header("Location: student_dashboard.php");
        exit;
      }
    }
    $err = "Invalid Student ID/Username/Email or Password.";
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Student Login - LMS School</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="logo">
      <img src="school img_logo.jpg" alt="School Logo" onerror="this.style.visibility='hidden'">
    <h1>Student Login</h1>
</div>
    <?php if($err): ?><div class="msg error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

    <form method="post">
      <div class="form-grid">
        <div class="full">
          <input name="login_user" placeholder="Student ID / Username / Email" required>
        </div>
        <div class="full">
          <input name="login_pass" type="password" placeholder="Password" required>
        </div>
      </div>
      <div class="actions">
        <button class="btn-primary" type="submit">Login</button>
        <a class="btn-ghost" href="student_signup.php">Create account</a>
      </div>
      <div class="extra-actions">
        <a class="btn-link" href="forgot_password.php">Forgot Password?</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
