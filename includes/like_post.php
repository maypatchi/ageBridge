<?php
session_start();
$username = $_SESSION['username'];

$postData = json_decode(file_get_contents('php://input'), true);

if (isset($postData['like_button']) && isset($postData['post_id'])) {
    $post_id = $postData['post_id'];

    $host = "localhost";
    $user = "ispatchima_admin";
    $password = "admin123!@#";
    $db = "ispatchima_sadna";

    $conn = new mysqli($host, $user, $password, $db);
    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
    }

    // בדיקה אם המשתמש עשה לייק לפוסט הנוכחי
    $liked_sql = "SELECT * FROM likes WHERE username='$username' AND post_id='$post_id'";
    $liked_result = $conn->query($liked_sql);
    $liked = $liked_result->num_rows > 0;

    if ($liked) {
        // המשתמש עשה לייק - הסרת הלייק
        $sql = "DELETE FROM likes WHERE username='$username' AND post_id='$post_id'";
    } else {
        // המשתמש לא עשה לייק - הוספת הלייק
        $sql = "INSERT INTO likes (username, post_id) VALUES ('$username', '$post_id')";
    }

    if ($conn->query($sql) === TRUE) {
        // עדכון מספר הלייקים
        $likes_count_sql = "SELECT COUNT(*) AS likes_count FROM likes WHERE post_id='$post_id'";
        $likes_count_result = $conn->query($likes_count_sql);
        $likes_count_row = $likes_count_result->fetch_assoc();
        $likes_count = $likes_count_row['likes_count'];

        echo json_encode(['success' => true, 'likes_count' => $likes_count, 'liked' => !$liked]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating record: ' . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>