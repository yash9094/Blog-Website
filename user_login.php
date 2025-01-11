<?php
session_start();
include "inc/config.php";
$message = ''; 


if (isset($_GET['registered']) && $_GET['registered'] == 'true') {
    $message = "Your account was created successfully!";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    
    $sql = "SELECT * FROM user WHERE username='$username'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    
    if ($user && password_verify($password, $user['password'])) {
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

    
        header("Location: index.php"); // change this to your desired page
        exit();
    } else {
        $message = "Invalid username or password!";
    }

    mysqli_close($conn);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login-People's Thoughts</title>
    <!--Google Font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    <!--Google Font-->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <h1 class="heading-welcome">PEOPLE'S THOUGHTS</h1>

    <div class="container">

    <?php if (!empty($message)): ?>
            <div class="message <?php echo (strpos($message, 'successful') !== false) ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="user_login.php" method="POST">
            <h1 class="heading-register">LOGIN</h1>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="off">
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Login</button>
                <br>
                <p>Don't Have Account ? <a href="user_register.php">Register Here</a></p>
            </div>
        </form>
    </div>


</body>

</html>