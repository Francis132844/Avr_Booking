<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user name
$user_query = $conn->prepare("SELECT name FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();
$name = $user['name'] ?? 'User';

// Get booking stats
$stats = ['total' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0];
$query = $conn->prepare("
    SELECT status, COUNT(*) as count 
    FROM bookings 
    WHERE user_id = ? 
    GROUP BY status
");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
while ($row = $result->fetch_assoc()) {
    $stats[strtolower($row['status'])] = $row['count'];
    $stats['total'] += $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to bottom right, #004422, #008000);
            color: #fff;
        }

        .content {
            margin-left: 250px;
            padding: 40px;
        }

        .card {
            background: rgba(255, 255, 255, 0.07);
            border-radius: 18px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px);
            animation: fadeIn 0.8s ease;
        }

        .card h1 {
            margin: 0;
            font-size: 32px;
            color: #d0ffd0;
        }

        .card p {
            font-size: 18px;
            color: #ecf5ec;
            margin-top: 12px;
        }

        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
        }

        .stat-card {
            flex: 1;
            min-width: 200px;
            padding: 25px 18px;
            border-radius: 15px;
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
        }

        .total { background-color: rgba(26, 188, 156, 0.8); }
        .approved { background-color: rgba(46, 204, 113, 0.8); }
        .pending { background-color: rgba(243, 156, 18, 0.8); }
        .rejected { background-color: rgba(231, 76, 60, 0.8); }

        .stat-icon {
            font-size: 34px;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 15px;
            font-weight: 500;
        }

        .stat-value {
            font-size: 30px;
            font-weight: 700;
            margin-top: 6px;
        }

        #greeting {
            font-size: 15px;
            margin-top: 10px;
            color: #b4ffb4;
            font-weight: 600;
            animation: slideIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @media screen and (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 20px;
            }

            .stats-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <div class="card">
        <h1>🏠 AVR Dashboard</h1>
        <p>Welcome back, <strong><?php echo htmlspecialchars($name); ?></strong>!</p>
        <div id="greeting">Loading greeting...</div>
    </div>

    <div class="stats-container">
        <div class="stat-card total" onclick="alert('📋 Total Bookings: <?php echo $stats['total']; ?>')">
            <div class="stat-icon">📋</div>
            <div class="stat-label">Total Bookings</div>
            <div class="stat-value"><?php echo $stats['total']; ?></div>
        </div>
        <div class="stat-card approved" onclick="alert('✅ Approved: <?php echo $stats['approved']; ?>')">
            <div class="stat-icon">✅</div>
            <div class="stat-label">Approved</div>
            <div class="stat-value"><?php echo $stats['approved']; ?></div>
        </div>
        <div class="stat-card pending" onclick="alert('⏳ Pending: <?php echo $stats['pending']; ?>')">
            <div class="stat-icon">⏳</div>
            <div class="stat-label">Pending</div>
            <div class="stat-value"><?php echo $stats['pending']; ?></div>
        </div>
        <div class="stat-card rejected" onclick="alert('❌ Rejected: <?php echo $stats['rejected']; ?>')">
            <div class="stat-icon">❌</div>
            <div class="stat-label">Rejected</div>
            <div class="stat-value"><?php echo $stats['rejected']; ?></div>
        </div>
    </div>
</div>

<script>
    function updateGreeting() {
        const greetingEl = document.getElementById("greeting");
        const now = new Date();
        const hour = now.getHours();
        let greet = "Hello";

        if (hour < 12) {
            greet = "☀️ Good morning";
        } else if (hour < 18) {
            greet = "🌤️ Good afternoon";
        } else {
            greet = "🌙 Good evening";
        }

        const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        greetingEl.textContent = `${greet}, it's currently ${timeString}.`;
    }

    updateGreeting();
</script>

</body>
</html>
