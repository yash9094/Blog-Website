<?php
session_start();
include "inc/admin_header.php";
include "inc/config.php";

// Handling  post deletion
if (isset($_GET['delete'])) {
    $post_id = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    
    if ($post_id) {
        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Post deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting post: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Geting  all posts
$posts = [];
$search = isset($_GET['search']) ? "%{$_GET['search']}%" : '%';
$stmt = $conn->prepare("
    SELECT p.id, p.title, p.content, p.created_at, u.username as author 
    FROM posts p
    JOIN user u ON p.user_id = u.id
    WHERE p.title LIKE ? OR u.username LIKE ?
    ORDER BY p.created_at DESC
");
$stmt->bind_param("ss", $search, $search);
$stmt->execute();
$result = $stmt->get_result();
$posts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .page-content {
            padding: 2rem;
            max-width: 1200px;
            margin: auto;
        }

        h1 {
            color: #333;
            margin-bottom: 1rem;
        }

        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .search-bar {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
        }

        .search-bar input {
            padding: 0.8rem;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .posts-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
        }

        .posts-table th, 
        .posts-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .posts-table th {
            background-color: #007bff;
            color: white;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-edit, .btn-delete {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            transition: background 0.3s;
        }

        .btn-edit {
            background-color: #3498db;
        }

        .btn-delete {
            background-color: #e74c3c;
        }

        .btn-edit:hover {
            background-color: #2980b9;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }
        .btn-search {
    background-color: #007bff; 
    color: white;
    padding: 0.8rem 1.2rem; 
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s; 
    font-size: 1rem;
}

.btn-search:hover {
    background-color: #0056b3; 
    transform: translateY(-2px); 
}

.btn-search:focus {
    outline: none; 
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); 
}
    </style>
</head>
<body>
<div class="page-content">
    <div class="page-header">
        <h1>Manage Posts</h1>
    </div>

    <div class="content-section">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search" autocomplete="off" placeholder="Search posts..." 
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit" class="btn-search" >Search</button>
            </form>
        </div>

        <div class="posts-table">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Content Preview</th>
                        <th>Author</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?= htmlspecialchars($post['title']) ?></td>
                        <td><?= substr(htmlspecialchars($post['content']), 0, 50) ?>...</td>
                        <td><?= htmlspecialchars($post['author']) ?></td>
                        <td><?= date('M j, Y', strtotime($post['created_at'])) ?></td>
                        <td>
                            <div class="actions">
                                <!-- <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn-edit">Edit</a> -->
                                <a href="?delete=<?= $post['id'] ?>" class="btn-delete" 
                                   onclick="return confirm('Are you sure? This cannot be undone.')">
                                    Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>