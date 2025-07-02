<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Post a new announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['announcement'])) {
    $msg = trim($_POST['announcement']);
    if (!empty($msg)) {
        $stmt = $conn->prepare("INSERT INTO announcements (message) VALUES (?)");
        $stmt->bind_param("s", $msg);
        $stmt->execute();

        // Notify all users
        $user_result = $conn->query("SELECT id FROM users");
        $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");

        while ($user = $user_result->fetch_assoc()) {
            $user_id = $user['id'];
            $notif_stmt->bind_param("is", $user_id, $msg);
            $notif_stmt->execute();
        }

        header("Location: announcements.php");
        exit;
    }
}

// Delete selected announcements
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_selected']) && isset($_POST['announcement_ids'])) {
    $ids = $_POST['announcement_ids'];
    foreach ($ids as $id) {
        $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: announcements.php");
    exit;
}

// Fetch announcements
$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Announcements</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #ecf0f1;
        }

        .main-container {
            display: flex;
            justify-content: center;
            padding: 60px 20px;
            min-height: 100vh;
            margin-left: 260px;
            box-sizing: border-box;
        }

        .content-box {
            width: 100%;
            max-width: 900px;
        }

        h1 {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        .form-box {
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.07);
            margin-bottom: 30px;
        }

        textarea {
            width: 100%;
            padding: 14px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            resize: vertical;
            min-height: 100px;
        }

        button {
            margin-top: 15px;
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #2980b9;
        }

        .announcement-box {
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .announcement-box h3 {
            margin-top: 0;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .announcement-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 16px 20px;
            border-left: 5px solid #3498db;
            background-color: #fdfdfd;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }

        .announcement-item:last-child {
            margin-bottom: 0;
        }

        .message-text {
            font-size: 16px;
            color: #34495e;
            margin-bottom: 6px;
        }

        .date {
            font-size: 13px;
            color: #7f8c8d;
        }

        button[name="delete_selected"] {
            background-color: #e74c3c;
        }

        button[name="delete_selected"]:hover {
            background-color: #c0392b;
        }

        @media (max-width: 768px) {
            .main-container {
                margin-left: 0;
                padding: 20px;
            }
        }

        input[type="checkbox"] {
            margin-top: 5px;
            transform: scale(1.2);
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main-container">
    <div class="content-box">
        <h1>📨 Send Announcements</h1>

        <div class="form-box">
            <form method="post" id="announcementForm">
                <label for="announcement"><strong>Enter Announcement Message:</strong></label>
                <textarea name="announcement" id="announcement" placeholder="e.g., AVR Room 1 will be unavailable on July 3 due to maintenance." required></textarea>
                <button type="submit">📢 Post Announcement</button>
            </form>
        </div>

        <div class="announcement-box">
            <h3>📃 Recent Announcements</h3>
            <form method="post">
                <?php if ($announcements->num_rows > 0): ?>
                    <?php while ($row = $announcements->fetch_assoc()): ?>
                        <div class="announcement-item">
                            <input type="checkbox" name="announcement_ids[]" value="<?= $row['id'] ?>">
                            <div>
                                <div class="message-text"><?= htmlspecialchars($row['message']) ?></div>
                                <div class="date">🕒 <?= date("F j, Y - g:i A", strtotime($row['created_at'])) ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <button type="submit" name="delete_selected">🗑️ Delete Selected</button>
                <?php else: ?>
                    <p>No announcements posted yet.</p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script>
    const textarea = document.getElementById('announcement');
    const form = document.getElementById('announcementForm');

    textarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault(); // Prevent newline
            form.submit();      // Submit the form
        }
    });
</script>

</body>
</html>
