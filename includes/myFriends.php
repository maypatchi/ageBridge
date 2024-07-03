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

//שליפת רשימת כל החיבורים של המשתמש המחובר
$sql = "SELECT users.profilePicture, users.firstName, users.lastName, users.birthdate, users.city, users.hobbies, users.username
        FROM friend_requests 
        INNER JOIN users ON friend_requests.sender_username = users.username 
        WHERE friend_requests.receiver_username = '$username' AND friend_requests.status = 'accepted'
        UNION
        SELECT users.profilePicture, users.firstName, users.lastName, users.birthdate, users.city, users.hobbies, users.username
        FROM friend_requests 
        INNER JOIN users ON friend_requests.receiver_username = users.username 
        WHERE friend_requests.sender_username = '$username' AND friend_requests.status = 'accepted'";

$result = $conn->query($sql);

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
    <title>החיבורים שלי</title>
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
                            <a class="nav-link active desA" href="myFriends.php">החיבורים שלי</a>
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
                <h2>החיבורים שלי</h2>
                <div class="user-container">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="friend-card" id="friend-' . $row['username'] . '">';
                            echo '<img src="' . $row['profilePicture'] . '" alt="Profile Picture">';
                            echo '<div class="friend-details">';
                            echo '<div class="remove-btn-container">';
                            echo '<button onclick="removeFriend(\'' . $row['username'] . '\')" class="btn btn-danger">הסר</button>'; //כפתור להסרת חיבור
                            echo '</div>';
                            echo '<div><b>' . $row['firstName'] . ' ' . $row['lastName'] . '</b></div>';
                            $birthdate = new DateTime($row['birthdate']);
                            $today = new DateTime('today');
                            $age = $birthdate->diff($today)->y;
                            echo '<div>גיל: ' . $age . '</div>';
                            echo '<div>עיר מגורים: ' . $row['city'] . '</div>';
                            echo '<div>תחביבים: ' . $row['hobbies'] . '</div>';
                            echo '<a href="chat.php?receiver=' . $row['username'] . '" class="btn btn-primary">התחל צ\'אט</a>'; //כפתור להתחלת צ'אט
                            echo '</div>'; 
                            echo '</div>';                             
                        }
                    } else {
                        echo '<p>עדיין אין אף אחד ברשימה שלך</p>';
                        echo '<p>מעוניין להכיר אנשים חדשים? לחץ על <a href="findingMatches.html">חיפוש התאמות</a></p>';
                    }
                    ?>
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

    <script src="../JS/myFriends.js"></script>

</body>

</html>