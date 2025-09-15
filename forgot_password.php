<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// PHPMailer Files include
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $stmt = $conn->prepare("SELECT admin_id FROM admins WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $code = rand(100000, 999999); // 6 digit OTP
        $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $upd = $conn->prepare("UPDATE admins SET reset_code=?, reset_expiry=? WHERE email=?");
        $upd->bind_param("sss", $code, $expiry, $email);
        $upd->execute();

       // ---------- PHPMailer Setup ----------
$mail = new PHPMailer(true);
try {
    // Debugging enable
    $mail->SMTPDebug = 2; 
    $mail->Debugoutput = 'html';

    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'kumrdiwakarraj@gmail.com'; 
    $mail->Password   = 'wnsjignfsjksakpk';   // App Password (no spaces)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 465;



            // Sender & Recipient
            $mail->setFrom('kumrdiwakarraj@gmail.com', 'LMS School');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Password Reset Code - LMS School";
            $mail->Body    = "<h3>Your password reset code is: <b>$code</b></h3>
                              <p>This code will expire in 10 minutes.</p>";

            $mail->send();

            $_SESSION['reset_email'] = $email;
            header("Location: verify_code.php");
            exit;

        } catch (Exception $e) {
            $msg = "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $msg = "Email not found.";
    }
}
?>
<!doctype html>
<html>
    <link rel="stylesheet" href="style.css">
<head>
    <div class="wrapper">
    <div class="card">
<title>Forgot Password</title></head>
<body>
<h2>Forgot Password</h2>
<?php if($msg): ?><p style="color:red;"><?= $msg ?></p><?php endif; ?>
    <div class="form-grid">
<form method="post">
    <div class="full">
  <input type="email" name="email" placeholder="Enter your email" required>
  </div>
  <div class="actions">
  <button type="submit">Send Code</button>
</div>
</div>
</div>
</div>
</form>
</body>
</html>
