<?php
session_start();
include "inc/admin_header.php";
include "inc/config.php";

// Handle role update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_role'])) {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $new_role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

    if ($user_id && in_array($new_role, ['admin', 'editor', 'user'])) {
        $stmt = $conn->prepare("UPDATE user SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $new_role, $user_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Role updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating role: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $user_id = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);

    if ($user_id) {
        // Prevent self-deletion
        if ($user_id == $_SESSION['admin_id']) {
            $_SESSION['error'] = "You cannot delete your own account!";
        } else {
            $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "User  deleted successfully!";
            } else {
                $_SESSION['error'] = "Error deleting user: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Get all users
$users = [];
$search = isset($_GET['search']) ? "%{$_GET['search']}%" : '%';
$stmt = $conn->prepare("
    SELECT id, username, email, role, created_at 
    FROM user 
    WHERE username LIKE ? OR email LIKE ?
    ORDER BY created_at DESC
");
$stmt->bind_param("ss", $search, $search);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .user-management {
            padding: 2rem;
            max-width: 1200px;
            margin: auto;
        }

        h2 {
            /* color: #333; */
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
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
        }

        .search-bar input {
            padding: 0.8rem;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
        }

        .users-table th,
        .users-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .users-table th {
            background: #007bff;
            color: white;
        }

        .role-select {
            padding: 0.3rem;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .delete-btn {
            color: #e74c3c;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            text-decoration: none;
        }

        .delete-btn:hover {
            background: #f8d7da;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>

    <div class="main-content">
        <div class="user-management">
            <h2>Manage Users</h2>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="message success"><?php echo $_SESSION['message'];
                                                unset($_SESSION['message']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="message error"><?php echo $_SESSION['error'];
                                            unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <!-- Search Bar -->
            <form class="search-bar" method="GET">
                <input type="text" name="search" placeholder="Search users..."
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit" class="btn">Search</button>
            </form>

            <!-- Users Table -->
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="role" class="role-select" onchange="this.form.submit()">
                                        <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : '' ?>>User </option>
                                        <option value="editor" <?php echo $user['role'] == 'editor' ? 'selected' : '' ?>>Editor</option>
                                        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <input type="hidden" name="update_role" value="1">
                                </form>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="?delete=<?php echo $user['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?');">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Live search functionality
        const searchInput = document.querySelector('input[name="search"]');
        searchInput.addEventListener('input', function(e) {
            const searchValue = this.value.toLowerCase();
            document.querySelectorAll('.users-table tbody tr').forEach(row => {
                const username = row.children[0].textContent.toLowerCase();
                const email = row.children[1].textContent.toLowerCase();
                row.style.display = (username.includes(searchValue) || email.includes(searchValue)) ? '' : 'none';
            });
        });
    </script>
</body>

</html>