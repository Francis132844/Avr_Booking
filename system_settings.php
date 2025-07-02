<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Update settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $timezone = $_POST['timezone'];
    $max_hours = intval($_POST['max_booking_hours']);
    $blackout_dates = trim($_POST['blackout_dates']);

    $stmt = $conn->prepare("UPDATE system_settings SET timezone = ?, max_booking_hours = ?, blackout_dates = ? WHERE id = 1");
    $stmt->bind_param("sis", $timezone, $max_hours, $blackout_dates);
    $stmt->execute();
    header("Location: system_settings.php");
    exit;
}

// Fetch current settings
$settings = $conn->query("SELECT * FROM system_settings WHERE id = 1")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>System Settings</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; }
        .admin-content { margin-left: 260px; padding: 30px; }
        .form-box {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            max-width: 600px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 20px;
            padding: 10px 15px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .note {
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="admin-content">
    <h1>⚙️ System Settings</h1>

    <div class="form-box">
        <form method="post">
            <label>Timezone</label>
            <select name="timezone" required>
                <?php
                $timezones = timezone_identifiers_list();
                foreach ($timezones as $tz) {
                    echo "<option value='$tz'" . ($tz == $settings['timezone'] ? " selected" : "") . ">$tz</option>";
                }
                ?>
            </select>

            <label>Room Booking Limit (Hours)</label>
            <input type="number" name="max_booking_hours" value="<?php echo $settings['max_booking_hours']; ?>" min="1" required>

            <label>Blackout Dates (comma-separated YYYY-MM-DD)</label>
            <textarea name="blackout_dates" rows="4"><?php echo htmlspecialchars($settings['blackout_dates']); ?></textarea>
            <div class="note">e.g., 2025-12-25, 2025-01-01</div>

            <button type="submit">💾 Save Settings</button>
        </form>
    </div>
</div>

</body>
</html>
