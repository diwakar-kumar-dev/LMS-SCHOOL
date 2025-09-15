<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['student_id'])) { 
    header("Location: student_login.php"); 
    exit; 
}

$student_class = $_SESSION['student_class'] ?? null;
$videos = [];

if ($student_class) {
    $stmt = $conn->prepare("SELECT * FROM videos WHERE class = ? ORDER BY uploaded_at DESC");
$stmt->bind_param("s", $student_class);

    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $videos[] = $row;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Class Videos - Student</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="logo">
      <img src="logo.png" alt="logo" onerror="this.style.visibility='hidden'">
      <div>
        <h2>LMS School</h2>
        <div class="small">Hello, <?= htmlspecialchars($_SESSION['student_name']) ?></div>
      </div>
    </div>
    <nav class="nav">
      <a href="student_dashboard.php">Home</a>
      <a href="admission_form.php">Admission Form</a>
      <a href="results.php">Result</a>
      <a href="video.php" class="active">Video</a>
      <a href="logout.php">Logout</a>
    </nav>
  </aside>

  <main>
    <div class="header">
      <strong>Class Videos (Class <?= htmlspecialchars($student_class) ?>)</strong>

    </div>
    <div class="content">
      <?php if (empty($videos)): ?>
        <div class="card">
          <p>No videos uploaded yet for your class (<?= htmlspecialchars($student_class ?? "N/A") ?>).</p>
        </div>
      <?php else: ?>
        <?php foreach ($videos as $video): ?>
          <div class="card">
            <h3><?= htmlspecialchars($video['title']) ?></h3>
            <p class="small">Uploaded on <?= htmlspecialchars($video['uploaded_at']) ?></p>
            <video width="100%" controls>
              <source src="<?= htmlspecialchars($video['file_path']) ?>" type="video/mp4">
              Your browser does not support the video tag.
            </video>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
</div>
</body>
</html>
