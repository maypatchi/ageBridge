<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $postId = $data['postId'];
    $commentContent = $data['commentContent'];
    $username = $_SESSION['username'];

    $host = "localhost";
    $user = "ispatchima_admin";
    $password = "admin123!@#";
    $db = "ispatchima_sadna";

    $conn = new mysqli($host, $user, $password, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $commentContent = $conn->real_escape_string($commentContent);

    //הכנסת פרטי התגובה לבסיס הנתונים
    $sql = "INSERT INTO comments (post_id, username, content, created_at) VALUES ('$postId', '$username', '$commentContent', NOW())";

    if ($conn->query($sql) === TRUE) {
        $commentId = $conn->insert_id;
        $commentQuery = "SELECT comments.*, users.firstName, users.lastName FROM comments INNER JOIN users ON comments.username = users.username WHERE comments.id = $commentId";
        $commentResult = $conn->query($commentQuery);
        $comment = $commentResult->fetch_assoc();
        echo json_encode(['success' => true, 'comment' => $comment]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }

    $conn->close();
}
?>
