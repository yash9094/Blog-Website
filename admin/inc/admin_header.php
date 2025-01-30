<!-- admin_header.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
    
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }

        .admin-dashboard {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background: #2c3e50;
            color: white;
            padding: 1rem;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            margin: 0 0 1rem;
            font-size: 1.5rem;
            text-align: center;
        }

        .sidebar nav a {
            display: block;
            color: white;
            padding: 1rem;
            text-decoration: none;
            transition: background 0.3s, padding-left 0.3s;
        }

        .sidebar nav a:hover {
            background: #34495e;
            padding-left: 1.5rem; 
        }

        .main-content {
            padding: 2rem;
            background: #f8f9fa;
            overflow-y: auto; 
        }
        .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px); 
}

.stat-card h3 {
    margin-top: 0;
    color: #3498db;
    font-size: 1.2rem;
}

.stat-card p {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
}
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
                <a href="manage_posts.php"><i class="fas fa-file-alt"></i> Manage Posts</a>
                <a href="system_setting.php"><i class="fas fa-cogs"></i> System Settings</a>
                <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>

        
        <div class="main-content">