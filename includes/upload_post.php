<?php
session_start();
if (!isset($_SESSION['firstName'])) {
    header("Location: ../index.php"); 
    exit();
}

$host = "localhost";
$user = "ispatchima_admin";
$password = "admin123!@#";
$db = "ispatchima_sadna";

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitPost'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $username = $_SESSION['username'];
    $imagePath = '';

    if ($_FILES['image']['name']) {
        $targetDir = "../uploads/";
        $imageName = basename($_FILES['image']['name']);
        $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);
        $uniqueImageName = uniqid() . "." . $imageExt;
        $imagePath = $targetDir . $uniqueImageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            die("שגיאה בהעלאת התמונה");
        }
    }

    //העלת הפוסט לבסיס הנתונים
    $sql = "INSERT INTO posts (users_username, title, content, created_at, image_path) VALUES ('$username', '$title', '$content', NOW(), '$imagePath')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('הפוסט הועלה בהצלחה!'); window.location.href = 'forum.php';</script>";
    } else {
        echo "<script>alert('שגיאה בהעלאת הפוסט: " . $conn->error . "'); window.location.href = 'forum.php';</script>";
    }
}

$conn->close();
?>
