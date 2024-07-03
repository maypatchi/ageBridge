<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$receiver_username = $_GET['receiver'];

$host = "localhost";
$user = "ispatchima_admin";
$password = "admin123!@#";
$db = "ispatchima_sadna";

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sender_username = $_SESSION['username'];

// עדכון סטטוס ההודעות ל"נקרא" כאשר המשתמש נכנס לצ'אט
$sql_update_status = "UPDATE messages SET status = 'read' WHERE receiver_username = '$sender_username' AND sender_username = '$receiver_username' AND status = 'unread'";
$conn->query($sql_update_status);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //הכנסת הודעה חדשה לבסיס הנתונים
    if (isset($_POST['action']) && $_POST['action'] == 'send_message') {
        $message = $_POST['message'];

        $sql = "INSERT INTO messages (sender_username, receiver_username, message, status, time_sent) VALUES ('$sender_username', '$receiver_username', '$message', 'unread', NOW())";

        if ($conn->query($sql) === TRUE) {
            echo "success";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        exit();
    }

    //שליפת ההודעות הקודמות בין שני משתמשים
    if (isset($_POST['action']) && $_POST['action'] == 'get_messages') {
        $sql = "SELECT messages.*, 
                       sender.firstName AS sender_firstName, sender.lastName AS sender_lastName, 
                       receiver.firstName AS receiver_firstName, receiver.lastName AS receiver_lastName
                FROM messages
                JOIN users AS sender ON messages.sender_username = sender.username
                JOIN users AS receiver ON messages.receiver_username = receiver.username
                WHERE (messages.sender_username = '$sender_username' OR messages.receiver_username = '$sender_username')
                  AND (messages.receiver_username = '$receiver_username' OR messages.sender_username = '$receiver_username')
                ORDER BY messages.id ASC";
        $result = $conn->query($sql);

        //הצגת הודעות קיימות
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $message_class = ($row['sender_username'] == $_SESSION['username']) ? 'sent' : 'received';
                $sender_name = ($row['sender_username'] == $_SESSION['username']) ? "אני" : $row['sender_firstName'] . ' ' . $row['sender_lastName'];
                echo "<div class='message $message_class'><b>$sender_name:</b> {$row['message']} <span style='font-size: 12px; color: #888;'>{$row['time_sent']}</span></div>";
            }
        }
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="he">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/ageBridge.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>צ'אט</title>
</head>

<body>
    <header id="header">
        <nav class="navbar navbar-light navbar-expand-lg">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand" href="home.php"><img id="logo" class="img-fluid" src="../images/logoHome.png"
                        alt="logo"></a>
                <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link desA" href="home.php">דף הבית</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="profile.php">הפרופיל שלי</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="myFriends.php">החיבורים שלי</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="findingMatches.html">איתור התאמות</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="calendar.php">היומן שלי</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="forum.php">פורום</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="logout.php">התנתק מהמערכת</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div id="chat-container">
            <div id="chat-header">
                <?php
                //שליפת פרטי המשתמש שאנו מדברים איתו
                $sql_receiver = "SELECT profilePicture, firstName, lastName FROM users WHERE username = '$receiver_username'";
                $result_receiver = $conn->query($sql_receiver);

                //הצגת החלק העליון של הצ'אט הכולל שם ותמונה
                if ($result_receiver->num_rows > 0) {
                    $row_receiver = $result_receiver->fetch_assoc();
                    echo '<div class="chat-user-info">';
                    echo '<div class="profile-picture-container">';
                    echo '<img src="' . $row_receiver['profilePicture'] . '" alt="Profile Picture" class="profile-picture">';
                    echo '</div>';
                    echo '<h2 class="chat-user-name">' . $row_receiver['firstName'] . ' ' . $row_receiver['lastName'] . '</h2>';
                    echo '</div>';
                }
                ?>
            </div>

            <div class="chat-box" id="chat-box"> <!--כאן ייטענו ההודעות הקודמות באמצעות הקוד שנכתב מעלה והודעות חדשות באמצעות הג'אווה סקריפט--></div>

            <form id="message-form" method="post" class="typing-area">
                <input type="hidden" class="incoming_id" name="receiver" value="<?php echo $receiver_username; ?>">
                <input id="desing-writeMessage" type="text" name="message" class="input-field"
                    placeholder="הקלד הודעה כאן..." autocomplete="off" required>
                <button id="desing-submit" type="submit" name="submit">שליחה</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 כל הזכויות שמורות לאילנה, אלינה ומאי</p>
        <a href="#header"><img id="desing-up-arrow" src="../images/up-arrow.png"></a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

    <script src="../JS/chat.js"></script>

</body>

</html>