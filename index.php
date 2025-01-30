<?php
session_start();
include "inc/config.php";
include "inc/header.php";

// Get the logged-in user's ID
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


// Initialize search variable
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination variables
$limit = 5; // Number of posts per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch all post
if ($userId) {
    // User is logged in, exclude their posts
    $query = "SELECT posts.*, user.username FROM posts 
              JOIN user ON posts.user_id = user.id 
              WHERE posts.user_id != ? AND (posts.title LIKE ? OR posts.content LIKE ?) 
              ORDER BY posts.created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    $searchTerm = '%' . $search . '%';
    $stmt->bind_param("issii", $userId, $searchTerm, $searchTerm, $limit, $offset);
} else {
    // User is not logged in, show all posts
    $query = "SELECT posts.*, user.username FROM posts 
              JOIN user ON posts.user_id = user.id 
              WHERE (posts.title LIKE ? OR posts.content LIKE ?) 
              ORDER BY posts.created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    $searchTerm = '%' . $search . '%';
    $stmt->bind_param("ssii", $searchTerm, $searchTerm, $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

// Fetch total number of posts for pagination
$totalQuery = "SELECT COUNT(*) as total FROM posts WHERE user_id != ? AND (title LIKE ? OR content LIKE ?)";
$totalStmt = $conn->prepare($totalQuery);
$totalStmt->bind_param("iss", $userId, $searchTerm, $searchTerm);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalPosts = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $limit);
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

        <form action="index.php" method="GET" id="search-form">
            <input type="text" name="search" id="search-input" class="search-input" placeholder="Search posts..." value="<?php echo htmlspecialchars($search); ?>" autocomplete="off">
            <button type="submit" id="search-button" class="search-button">Search</button>
        </form>

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
                    <?php if ($userId): ?>
                    <a href="view_post.php?id=<?php echo $row['id']; ?>" class="read-more">Read More</a>
                    <?php else: ?>
                        <a href="user_login.php" class="read-more">Read More (Login Required)</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No posts available.</p>
        <?php endif; ?>

        <!-- Pagination Links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
            <?php endif; ?>
        </div>

    </div>
</body>
<?php
include "inc/footer.php";
?>

</html>