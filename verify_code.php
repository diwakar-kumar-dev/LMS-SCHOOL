<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit;
}

$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = trim($_POST['code']);
    $email = $_SESSION['reset_email'];

    $stmt = $conn->prepare("SELECT reset_code, reset_expiry FROM admins WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        if ($row['reset_code'] === $code && strtotime($row['reset_expiry']) > time()) {
            $_SESSION['verified_email'] = $email;
            header("Location: reset_password.php");
            exit;
        } else {
            $msg = "Invalid or expired code.";
        }
    }
}
?>
<!doctype html>
<html>
<head><title>Verify Code</title></head>
<body>
<h2>Enter Verification Code</h2>
<?php if($msg): ?><p style="color:red;"><?= $msg ?></p><?php endif; ?>
<form method="post">
  <input type="text" name="code" placeholder="6-digit code" maxlength="6" required>
  <button type="submit">Verify</button>
</form>
</body>
</html>
