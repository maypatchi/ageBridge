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

// קבלת הערך של הסטטוס מהטופס אם הוא קיים
$status = isset($_POST['status']) ? $_POST['status'] : 'all';


//שליפת הנתונים של הפוסטים כולל לייקים ותגובות
$sql = "
    SELECT 
        p.id AS 'Post_ID',
        p.users_username AS 'Username',
        p.title AS 'Title',
        p.content AS 'Content',
        p.created_at AS 'Created_At',
        COUNT(DISTINCT l.id) AS 'Likes_Count',
        COUNT(DISTINCT c.id) AS 'Comments_Count'
    FROM 
        posts p
    LEFT JOIN 
        likes l ON p.id = l.post_id
    LEFT JOIN 
        comments c ON p.id = c.post_id
    WHERE 
        p.status IN ('upload', 'deleted') ";

if ($status !== 'all') {
    $sql .= " AND p.status = '$status' ";
}

$sql .= "
    GROUP BY 
        p.id, p.users_username, p.title, p.content, p.created_at
    ORDER BY 
        p.created_at DESC;
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
    <title>דוח פוסטים</title>
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
            <h2 id="design-h2">דו"ח פוסטים</h2>
        </div>

        <div>
            <button id="exportBtn2" class="btn btn-primary design-csv">ייצוא ל-CSV</button>
        </div>

        <div class="select-status">
            <!-- טופס עם תיבת בחירה לבחירת סטטוס -->
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="status-selector">
                    <label for="status">בחר סטטוס להצגה:</label>
                    <select name="status" id="status">
                        <option value="all" <?php if ($status === 'all')
                            echo 'selected'; ?>>הצג הכל</option>
                        <option value="upload" <?php if ($status === 'upload')
                            echo 'selected'; ?>>פוסטים במצב פעילים
                        </option>
                        <option value="deleted" <?php if ($status === 'deleted')
                            echo 'selected'; ?>>פוסטים שנמחקו
                        </option>
                    </select>
                </div>
            </form>
        </div>

        <!--טבלה שמציגה את הדוח -->
        <div class="container-table">
            <?php
            if ($result->num_rows > 0) {
                echo "<table id='reportTable2'>";
                echo "<tr>";
                echo "<th>שם כותב הפוסט</th>";
                echo "<th>כותרת</th>";
                echo "<th>תוכן</th>";
                echo "<th>תאריך יצירה</th>";
                echo "<th>מספר לייקים</th>";
                echo "<th>מספר תגובות</th>";
                echo "</tr>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['Username'] . "</td>";
                    echo "<td>" . $row['Title'] . "</td>";
                    echo "<td>" . $row['Content'] . "</td>";
                    echo "<td>" . $row['Created_At'] . "</td>";
                    echo "<td>" . $row['Likes_Count'] . "</td>";
                    echo "<td>" . $row['Comments_Count'] . "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<p class='container1'>לא נמצאו פוסטים שנמחקו</p>";
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
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

    <script src="../JS/Forum_Report.js"></script>

    <script src="../JS/csv.js"></script>

</body>

</html>