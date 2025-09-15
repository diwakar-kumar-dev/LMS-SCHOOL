<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$err = $ok = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $school_id = trim($_POST['school_id'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    // ---------- VALIDATIONS ----------
    if (!preg_match('/^[A-Za-z0-9_-]{4,20}$/', $school_id)) {
        $err = "School ID must be 4-20 characters (letters, numbers, - , _).";
    } elseif (!preg_match('/^[A-Za-z]+$/', $firstname)) {
        $err = "First name alphabets only.";
    } elseif (!preg_match('/^[A-Za-z]+$/', $lastname)) {
        $err = "Last name alphabets only.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = "Invalid email.";
    } elseif (strlen($username) < 4) {
        $err = "Username min 4 chars.";
    } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{4,}$/', $username)) { 
        $err = "Username must contain alphabets, numbers & special characters.";
    } elseif (strlen($password) < 6) {
        $err = "Password min 6 chars.";
    } elseif ($password !== $confirm) {
        $err = "Password & Confirm password do not match.";
    } else {
        // ---------- CHECK DUPLICATE ----------
        $stmt = $conn->prepare("SELECT id FROM schools WHERE school_id=? OR email=? OR username=? LIMIT 1");
        $stmt->bind_param("sss", $school_id, $email, $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $err = "School ID, Email or Username already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $conn->prepare("INSERT INTO schools (school_id, firstname, lastname, email, username, password_hash) VALUES (?,?,?,?,?,?)");
            $stmt2->bind_param("ssssss", $school_id, $firstname, $lastname, $email, $username, $hash);

            if ($stmt2->execute()) {
                $ok = "✅ Signup successful! Please login.";
            } else {
                $err = "Signup failed. Try again.";
            }
            $stmt2->close();
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>School Signup - LMS</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="logo">
      <img src="school img_logo.jpg" alt="School Logo" onerror="this.style.visibility='hidden'">
      <h1>School Signup</h1>
    </div>

    <?php if($err): ?>
      <div class="msg error"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <?php if($ok): ?>
      <div class="msg success">
        <?= htmlspecialchars($ok) ?> <a href="school_login.php">Login here</a>.
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="form-grid">
        <input name="school_id" 
               placeholder="School ID (unique) *" 
               required 
               value="<?= htmlspecialchars($school_id ?? '') ?>">

        <input name="firstname" 
               placeholder="First Name *" 
               required 
               value="<?= htmlspecialchars($firstname ?? '') ?>">

        <input name="lastname" 
               placeholder="Last Name *" 
               required 
               value="<?= htmlspecialchars($lastname ?? '') ?>">

        <input name="email" 
               type="email" 
               placeholder="Email (unique) *" 
               required 
               value="<?= htmlspecialchars($email ?? '') ?>">

        <input name="username" 
               placeholder="Username (unique) *" 
               required 
               value="<?= htmlspecialchars($username ?? '') ?>">

        <input name="password" 
               type="password" 
               placeholder="Password *" 
               required>

        <input name="confirm_password" 
               type="password" 
               placeholder="Confirm Password *" 
               required>

        <button type="submit" class="btn-primary">Sign Up</button>
        <a href="school_login.php" class="btn-ghost">Already have account?</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
