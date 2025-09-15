<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit;
}

// Get student details from session
$std_id    = $_SESSION['student_id'];
$std_class = $_SESSION['student_class'] ?? null;
$std_roll  = $_SESSION['student_roll'] ?? null;

// Fetch results for this student (by class & roll_no)
// $results_by_class = [];
// if ($std_class && $std_roll) {
//     $stmt = $conn->prepare("SELECT * FROM results WHERE class = ? AND roll_no = ? ORDER BY uploaded_at DESC");
//     $stmt->bind_param("si", $std_class, $std_roll);
//     $stmt->execute();
//     $res = $stmt->get_result();

//     while($row = $res->fetch_assoc()) {
//         $results_by_class[$row['class']][] = $row;
//     }
// }

// Fetch results uploaded by admin
$stmt = $conn->prepare("SELECT * FROM results ORDER BY class, roll_no");
$stmt->execute();
$res = $stmt->get_result();

// Group results by class
$results_by_class = [];
while($row = $res->fetch_assoc()) {
    $results_by_class[$row['class']][] = $row;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Results - Student Dashboard</title>
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
            <a href="video.php">Video</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>

    <main>
        <div class="header"><strong>My Results</strong></div>
        <div class="content">
            <?php if(empty($results_by_class)): ?>
                <div class="card">
                    <p>No results uploaded yet.</p>
                </div>
            <?php else: ?>
                <?php foreach($results_by_class as $class => $results): ?>
                    <div class="card">
                        <h2><?= htmlspecialchars($class) ?></h2>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Roll No</th>
                                    <th>File</th>
                                    <th>Uploaded At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($results as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['title']) ?></td>
                                    <td><?= htmlspecialchars($r['roll_no']) ?></td>
                                    <td>
                                        <?php if(!empty($r['file_path'])): ?>
                                            <a href="<?= htmlspecialchars($r['file_path']) ?>" target="_blank">View</a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($r['uploaded_at']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
