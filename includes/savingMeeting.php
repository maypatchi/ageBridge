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

//הכנסת פגישה לטבלה בבסיס הנתונים
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $communication = $_POST['communication'];
    $location = $_POST['location'];
    $username = $_POST['user']; // שם המשתמש איתו נקבעה הפגישה
    $creator = $_SESSION['username']; // יוצר הפגישה 

    $sql = "INSERT INTO meetings (date, time, communication, location, user, creator, status) 
    VALUES ('$date', '$time', '$communication', '$location', '$username', '$creator', NULL)";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
}

$conn->close();
?>
