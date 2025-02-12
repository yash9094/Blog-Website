<?php
include 'inc/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .about-image-section {
            margin-top: 10px;
            margin-left: 10px;
            margin-bottom: 10px;
            border-radius: 20px;
            flex: 1;
            background-image: url('inc/img/img-01.jpg');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body>
    <div class="about-container">
        <div class="about-image-section"></div>
        <div class="about-info-section">
            <div>
                <h1 id="about-header">About Us</h1>
                <p class="about-para">Welcome to our blog! We are passionate about sharing knowledge and insights on various topics. Our team consists of experienced writers and enthusiasts who strive to provide valuable content to our readers.</p>
                <p class="about-para">Our mission is to inspire, educate, and entertain through our articles. We believe in the power of words and the impact they can have on people's lives. Thank you for visiting our blog, and we hope you enjoy reading our posts!</p>
            </div>
        </div>
    </div>
</body>

</html>
<?php
include 'inc/footer.php';
?>