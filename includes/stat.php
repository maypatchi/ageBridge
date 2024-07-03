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

// שאילתה לספירת גברים ונשים מטבלת users
$sql_gender_count = "SELECT gender, COUNT(*) as count FROM users GROUP BY gender";
$result_gender_count = $conn->query($sql_gender_count);

// שאילתה לספירת מבוגרים ומתנדבים מטבלת users
$sql_user_type_count = "SELECT type, COUNT(*) as count FROM users WHERE type IN ('מבוגר', 'מתנדב') GROUP BY type";
$result_user_type_count = $conn->query($sql_user_type_count);

// שאילתה לסטטיסטיקת דירוגי הפגישות מטבלת meetings עם מיון לפי דירוג
$sql_meeting_ratings = "SELECT rating, COUNT(*) as count FROM meetings GROUP BY rating ORDER BY rating ASC";
$result_meeting_ratings = $conn->query($sql_meeting_ratings);
?>

<!DOCTYPE html>
<html lang="he">

<head>
    <meta charset="UTF-8">
    <title>סטטיסטיקות</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/ageBridge.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a class="navbar-brand" href="managerHome.html"><img id="logo" class="img-fluid" src="../images/logoHome.png"
                        alt="logo"></a>
                <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link desA" href="managerHome.html">דף הבית</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="managerReports.html">דו"חות</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active desA" href="stat.php">ניתוחים סטטיסטיים </a>
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
        <div>
            <h2 id="design-h2">סטטיסטיקות</h2>
        </div>
        <div class="container-stat charts-wrapper">
            <div class="chart-container">
                <h3>התפלגות לפי מין</h3>
                <canvas id="genderChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>התפלגות לפי סוג המשתמש</h3>
                <canvas id="userTypeChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>דירוגי מפגשים</h3>
                <canvas id="meetingRatingsChart"></canvas>
            </div>
        </div>

        <script>
            // תרשים עוגה להתפלגות מין המשתמשים
            var genderData = {
                labels: [
                    <?php
                    $gender_labels = [];
                    while ($row = $result_gender_count->fetch_assoc()) {
                        if ($row['gender'] == 'זכר') {
                            $gender_labels[] = 'גברים';
                        } else if ($row['gender'] == 'נקבה') {
                            $gender_labels[] = 'נשים';
                        }
                    }
                    echo "'" . implode("', '", $gender_labels) . "'";
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php
                        $result_gender_count->data_seek(0);
                        while ($row = $result_gender_count->fetch_assoc()) {
                            echo $row['count'] . ", ";
                        }
                        ?>
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 99, 132, 0.5)',
                    ]
                }]
            };

            // תרשים עוגה להתפלגות מתנדבים ומבוגרים
            var userTypeData = {
                labels: [
                    <?php
                    while ($row = $result_user_type_count->fetch_assoc()) {
                        echo "'" . $row['type'] . "', ";
                    }
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php
                        $result_user_type_count->data_seek(0);
                        while ($row = $result_user_type_count->fetch_assoc()) {
                            echo $row['count'] . ", ";
                        }
                        ?>
                    ],
                    backgroundColor: [
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                    ]
                }]
            };

            // תרשים עמודות לדירוגי פגישות 
            var meetingRatingsData = {
                labels: [
                    <?php
                    while ($row = $result_meeting_ratings->fetch_assoc()) {
                        echo "'" . $row['rating'] . "', ";
                    }
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php
                        $result_meeting_ratings->data_seek(0);
                        while ($row = $result_meeting_ratings->fetch_assoc()) {
                            echo $row['count'] . ", ";
                        }
                        ?>
                    ],
                    backgroundColor: [
                        'rgba(153, 102, 255, 0.5)',
                        'rgba(255, 159, 64, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                    ]
                }]
            };

            //בניית התרשימים עצמם 
            var genderChart = new Chart(document.getElementById('genderChart'), {
                type: 'pie',
                data: genderData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            var userTypeChart = new Chart(document.getElementById('userTypeChart'), {
                type: 'pie',
                data: userTypeData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            var meetingRatingsChart = new Chart(document.getElementById('meetingRatingsChart'), {
                type: 'bar',
                data: meetingRatingsData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false,
                        }
                    }
                }
            });
        </script>
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
