<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$err = $ok = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $firstname = trim($_POST['firstname'] ?? '');
  $middlename = trim($_POST['middlename'] ?? '');
  $lastname = trim($_POST['lastname'] ?? '');
  $gender = $_POST['gender'] ?? '';
  $mobile = preg_replace('/\D/','', $_POST['mobile'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirm  = $_POST['confirm_password'] ?? '';

  // ✅ Validation
  if (!preg_match('/^[A-Za-z]+$/', $firstname)) $err = "First name alphabets only.";
  elseif ($middlename !== "" && !preg_match('/^[A-Za-z]+$/', $middlename)) $err = "Middle name alphabets only.";
  elseif (!preg_match('/^[A-Za-z]+$/', $lastname)) $err = "Last name alphabets only.";
  elseif (!in_array($gender, ['Male','Female','Other'])) $err = "Select gender.";
  elseif (!preg_match('/^\d{10}$/', $mobile)) $err = "Mobile must be 10 digits.";
  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err = "Invalid email.";
elseif (strlen($username) < 4) $err = "Username min 4 chars.";
elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{4,}$/', $username)) 
  $err = "Username must contain alphabets, numbers & special characters.";

elseif (strlen($password) < 6) $err = "Password min 6 chars.";

  elseif ($password !== $confirm) $err = "Password & Confirm must match.";
  else {
    // ✅ Check unique email/username
    $stmt = $conn->prepare("SELECT id FROM students WHERE email=? OR username=? LIMIT 1");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $err = "Email or Username already exists.";
    } else {
      // ✅ Generate Unique Student ID
      $year = date("Y");
      $prefix = "STU".$year;
      $res = $conn->query("SELECT COUNT(*) as total FROM students WHERE student_id LIKE '$prefix%'");
      $row = $res->fetch_assoc();
      $next = str_pad($row['total'] + 1, 4, "0", STR_PAD_LEFT);
      $student_id = $prefix.$next;

      // ✅ Save Student
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt2 = $conn->prepare("INSERT INTO students (student_id, firstname, middlename, lastname, gender, mobile, email, username, password_hash) VALUES (?,?,?,?,?,?,?,?,?)");
      $stmt2->bind_param("sssssssss", $student_id, $firstname, $middlename, $lastname, $gender, $mobile, $email, $username, $hash);

      if ($stmt2->execute()) {
        $ok = "✅ Signup successful! Your Student ID is <b>$student_id</b>. Please login.";
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
  <title>Student Signup - LMS School</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="wrapper">
    <div class="card">
      <div class="logo">
      <img src="school img_logo.jpg" alt="School Logo" onerror="this.style.visibility='hidden'">
      <h1>Student Signup</h1>
</div>
      <?php if($err): ?><div class="msg error"><?= htmlspecialchars($err) ?></div><?php endif; ?>
      <?php if($ok): ?><div class="msg success"><?= $ok ?> <a href="student_login.php">Login here</a>.</div><?php endif; ?>

      <form method="post">
  <div class="form-grid">
    <div>
      <input name="firstname" 
             placeholder="First name *" 
             required 
             value="<?= htmlspecialchars($firstname ?? '') ?>">
    </div>

    <div>
      <input name="middlename" 
             placeholder="Middle name" 
             value="<?= htmlspecialchars($middlename ?? '') ?>">
    </div>

    <div>
      <input name="lastname" 
             placeholder="Last name *" 
             required 
             value="<?= htmlspecialchars($lastname ?? '') ?>">
    </div>

    <div>
      <select name="gender" required>
        <option value="">Select gender *</option>
        <option value="Male" >Male</option>
        <option value="Female" >Female</option>
        <option value="Other" >Other</option>
      </select>
    </div>

    <div>
      <input name="mobile" 
             maxlength="10" 
             placeholder="Mobile (10 digits) *" 
             required 
             value="<?= htmlspecialchars($mobile ?? '') ?>">
    </div>

    <div>
      <input name="email" 
             type="email" 
             placeholder="Email (unique) *" 
             required 
             value="<?= htmlspecialchars($email ?? '') ?>">
    </div>

    <div class="full">
      <input name="username" 
             placeholder="Username (unique) *" 
             required 
             value="<?= htmlspecialchars($username ?? '') ?>">
    </div>

    <div>
      <input name="password" 
             type="password" 
             placeholder="Password *" 
             required>
    </div>

    <div>
      <input name="confirm_password" 
             type="password" 
             placeholder="Confirm password *" 
             required>
    </div>
  </div>

  <div class="actions">
    <button class="btn-primary" type="submit">Sign up</button>
    <a class="btn-ghost" href="student_login.php">Already have account?</a>
  </div>
</form>

    </div>
  </div>
</body>
</html>
