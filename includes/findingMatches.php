<?php
require_once 'vendor/autoload.php';
use Phpml\Classification\KNearestNeighbors;
use Phpml\Math\Distance\Euclidean;

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
    die("You need to log in first.");
}

//שליפת פרטים על המשתמש המחובר
$username = $_SESSION['username'];
$sql = "SELECT type, city, hobbies FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("User not found.");
}

$row = $result->fetch_assoc();
$userType = $row['type'];
$currentCity = $row['city'];
$userHobbies = explode(", ", $row['hobbies']);

// שליפת רשימת החברים של המשתמש ורשימת הבקשות שנמצאות במצב ממתין
$friends_sql = "SELECT sender_username AS friend_username FROM friend_requests WHERE receiver_username = ? AND status IN ('accepted', 'pending')
                UNION
                SELECT receiver_username AS friend_username FROM friend_requests WHERE sender_username = ? AND status IN ('accepted', 'pending')";
$stmt = $conn->prepare($friends_sql);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$friends_result = $stmt->get_result();
$friends_usernames = [];

while ($friend_row = $friends_result->fetch_assoc()) {
    $friends_usernames[] = $friend_row['friend_username'];
}

//בניית מודל חיזוי המתבסס על פגישות עבר
$samples = [];
$labels = [];

$meeting_sql = "SELECT creator, user, rating FROM meetings";
$meeting_result = $conn->query($meeting_sql);

while ($meeting_row = $meeting_result->fetch_assoc()) {
    $creator = $meeting_row['creator'];
    $meetingUser = $meeting_row['user'];
    $rating = $meeting_row['rating'];

    // מציאת תחביבים משותפים בין יוצר הפגישה למשתמש שאיתו נערכה הפגישה
    $creator_sql = "SELECT hobbies FROM users WHERE username = ?";
    $stmt = $conn->prepare($creator_sql);
    $stmt->bind_param("s", $creator);
    $stmt->execute();
    $creator_result = $stmt->get_result();
    $creator_hobbies = [];

    if ($creator_result->num_rows > 0) {
        $creator_row = $creator_result->fetch_assoc();
        $creator_hobbies = explode(", ", $creator_row['hobbies']);
    }

    $user_sql = "SELECT hobbies FROM users WHERE username = ?";
    $stmt = $conn->prepare($user_sql);
    $stmt->bind_param("s", $meetingUser);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user_hobbies = [];

    if ($user_result->num_rows > 0) {
        $user_row = $user_result->fetch_assoc();
        $user_hobbies = explode(", ", $user_row['hobbies']);
    }

    $commonHobbiesCount = count(array_intersect($creator_hobbies, $user_hobbies));
    $samples[] = [$commonHobbiesCount];
    $labels[] = $rating;
}

//שליחת הנתונים לאימון
if (count($samples) > 0 && count($labels) > 0) {
    $knn = new KNearestNeighbors($k = 3, new Euclidean());
    $knn->train($samples, $labels);
}

// מציאת התאמות לפי החיפוש שהתבצע
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['hobbies'])) {
        echo '<script>alert("יש לבחור לפחות תחביב אחד.")</script>';
    } else {
        $searchHobbies = $_POST['hobbies'];
        $regex = implode('|', $searchHobbies);

        $searchType = $userType == 'מבוגר' ? 'מתנדב' : 'מבוגר';

        $sql = "SELECT * FROM users WHERE type = ? AND hobbies REGEXP ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $searchType, $regex);
        $stmt->execute();
        $result = $stmt->get_result();

        $maxDistance = $_POST['distance'];
        $matches = [];
        while ($row = $result->fetch_assoc()) {
            if (in_array($row['username'], $friends_usernames)) {
                continue; // דילוג על משתמשים שכבר נמצאים ברשימת החברים או בבקשות החברות
            }

            $userHobbiesMatch = explode(", ", $row['hobbies']);
            $commonHobbies = array_intersect($searchHobbies, $userHobbiesMatch);

            if (count($commonHobbies) > 0) {
                $distance = calculateDistance($currentCity, $row['city']);
                if ($distance <= $maxDistance) {
                    $row['distance'] = $distance;
                    $matches[] = $row;
                }
            }
        }

        // חיזוי אחוז ההתאמה בעזרת KNN
        if (isset($knn) && count($matches) > 0) {
            foreach ($matches as &$match) {
                $match_hobbies = explode(", ", $match['hobbies']);
                $commonHobbiesCount = count(array_intersect($userHobbies, $match_hobbies));
                $predictedRating = $knn->predict([$commonHobbiesCount]);
                $match['predictedRating'] = $predictedRating;
                $match['matchPercentage'] = ($predictedRating / 5) * 100;
            }
        }
    }
}

// פונקציה לחישוב מרחק
function calculateDistance($origin, $destination)
{
    $apiKey = '0c85a7bac00d40658bd99b04db2a8f23';
    $origin = urlencode($origin);
    $destination = urlencode($destination);

    // שימוש ב-API
    $urlOrigin = "https://api.opencagedata.com/geocode/v1/json?q=$origin&key=$apiKey";
    $urlDestination = "https://api.opencagedata.com/geocode/v1/json?q=$destination&key=$apiKey";

    $responseOrigin = file_get_contents($urlOrigin);
    $responseDestination = file_get_contents($urlDestination);

    $dataOrigin = json_decode($responseOrigin, true);
    $dataDestination = json_decode($responseDestination, true);

    if ($dataOrigin['status']['code'] == 200 && $dataDestination['status']['code'] == 200) {
        $originCoordinates = $dataOrigin['results'][0]['geometry'];
        $destinationCoordinates = $dataDestination['results'][0]['geometry'];

        $lat1 = $originCoordinates['lat'];
        $lon1 = $originCoordinates['lng'];
        $lat2 = $destinationCoordinates['lat'];
        $lon2 = $destinationCoordinates['lng'];

        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    } else {
        return PHP_INT_MAX;
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
    <title>איתור התאמות</title>
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
                            <a class="nav-link active desA" href="findingMatches.html">איתור התאמות</a>
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
            <h2 id="design-h2">התאמות שנמצאו:</h2>
            <?php if (isset($matches) && count($matches) > 0) { ?>
                <div>
                    <button id="sortButton" class="btn btn-primary designSortButton" onclick="sortMatchesByDistance()">מיין
                        לפי
                        מרחק</button>
                </div>
                <div>
                    <button id="sortMatchPercentageButton" class="btn btn-primary designSortButton"
                        onclick="sortMatchesByMatchPercentage()">מיין
                        לפי אחוז התאמה</button>
                </div>
            <?php } ?>
            <div class="match-container" id="matchContainer">
                <?php
                if (isset($matches) && count($matches) > 0) {
                    $printedUsernames = array(); // מערך עזר שיעקוב אחר המשתמשים שהודפסו
                    foreach ($matches as $match) {
                        if (!in_array($match['username'], $printedUsernames)) { // בדיקה אם המשתמש כבר הודפס
                            echo '<div class="match-card" data-match-percentage="' . $match['matchPercentage'] . '">';
                            echo '<img src="' . $match['profilePicture'] . '" alt="Profile Picture">';
                            echo '<div>שם: ' . $match['firstName'] . ' ' . $match['lastName'] . '</div>';
                            echo '<div>מין: ' . $match['gender'] . '</div>';
                            echo '<div>גיל: ' . (date("Y") - date("Y", strtotime($match['birthdate']))) . '</div>';
                            echo '<div>תחביבים: ' . $match['hobbies'] . '</div>';
                            echo '<div style="display: none;">מרחק: ' . number_format($match['distance'], 2) . ' ק"מ</div>';
                            echo '<div>עיר מגורים: ' . $match['city'] . '</div>';
                            echo '<div>אחוז התאמה: ' . number_format($match['matchPercentage'], 2) . '%</div>';
                            echo '<button id="changeBtn" style="background-color: blue;" onclick="sendFriendRequest(this, \'' . $match['username'] . '\')">שלח בקשה</button>';
                            echo '</div>';
                            $printedUsernames[] = $match['username']; // הוספת המשתמש למערך העזר
                        }
                    }
                } else {
                    echo '<p>לא נמצאו התאמות, נסה לשנות את מאפייני החיפוש שלך</p>';
                }
                ?>
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
    
    <script src="../JS/findingMatchesRequestFriends.js"></script>

    <script>
        // פונקציה למיון התאמות לפי מרחק
        function sortMatchesByDistance() {
            const matchContainer = document.getElementById('matchContainer');
            const matchCards = Array.from(matchContainer.getElementsByClassName('match-card'));

            matchCards.sort((a, b) => {
                const distanceA = parseFloat(a.querySelector('div:nth-of-type(5)').textContent.split(' ')[1]);
                const distanceB = parseFloat(b.querySelector('div:nth-of-type(5)').textContent.split(' ')[1]);
                return distanceA - distanceB;
            });

            matchContainer.innerHTML = '';
            matchCards.forEach(card => matchContainer.appendChild(card));
        }

        // פונקציה למיון התאמות לפי אחוז התאמה
        function sortMatchesByMatchPercentage() {
            const matchContainer = document.getElementById('matchContainer');
            const matchCards = Array.from(matchContainer.getElementsByClassName('match-card'));

            matchCards.sort((a, b) => {
                const matchPercentageA = parseFloat(a.getAttribute('data-match-percentage'));
                const matchPercentageB = parseFloat(b.getAttribute('data-match-percentage'));
                return matchPercentageB - matchPercentageA;
            });

            matchContainer.innerHTML = '';
            matchCards.forEach(card => matchContainer.appendChild(card));
        }


    </script>

</body>

</html>