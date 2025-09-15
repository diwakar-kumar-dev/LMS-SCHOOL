<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name     = trim($_POST['student_name']);
    $father   = trim($_POST['father_name']);
    $mother   = trim($_POST['mother_name']);
    $dob      = $_POST['dob'];
    $class    = trim($_POST['class']);
    $roll_no  = trim($_POST['roll_no']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);

    // agar student login hai to uska id save hoga, warna NULL save hoga
    $created_by = $_SESSION['student_id'] ?? null;

    $stmt = $conn->prepare("INSERT INTO students_record 
        (student_name, father_name, mother_name, dob, class, roll_no, email, phone, created_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssi", $name, $father, $mother, $dob, $class, $roll_no, $email, $phone, $created_by);

    if ($stmt->execute()) {
        $msg = "✅ Admission form submitted successfully!";
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admission Form - LMS School</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="logo" onerror="this.style.visibility='hidden'">
            <div>
                <h2>LMS School</h2>
                <?php if(isset($_SESSION['student_name'])): ?>
                  <div class="small">Hello, <?= htmlspecialchars($_SESSION['student_name']) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <nav class="nav">
            <a href="student_dashboard.php">Home</a>
            <a href="Admissions_form.php">Admission Form</a>
            <a href="Results.php">Result</a>
            <a href="video.php">Video</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>

    <main>
        <div class="header"><strong>Admission Form</strong></div>
        <div class="content">
            <div class="card">
                <h2>Fill Admission Details</h2>
                <?php if($msg): ?>
                    <p style="color: green;"><?= htmlspecialchars($msg) ?></p>
                <?php endif; ?>
                <form method="post">
                    <input type="text" name="student_name" placeholder="Student Name" required><br>
                    <input type="text" name="father_name" placeholder="Father Name" required><br>
                    <input type="text" name="mother_name" placeholder="Mother Name" required><br>
                    <label>DOB:</label><br>
                    <input type="date" name="dob" required><br>

                    <!-- Class Dropdown -->
                    <select name="class" required>
                        <option value="">Select Class</option>
                        <?php
                        for($i=1;$i<=12;$i++){
                            echo "<option value='Class $i'>Class $i</option>";
                        }
                        ?>
                    </select><br>

                    <input type="text" name="roll_no" placeholder="Roll Number" required><br>
                    <input type="email" name="email" placeholder="Email" required><br>
                    <input type="text" name="phone" placeholder="Phone Number" required><br>
                    <button type="submit" class="btn-primary">Submit Admission Form</button>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>
