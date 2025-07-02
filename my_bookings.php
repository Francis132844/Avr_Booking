<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch bookings of the logged-in user
$stmt = $conn->prepare("SELECT id, event_name, start_datetime, end_datetime, status FROM bookings WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>My Bookings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f4f7f6;
            color: #333;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            align-items: flex-start;
            padding: 40px 15px;
        }
        .container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            max-width: 1000px;
            width: 100%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease-in-out;
        }
        h1 {
            color: #007a33;
            font-weight: 600;
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }
        thead tr {
            background: #007a33;
            color: white;
        }
        thead th {
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
        }
        tbody tr {
            background: #fff;
            box-shadow: 0 3px 8px rgba(0,0,0,0.07);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            border-radius: 8px;
        }
        tbody tr:hover {
            transform: scale(1.01);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        tbody td {
            padding: 18px 20px;
            vertical-align: middle;
            font-size: 0.95rem;
        }

        .btn {
            padding: 8px 16px;
            margin-right: 6px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .edit-btn {
            background-color: #f39c12;
            color: white;
        }
        .edit-btn:hover {
            background-color: #d48806;
        }
        .cancel-btn {
            background-color: #e74c3c;
            color: white;
        }
        .cancel-btn:hover {
            background-color: #c0392b;
        }

        .status-pending {
            color: #e67e22;
            font-weight: 700;
        }
        .status-approved {
            color: #27ae60;
            font-weight: 700;
        }
        .status-rejected {
            color: #c0392b;
            font-weight: 700;
        }

        @media (max-width: 600px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            tbody tr {
                margin-bottom: 20px;
                border: 1px solid #ddd;
                border-radius: 10px;
                padding: 15px;
            }
            tbody td {
                padding: 12px 10px;
                text-align: right;
                position: relative;
                font-size: 0.9rem;
            }
            tbody td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                font-weight: 600;
                font-size: 0.8rem;
                color: #555;
                text-align: left;
            }
            .btn {
                padding: 6px 12px;
                font-size: 0.8rem;
                margin-top: 6px;
            }
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #2ecc71;
            color: white;
            padding: 14px 22px;
            border-radius: 8px;
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
            font-size: 0.9rem;
            display: none;
            z-index: 1000;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="container">
    <h1>📄 My Bookings</h1>

    <table>
        <thead>
            <tr>
                <th>Event</th>
                <th>Date &amp; Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows === 0): ?>
            <tr>
                <td colspan="4" style="text-align:center; padding:30px; color:#777;">
                    You have no bookings yet.
                </td>
            </tr>
        <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td data-label="Event"><?php echo htmlspecialchars($row['event_name']); ?></td>
                <td data-label="Date & Time">
                    <?php
                        echo date('M d, Y h:i A', strtotime($row['start_datetime'])) . ' - ' . date('h:i A', strtotime($row['end_datetime']));
                    ?>
                </td>
                <td data-label="Status" class="status-<?php echo strtolower($row['status']); ?>">
                    <?php echo ucfirst($row['status']); ?>
                </td>
                <td data-label="Action">
                    <?php if ($row['status'] === 'pending'): ?>
                        <a class="btn edit-btn" href="edit_booking.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a 
                          class="btn cancel-btn" 
                          href="cancel_booking.php?id=<?php echo $row['id']; ?>" 
                          onclick="return confirmCancel();"
                        >Cancel</a>
                    <?php else: ?>
                        &mdash;
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="toast" id="toastMessage">Booking cancelled successfully.</div>

<script>
    // Scroll to top smoothly
    window.onload = function () {
        window.scrollTo({ top: 0, behavior: "smooth" });
    }

    // Cancel confirmation
    function confirmCancel() {
        return confirm("Are you sure you want to cancel this booking?");
    }

    // Show toast if redirected from cancellation
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('cancelled') === 'true') {
        const toast = document.getElementById('toastMessage');
        toast.style.display = 'block';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 3000);
    }
</script>

</body>
</html>
