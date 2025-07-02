<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fa;
            margin: 0;
        }

        .admin-content {
            margin-left: 260px;
            padding: 40px;
        }

        h1 {
            font-size: 26px;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            border-collapse: collapse;
        }

        th, td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        th {
            background:linear-gradient(180deg, #004225, #00793f);
            color: #fff;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background-color: #f9fbff;
        }

        .btn {
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            margin: 2px;
        }

        .btn-approve {
            background-color: #2ecc71;
            color: white;
        }

        .btn-reject {
            background-color: #e74c3c;
            color: white;
        }

        .btn-disabled {
            background-color: #ccc;
            color: #555;
            cursor: not-allowed;
        }

        .status {
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .status.pending { background: #f1c40f; color: #fff; }
        .status.approved { background: #2ecc71; color: #fff; }
        .status.rejected { background: #e74c3c; color: #fff; }

        .rejection-note {
            font-size: 12px;
            color: #c0392b;
            font-style: italic;
            margin-top: 4px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background: #fff;
            margin: 10% auto;
            padding: 25px;
            width: 400px;
            border-radius: 10px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        .modal-content h3 {
            text-align: center;
            margin-bottom: 10px;
        }

        .modal-content .btn {
            width: 48%;
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="admin-content">
    <h1>📝 Manage All Booking Requests</h1>

    <table>
        <thead>
            <tr>
                <th>Requested By</th>
                <th>Event Name</th>
                <th>Date & Time</th>
                <th>Purpose</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="bookingTable">
            <?php include 'fetch_bookings.php'; ?>
        </tbody>
    </table>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal">
    <div class="modal-content">
        <form method="post" action="reject_booking.php">
            <h3>Reject Booking</h3>
            <input type="hidden" name="reject_id" id="reject_id">
            <label>Rejection Reason (optional):</label>
            <textarea name="rejection_message" rows="4" placeholder="Provide a reason..."></textarea>
            <br><br>
            <button type="submit" class="btn btn-reject">Confirm Reject</button>
            <button type="button" class="btn" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="modal">
    <div class="modal-content" style="text-align: center;">
        <h3>Approve this booking?</h3>
        <br>
        <button id="approveConfirmBtn" class="btn btn-approve">Yes, Approve</button>
        <button class="btn" onclick="closeApproveModal()">Cancel</button>
    </div>
</div>

<script>
    let approveUrl = '';

    function openRejectModal(id) {
        document.getElementById("reject_id").value = id;
        document.getElementById("rejectModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("rejectModal").style.display = "none";
    }

    function openApproveModal(url) {
        approveUrl = url;
        document.getElementById("approveModal").style.display = "block";
    }

    function closeApproveModal() {
        document.getElementById("approveModal").style.display = "none";
        approveUrl = '';
    }

    window.onload = function () {
        document.getElementById("approveConfirmBtn").onclick = function () {
            if (approveUrl) window.location.href = approveUrl;
        };
    }

    window.onclick = function (event) {
        if (event.target === document.getElementById("rejectModal")) closeModal();
        if (event.target === document.getElementById("approveModal")) closeApproveModal();
    };

    // Auto-refresh bookings every 1 second
    setInterval(() => {
        fetch('fetch_bookings.php')
            .then(res => res.text())
            .then(data => {
                document.getElementById("bookingTable").innerHTML = data;
            });
    }, 1000);

    // Auto-refresh notification badge every 1 second
    setInterval(() => {
        fetch('get_unread_count_admin.php')
            .then(res => res.text())
            .then(count => {
                const badge = document.getElementById("notifBadge");
                if (badge) badge.textContent = count > 0 ? count : '';
            });
    }, 1000);
</script>

</body>
</html>
