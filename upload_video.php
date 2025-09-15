<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { 
    header("Location: admin_login.php"); 
    exit; 
}

$msg = "";

// Form handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $class = trim($_POST['class']); // now only number like 1,2,3...

    if (empty($title) || empty($class)) {
        $msg = "<span style='color:red;'>Please enter title and select class.</span>";
    } elseif (isset($_FILES['video_file']) && $_FILES['video_file']['error'] == 0) {
        $target_dir = "uploads/videos/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $filename = time() . "_" . basename($_FILES["video_file"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["video_file"]["tmp_name"], $target_file)) {
            // save in DB
            $stmt = $conn->prepare("INSERT INTO videos (title, class, file_path, uploaded_by, uploaded_at) 
                                    VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssi", $title, $class, $target_file, $_SESSION['admin_id']);
            if ($stmt->execute()) {
                $msg = "<span style='color:green;'>Video uploaded successfully!</span>";
            } else {
                $msg = "<span style='color:red;'>Database error: " . $conn->error . "</span>";
            }
        } else {
            $msg = "<span style='color:red;'>Error uploading video.</span>";
        }
    } else {
        $msg = "<span style='color:red;'>No video file selected.</span>";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Upload Video</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
<?include "includes/sidebar_admin.php";?>
  <main>
    <div class="header"><strong>Upload Video</strong></div>
    <div class="content">
      <div class="card">
        <h2>Upload Teaching Videos</h2>
        <?php if (!empty($msg)) echo "<p>$msg</p>"; ?>

        <form method="post" enctype="multipart/form-data" class="form">
          <label>Video Title</label>
          <input type="text" name="title" placeholder="Video Title" required><br>

          <!-- Class Box -->
          <label>Select Class</label>
          <select name="class" required>
            <option value="">Select Class</option>
            <?php 
            for ($i=1; $i<=12; $i++) {
                echo "<option value='$i'>Class $i</option>";
            }
            ?>
          </select><br>

          <label>Upload Video</label>
          <input type="file" name="video_file" accept="video/*" required><br>

          <button type="submit" class="btn-primary">Upload</button>
        </form>
      </div>
    </div>
  </main>
</div>
</body>
</html>
