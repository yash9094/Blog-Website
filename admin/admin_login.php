<?php
session_start();
include "inc/config.php";


if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security violation detected");
    }

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? AND role = 'admin'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        // Regenerate session ID
        session_regenerate_id(true);
        
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_role'] = $admin['role'];
        $_SESSION['last_activity'] = time();
        
        header("Location: index.php");
        exit();
    } else {
        $message = "Invalid admin credentials!";
    }
    $stmt->close();
}

// Generate CSRF token FOr More Securtiy.
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - People's Thoughts</title>
    <style>
        .admin-login {
            max-width: 400px;
            margin: 5% auto;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .admin-login h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 2rem;
        }
        .admin-login .form-group {
            margin-bottom: 1.5rem;
        }
        .admin-login input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 0.5rem;
        }
        .admin-login button {
            width: 100%;
            padding: 1rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-login">
        <h1>Admin Portal</h1>
        <?php if (!empty($message)): ?>
            <div class="message error"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="admin_login.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required autocomplete="off">
            </div>
            
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>