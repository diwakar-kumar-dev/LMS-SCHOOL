<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['school_id'])) { header("Location: school_login.php"); exit; }

$school_id = $_SESSION['school_id'];
$msg = "";

/* === Function for Validation === */
function validateStudent($firstname, $lastname, $roll_no, $class, $email = "") {
    if (empty($firstname) || empty($lastname) || empty($roll_no) || empty($class)) {
        return "All required fields must be filled.";
    }
    if (!preg_match("/^[a-zA-Z]+$/", $firstname)) {
        return "First name must contain only letters.";
    }
    if (!preg_match("/^[a-zA-Z]+$/", $lastname)) {
        return "Last name must contain only letters.";
    }
    if (!preg_match("/^[0-9a-zA-Z_-]+$/", $roll_no)) {
        return "Roll No must be alphanumeric.";
    }
    if (!preg_match("/^[0-9a-zA-Z]+$/", $class)) {
        return "Class must be alphanumeric.";
    }
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format.";
    }
    return "";
}

/* ====== Handle CSV Upload ====== */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['student_file'])) {
    if ($_FILES['student_file']['error'] == 0) {
        $file_ext = strtolower(pathinfo($_FILES['student_file']['name'], PATHINFO_EXTENSION));
        if ($file_ext !== "csv") {
            $msg = "<p style='color:red;'>Only CSV files are allowed!</p>";
        } else {
            $file = fopen($_FILES['student_file']['tmp_name'], "r");
            $row = 0; $fail_count = 0; $success_count = 0;

            while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                $row++;
                if ($row == 1) continue; // Skip header
                if (count($data) < 5) { $fail_count++; continue; }

                $firstname = trim($data[0]);
                $middlename = trim($data[1]);
                $lastname  = trim($data[2]);
                $roll_no   = trim($data[3]);
                $class     = trim($data[4]);
                $email     = trim($data[5] ?? '');

                $error = validateStudent($firstname, $lastname, $roll_no, $class, $email);
                if ($error) { $fail_count++; continue; }

                // Check duplicate Roll No in same school
                $check = $conn->prepare("SELECT id FROM student_list WHERE roll_no=? AND school_id=?");
                $check->bind_param("si", $roll_no, $school_id);
                $check->execute();
                $check->store_result();
                if ($check->num_rows > 0) { $fail_count++; $check->close(); continue; }
                $check->close();

                $stmt = $conn->prepare("INSERT INTO student_list 
                    (school_id, firstname, middlename, lastname, roll_no, class, email) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssss", $school_id, $firstname, $middlename, $lastname, $roll_no, $class, $email);
                if ($stmt->execute()) { $success_count++; } else { $fail_count++; }
                $stmt->close();
            }
            fclose($file);

            $msg = "<p style='color:green;'>$success_count students uploaded successfully.</p>";
            if ($fail_count > 0) $msg .= "<p style='color:red;'>$fail_count row(s) failed validation.</p>";
        }
    } else {
        $msg = "<p style='color:red;'>File upload error!</p>";
    }
}

/* ====== Handle Add Single Student ====== */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])) {
    $firstname = trim($_POST['firstname']);
    $middlename = trim($_POST['middlename']);
    $lastname = trim($_POST['lastname']);
    $roll_no = trim($_POST['roll_no']);
    $class = trim($_POST['class']);
    $email = trim($_POST['email']);

    $error = validateStudent($firstname, $lastname, $roll_no, $class, $email);
    if ($error) {
        $msg = "<p style='color:red;'>$error</p>";
    } else {
        // Check duplicate Roll No
        $check = $conn->prepare("SELECT id FROM student_list WHERE roll_no=? AND school_id=?");
        $check->bind_param("si", $roll_no, $school_id);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $msg = "<p style='color:red;'>Duplicate Roll No for this school.</p>";
        } else {
            $stmt = $conn->prepare("INSERT INTO student_list 
                (school_id, firstname, middlename, lastname, roll_no, class, email) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssss", $school_id, $firstname, $middlename, $lastname, $roll_no, $class, $email);
            if ($stmt->execute()) {
                $msg = "<p style='color:green;'>Student added successfully!</p>";
            } else {
                $msg = "<p style='color:red;'>Error: ".$conn->error."</p>";
            }
            $stmt->close();
        }
        $check->close();
    }
}

/* ====== Handle Delete ====== */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM student_list WHERE id=? AND school_id=?");
    $stmt->bind_param("ii", $id, $school_id);
    if ($stmt->execute()) {
        $msg = "<p style='color:green;'>Student removed successfully!</p>";
    } else {
        $msg = "<p style='color:red;'>Failed to delete student.</p>";
    }
    $stmt->close();
}

/* ====== Handle Search/Filter ====== */
$search = trim($_GET['search'] ?? "");
$filter_sql = "";
$params = [$school_id];
$types = "i";

if ($search !== "") {
    $searchLike = "%$search%";
    $filter_sql = " AND (s.id LIKE ? OR s.roll_no LIKE ? OR s.firstname LIKE ? OR s.lastname LIKE ?)";
    $params = array_merge([$school_id], array_fill(0, 4, $searchLike));
    $types = "issss";
}

/* ====== Fetch Students ====== */
$sql = "SELECT s.id, s.firstname, s.middlename, s.lastname, s.roll_no, s.class, s.email, sc.school_name 
        FROM student_list s 
        JOIN school_list sc ON s.school_id = sc.id 
        WHERE s.school_id = ? $filter_sql
        ORDER BY s.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!doctype html>
<html>
<head>
    <title>Student Records</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: center; }
        th { background: #f2f2f2; }
        form.search-box { margin: 15px 0; }
        input[type=text], input[type=email] { padding: 5px; }
    </style>
</head>
<body>
<h2>Student Records</h2>
<?= $msg ?>

<!-- Upload CSV -->
<!-- <form method="POST" enctype="multipart/form-data">
    <label>Upload Student List (CSV):</label>
    <input type="file" name="student_file" accept=".csv" required>
    <button type="submit">Upload</button>
</form>
<p><strong>CSV Format:</strong> firstname,middlename,lastname,roll_no,class,email</p>

<hr>  -->

 <!-- Add Single Student -->
 

<h3>Add New Student</h3>

<form method="POST">
   
    <input type="hidden" name="add_student" value="1">
    <input type="text" name="firstname" placeholder="First Name" required pattern="[A-Za-z]+" title="Only letters allowed">
    <input type="text" name="middlename" placeholder="Middle Name" pattern="[A-Za-z]*" title="Only letters allowed">
    <input type="text" name="lastname" placeholder="Last Name" required pattern="[A-Za-z]+" title="Only letters allowed">
    <input type="text" name="roll_no" placeholder="Roll No" required pattern="[A-Za-z0-9_-]+" title="Alphanumeric only">
    <input type="text" name="class" placeholder="Class" required pattern="[A-Za-z0-9]+" title="Alphanumeric only">
    <input type="email" name="email" placeholder="Email">
    <button type="submit">Add Student</button>
</form>

<hr>

<!-- Search / Filter -->

    
<h3>Search Students</h3>

<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Search by  Roll No" value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
    <a href="students__record.php">Reset</a>
</form>

<!-- Show Student Records -->
<h3>Student List</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Roll No</th>
        <th>Class</th>
        <th>Email</th>
        <th>School</th>
        <th>Action</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['firstname']." ".$row['middlename']." ".$row['lastname']) ?></td>
            <td><?= htmlspecialchars($row['roll_no']) ?></td>
            <td><?= htmlspecialchars($row['class']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['school_name']) ?></td>
            <td>
                <a href="edit_student.php?id=<?= $row['id'] ?>">Edit</a> | 
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this student?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="7">No students found.</td></tr>
    <?php endif; ?>
</table>
</body>
</html>
