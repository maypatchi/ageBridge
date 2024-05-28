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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sendRequest'])) {
    $sender_username = $_SESSION['username'];
    $receiver_username = $_POST['receiver_username'];
    $sql = "INSERT INTO friend_requests (sender_username, receiver_username, status) VALUES ('$sender_username', '$receiver_username', 'pending')";
    if ($conn->query($sql) === TRUE) {
        echo 'success';
    } else {
        echo 'error';
    }
}

$conn->close();
?>
