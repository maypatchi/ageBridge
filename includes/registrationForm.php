<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "ispatchima_admin";
$password = "admin123!@#";
$db = "ispatchima_sadna";

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$type = $_POST['type'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$birthdate = $_POST['birthdate'];
$gender = $_POST['gender'];
$city = $_POST['city'];
$hobbies = implode(", ", $_POST['hobbies']);
$username = $_POST['username'];
$password = $_POST['password'];

$targetDir = "../uploads/";

// קביעת סיומת הקובץ המקורית
$imageFileType = pathinfo($_FILES['profilePicture']['name'], PATHINFO_EXTENSION);

// שמירת התמונה בשרת עם שם המשתמש והסיומת המקורית
$targetFilePath = $targetDir . $username . '.' . $imageFileType;

$check = getimagesize($_FILES['profilePicture']['tmp_name']);
if($check !== false) {
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if(move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetFilePath)) {
        echo "File uploaded successfully to " . $targetFilePath . "<br>";
    } else {
        echo "Sorry, there was an error uploading your file.";
        exit();
    }
} else {
    echo "File is not an image.";
    exit();
}

// בודק אם שם המשתמש שנבחר כבר קיים במערכת
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<script type='text/javascript'>
            alert('שם המשתמש שבחרת כבר קיים, בחר שם אחר');
            window.history.back();
          </script>";
} else {
    $sql = "INSERT INTO users (type, firstName, lastName, birthdate, gender, city, hobbies, username, password, profilePicture) 
            VALUES ('$type', '$firstName', '$lastName', '$birthdate', '$gender', '$city', '$hobbies', '$username', '$password', '$targetFilePath')";

    if ($conn->query($sql) === FALSE) {
        echo "<script type='text/javascript'>
                alert('משהו השתבש, נסה להירשם שנית " . $conn->error . "');
                window.location.href = 'registration.html'; 
              </script>";
    } else {
        echo "<script type='text/javascript'>
                alert('נרשמת בהצלחה למערכת, לחץ על אישור והתחבר');
                window.location.href = '../index.php';
              </script>";
    }
}

$conn->close();
?>
