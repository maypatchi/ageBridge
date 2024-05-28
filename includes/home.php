<?php
session_start();
if (!isset($_SESSION['firstName'])) {
    header("Location: ../index.php"); // אם המשתמש לא מחובר, נעביר אותו לדף התחברות
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
    if (isset($_POST['acceptRequest'])) {
        $requestId = $_POST['request_id'];
        $sql = "UPDATE friend_requests SET status = 'accepted' WHERE id = $requestId";
        $conn->query($sql);
    } elseif (isset($_POST['rejectRequest'])) {
        $requestId = $_POST['request_id'];
        $sql = "UPDATE friend_requests SET status = 'rejected' WHERE id = $requestId";
        $conn->query($sql);
    }
}

$sql = "SELECT friend_requests.*, users.profilePicture, users.firstName, users.lastName, users.birthdate, users.city, users.hobbies 
        FROM friend_requests 
        INNER JOIN users ON friend_requests.sender_username = users.username 
        WHERE friend_requests.receiver_username = '$username' AND friend_requests.status = 'pending'";
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
    <title>דף הבית</title>
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
                            <a class="nav-link active desA" href="home.php">דף הבית</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="profile.php">הפרופיל שלי</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="myFriends.php">החברים שלי</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="findingMatches.html">איתור התאמות</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="#">היומן שלי</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="#">פורום</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="#">התנתק מהמערכת</a>
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
                <div class="d-flex justify-content-center" id="allCircles">
                    <div class="d-flex justify-content-center" id="twoCircles">
                        <div class="circle">
                            <a href="findingMatches.html">
                                <img class="circle-img" src="../images/findMatches.png" alt="Icon 1">
                                <p class="circle-lable">איתור התאמות</p>
                            </a>
                        </div>
                        <div class="circle">
                            <a href="#">
                                <img class="circle-img" src="../images/calendar.png" alt="Icon 2">
                                <p class="circle-lable">היומן שלי</p>
                            </a>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center" id="twoCircles">
                        <div class="circle">
                            <a href="#">
                                <img class="circle-img" src="../images/forum.png" alt="Icon 3">
                                <p class="circle-lable">כניסה לפורום</p>
                            </a>
                        </div>
                        <div class="circle">
                            <a href="myFriends.php">
                                <img class="circle-img" src="../images/myFriends.png" alt="Icon 4">
                                <p class="circle-lable">החברים שלי</p>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <h4>בקשות חברות</h4>
                    <div class="request-container">
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<div class="request-card">';
                                echo '<img src="' . $row['profilePicture'] . '" alt="Profile Picture">';
                                echo '<div>שם מלא: ' . $row['firstName'] . ' ' . $row['lastName'] . '</div>';
                                $birthdate = new DateTime($row['birthdate']);
                                $today = new DateTime('today');
                                $age = $birthdate->diff($today)->y;
                                echo '<div>גיל: ' . $age . '</div>';
                                echo '<div>עיר מגורים: ' . $row['city'] . '</div>';
                                echo '<div>תחביבים: ' . $row['hobbies'] . '</div>';
                                echo '<form method="POST">';
                                echo '<input type="hidden" name="request_id" value="' . $row['id'] . '">';
                                echo '<button id="reject" type="submit" name="rejectRequest">דחה</button>';
                                echo '<button id="accept" type="submit" name="acceptRequest">קבל</button>';
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
        </section>
    </main>

    <footer>
        <p>&copy; 2024 כל הזכויות שמורות לאילנה, אלינה ומאי</p>
        <a href="#header"><img id="desing-up-arrow" src="../images/up-arrow.png"></a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>

</html>