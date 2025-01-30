<?php
session_start();
include "inc/config.php";

// Security check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Session timeout (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: admin_login.php");
    exit();
}
$_SESSION['last_activity'] = time();

// Get statistics
$stats = [
    'total_users' => $conn->query("SELECT COUNT(*) FROM user")->fetch_row()[0],
    'total_posts' => $conn->query("SELECT COUNT(*) FROM posts")->fetch_row()[0],
    'today_posts' => $conn->query("SELECT COUNT(*) FROM posts WHERE DATE(created_at) = CURDATE()")->fetch_row()[0]
];
?>

<?php include "inc/admin_header.php"; ?>

<h1>Welcome Admin</h1>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Users</h3>
        <p><?php echo $stats['total_users']; ?></p>
    </div>
    <div class="stat-card">
        <h3>Total Posts</h3>
        <p><?php echo $stats['total_posts']; ?></p>
    </div>
    <div class="stat-card">
        <h3>Today's Posts</h3>
        <p><?php echo $stats['today_posts']; ?></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // const ctx = document.createElement('canvas');
    // ctx.style.marginTop = '2rem';
    // document.querySelector('.main-content').appendChild (ctx);
    
    // new Chart(ctx, {
    //     type: 'line',
    //     data: {
    //         labels: ['Users', 'Posts', 'Comments'],
    //         datasets: [{
    //             label: 'Site Statistics',
    //             data: [
    //                 <?php echo $stats['total_users']; ?>,
    //                 <?php echo $stats['total_posts']; ?>,
    //                 0 // Replace with actual comment count
    //             ],
    //             borderColor: '#3498db',
    //             tension: 0.1
    //         }]
    //     }
    // });
</script>
</div> 
</div> 
</body>
</html>