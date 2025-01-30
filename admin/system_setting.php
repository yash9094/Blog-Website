<?php
require_once 'inc/admin_header.php';
include "inc/config.php";

$conn->query("
    CREATE TABLE IF NOT EXISTS system_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(255) NOT NULL UNIQUE,
        setting_value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Basic settings
        $settings = [
            'site_title' => $_POST['site_title'],
            'site_description' => $_POST['site_description'],
            'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0,
            'posts_per_page' => (int)$_POST['posts_per_page']
        ];

        // Handle logo upload
        if (!empty($_FILES['site_logo']['name'])) {
            $uploadDir = 'uploads/';
            $allowedTypes = ['image/png', 'image/jpeg', 'image/svg+xml'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            if (!in_array($_FILES['site_logo']['type'], $allowedTypes)) {
                throw new Exception("Invalid file type. Only PNG, JPG, and SVG are allowed.");
            }

            if ($_FILES['site_logo']['size'] > $maxSize) {
                throw new Exception("File size exceeds 2MB limit.");
            }

            $fileName = uniqid('logo_') . '_' . basename($_FILES['site_logo']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $targetPath)) {
                $settings['site_logo'] = $targetPath;
            }
        }

        // Save settings
        foreach ($settings as $key => $value) {
            $stmt = $conn->prepare("
                INSERT INTO system_settings (setting_key, setting_value) 
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            $stmt->bind_param("sss", $key, $value, $value);
            $stmt->execute();
            $stmt->close();
        }

        $_SESSION['message'] = "Settings updated successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// Get current settings
$result = $conn->query("SELECT setting_key, setting_value FROM system_settings");
$currentSettings = [];
while ($row = $result->fetch_assoc()) {
    $currentSettings[$row['setting_key']] = $row['setting_value'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Setting</title>
    <style>
.system-settings {
    max-width: 800px;
    margin: 0 auto;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group textarea {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.checkbox-group label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.current-logo {
    margin: 1rem 0;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 4px;
}

.btn-primary {
    background: #3498db;
    color: white;
    padding: 1rem 2rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
}

.btn-primary:hover {
    background: #2980b9;
}
    </style>
</head>
<body>
<div class="page-content">
    <div class="page-header">
        <h1>System Settings</h1>
    </div>

    <div class="content-section">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Site Title</label>
                <input type="text" name="site_title" 
                    value="<?= htmlspecialchars($currentSettings['site_title'] ?? 'My Blog') ?>" required>
            </div>

            <div class="form-group">
                <label>Site Description</label>
                <textarea name="site_description" rows="3"><?= 
                    htmlspecialchars($currentSettings['site_description'] ?? 'A great blogging platform') 
                ?></textarea>
            </div>

            <div class="form-group">
                <label>Posts Per Page</label>
                <input type="number" name="posts_per_page" min="1" max="50"
                    value="<?= htmlspecialchars($currentSettings['posts_per_page'] ?? 10) ?>" required>
            </div>

            <div class="form-group">
                <label>Site Logo</label>
                <?php if (!empty($currentSettings['site_logo'])): ?>
                    <div class="current-logo">
                        <img src="<?= $currentSettings['site_logo'] ?>" alt="Current Logo" height="50">
                    </div>
                <?php endif; ?>
                <input type="file" name="site_logo" accept="image/png, image/jpeg, image/svg+xml">
            </div>

            <div class="form-group checkbox-group">
                <label>
                    <input type="checkbox" name="maintenance_mode" 
                        <?= ($currentSettings['maintenance_mode'] ?? 0) ? 'checked' : '' ?>>
                    Enable Maintenance Mode
                </label>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>


