<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT message, created_at, is_read FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
        $is_announcement = stripos($row['message'], 'announcement') !== false;
        ?>
        <div class="notification <?= $row['is_read'] ? '' : 'unread' ?>">
            <?php if ($is_announcement): ?>
                <span class="emoji-badge">📢</span>
            <?php endif; ?>
            <?= htmlspecialchars($row['message']) ?>
            <time><?= date("F j, Y - g:i A", strtotime($row['created_at'])) ?></time>
        </div>
    <?php endwhile;
else:
    echo '<p class="no-notifs">No notifications yet.</p>';
endif;
?>
