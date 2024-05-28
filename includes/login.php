<?php
session_start();
$host = "localhost";
$user = "ispatchima_admin";
$password = "admin123!@#";
$db = "ispatchima_sadna";

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];

// חיפוש המשתמש בבסיס הנתונים
$sql = "SELECT * FROM users WHERE username = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // משתמש נמצא
    $row = $result->fetch_assoc();
    $_SESSION['username'] = $username;
    $_SESSION['firstName'] = $row['firstName'];
    header("Location: home.php"); // העברת המשתמש לדף הבית
} else {
    // שם המשתמש או הסיסמה אינם נכונים
    header("Location: ../index.php?error=1"); // החזרת המשתמש לדף ההתחברות עם הודעת שגיאה
}
$stmt->close();
$conn->close();
?>
