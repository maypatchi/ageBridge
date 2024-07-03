<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/ageBridge.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>פורום</title>
    <style>
        .card-body {
            text-align: right;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px;
            background-color: white;
            width: 85%;
            margin: 10px auto;
            position: relative;
            direction: rtl;
        }

        .form-check {
            display: flex;
            align-items: center;
            direction: rtl;
            margin-bottom: 10px;
        }

        .form-check input[type="radio"] {
            margin-left: 10px;
        }

        .post-details {
            text-align: left;
            margin-top: 10px;
        }
    </style>
</head>

<body class="forum-php">
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
                            <a class="nav-link active desA" href="forum.php">פורום</a>
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
            <h1 class="design-h1">פורום</h1>
            <div id="postsContainer">
                <?php
                $username = $_SESSION['username']; 
             
                $host = "localhost";
                $user = "ispatchima_admin";
                $password = "admin123!@#";
                $db = "ispatchima_sadna";

                $conn = new mysqli($host, $user, $password, $db);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                //שליפת כל הפוסטים מבסיס הנתונים במצב upload
                $sql = "SELECT posts.*, users.firstName, users.lastName, COUNT(likes.id) AS likes_count
                FROM posts 
                INNER JOIN users ON posts.users_username = users.username
                LEFT JOIN likes ON posts.id = likes.post_id
                WHERE posts.status = 'upload'
                GROUP BY posts.id
                ORDER BY posts.created_at DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        //בדיקה אם המשתמש עשה לייק לפוסט הנוכחי
                        $liked_sql = "SELECT * FROM likes WHERE username='$username' AND post_id='" . $row['id'] . "'";
                        $liked_result = $conn->query($liked_sql);
                        $liked = $liked_result->num_rows > 0;

                        //הצגת הפוסט
                        echo "<div class='card-body' data-post-id='" . $row['id'] . "'>";
                        echo "<h3><strong>" . $row['title'] . "</strong></h3>";
                        echo "<p>מחבר הפוסט: " . $row['firstName'] . " " . $row['lastName'] . "</p>";
                        echo "<p>" . $row['content'] . "</p>";
                        if (!empty($row['image_path'])) {
                            echo "<img src='" . $row['image_path'] . "' class='card-img-top' alt='תמונה'>";
                        }
                        echo "<p>תאריך העלאה: " . $row['created_at'] . "</p>";
                        echo "<p>כמות הלייקים: <span class='likes-count'>" . $row['likes_count'] . "</span></p>";
                        echo "<form method='POST' action='like_post.php'>";
                        echo "<input type='hidden' name='post_id' value='" . $row['id'] . "'>";
                        echo "<button type='button' class='like-button " . ($liked ? 'unlike-button' : '') . "' name='like_button'>" . ($liked ? 'Unlike' : 'Like') . "</button>";
                        echo "<button type='button' class='report-button' data-post-id='" . $row['id'] . "'>דווח</button>";
                        echo "</form>";

                        //שליפת תגובות עבור פוסט מסוים מבסיס הנתונים
                        echo "<div class='post-comments'>";
                        $comments_sql = "SELECT comments.*, users.firstName, users.lastName 
                         FROM comments 
                         INNER JOIN users ON comments.username = users.username 
                         WHERE comments.post_id = " . $row['id'] . " 
                         ORDER BY comments.created_at ASC";
                        $comments_result = $conn->query($comments_sql);

                        //הצגת תגובות עבור פוסט
                        if ($comments_result->num_rows > 0) {
                            while ($comment = $comments_result->fetch_assoc()) {
                                echo "<div class='comment'>";
                                echo "<p><strong>" . $comment['firstName'] . " " . $comment['lastName'] . ":</strong> " . $comment['content'] . "</p>";
                                echo "<p><small>תאריך תגובה: " . $comment['created_at'] . "</small></p>";
                                echo "</div>";
                            }
                        } else {
                            echo "<p>אין תגובות</p>";
                        }
                        echo "</div>";

                        // טופס הוספת תגובה
                        echo "<button type='button' class='comment-button' data-post-id='" . $row['id'] . "'>הוסף תגובה</button>";
                        echo "<form class='add-comment-form' method='POST' style='display:none;' data-post-id='" . $row['id'] . "'>";
                        echo "<input type='hidden' name='postId' value='" . $row['id'] . "'>";
                        echo "<div>";
                        echo "<label for='commentContent'>תוכן תגובה</label>";
                        echo "<textarea class='form-control' name='commentContent' rows='3' required></textarea>";
                        echo "</div>";
                        echo "<button type='submit' class='btn btn-primary' name='submitComment'>הוסף תגובה</button>";
                        echo "</form>";
                        echo "</div>";
                    }
                } else {
                    echo "אין פוסטים להצגה";
                }
                $conn->close();
                ?>

            </div>
            <!-- כפתור להוספת פוסט חדש -->
            <button id="addPostBtn" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                <span>הוסף פוסט חדש</span>
            </button>
        </div>

        <!-- חלונית קופצת להוספת פוסט חדש -->
        <div class="modal fade" id="addPostModal" tabindex="-1" role="dialog" aria-labelledby="addPostModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPostModalLabel">הוספת פוסט חדש</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addPostForm" method="POST" action="upload_post.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">כותרת</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="content">תוכן</label>
                                <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="image">תמונה</label>
                                <input type="file" class="form-control" id="image" accept="image/*" name="image">
                            </div>
                            <button type="submit" class="btn btn-primary btn-full-width" name="submitPost">הוסף
                                פוסט</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- חלונית קופצת לדיווח על פוסט -->
        <div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reportModalLabel">דיווח על פוסט</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-title">מהי סיבת הדיווח?</div>
                        <form id="reportForm" method="POST">
                            <div>
                                <div class="form-check">
                                    <input type="radio" name="reason" id="reason1" value="תוכן בלתי הולם">
                                    <label class="lbl2" for="reason1"> תוכן בלתי הולם </label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="reason" id="reason2" value="הטייה או הונאה">
                                    <label class="lbl2" for="reason2"> הטייה או הונאה </label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="reason" id="reason3" value="תוכן אלים או פוגעני">
                                    <label class="lbl2" for="reason3"> תוכן אלים או פוגעני</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="reason" id="reason4" value="ספאם">
                                    <label class="lbl2" for="reason4">ספאם</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="reason" id="reason5" value="אחר">
                                    <label class="lbl2" for="reason5">אחר </label>
                                </div>
                            </div>
                            <input type="hidden" id="reportReason" name="report_reason">
                            <button type="submit" class="btn btn-primary btn-full-width">שלח דיווח</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 כל הזכויות שמורות לאילנה, אלינה ומאי</p>
        <a href="#header"><img id="desing-up-arrow" src="../images/up-arrow.png"></a>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
        </script>
    <script src="../JS/forum.js"></script>

</body>

</html>