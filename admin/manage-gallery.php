<?php
// Manage Gallery
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../php/config.php';
require_once '../php/db-connection.php';

$db = Database::getInstance();
$galleryItems = $db->fetchAll("SELECT * FROM gallery ORDER BY id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - Ayu Hotel Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-layout">
        <div class="sidebar">
            <h2>Ayu Hotel Admin</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage-bookings.php">Bookings</a></li>
                <li><a href="manage-rooms.php">Rooms</a></li>
                <li><a href="manage-events.php">Events</a></li>
                <li><a href="manage-gallery.php">Gallery</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header-actions">
                <h1>Manage Gallery</h1>
                <a href="#" class="btn">Upload New Image</a>
            </div>
            
            <div class="section">
                <div class="gallery-grid">
                    <?php foreach ($galleryItems as $item): ?>
                    <div class="gallery-item">
                        <img src="../assets/images/<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="gallery-item-info">
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p>Category: <?php echo htmlspecialchars($item['category']); ?></p>
                            <div class="gallery-item-actions">
                                <a href="#" class="btn">Edit</a>
                                <a href="#" class="btn" style="background: #e74c3c;">Delete</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
