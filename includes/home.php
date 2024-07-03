<?php
session_start();
if (!isset($_SESSION['firstName'])) {
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

$username = $_SESSION['username'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //אישור או ביטול של בקשת חברות
    if (isset($_POST['acceptRequest'])) {
        $requestId = $_POST['request_id'];
        $sql = "UPDATE friend_requests SET status = 'accepted' WHERE id = $requestId";
        $conn->query($sql);
    } elseif (isset($_POST['rejectRequest'])) {
        $requestId = $_POST['request_id'];
        $sql = "UPDATE friend_requests SET status = 'rejected' WHERE id = $requestId";
        $conn->query($sql);
    }

    //אישור או ביטול פגישה
    if (isset($_POST['status']) && isset($_POST['meeting_id'])) {
        $meetingId = $_POST['meeting_id'];
        $status = $_POST['status'];
        $sql = "UPDATE meetings SET status = '$status' WHERE id = $meetingId";
        $conn->query($sql);
    }

    //דירוג פגישה
    if (isset($_POST['rating']) && isset($_POST['meeting_id'])) {
        $rating = $_POST['rating'];
        $meetingId = $_POST['meeting_id'];
        $sql = "UPDATE meetings SET rating = '$rating' WHERE id = $meetingId";
        $conn->query($sql);
    }
}

//הצגת בקשות חברות ממתינות
$sql = "SELECT friend_requests.*, users.profilePicture, users.firstName, users.lastName, users.birthdate, users.city, users.hobbies 
        FROM friend_requests 
        INNER JOIN users ON friend_requests.sender_username = users.username 
        WHERE friend_requests.receiver_username = '$username' AND friend_requests.status = 'pending'";
$result = $conn->query($sql);


//הצגת הודעות שלא נקראו
$message_sql = "SELECT messages.*, users.firstName, users.lastName 
                FROM messages 
                INNER JOIN users ON messages.sender_username = users.username 
                WHERE messages.receiver_username = '$username' AND messages.status = 'unread'
                ORDER BY messages.time_sent DESC";
$messages_result = $conn->query($message_sql);

$new_message_notification = "אין הודעות חדשות";
$new_messages = array();

//בדיקה אם יש הודעות חדשות
if ($messages_result->num_rows > 0) {
    $last_message_per_user = array();

    while ($row = $messages_result->fetch_assoc()) {
        // שמירת הודעת המשתמש במערך האקראי בשם המשתמש של השולח
        $last_message_per_user[$row['sender_username']] = [
            'id' => $row['id']
        ];
    }

    // יצירת מחרוזת התראה לכל משתמש שהתקבלה ממנו הודעה חדשה
    foreach ($last_message_per_user as $sender_username => $last_message) {
        $sender_name_sql = "SELECT firstName, lastName FROM users WHERE username = '$sender_username'";
        $sender_result = $conn->query($sender_name_sql);
        if ($sender_result->num_rows > 0) {
            $sender_row = $sender_result->fetch_assoc();
            $sender_full_name = $sender_row['firstName'] . " " . $sender_row['lastName'];
            $chat_link = "chat.php?receiver=" . $sender_username;
            $new_messages[] = '<a href="' . $chat_link . '" class="new-message-notification" data-id="' . $last_message['id'] . '">יש לך הודעה חדשה מ' . $sender_full_name . '</a>';
        }
    }

    // אם יש הודעות חדשות, נעדכן את חלונית ההתראות
    if (!empty($new_messages)) {
        $new_message_notification = implode("<br>", $new_messages);
    }
}

date_default_timezone_set('Asia/Jerusalem'); // הגדרת איזור זמן נכון
// בדיקת פגישות ביום שלמחרת
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$meeting_sql = "SELECT meetings.*, users.firstName, users.lastName 
                FROM meetings 
                INNER JOIN users ON meetings.user = users.username
                WHERE meetings.creator = '$username' AND meetings.date = '$tomorrow' AND meetings.status IS NULL";
$meeting_result = $conn->query($meeting_sql);
$meetings = [];
if ($meeting_result->num_rows > 0) {
    while ($row = $meeting_result->fetch_assoc()) {
        $meetings[] = $row;
    }
}

//בדיקת פגישות שהתקיימו אתמול
$yesterday = date('Y-m-d', strtotime('-1 day'));
$meeting_sql = "SELECT meetings.*, users.firstName, users.lastName 
                FROM meetings 
                INNER JOIN users ON meetings.user = users.username
                WHERE meetings.creator = '$username' AND meetings.date = '$yesterday' 
                      AND (meetings.status = 'accepted' OR meetings.status IS NULL) 
                      AND meetings.rating IS NULL";
$meeting_result = $conn->query($meeting_sql);
$yesterdayMeetings = [];
if ($meeting_result->num_rows > 0) {
    while ($row = $meeting_result->fetch_assoc()) {
        $yesterdayMeetings[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="he">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/ageBridge.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>דף הבית</title>

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
                            <a class="nav-link active desA" href="home.php">דף הבית</a>
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
        <section>
            <div class="container1">
                <h1>שלום <?php echo $_SESSION['firstName']; ?></h1>

                <div class="d-flex justify-content-between" id="all-items">
                    <div class="item">
                        <h4>הודעות חדשות</h4>
                        <div class="message-container">
                            <?php
                            //הצגת הודעות חדשות
                            if (!empty($new_messages)) {
                                foreach ($new_messages as $notification) {
                                    echo '<div class="new-message-notification">';
                                    echo $notification;
                                    echo '</div>';
                                }
                            } else {
                                // אם אין הודעות חדשות
                                echo '<div class="new-message-notification">אין הודעות חדשות</div>';
                            }
                            ?>
                        </div>
                    </div>

                    <div class="item">
                        <h4>בקשות חברות</h4>
                        <div class="request-container">
                            <?php
                            //הצגת בקשות חברות חדשות אם ישנן
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<div class="request-card">';
                                    echo '<img class="request-card-img" src="' . $row['profilePicture'] . '" alt="Profile Picture">';
                                    echo '<div>שם מלא: ' . $row['firstName'] . ' ' . $row['lastName'] . '</div>';
                                    $birthdate = new DateTime($row['birthdate']);
                                    $today = new DateTime('today');
                                    $age = $birthdate->diff($today)->y;
                                    echo '<div>גיל: ' . $age . '</div>';
                                    echo '<div>עיר מגורים: ' . $row['city'] . '</div>';
                                    echo '<div>תחביבים: ' . $row['hobbies'] . '</div>';
                                    echo '<form method="POST">';
                                    echo '<input type="hidden" name="request_id" value="' . $row['id'] . '">';
                                    echo '<button class="reject" type="submit" name="rejectRequest">דחה</button>';
                                    echo '<button class="accept" type="submit" name="acceptRequest">קבל</button>';
                                    echo '</form>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p>אין בקשות חברות ממתינות</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center" id="allCircles">
                    <div class="d-flex justify-content-center" id="twoCircles">
                        <div class="circle">
                            <a href="findingMatches.html">
                                <img class="circle-img" src="../images/findMatches.png" alt="Icon 1">
                                <p class="circle-lable">איתור התאמות</p>
                            </a>
                        </div>
                        <div class="circle">
                            <a href="calendar.html">
                                <img class="circle-img" src="../images/calendar.png" alt="Icon 2">
                                <p class="circle-lable">היומן שלי</p>
                            </a>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center" id="twoCircles">
                        <div class="circle">
                            <a href="forum.php">
                                <img class="circle-img" src="../images/forum.png" alt="Icon 3">
                                <p class="circle-lable">כניסה לפורום</p>
                            </a>
                        </div>
                        <div class="circle">
                            <a href="myFriends.php">
                                <img class="circle-img" src="../images/myFriends.png" alt="Icon 4">
                                <p class="circle-lable">החיבורים שלי</p>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 כל הזכויות שמורות לאילנה, אלינה ומאי</p>
        <a href="#header"><img id="desing-up-arrow" src="../images/up-arrow.png"></a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="../JS/home.js"></script>

    <script>
        /* הצגת חלונית תזכורת על פגישה שמתקיימת מחר */
        <?php if (!empty($meetings)) { ?>
            var meetings = <?php echo json_encode($meetings); ?>;
        <?php } ?>
        function showReminder() {
            if (meetings && meetings.length > 0) {
                meetings.forEach(meeting => {
                    //יצירת תאריך ושעה בפורמט מתאים
                    var date = new Date(meeting.date);
                    var formattedDate = date.toLocaleDateString('he-IL', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    });
                    var time = meeting.time.split(':').slice(0, 2).join(':');

                    //תוכן החלונית
                    var reminderHtml = `
                    <div class="meeting-reminder" id="meeting-reminder-${meeting.id}">
                        <h3>מתזכרים אותך לקראת מחר</h3>
                        <p>תאריך: ${formattedDate}</p>
                        <p>שעה: ${time}</p>
                        <p>סוג פגישה: ${meeting.communication}</p>
                        <p>מיקום: ${meeting.location}</p>
                        <p>עם מי הפגישה: ${meeting.firstName} ${meeting.lastName}</p>
                        <button class="reject" onclick="updateMeetingStatus(${meeting.id}, 'rejected')">בטל</button>
                        <button class="accept" onclick="updateMeetingStatus(${meeting.id}, 'accepted')">אשר</button>
                        <p>אם אתה מחליט לבטל את הפגישה, אל תשכח לעדכן את הצד השני</p>
                    </div>
                `;
                    document.body.insertAdjacentHTML('beforeend', reminderHtml);
                });
            }
        }

        //עדכון סטטוס קיום פגישה בהתאם לקלט שהתקבל מהמשתמש
        function updateMeetingStatus(meetingId, status) {
            fetch('home.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'meeting_id=' + meetingId + '&status=' + status
            })
                //טיפול בתשובת השרת
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    //הסתרת תזכורת הפגישה
                    var reminderElement = document.getElementById('meeting-reminder-' + meetingId);
                    if (reminderElement) {
                        reminderElement.style.display = 'none';
                    }
                })

                //הדפסת השגיאה 
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>

    <script>
        /* חלונית קופצת לדירוג פגישה שהתקיימה אתמול */
        var yesterdayMeetings = <?php echo json_encode($yesterdayMeetings); ?>;

        function closePopupAndShowNext() {
            var popup = document.querySelector('.meeting-rating-form');
            if (popup) {
                popup.remove();
            }

            if (yesterdayMeetings.length > 0) {
                var nextMeeting = yesterdayMeetings.shift(); //תפיסת הפגישה הראשונה שמופיעה במערך

                var ratingHtml = `
                <div class="meeting-rating-form">
                    <h3>איך הייתה לך הפגישה עם ${nextMeeting.firstName} ${nextMeeting.lastName} אתמול?</h3>
                    <form action="home.php" method="POST">
                        <div class="rating-options">
                            <div class="rating-option">
                                <input type="radio" id="rating1" name="rating" value="1">
                                <label for="rating1">1</label>
                                <div>בכלל לא טובה</div>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="rating2" name="rating" value="2">
                                <label for="rating2">2</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="rating3" name="rating" value="3">
                                <label for="rating3">3</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="rating4" name="rating" value="4">
                                <label for="rating4">4</label>
                            </div>
                            <div class="rating-option">
                                <input type="radio" id="rating5" name="rating" value="5">
                                <label for="rating5">5</label>
                                <div>מעולה</div>
                            </div>
                        </div>
                        <input type="hidden" name="meeting_id" value="${nextMeeting.id}">
                        <button type="button" onclick="submitRating(${nextMeeting.id});">שלח דירוג</button>
                    </form>
                    <form action="home.php" method="POST">
                        <input type="hidden" name="meeting_id" value="${nextMeeting.id}">
                        <input type="hidden" name="status" value="cancelled">
                        <input type="submit" value="הפגישה לא התקיימה">
                    </form>
                </div>
            `;
                document.body.insertAdjacentHTML('beforeend', ratingHtml);
            } else {
                showReminder();
            }
        }

        //עדכון הדירוג במסד הנתונים
        function submitRating(meetingId) {
            var rating = document.querySelector('input[name="rating"]:checked').value;
            fetch('home.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'meeting_id=' + meetingId + '&rating=' + rating
            })
                //טיפול בתשובת השרת
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    closePopupAndShowNext();
                })

                //הדפסת השגיאה 
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // פונקציה שמציגה קודם חלונית דירוג ולאחר מכן חלונית תזכורת על פגישה 
        window.onload = function () {
            if (yesterdayMeetings.length > 0) {
                var notRatedMeetings = yesterdayMeetings.filter(function (meeting) {
                    return meeting.rating === null;
                });
                if (notRatedMeetings.length > 0) {
                    yesterdayMeetings = notRatedMeetings;
                    closePopupAndShowNext();
                } else {
                    showReminder();
                }
            } else {
                showReminder();
            }
        };
    </script>

</body>

</html>