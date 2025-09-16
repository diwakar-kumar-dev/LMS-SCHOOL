<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit; }

$msg = "";

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['school_id'])) {
    $school_id = $_POST['school_id'];

    if ($_FILES['student_file']['error'] == 0) {
        $file = fopen($_FILES['student_file']['tmp_name'], "r");
        $row = 0; $fail_count = 0;

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $row++;
            if ($row == 1) continue; // Skip header row

            // Expected format: firstname,middlename,lastname,roll_no,class,email
            if (count($data) < 6) {
                $fail_count++;
                continue;
            }

            $firstname = trim($data[0]);
            $middlename = trim($data[1]);
            $lastname = trim($data[2]);
            $roll_no = trim($data[3]);
            $class = trim($data[4]);
            $email = trim($data[5]);

            if ($firstname == "" || $lastname == "" || $roll_no == "" || $class == "") {
                $fail_count++;
                continue;
            }

            $stmt = $conn->prepare("INSERT INTO student_list (school_id, firstname, middlename, lastname, roll_no, class, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssss", $school_id, $firstname, $middlename, $lastname, $roll_no, $class, $email);
            if (!$stmt->execute()) {
                $fail_count++;
            }
            $stmt->close();
        }
        fclose($file);

        if ($fail_count > 0) {
            $msg = "<p style='color:red;'>Upload completed but $fail_count row(s) failed due to wrong format.</p>";
        } else {
            $msg = "<p style='color:green;'>Data uploaded successfully!</p>";
        }
    } else {
        $msg = "<p style='color:red;'>File upload error!</p>";
    }
}

// Fetch schools for dropdown
$schools = $conn->query("SELECT id, school_name FROM school_list");
?>
<!doctype html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-grid">
        <div class="content">
      <div class="card">
<h2>Add Student Record</h2>
<?= $msg ?>
<form method="POST" enctype="multipart/form-data">
    <label>Select School:</label>
    <select name="school_id" required>
        <option value="">-- Select School --</option>
        <?php while($row = $schools->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['school_name']) ?></option>
        <?php endwhile; ?>
</select>

    <br><br>

    <label>Upload CSV File:</label>
    <input type="file" name="student_file" accept=".csv" required>
    <br><br>

    <button type="submit">Upload Students</button>
</form>
<p><strong>CSV Format:</strong> firstname,middlename,lastname,roll_no,class,email</p>
</div>
</div>
</div>
</body>
</html>
