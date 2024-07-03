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

//שליפת כל הפוסטים שהתקבל עליהם דיווח
$sql = "
    SELECT
        reports.id AS report_id,
        reports.post_id,
        posts.title AS post_title,
        posts.content AS post_content,
        posts.created_at AS post_created_at,
        posts.users_username AS post_author,
        reports.reason,
        reports.reported_at
    FROM
        reports
    JOIN
        posts ON reports.post_id = posts.id
    WHERE
        posts.status = 'upload'
    ORDER BY
        reports.reported_at DESC
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
    <title>דיווחים על פוסטים</title>
    <style>
        th,
        td {
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
            <h2 id="design-h2">דיווחים על פוסטים</h2>
        </div>

        <div>
            <button id="exportBtn3" class="btn btn-primary design-csv">ייצוא ל-CSV</button>
        </div>

        <!-- טבלת פוסטים -->
        <div class="container-table">
            <?php
            if ($result->num_rows > 0) {
                echo "<table id='reportTable3'>";
                echo "<tr>
                <th>מספר דיווח</th>
                <th>מספר פוסט</th>
                <th>כותרת הפוסט</th>
                <th>תוכן הפוסט</th>
                <th>מחבר הפוסט</th>
                <th>תאריך יצירת הפוסט</th>
                <th>סיבת הדיווח</th>
                <th>תאריך דיווח</th>
                <th>פעולה</th>
              </tr>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['report_id'] . "</td>";
                    echo "<td>" . $row['post_id'] . "</td>";
                    echo "<td>" . $row['post_title'] . "</td>";
                    echo "<td>" . $row['post_content'] . "</td>";
                    echo "<td>" . $row['post_author'] . "</td>";
                    echo "<td>" . $row['post_created_at'] . "</td>";
                    echo "<td>" . $row['reason'] . "</td>";
                    echo "<td>" . $row['reported_at'] . "</td>";
                    //לחצן למחיקת פוסט
                    echo "<td><button class='delete-button' onclick='deletePost(" . $row['post_id'] . ")'>מחק פוסט</button></td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "לא נמצאו דיווחים.";
            }
            ?>
        </div>

    </main>

    <footer>
        <p>&copy; 2024 כל הזכויות שמורות לאילנה, אלינה ומאי</p>
        <a href="#header"><img id="desing-up-arrow" src="../images/up-arrow.png"></a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

    <script src="../JS/Post_Report.js"></script>

    <script src="../JS/csv.js"></script>

</body>

</html>