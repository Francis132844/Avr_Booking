<?php
include 'db.php';

$result = $conn->query("
    SELECT b.id, b.event_name, b.purpose, b.start_datetime, b.end_datetime, 
           b.status, b.rejection_message, u.name AS requester 
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    ORDER BY b.start_datetime DESC
");

while ($row = $result->fetch_assoc()):
?>
<tr>
    <td><?= htmlspecialchars($row['requester']) ?></td>
    <td><?= htmlspecialchars($row['event_name']) ?></td>
    <td>
        <?= date('M d, Y h:i A', strtotime($row['start_datetime'])) ?> – 
        <?= date('h:i A', strtotime($row['end_datetime'])) ?>
    </td>
    <td>
        <?= htmlspecialchars($row['purpose']) ?>
        <?php if ($row['status'] === 'rejected' && $row['rejection_message']): ?>
            <div class="rejection-note">🛑 <?= htmlspecialchars($row['rejection_message']) ?></div>
        <?php endif; ?>
    </td>
    <td><span class="status <?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
    <td>
        <?php if ($row['status'] === 'pending'): ?>
            <a href="approve_booking.php?id=<?= $row['id'] ?>" class="btn btn-approve" onclick="event.preventDefault(); openApproveModal(this.href);">Approve</a>
            <button class="btn btn-reject" onclick="openRejectModal(<?= $row['id'] ?>)">Reject</button>
        <?php else: ?>
            <span class="btn btn-disabled">No Action</span>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
