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

// מחיקת פוסט מבסיס הנתונים ע"י עדכון סטטוס לנמחק
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $postId = $conn->real_escape_string($_POST['post_id']);

    $updateSql = "UPDATE posts SET status = 'deleted' WHERE id = '$postId'";

    if ($conn->query($updateSql) === TRUE) {
        echo "הפוסט נמחק בהצלחה";
    } else {
        echo "Error deleting post: " . $conn->error;
    }
}

$conn->close();
?>
