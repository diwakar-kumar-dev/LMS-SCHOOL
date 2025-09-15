<?php
require_once "config.php";

$err = $ok = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Sanitize
  $firstname = trim($_POST['firstname'] ?? '');
  $middlename = trim($_POST['middlename'] ?? '');
  $lastname = trim($_POST['lastname'] ?? '');
  $gender = $_POST['gender'] ?? '';
  $mobile = preg_replace('/\D/','', $_POST['mobile'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirm  = $_POST['confirm_password'] ?? '';

  // Server-side validation
  if (!preg_match('/^[A-Za-z]+$/', $firstname)) $err = "First name alphabets only.";
  elseif ($middlename !== "" && !preg_match('/^[A-Za-z]+$/', $middlename)) $err = "Middle name alphabets only.";
  elseif (!preg_match('/^[A-Za-z]+$/', $lastname)) $err = "Last name alphabets only.";
  elseif (!in_array($gender, ['Male','Female','Other'])) $err = "Select gender.";
  elseif (!preg_match('/^\d{10}$/', $mobile)) $err = "Mobile must be 10 digits.";
  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err = "Invalid email.";
  elseif (strlen($username) < 4) $err = "Username min 4 chars.";
  elseif (strlen($password) < 6) $err = "Password min 6 chars.";
  elseif ($password !== $confirm) $err = "Password & Confirm must match.";
  else {
    // Check unique email/username
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? OR username=? LIMIT 1");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
      $err = "Email or Username already exists.";
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt2 = $conn->prepare("INSERT INTO users (firstname, middlename, lastname, gender, mobile, email, username, password_hash) VALUES (?,?,?,?,?,?,?,?)");
      $stmt2->bind_param("ssssssss", $firstname, $middlename, $lastname, $gender, $mobile, $email, $username, $hash);
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
  <title>Signup - Diwakar Public School</title>
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
          <h1>Diwakar Public School</h1>
          <div class="small">Create your account</div>
        </div>
      </div>

      <?php if($err): ?><div class="msg error"><?= htmlspecialchars($err) ?></div><?php endif; ?>
      <?php if($ok): ?><div class="msg success"><?= htmlspecialchars($ok) ?> <a class="link" href="login.php">Login here</a>.</div><?php endif; ?>

      <form id="signupForm" method="post" onsubmit="return validateSignup()">
        <div class="form-grid">
          <div><input id="firstname" name="firstname" placeholder="First name *" required></div>
          <div><input id="middlename" name="middlename" placeholder="Middle name (optional)"></div>
          <div><input id="lastname"  name="lastname" placeholder="Last name *" required></div>
          <div>
            <select id="gender" name="gender" required>
              <option value="">Select gender *</option>
              <option>Male</option>
              <option>Female</option>
              <option>Other</option>
            </select>
          </div>
          <div><input id="mobile" name="mobile" maxlength="10" placeholder="Mobile (10 digits) *" required></div>
          <div><input id="email" name="email" type="email" placeholder="Email (unique) *" required></div>
          <div class="full"><input id="username" name="username" placeholder="Username (unique) *" required></div>
          <div><input id="password" name="password" type="password" placeholder="Password *" required></div>
          <div><input id="confirm_password" name="confirm_password" type="password" placeholder="Confirm password *" required></div>
        </div>

        <div class="actions">
          <button class="btn-primary" type="submit">Sign up</button>
          <a class="btn-ghost" href="login.php" >Already have account? </a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
