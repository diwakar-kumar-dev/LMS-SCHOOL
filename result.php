<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['school_id'])) { header("Location: school_login.php"); exit; }

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
    <title>Student Results - School Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="logo" onerror="this.style.visibility='hidden'">
            <div>
                <h2>LMS School</h2>
                <div class="small">Hello, <?= htmlspecialchars($_SESSION['school_name']) ?></div>
            </div>
        </div>
        <nav class="nav">
            <a href="school_dashboard.php">Home</a>
            <!-- <a href="library.php">Library</a> -->
            <a href="department.php">Department</a>
            <a href="student_record.php">Student Record</a>
            <a href="result.php">Result</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main>
        <div class="header"><strong>Student Results</strong></div>
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
                                    <th>Std ID</th>
                                    <th>Title</th>
                                    <th>Roll No</th>
                                    <th>File</th>
                                    <th>Uploaded At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($results as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['std_id']) ?></td>
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
