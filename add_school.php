<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit; }

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $school_name = trim($_POST['school_name'] ?? '');
    $school_code = trim($_POST['school_code'] ?? '');

    if ($school_name == "" || $school_code == "") {
        $msg = "<p style='color:red;'>All fields are required!</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO school_list (school_name, school_code) VALUES (?, ?)");
        $stmt->bind_param("ss", $school_name, $school_code);

        if ($stmt->execute()) {
            $msg = "<p style='color:green;'>School added successfully!</p>";
        } else {
            $msg = "<p style='color:red;'>Error: ".$conn->error."</p>";
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
    <title>Add School</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-grid">
        <div class="content">
      <div class="card">
<h2>Add School Record</h2>
<?= $msg ?>
<form method="POST">
    
        <!-- <div class="full"> -->
    <label>School Name:</label>
    <input type="text" name="school_name" required><br><br>

    <label>School Code:</label>
    <input type="text" name="school_code" required><br><br>

    <button type="submit">Add School</button>
</div>
</div>  
</form>
</body>
</html>
