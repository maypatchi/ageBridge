<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'])) {
    $message_id = $_POST['message_id'];

    $host = "localhost";
    $user = "ispatchima_admin";
    $password = "admin123!@#";
    $db = "ispatchima_sadna";

    $conn = new mysqli($host, $user, $password, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    //עדכון סטטוס הודעה לנקראה
    $sql = "UPDATE messages SET status = 'read' WHERE id = $message_id";

    if ($conn->query($sql) === TRUE) {
        echo "Status updated successfully";
    } else {
        echo "Error updating status: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Invalid request";
}
?>
