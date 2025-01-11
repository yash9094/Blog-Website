<?php
session_start();
include "inc/config.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php"); // Redirect to login page if not logged in
    exit();
}

// Check if the post ID is provided
if (isset($_GET['id'])) {
    $postId = $_GET['id'];
    $userId = $_SESSION['user_id']; // Get the logged-in user's ID

    // Prepare and execute the delete query
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $postId, $userId);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Post deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting post: " . $stmt->error;
    }
} else {
    $_SESSION['error_message'] = "No post ID provided.";
}

// Redirect back to the add_post.php page
header("Location: add_post.php");
exit();
?>