<?php
session_start();
if (!isset($_SESSION['username'])) {
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

$loggedInUser = $_SESSION['username'];
$friendToRemove = $_GET['username'];

// מחיקת החבר מרשימת החברים
$sqlDeleteFriend = "DELETE FROM friend_requests
                    WHERE (sender_username = '$loggedInUser' AND receiver_username = '$friendToRemove')
                       OR (sender_username = '$friendToRemove' AND receiver_username = '$loggedInUser')";
$resultDelete = $conn->query($sqlDeleteFriend);

// במקרה של הצלחה, מחיקת הפגישות הקשורות משני הצדדים
if ($resultDelete === TRUE) {
    $sqlDeleteMeetings = "DELETE FROM meetings
                          WHERE ((creator = '$loggedInUser' AND user = '$friendToRemove') OR (creator = '$friendToRemove' AND user = '$loggedInUser'))
                          AND date > CURDATE()";
    $resultDeleteMeetings = $conn->query($sqlDeleteMeetings);

    if ($resultDeleteMeetings === TRUE) {
        echo "success";
    } else {
        echo "error: " . $conn->error;
    }
} else {
    echo "error: " . $conn->error;
}

$conn->close();
?>