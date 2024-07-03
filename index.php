<!DOCTYPE html>
<html lang="he">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/ageBridge.css">
    <title>AgeBridge</title>
</head>

<body>
    <div class="container">
        <div class="login-form">
            <form action="includes/login.php" method="POST">
                <input class="input-design-login-form" type="text" name="username" placeholder="שם משתמש" required><br>
                <input class="input-design-login-form" type="password" name="password" placeholder="סיסמה" required><br>
                <input id="color-btn" class="input-design-login-form" type="submit" value="התחברות">
            </form>
            <?php
            if (isset($_GET['error'])) {
                echo "<p class='error-design'>שם משתמש או סיסמה לא נכונים</p>";
            }
            ?>
            <div class="register-option">
                <p>רוצה להצטרף אלינו?
                    <a href="includes/registrationForm.html">הירשם כאן</a>
                </p>
            </div>
        </div>
        <div class="login-info">
            <img src="images/logo.png" alt="לוגו">
            <p> ברוכים הבאים למקום המחבר בין מבוגרים ומתנדבים.<br>
                כאן המתנדבים יוכלו להפיג את תחושת הבדידות של המבוגרים, להעניק להם תחושת מסוגלות ומשמעות ולהשכיל מניסיון
                חייהם.
                באתר זה תוכלו ליצור קשר עם מתנדבים או להתנדב בעצמכם וכך ליצור קהילה טובה ומאוחדת יותר.<br>
                הצטרפו אלינו עוד היום!</p>
        </div>
    </div>
</body>

</html>