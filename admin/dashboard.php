<?php
// Admin Dashboard
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../php/config.php';
require_once '../php/db-connection.php';

$db = Database::getInstance();

// Get statistics
$totalBookings = $db->fetchOne("SELECT COUNT(*) as count FROM bookings")['count'];
$pendingBookings = $db->fetchOne("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'")['count'];
$confirmedBookings = $db->fetchOne("SELECT COUNT(*) as count FROM bookings WHERE status = 'confirmed'")['count'];
$totalRevenue = $db->fetchOne("SELECT SUM(total_price) as total FROM bookings WHERE payment_status = 'paid'")['total'] ?? 0;

// Get recent bookings
$recentBookings = $db->fetchAll("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 10");

// Get recent messages
$recentMessages = $db->fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Ayu Hotel Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #1a1a1a;
            color: white;
            padding: 20px;
        }
        .sidebar h2 {
            color: #c9a227;
            margin-bottom: 30px;
        }
        .sidebar ul {
            list-style: none;
        }
        .sidebar li {
            margin-bottom: 10px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 4px;
        }
        .sidebar a:hover {
            background: #c9a227;
        }
        .main-content {
            flex: 1;
            padding: 40px;
            background: #f8f8f8;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            color: #c9a227;
        }
        .section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .section h2 {
            margin-bottom: 20px;
            color: #1a1a1a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f8f8;
            font-weight: 600;
        }
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .status.pending {
            background: #fff3cd;
            color: #856404;
        }
        .status.confirmed {
            background: #d4edda;
            color: #155724;
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
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
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></h1>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Bookings</h3>
                    <div class="value"><?php echo $totalBookings; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Pending Bookings</h3>
                    <div class="value"><?php echo $pendingBookings; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Confirmed Bookings</h3>
                    <div class="value"><?php echo $confirmedBookings; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <div class="value">$<?php echo number_format($totalRevenue, 2); ?></div>
                </div>
            </div>
            
            <div class="section">
                <h2>Recent Bookings</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Booking Ref</th>
                            <th>Guest Name</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentBookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['booking_ref']); ?></td>
                            <td><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['check_in']); ?></td>
                            <td><?php echo htmlspecialchars($booking['check_out']); ?></td>
                            <td><span class="status <?php echo $booking['status']; ?>"><?php echo htmlspecialchars($booking['status']); ?></span></td>
                            <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="section">
                <h2>Recent Messages</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentMessages as $message): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($message['name']); ?></td>
                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                            <td><?php echo htmlspecialchars($message['subject']); ?></td>
                            <td><?php echo htmlspecialchars($message['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
