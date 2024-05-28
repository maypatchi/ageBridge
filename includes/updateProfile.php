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
$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "No user found";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newCity = $_POST['city'];
    $newHobbies = implode(", ", $_POST['hobbies']);
    $newPassword = $_POST['password'];

    $updateSql = "UPDATE users SET city='$newCity', hobbies='$newHobbies', password='$newPassword' WHERE username='$username'";
    if ($conn->query($updateSql) === TRUE) {
        echo "Profile updated successfully";
    } else {
        echo "Error updating profile: " . $conn->error;
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
    <title>עריכת פרופיל</title>
    <style>
        .edit-profile-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .edit-profile-form {
            text-align: right;
        }
        .form-group {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <header>
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
                            <a class="nav-link active desA" href="profile.php">הפרופיל שלי</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link desA" href="#">החברים שלי</a>
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
        <div class="container edit-profile-container">
            <form action="editProfile.php" method="post" class="edit-profile-form">
                <div class="form-group">
                    <label for="city">עיר מגורים:</label>
                    <input type="text" id="city" name="city" value="<?php echo $user['city']; ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="hobbies">תחביבים:</label>
                    <textarea id="hobbies" name="hobbies" class="form-control" required><?php echo $user['hobbies']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="password">סיסמה חדשה:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">עדכן פרופיל</button>
            </form>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>
</html>
