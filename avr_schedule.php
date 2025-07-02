<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch approved bookings
$events = [];
$result = $conn->query("SELECT id, event_name, start_datetime, end_datetime, purpose FROM bookings WHERE status = 'approved'");

while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['event_name'],
        'start' => $row['start_datetime'],
        'end' => $row['end_datetime'],
        'description' => $row['purpose']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AVR Schedule Calendar</title>

    <!-- ✅ OFFLINE FullCalendar files -->
    <link href="fullcalendar/fullcalendar.min.css" rel="stylesheet" />
    <script src="fullcalendar/fullcalendar.min.js"></script>
    <script src="fullcalendar/locales-all.min.js"></script>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #ecf0f1;
            margin: 0;
        }

        .admin-content {
            margin-left: 260px;
            padding: 40px;
        }

        h1 {
            font-size: 26px;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        #calendar {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .fc-event {
            cursor: pointer;
            background-color: #3498db !important;
            border: none !important;
            color: #fff !important;
            font-weight: 500;
            padding: 4px 6px;
            border-radius: 4px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 99;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 25px;
            width: 420px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-content h3 {
            margin-top: 0;
            font-size: 22px;
            color: #2c3e50;
        }

        .modal-content p {
            margin: 8px 0;
            font-size: 14px;
        }

        .modal-content button {
            margin-top: 15px;
            background: #3498db;
            color: #fff;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }

        .modal-content button:hover {
            background: #2980b9;
        }

        @media screen and (max-width: 768px) {
            .admin-content {
                margin-left: 0;
                padding: 20px;
            }

            .modal-content {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="admin-content">
    <h1>🗓 AVR Schedule Calendar</h1>
    <div id='calendar'></div>
</div>

<!-- View Event Modal -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <h3 id="modalTitle">Event Title</h3>
        <p><strong>📅 Start:</strong> <span id="modalStart"></span></p>
        <p><strong>⏰ End:</strong> <span id="modalEnd"></span></p>
        <p><strong>📝 Purpose:</strong></p>
        <p id="modalDesc" style="background: #f4f6f8; padding: 10px; border-radius: 6px;"></p>
        <button onclick="closeModal()">Close</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 'auto',
            locale: 'en',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: <?php echo json_encode($events); ?>,
            eventClick: function (info) {
                const event = info.event;
                document.getElementById('modalTitle').innerText = event.title;
                document.getElementById('modalStart').innerText = new Date(event.start).toLocaleString();
                document.getElementById('modalEnd').innerText = new Date(event.end).toLocaleString();
                document.getElementById('modalDesc').innerText = event.extendedProps.description;
                document.getElementById('eventModal').style.display = "block";
            }
        });
        calendar.render();
    });

    function closeModal() {
        document.getElementById("eventModal").style.display = "none";
    }
</script>

</body>
</html>
