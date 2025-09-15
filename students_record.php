<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['school_id'])) { header("Location: school_login.php"); exit; }

// Fetch student records uploaded by admin
$stmt = $conn->prepare("SELECT * FROM students_record ORDER BY class, roll_no");
$stmt->execute();
$res = $stmt->get_result();

$records_by_class = [];
while($row = $res->fetch_assoc()) {
    $records_by_class[$row['class']][] = $row;
}
?>
<div class="card">
  <link rel="stylesheet" href="style.css">
  <h2>Student Records</h2>
  <?php foreach($records_by_class as $class => $students): ?>
    <h3><?= htmlspecialchars($class) ?></h3>
    <table class="table">
      <tr>
        <th>Roll No</th>
        <th>Student Name</th>
        <th>Father Name</th>
        <th>Mother Name</th>
        <th>DOB</th>
        <th>DOA</th>
      </tr>
      <?php foreach($students as $s): ?>
      <tr>
        <td><?= htmlspecialchars($s['roll_no']) ?></td>
        <td><?= htmlspecialchars($s['student_name']) ?></td>
        <td><?= htmlspecialchars($s['father_name']) ?></td>
        <td><?= htmlspecialchars($s['mother_name']) ?></td>
        <td><?= htmlspecialchars($s['dob']) ?></td>
        <td><?= htmlspecialchars($s['doa']) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endforeach; ?>
</div>
