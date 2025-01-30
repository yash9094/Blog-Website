<?php
session_start();
include "inc/config.php";
include "inc/header.php";

if (isset($_GET['id'])) {
    $postId = $_GET['id'];

    // Fetch the post data
    $query = "SELECT posts.*, user.username FROM posts 
              JOIN user ON posts.user_id = user.id 
              WHERE posts.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
    } else {
        echo "Post not found.";
        exit();
    }
} else {
    echo "No post ID provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="post-container">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p><strong>Posted by: <?php echo htmlspecialchars($post['username']); ?></strong></p>
        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
        <p><em>Posted on <?php echo date('Y-m-d H:i:s', strtotime($post['created_at'])); ?></em></p>
        <a href="index.php" class="back-btn">Back to Home</a>
    </div>
</body>
</html>

<?php
include "inc/footer.php";
?>