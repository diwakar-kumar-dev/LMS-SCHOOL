<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['verified_email'])) {
    header("Location: forgot_password.php");
    exit;
}

$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pass1 = $_POST['password'];
    $pass2 = $_POST['confirm_password'];

    if ($pass1 !== $pass2) {
        $msg = "Passwords do not match.";
    } elseif (strlen($pass1) < 6) {
        $msg = "Password must be at least 6 characters.";
    } else {
        $hash = password_hash($pass1, PASSWORD_DEFAULT);
        $email = $_SESSION['verified_email'];

        $stmt = $conn->prepare("UPDATE admins SET password_hash=?, reset_code=NULL, reset_expiry=NULL WHERE email=?");
        $stmt->bind_param("ss", $hash, $email);
        $stmt->execute();

        unset($_SESSION['reset_email'], $_SESSION['verified_email']);
        header("Location: admin_login.php?reset=success");
        exit;
    }
}
?>
<!doctype html>
<html>
    <link rel="stylesheet" href="style.css">
<head><title>Reset Password</title></head>
<body>
     <div class="wrapper">
    <div class="card">
<h2>Reset Password</h2>
<?php if($msg): ?><p style="color:red;"><?= $msg ?></p><?php endif; ?>
<form method="post">
  <input type="password" name="password" placeholder="New Password" required><br>
  <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
  <button type="submit">Reset Password</button>
</div>
</div>
</form>
</body>
</html>
