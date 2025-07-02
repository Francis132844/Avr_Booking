<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Get today's date
$today = date('Y-m-d');

// Today's bookings
$todayStmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings WHERE booking_date = ?");
$todayStmt->bind_param("s", $today);
$todayStmt->execute();
$todayResult = $todayStmt->get_result()->fetch_assoc();
$totalToday = $todayResult['count'];

// Status counts
$pending = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'")->fetch_assoc()['count'];
$approved = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'approved'")->fetch_assoc()['count'];
$rejected = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'rejected'")->fetch_assoc()['count'];

$adminName = $_SESSION['admin_name'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to right, #eef2f3, #ffffff);
            color: #333;
        }

        .admin-content {
            margin-left: 250px;
            padding: 40px;
            transition: all 0.3s ease;
        }

        h1 {
            color: #1a1a1a;
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .date-info {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 25px;
        }

        .card {
            background: #ffffff;
            border-left: 6px solid #3498db;
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.07);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.12);
        }

        .card h3 {
            font-size: 15px;
            margin-bottom: 10px;
            color: #555;
            font-weight: 600;
            text-transform: uppercase;
        }

        .card h2 {
            font-size: 40px;
            color: #2c3e50;
            margin: 0;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .admin-content {
                margin-left: 0;
                padding: 20px;
            }
        }

        /* Welcome Modal */
        .welcome-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #ffffff;
            padding: 30px 45px;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
            z-index: 9999;
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            opacity: 0;
            animation: fadeIn 0.5s forwards;
        }

        .fade-out {
            animation: fadeOut 0.5s forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -60%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }

        @keyframes fadeOut {
            from { opacity: 1; transform: translate(-50%, -50%); }
            to { opacity: 0; transform: translate(-50%, -60%); }
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="welcome-modal" id="welcomeModal">
    👋 Welcome back, <?= htmlspecialchars($adminName) ?>!
</div>

<div class="admin-content">
    <h1>🖥 Admin Dashboard</h1>
    <div class="date-info" id="current-date"></div>

    <div class="card-grid">
        <div class="card" style="border-left-color: #2980b9;">
            <h3>📆 Bookings Today</h3>
            <h2><?= $totalToday ?></h2>
        </div>
        <div class="card" style="border-left-color: #f39c12;">
            <h3>⏳ Pending</h3>
            <h2><?= $pending ?></h2>
        </div>
        <div class="card" style="border-left-color: #27ae60;">
            <h3>✅ Approved</h3>
            <h2><?= $approved ?></h2>
        </div>
        <div class="card" style="border-left-color: #e74c3c;">
            <h3>❌ Rejected</h3>
            <h2><?= $rejected ?></h2>
        </div>
    </div>
</div>

<script>
    window.onload = function () {
        // Display current date
        const today = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date').textContent = today.toLocaleDateString(undefined, options);

        // Modal fade
        const modal = document.getElementById("welcomeModal");

        setTimeout(() => {
            modal.classList.add("fade-out");
        }, 3000);

        setTimeout(() => {
            modal.style.display = 'none';
        }, 3500);
    };
</script>

</body>
</html>
