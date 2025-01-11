<?php
session_start();
include "inc/config.php";
include "inc/header.php";

// Get the logged-in user's ID
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch all post
$query = "SELECT posts.*, user.username FROM posts 
          JOIN user ON posts.user_id = user.id 
          WHERE posts.user_id != ? 
          ORDER BY posts.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="post-container">
        <h1>PEOPLE's THOUGHTS</h1>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="user-post">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><strong>Posted by: <?php echo htmlspecialchars($row['username']); ?></strong></p>
                    <p>
                        <?php
                        // Display a truncated version of the content
                        $content = strip_tags($row['content']); // Remove HTML tags
                        $truncatedContent = strlen($content) > 150 ? substr($content, 0, 150) . '...' : $content;
                        echo nl2br(htmlspecialchars($truncatedContent));
                        ?>
                    </p>
                    <a href="view_post.php?id=<?php echo $row['id']; ?>" class="read-more">Read More</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No posts available.</p>
        <?php endif; ?>
    </div>
</body>
<?php
include "inc/footer.php";
?>
</html>