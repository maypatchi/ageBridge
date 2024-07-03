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

$username = $_SESSION['username'];

//שליפת רשימת החברים של המשתמש המחובר
$sql = "SELECT users.firstName, users.lastName, users.username
        FROM friend_requests 
        INNER JOIN users ON friend_requests.sender_username = users.username 
        WHERE friend_requests.receiver_username = '$username' AND friend_requests.status = 'accepted'
        UNION
        SELECT users.firstName, users.lastName, users.username
        FROM friend_requests 
        INNER JOIN users ON friend_requests.receiver_username = users.username 
        WHERE friend_requests.sender_username = '$username' AND friend_requests.status = 'accepted'";

$result = $conn->query($sql);

$friends = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $friends[] = $row;
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
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>יומן פגישות</title>
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
                            <a class="nav-link active desA" href="calendar.php">היומן שלי</a>
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

    <main id="calendar-body">
        <h1>יומן פגישות</h1>
        <h6>להוספת פגישה חדשה לחצ/י על התאריך המבוקש</h6>
        <div id="calendar"> <!--כאן ישתל היומן באמצעות ספריית ג'וואה סקריפט--></div>
    </main>

    <!-- חלונית קופצת -->
    <div class="modal fade" id="meetingModal" tabindex="-1" aria-labelledby="meetingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="meetingModalLabel">הוסף פגישה</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="meetingForm" action="savingMeeting.php" method="post">
                        <div class="mb-3">
                            <label for="date" class="form-label">תאריך</label>
                            <input type="date" class="form-control rtl-input" id="date" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="time" class="form-label">שעה</label>
                            <input type="time" class="form-control rtl-input" id="time" name="time" required>
                        </div>
                        <div class="mb-3">
                            <label for="communication" class="form-label">סוג פגישה</label>
                            <select class="form-select rtl-input" id="communication" name="communication" required>
                                <option value="פגישה פרונטלית">פגישה פרונטלית</option>
                                <option value="שיחה טלפונית">שיחה טלפונית</option>
                                <option value="שיחת וידיאו">שיחת וידיאו</option>
                                <option value="אחר">אחר</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">מיקום</label>
                            <input type="text" class="form-control rtl-input" id="location" name="location">
                        </div>
                        <div class="mb-3">
                            <label for="user" class="form-label">עם מי הפגישה</label>
                            <select class="form-select rtl-input" id="user" name="user" required>
                                <?php foreach ($friends as $friend): ?>
                                    <option value="<?php echo $friend['username']; ?>">
                                        <?php echo $friend['firstName'] . ' ' . $friend['lastName']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-full-width">הוסף פגישה</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 כל הזכויות שמורות לאילנה, אלינה ומאי</p>
        <a href="#header"><img id="desing-up-arrow" src="../images/up-arrow.png"></a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales-all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="../JS/calendar.js"></script>
</body>

</html>
