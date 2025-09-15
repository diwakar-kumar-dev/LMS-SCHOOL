<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit; }

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $std_id = trim($_POST['std_id']);
    $class = trim($_POST['class']);
    $roll_no = trim($_POST['roll_no']);

    if (isset($_FILES['result_file']) && $_FILES['result_file']['error'] == 0) {
        $target_dir = "uploads/results/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $filename = time() . "_" . basename($_FILES["result_file"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["result_file"]["tmp_name"], $target_file)) {
            // Save into DB
            $stmt = $conn->prepare("INSERT INTO results (title, std_id, class, roll_no, file_path, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sisssi", $title, $std_id, $class, $roll_no, $target_file, $_SESSION['admin_id']);
            $stmt->execute();
            $msg = "Result uploaded successfully!";
        } else {
            $msg = "Error uploading result.";
        }
    } else {
        $msg = "No result file selected.";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Upload Result</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
<?include "includes/sidebar_admin.php";?>
  <main>
    <div class="header"><strong>Upload Result</strong></div>
    <div class="content">
      <div class="card">
        <h2>Upload Student Results</h2>
        <?php if (!empty($msg)) echo "<p style='color:green;'>$msg</p>"; ?>
        <form method="post" enctype="multipart/form-data">
          <input type="text" name="title" placeholder="Result Title" required><br>
          <input type="text" name="std_id" placeholder="Student ID" required><br>
          <input type="text" name="class" placeholder="Class" required><br>
          <input type="text" name="roll_no" placeholder="Roll Number" required><br>
          <input type="file" name="result_file" accept=".pdf,.doc,.docx,.jpg,.png" required><br>
          <button type="submit" class="btn-primary">Upload</button>
        </form>
      </div>
    </div>
  </main>
</div>
</body>
</html>
