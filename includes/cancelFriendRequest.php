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

if (!isset($_SESSION['username'])) {
    die("You need to log in first.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelRequest'])) {
    $sender_username = $_SESSION['username'];
    $receiver_username = $_POST['receiver_username'];
    //מחיקת הבקשה מבסיס הנתונים
    $sql = "DELETE FROM friend_requests WHERE sender_username = '$sender_username' AND receiver_username = '$receiver_username' AND status = 'pending'";
    if ($conn->query($sql) === TRUE) {
        echo 'success';
    } else {
        echo 'error';
    }
}

$conn->close();
?>
