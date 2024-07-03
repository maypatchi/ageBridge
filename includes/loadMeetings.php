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
    die("User is not logged in");
}

$username = $_SESSION['username'];

//שליפת נתוני פגישות עבור המשתמש המחובר
$sql = "SELECT m.date, m.time, m.communication, m.location, u.firstName, u.lastName 
        FROM meetings AS m 
        INNER JOIN users AS u ON m.user = u.username
        WHERE m.creator = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$events = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fullName = $row['firstName'] . ' ' . $row['lastName'];
        $events[] = [
            'title' => $fullName,
            'date' => $row['date'],
            'time' => $row['time'],
            'communication' => $row['communication'],
            'location' => $row['location'],
            'user' => $fullName, 
            'start' => $row['date'] . 'T' . $row['time'],
            'allDay' => false
        ];
    }
}

echo json_encode($events);

$stmt->close();
$conn->close();
?>
