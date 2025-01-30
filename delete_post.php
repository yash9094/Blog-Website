<?php
session_start();
include "inc/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php"); 
    exit();
}


if (isset($_GET['id'])) {
    $postId = $_GET['id'];
    $userId = $_SESSION['user_id']; 

    
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $postId, $userId);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Post deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting post: " . $stmt->error;
    }

    $stmt->close(); 
} else {
    $_SESSION['error_message'] = "No post ID provided.";
}

header("Location: add_post.php");
exit();
?>