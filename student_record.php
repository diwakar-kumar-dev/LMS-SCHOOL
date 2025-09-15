<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit; }

// Form handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name       = trim($_POST['student_name']);
    $father     = trim($_POST['father_name']);
    $mother     = trim($_POST['mother_name']);
    $dob        = $_POST['dob'];
    $doa        = $_POST['doa'];
    $class      = trim($_POST['class']);
    $roll_no    = trim($_POST['roll_no']);

    $stmt = $conn->prepare("INSERT INTO students_record 
        (student_name, father_name, mother_name, dob, doa, class, roll_no, created_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $name, $father, $mother, $dob, $doa, $class, $roll_no, $_SESSION['admin_id']);

    if ($stmt->execute()) {
        $msg = "Student record added successfully!";
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>School Record</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
<?include "includes/sidebar_admin.php";?>

  <main>
    <div class="header"><strong>School Record</strong></div>
    <div class="content">
      <div class="card">
        <h2>Add Student Record</h2>
        <?php if (!empty($msg)) echo "<p style='color:green;'>$msg</p>"; ?>
        <form method="post">
          <input type="text" name="std_id" placeholder="Student ID" required><br>
          <input type="text" name="student_name" placeholder="Student Name" required><br>
          <input type="text" name="father_name" placeholder="Father Name" required><br>
          <input type="text" name="mother_name" placeholder="Mother Name" required><br>
          <label>DOB: </label><br>
          <input type="date" name="dob" required><br>
          <label>DOA:</label><br>
          <input type="date" name="doa" required><br>
          
          <!-- Class Dropdown -->
          <select name="class" required>
            <option value="">Select Class</option>
            <option value="Class 1">Class 1</option>
            <option value="Class 2">Class 2</option>
            <option value="Class 3">Class 3</option>
            <option value="Class 4">Class 4</option>
            <option value="Class 5">Class 5</option>
            <option value="Class 6">Class 6</option>
            <option value="Class 7">Class 7</option>
            <option value="Class 8">Class 8</option>
            <option value="Class 9">Class 9</option>
            <option value="Class 10">Class 10</option>
            <option value="Class 11">Class 11</option>
            <option value="Class 12">Class 12</option>
          </select><br>

          <input type="text" name="roll_no" placeholder="Roll Number" required><br>
          <button type="submit" class="btn-primary">Save Record</button>
        </form>
      </div>
    </div>
  </main>
</div>
</body>
</html>
