<?php
session_start();
include "inc/config.php"; 
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; 

    // Server-side validation
    $errors = [];

    // Fullname Validation
    if (empty($fullname) || !preg_match("/^[a-zA-Z\s]+$/", $fullname)) {
        $errors[] = "Please enter a valid fullname (letters and spaces only).";
    }

    // Username Validation
    if (strlen($username) < 5) {
        $errors[] = "Username must be at least 5 characters long.";
    }

    // Email Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // Password Validation
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    
    if (empty($errors)) {
        
        $checkQuery = "SELECT * FROM user WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $checkResult = $stmt->get_result();

        if ($checkResult->num_rows > 0) {
            $message = "Username or Email already exists!";
        } else {
           
            $passwordHash = password_hash($password, PASSWORD_DEFAULT); 
            $role = 'editor'; 
            $sql = "INSERT INTO user (fullname, username, email, password, role) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $fullname, $username, $email, $passwordHash, $role);
            if ($stmt->execute()) {
                $message = "Registration successful!";
                header("Location: user_login.php?registered=true");
                exit();
            } else {
                $message = "Error: " . $stmt->error;
            }
        }
    } else {
       
        $message = implode("<br>", $errors);
    }

    $stmt->close(); 
}

$conn->close(); 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Register - People's Thoughts</title>
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    <!-- Google Font -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <h1 class="heading-welcome">Welcome To, PEOPLE'S THOUGHTS</h1>

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
                <input type="text" id="fullname" name="fullname" placeholder="Enter your fullname" required autocomplete="off">
                <small id="fullnameError" class="error-message"></small>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required autocomplete="off">
                <small id="usernameError" class="error-message"></small>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required autocomplete="off">
                <small id="emailError" class="error-message"></small>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="off">
                <small id="passwordError" class="error-message"></small>
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Register</button>
                <p>Already a User? <a href="user_login.php">Login</a></p>
            </div>
        </form>
    </div>

    <script src="js/script.js"></script>
</body>

</html>