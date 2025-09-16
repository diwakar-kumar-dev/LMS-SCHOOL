<?php
require_once "config.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['school_id'])) { 
    header("Location: school_login.php"); 
    exit; 
}

$school_id = $_SESSION['school_id'];
$id = intval($_GET['id'] ?? 0);

$msg = "";
$errors = [];
$student = [];

/* Fetch student first */
$stmt = $conn->prepare("SELECT * FROM student_list WHERE id=? AND school_id=?");
$stmt->bind_param("ii", $id, $school_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) { 
    die("Invalid student"); 
}

/* Handle form submit */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname = trim($_POST['firstname'] ?? '');
    $middlename = trim($_POST['middlename'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $roll_no   = trim($_POST['roll_no'] ?? '');
    $class     = trim($_POST['class'] ?? '');
    $email     = trim($_POST['email'] ?? '');

    /* ===== Validation ===== */
    if ($firstname === '' || !preg_match("/^[a-zA-Z]+$/", $firstname)) {
        $errors[] = "First name is required and must contain only letters.";
    }
    if ($middlename !== '' && !preg_match("/^[a-zA-Z]+$/", $middlename)) {
        $errors[] = "Middle name must contain only letters.";
    }
    if ($lastname === '' || !preg_match("/^[a-zA-Z]+$/", $lastname)) {
        $errors[] = "Last name is required and must contain only letters.";
    }
    if ($roll_no === '' || !preg_match("/^[0-9A-Za-z_-]+$/", $roll_no)) {
        $errors[] = "Roll No is required and must be alphanumeric (with - or _ allowed).";
    }
    if ($class === '' || !preg_match("/^[0-9A-Za-z ]+$/", $class)) {
        $errors[] = "Class is required and must be alphanumeric.";
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE student_list 
            SET firstname=?, middlename=?, lastname=?, roll_no=?, class=?, email=? 
            WHERE id=? AND school_id=?");
        $stmt->bind_param("ssssssii", $firstname, $middlename, $lastname, $roll_no, $class, $email, $id, $school_id);

        if ($stmt->execute()) {
            $msg = "<p style='color:green;'>Student updated successfully!</p>";
            // Refresh student data
            $student['firstname'] = $firstname;
            $student['middlename'] = $middlename;
            $student['lastname'] = $lastname;
            $student['roll_no'] = $roll_no;
            $student['class'] = $class;
            $student['email'] = $email;
        } else {
            $msg = "<p style='color:red;'>Database error: ".$conn->error."</p>";
        }
        $stmt->close();
    } else {
        $msg = "<p style='color:red;'>" . implode("<br>", $errors) . "</p>";
    }
}
?>
<!doctype html>
<html>
<head>
    <title>Edit Student</title>
</head>
<link rel="stylesheet" href="style.css">
<body>
    
     <div class="form-grid">
        <div class="wrapper">
  <div class="card">
<h2>Edit Student</h2>
<?= $msg ?>

<form method="POST">
    <label>First Name:</label>
    <input type="text" name="firstname" value="<?= htmlspecialchars($student['firstname']) ?>" required><br><br>

    <label>Middle Name:</label>
    <input type="text" name="middlename" value="<?= htmlspecialchars($student['middlename']) ?>"><br><br>

    <label>Last Name:</label>
    <input type="text" name="lastname" value="<?= htmlspecialchars($student['lastname']) ?>" required><br><br>

    <label>Roll No:</label>
    <input type="text" name="roll_no" value="<?= htmlspecialchars($student['roll_no']) ?>" required><br><br>

    <label>Class:</label>
    <input type="text" name="class" value="<?= htmlspecialchars($student['class']) ?>" required><br><br>

    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>"><br><br>


    <button type="submit">Update</button>
</form>
<a href="students__record.php">Back to Student Records</a>
</div>
</div>
</div>
</body>
</html>
