<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Mark all as read
if (isset($_GET['mark_all'])) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header("Location: notifications.php");
    exit;
}

// Get unread count
$unread_stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$unread_stmt->bind_param("i", $user_id);
$unread_stmt->execute();
$unread_stmt->bind_result($unread_count);
$unread_stmt->fetch();
$unread_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: #f4f7f6;
            display: flex;
            justify-content: center;
            padding: 20px;
            min-height: 100vh;
            color: #333;
        }

        .container {
            background: #fff;
            padding: 25px 25px 0;
            border-radius: 15px;
            max-width: 700px;
            width: 100%;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            animation: fadeIn 0.6s ease-in-out;
            display: flex;
            flex-direction: column;
            height: 90vh;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        h1 {
            font-weight: 600;
            font-size: 1.8rem;
            color: #007a33;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notif-bell {
            position: relative;
            font-size: 1.5rem;
        }

        .notif-count {
            background: #e74c3c;
            color: #fff;
            font-size: 0.75rem;
            padding: 3px 7px;
            border-radius: 50%;
            position: absolute;
            top: -8px;
            right: -12px;
            font-weight: 700;
            min-width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mark-read-btn {
            background: #007a33;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background 0.3s;
        }

        .mark-read-btn:hover {
            background: #00592c;
        }

        .scroll-box {
            overflow-y: auto;
            flex-grow: 1;
            padding-right: 10px;
            margin-bottom: 10px;
        }

        .notification {
            background: #fff;
            padding: 16px 20px;
            margin-bottom: 12px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .notification.unread {
            background-color: #eaf4ff;
            border-left: 6px solid #3498db;
            font-weight: 600;
        }

        .notification time {
            display: block;
            margin-top: 6px;
            font-size: 0.85rem;
            color: #777;
        }

        .emoji-badge {
            font-size: 1.2rem;
            margin-right: 6px;
            vertical-align: middle;
        }

        p.no-notifs {
            text-align: center;
            color: #666;
            font-size: 1rem;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px 15px 0;
                height: auto;
            }

            h1 {
                font-size: 1.5rem;
            }

            .scroll-box {
                max-height: 70vh;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* MODAL */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #ffffff;
            padding: 30px 25px;
            border-radius: 12px;
            text-align: center;
            max-width: 380px;
            width: 90%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            animation: fadeIn 0.4s ease-in-out;
        }

        .modal-content h2 {
            margin: 0;
            font-size: 1.3rem;
            color: #333;
        }

        .modal-content .emoji {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .modal-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .modal-buttons button {
            padding: 10px 18px;
            font-size: 0.95rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: 600;
        }

        .confirm {
            background: #007a33;
            color: white;
        }

        .confirm:hover {
            background: #005822;
        }

        .cancel {
            background: #e74c3c;
            color: white;
        }

        .cancel:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="container">
    <div class="header">
        <h1>
            🔔 Notifications
            <span class="notif-bell">
                <span class="notif-count" id="notif-badge"><?= $unread_count > 0 ? $unread_count : '' ?></span>
            </span>
        </h1>
        <?php if ($unread_count > 0): ?>
            <button onclick="openModal()" class="mark-read-btn">Mark All as Read</button>
        <?php endif; ?>
    </div>

    <div class="scroll-box" id="notifications-container">
        <?php include 'notifications_fetch.php'; ?>
    </div>
</div>

<!-- MODAL -->
<div class="modal" id="markReadModal">
    <div class="modal-content">
        <div class="emoji">❓</div>
        <h2>Are you sure you want to mark all notifications as read?</h2>
        <div class="modal-buttons">
            <button class="confirm" onclick="confirmMarkRead()">Yes, Mark All</button>
            <button class="cancel" onclick="closeModal()">Cancel</button>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById("markReadModal");

    function openModal() {
        modal.style.display = "flex";
    }

    function closeModal() {
        modal.style.display = "none";
    }

    function confirmMarkRead() {
        window.location.href = "?mark_all=1";
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    }

    // Auto-refresh every second
    setInterval(() => {
        // Refresh notification list
        fetch('notifications_fetch.php')
            .then(res => res.text())
            .then(data => {
                document.getElementById('notifications-container').innerHTML = data;
            });

        // Refresh unread count
        fetch('get_unread_count.php')
            .then(res => res.text())
            .then(count => {
                const badge = document.getElementById('notif-badge');
                const cleaned = count.trim();
                const num = parseInt(cleaned);

                if (!isNaN(num)) {
                    badge.textContent = num > 0 ? num : '';
                }
            });
    }, 1000);
</script>

</body>
</html>
