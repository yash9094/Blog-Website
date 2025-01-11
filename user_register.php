<?php
session_start();
include "inc/config.php";
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // password_hash for secure password hashing

    // if username or email already exists
    $checkQuery = "SELECT * FROM user WHERE username='$username' OR email='$email'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $message = "Username or Email already exists!";
    } else {
        // Insert query
        $sql = "INSERT INTO user (fullname, username, email, password) VALUES ('$fullname', '$username', '$email', '$password')";
        if (mysqli_query($conn, $sql)) {
            $message = "Registration successful!";
            header("Location: user_login.php?registered=true");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }

    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Register-People's Thoughts</title>
    <!--Google Font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    <!--Google Font-->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<h1 class="heading-welcome">Welcome To,PEOPLE'S THOUGHTS</h1>

    <div class="container">
        
        <form id="registerForm" action="user_register.php" method="POST">
        <h1 class="heading-register">REGISTER</h1>
        <?php if (!empty($message)): ?>
            <div class="message <?php echo (strpos($message, 'successful') !== false) ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
            <div class="form-group">
                <label for="fullname">Fullname</label>
                <input type="text" id="fullname" name="fullname" placeholder="Enter your fullname" autocomplete="off">
                <small id="fullnameError" class="error-message"></small>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" autocomplete="off">
                <small id="usernameError" class="error-message"></small>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" autocomplete="off">
                <small id="emailError" class="error-message"></small>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="off">
                <small id="passwordError" class="error-message"></small>
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Register</button>
                <p>Alredy User ?<a href="user_login.php">Login</a></p>
            </div>
        </form>
    </div>

<script src="js/script.js"></script>
</body>

</html>