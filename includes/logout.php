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

session_start();
session_unset();
session_destroy(); 

header("Location: ../index.php");
exit();
?>
