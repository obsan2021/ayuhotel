<?php
// Manage Rooms
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../php/config.php';
require_once '../php/db-connection.php';

$db = Database::getInstance();
$rooms = $db->fetchAll("SELECT * FROM rooms ORDER BY id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms - Ayu Hotel Admin</title>
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
                <h1>Manage Rooms</h1>
                <a href="#" class="btn">Add New Room</a>
            </div>
            
            <div class="section">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td><?php echo $room['id']; ?></td>
                            <td><?php echo htmlspecialchars($room['name']); ?></td>
                            <td><?php echo htmlspecialchars($room['type']); ?></td>
                            <td>$<?php echo number_format($room['price'], 2); ?></td>
                            <td><?php echo $room['capacity']; ?> guests</td>
                            <td><?php echo $room['available'] ? 'Available' : 'Unavailable'; ?></td>
                            <td>
                                <a href="#" class="btn">Edit</a>
                                <a href="#" class="btn" style="background: #e74c3c;">Delete</a>
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
