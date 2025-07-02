<?php
// Ensure database connection
if (!isset($conn)) {
    include 'db.php';
}
$pendingCount = $conn->query("SELECT COUNT(*) AS count FROM bookings WHERE status = 'pending'")->fetch_assoc()['count'];
?>

<!-- Inter font import -->
<link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">

<style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: 'Inter', sans-serif;
        background-color: #f4f6f8;
    }

    .admin-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 100vh;
        background: linear-gradient(180deg, #004225, #00793f);
        color: white;
        padding-top: 30px;
        box-shadow: 3px 0 10px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .admin-sidebar h2 {
        text-align: center;
        font-size: 22px;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 40px;
    }

    .admin-sidebar a {
        display: flex;
        align-items: center;
        color: #ecf0f1;
        text-decoration: none;
        padding: 14px 24px;
        margin: 6px 16px;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-size: 15px;
        position: relative;
    }

    .admin-sidebar a:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateX(5px);
    }

    .admin-sidebar a i,
    .admin-sidebar a span.emoji {
        margin-right: 12px;
        font-size: 18px;
    }

    .notif-badge {
        background-color: #e74c3c;
        color: white;
        padding: 3px 7px;
        font-size: 11px;
        font-weight: bold;
        border-radius: 50%;
        position: absolute;
        right: 18px;
        top: 12px;
    }

    .admin-content {
        margin-left: 270px;
        padding: 20px;
    }

    @media screen and (max-width: 768px) {
        .admin-sidebar {
            width: 100%;
            height: auto;
            position: relative;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 10px 0;
        }

        .admin-sidebar h2 {
            display: none;
        }

        .admin-sidebar a {
            margin: 6px 8px;
            padding: 10px 14px;
            font-size: 13px;
        }

        .admin-content {
            margin-left: 0;
        }

        .notif-badge {
            top: 6px;
            right: 10px;
        }
    }
</style>

<div class="admin-sidebar">
    <h2>👨‍💼 Admin Panel</h2>

    <a href="admin_dashboard.php"><span class="emoji">📋</span> Dashboard</a>

    <a href="manage_bookings.php">
        <span class="emoji">📝</span> Manage Bookings
        <?php if ($pendingCount > 0): ?>
            <span class="notif-badge"><?= $pendingCount ?></span>
        <?php endif; ?>
    </a>

    <a href="avr_schedule.php"><span class="emoji">📅</span> AVR Schedule</a>
    <a href="user_management.php"><span class="emoji">👥</span> User Management</a>
    <a href="announcements.php"><span class="emoji">📢</span> Announcements</a>
    <a href="system_settings.php"><span class="emoji">⚙️</span> System Settings</a>
    <a href="admin_logout.php"><span class="emoji">🚪</span> Logout</a>
</div>
