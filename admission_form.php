<?php
require_once "config.php";
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$info = $err = "";
$errors = []; // Add this line

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $student  = trim($_POST['student_name'] ?? '');
  $class    = trim($_POST['class_applied'] ?? '');
  $guardian = trim($_POST['guardian_name'] ?? '');
  $contact  = preg_replace('/\D/','', $_POST['contact'] ?? '');

  if ($student==='') {
    $errors['student_name'] = "Student name is required.";
  } elseif (!preg_match('/^[A-Za-z ]+$/', $student)) {
    $errors['student_name'] = "Only alphabets and spaces allowed.";
  }

  if ($class==='') {
    $errors['class_applied'] = "Class is required.";
  }

  if ($guardian==='') {
    $errors['guardian_name'] = "Guardian name is required.";
  } elseif (!preg_match('/^[A-Za-z ]+$/', $guardian)) {
    $errors['guardian_name'] = "Only alphabets and spaces allowed.";
  }

  if ($contact==='') {
    $errors['contact'] = "Contact is required.";
  } elseif (!preg_match('/^\d{10}$/', $contact)) {
    $errors['contact'] = "Contact must be exactly 10 digits.";
  }

  if (empty($errors)) {
    $stmt = $conn->prepare("INSERT INTO admissions (student_name, class_applied, guardian_name, contact) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $student, $class, $guardian, $contact);
    if ($stmt->execute()) {
      $info = "Admission form submitted ✅";
    } else {
      $err = "Failed to submit.";
    }
    $stmt->close();
  }
}
?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admission Form - Diwakar Public School</title>
  <!-- <link rel="stylesheet" href="assets/style.css"> -->
   <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="logo">
      <img src="school img_Logo.jpg" alt="logo" onerror="this.style.visibility='hidden'">
      <div>
        <h2>Diwakar Public School</h2>
        <div class="small">Admission</div>
      </div>
    </div>
    <nav class="nav">
      <a href="dashboard.php">Home</a>
      <a href="admission_form.php">Admission Form</a>
      <a href="#">Library</a>
      <a href="#">Department</a>
      <a href="#">Students</a>
      <a href="#">Teachers</a>
      <a href="#">Result</a>
      <a href="logout.php">Logout</a>
    </nav>
  </aside>

  <main>
    <div class="header"><strong>Student Admission Form</strong></div>
    <div class="content">
      <div class="card">
        <?php if($err): ?><div class="msg error"><?= htmlspecialchars($err) ?></div><?php endif; ?>
        <?php if($info): ?><div class="msg success"><?= htmlspecialchars($info) ?></div><?php endif; ?>

        <form method="post">
          <div class="form-grid">
            <div class="full"><input name="student_name" placeholder="Student full name *" value="<?= htmlspecialchars($_POST['student_name'] ?? '') ?>"
        class="<?= isset($errors['student_name']) ? 'error' : '' ?>"
        required> <?php if(isset($errors['student_name'])): ?>
        <small class="error-text"><?= $errors['student_name'] ?></small>
      <?php endif; ?>
    </div>
            <div><input name="class_applied" placeholder="Class applying for *"value="<?= htmlspecialchars($_POST['class_applied'] ?? '') ?>"
        class="<?= isset($errors['class_applied']) ? 'error' : '' ?>"
        required>
      <?php if(isset($errors['class_applied'])): ?>
        <small class="error-text"><?= $errors['class_applied'] ?></small>
      <?php endif; ?>
    </div>
            <div><input name="guardian_name" placeholder="Guardian name *"value="<?= htmlspecialchars($_POST['guardian_name'] ?? '') ?>"
        class="<?= isset($errors['guardian_name']) ? 'error' : '' ?>"
        required>
      <?php if(isset($errors['guardian_name'])): ?>
        <small class="error-text"><?= $errors['guardian_name'] ?></small>
      <?php endif; ?>
    </div>
            <div><input name="contact" maxlength="10" placeholder="Contact (10 digits) *" value="<?= htmlspecialchars($_POST['contact'] ?? '') ?>"
        class="<?= isset($errors['contact']) ? 'error' : '' ?>"
        required>
      <?php if(isset($errors['contact'])): ?>
        <small class="error-text"><?= $errors['contact'] ?></small>
      <?php endif; ?>
    </div>
          </div>
          <div class="actions">
            <button class="btn-primary" type="submit">Submit</button>
            <a class="btn-ghost" href="dashboard.php">Back</a>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>
</body>
</html>
