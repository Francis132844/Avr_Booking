<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = "";
$errors = [];

function getBookedSlots($conn, $date) {
    $slots = [];
    $stmt = $conn->prepare("SELECT start_datetime, end_datetime FROM bookings WHERE DATE(start_datetime) = ?");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $slots[] = [
            'start' => date("H:i", strtotime($row['start_datetime'])),
            'end' => date("H:i", strtotime($row['end_datetime']))
        ];
    }
    return $slots;
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "✅ Booking submitted successfully and is pending approval.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $purpose = $_POST['purpose'] ?? '';

    if ($event_name && $event_date && $start_time && $end_time && $purpose) {
        $start_datetime = "$event_date $start_time";
        $end_datetime = "$event_date $end_time";

        if (strtotime($start_datetime) >= strtotime($end_datetime)) {
            $errors[] = "⚠️ End time must be after start time.";
        } elseif (strtotime($start_datetime) < time()) {
            $errors[] = "⚠️ Cannot book past time slots.";
        } else {
            $stmt = $conn->prepare("INSERT INTO bookings 
                (user_id, event_name, start_datetime, end_datetime, purpose)
                VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $user_id, $event_name, $start_datetime, $end_datetime, $purpose);

            if ($stmt->execute()) {
                header("Location: book_avr.php?success=1");
                exit;
            } else {
                $errors[] = "❌ Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $errors[] = "⚠️ Please fill out all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Book AVR | DLSJBC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(to right, #e9fdf4, #d4f2e5);
            padding: 40px 15px;
            min-height: 100vh;
            color: #222;
            display: flex;
            justify-content: center;
        }

        .container {
            background: white;
            padding: 35px;
            border-radius: 15px;
            max-width: 700px;
            width: 100%;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            font-size: 26px;
            color: #006633;
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 18px;
            font-weight: 600;
            color: #333;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
        }

        textarea {
            resize: vertical;
        }

        input:focus, textarea:focus {
            border-color: #00aa55;
            outline: none;
        }

        button {
            width: 100%;
            margin-top: 25px;
            background-color: #007a33;
            color: white;
            padding: 14px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background-color: #005e26;
        }

        .msg, .error {
            padding: 15px;
            border-radius: 8px;
            font-size: 15px;
            margin-bottom: 15px;
            text-align: center;
        }

        .msg {
            background-color: #e9fff0;
            color: #1e824c;
            border: 1px solid #1e824c;
        }

        .error {
            background-color: #ffeaea;
            color: #c0392b;
            border: 1px solid #c0392b;
        }

        .slots {
            font-size: 14px;
            color: #444;
            margin-top: 8px;
        }

        #clock {
            text-align: center;
            color: #555;
            font-size: 14px;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px 20px;
            }
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById("event_date").setAttribute("min", today);
            updateClock();
            setInterval(updateClock, 1000);
        });

        function updateClock() {
            const now = new Date();
            const time = now.toLocaleTimeString();
            document.getElementById("clock").textContent = "🕒 " + time;
        }

        function showAvailableSlots(date) {
            if (!date) return document.getElementById("slots").innerHTML = "";

            fetch("available_slots.php?date=" + encodeURIComponent(date))
                .then(res => res.json())
                .then(data => {
                    const slotDiv = document.getElementById("slots");
                    if (data.length) {
                        slotDiv.innerHTML = "<strong>Booked Slots:</strong><br>" + data.map(s => `${s.start} - ${s.end}`).join("<br>");
                    } else {
                        slotDiv.innerHTML = "<strong>No bookings for this date yet.</strong>";
                    }
                })
                .catch(() => {
                    document.getElementById("slots").innerHTML = "⚠️ Error loading slots.";
                });
        }
    </script>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="container">
    <h1>📆 AVR Booking Form</h1>
    <div id="clock"></div>

    <?php foreach ($errors as $error): ?>
        <div class="error"><?= $error ?></div>
    <?php endforeach; ?>

    <?php if ($success): ?>
        <div class="msg"><?= $success ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="event_name">Event Name</label>
        <input type="text" name="event_name" id="event_name" required>

        <label for="event_date">Date</label>
        <input type="date" name="event_date" id="event_date" onchange="showAvailableSlots(this.value)" required>

        <div class="slots" id="slots"></div>

        <label for="start_time">Start Time</label>
        <input type="time" name="start_time" id="start_time" required>

        <label for="end_time">End Time</label>
        <input type="time" name="end_time" id="end_time" required>

        <label for="purpose">Purpose / Description</label>
        <textarea name="purpose" id="purpose" rows="4" required></textarea>

        <button type="submit">Submit Booking</button>
    </form>
</div>

</body>
</html>
