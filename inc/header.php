<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}
include "inc/config.php"
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>People's Thoughts</title>
    <!--Google Font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    <!--Google Font-->
    <link rel="stylesheet" href="css/navbar.css">
</head>

<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">People's Thoughts</a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="add_post.php">Posts</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="user_login.php">Login</a></li>
                    <li><a href="user_login.php">About</a></li>
                    <li><a href="user_login.php">Contact</a></li>
                <?php endif; ?>
            </ul>
            <div class="menu-toggle" id="mobile-menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>
    <script src="js/navbar.js"></script>
</body>

</html>