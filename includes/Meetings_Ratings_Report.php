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

//שליפת כל נתוני הפגישות
$sql = "
    SELECT 
        CONCAT(u.firstName, ' ', u.lastName) AS 'User_Name',
        CONCAT(u2.firstName, ' ', u2.lastName) AS 'Friend_Name',
        COUNT(m.id) AS 'Meetings_Count',
        m.communication AS 'Meeting_Type',
        m.rating AS 'Meeting_Rating'
    FROM 
        meetings m
    JOIN 
        users u ON m.creator = u.username
    JOIN 
        users u2 ON m.user = u2.username
    JOIN 
        friend_requests f ON (u.username = f.sender_username AND u2.username = f.receiver_username) OR (u.username = f.receiver_username AND u2.username = f.sender_username)
    WHERE 
        f.status = 'accepted'
    GROUP BY 
        u.username, u2.username, m.communication, m.rating
    ORDER BY 
        u.username, u2.username;
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/ageBridge.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>דוח מפגשים ודירוגים</title>
    <style>
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
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
                <a class="navbar-brand" href="managerHome.html"><img id="logo" class="img-fluid"
                        src="../images/logoHome.png" alt="logo"></a>
                <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link desA" href="managerHome.html">דף הבית</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="managerReports.html">דו"חות</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="stat.php">ניתוחים סטטיסטיים </a>
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
            <h2 id="design-h2">דוח מפגשים ודירוגים</h2>
        </div>
        
        <div>
            <button id="exportBtn1" class="btn btn-primary design-csv">ייצוא ל-CSV</button>
        </div>

        <!-- טבלת הפגישות-->
        <div class="container-table">
            <?php
            if ($result->num_rows > 0) {
                echo "<table id='reportTable1'>";
                echo "<tr>";
                echo "<th>שם משתמש</th>";
                echo "<th>עם מי הפגישה</th>";
                echo "<th>סך פגישות</th>";
                echo "<th>סוג פגישה</th>";
                echo "<th>דירוג פגישה</th>";
                echo "</tr>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['User_Name'] . "</td>";
                    echo "<td>" . $row['Friend_Name'] . "</td>";
                    echo "<td>" . $row['Meetings_Count'] . "</td>";
                    echo "<td>" . $row['Meeting_Type'] . "</td>";
                    echo "<td>" . ($row['Meeting_Rating'] ? $row['Meeting_Rating'] : '-') . "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "לא נמצאו דיווחים.";
            }

            $conn->close();
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 כל הזכויות שמורות לאילנה, אלינה ומאי</p>
        <a href="#header"><img id="desing-up-arrow" src="../images/up-arrow.png"></a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <script src="../JS/csv.js"></script>

</body>

</html>
