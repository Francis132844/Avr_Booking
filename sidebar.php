<!-- Inter font import -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    body {
        margin: 0;
        font-family: 'Inter', sans-serif;
        background-color: #f4f6f8;
    }

    .sidebar {
        height: 100vh;
        width: 250px;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #004422;
        padding-top: 25px;
        color: #fff;
        box-shadow: 2px 0 12px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 22px;
        font-weight: 700;
        color: #eaffea;
    }

    .sidebar a {
        display: flex;
        align-items: center;
        color: #e2f0e2;
        padding: 14px 20px;
        text-decoration: none;
        margin: 6px 18px;
        border-radius: 12px;
        transition: background-color 0.3s ease, transform 0.2s ease;
        font-size: 15px;
        position: relative;
    }

    .sidebar a:hover {
        background-color: #34a853;
        color: #ffffff;
        transform: translateX(5px);
    }

    .sidebar a.active {
        background-color: #34a853;
        color: #fff;
        font-weight: 600;
    }

    .sidebar a i,
    .sidebar a span.emoji {
        margin-right: 12px;
        font-size: 18px;
    }

    #notif-badge {
        background: #ff4d4d;
        color: #fff;
        font-size: 11px;
        font-weight: bold;
        padding: 4px 7px;
        border-radius: 50%;
        display: none;
        position: absolute;
        right: 18px;
        top: 10px;
        line-height: 1;
    }

    /* Mobile Responsive */
    @media screen and (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: fixed;
            bottom: 0;
            top: auto;
            display: flex;
            justify-content: space-around;
            padding: 8px 0;
            flex-direction: row;
            background-color: #004422;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
        }

        .sidebar h2 {
            display: none;
        }

        .sidebar a {
            flex-direction: column;
            margin: 0;
            padding: 10px 6px;
            font-size: 12px;
        }

        .sidebar a span.emoji {
            margin: 0 0 5px 0;
        }

        #notif-badge {
            top: 5px;
            right: 12px;
        }
    }
</style>

<div class="sidebar">
    <h2>👤 USER MENU</h2>
    <a href="dashboard.php"><span class="emoji">📋</span> Dashboard</a>
    <a href="book_avr.php"><span class="emoji">📅</span> Book AVR</a>
    <a href="my_bookings.php"><span class="emoji">📝</span> My Bookings</a>
    <a href="notifications.php" id="notif-link">
        <span class="emoji">🔔</span> Notifications
        <span id="notif-badge"></span>
    </a>
    <a href="profile.php"><span class="emoji">🙋‍♂️</span> Profile</a>
    <a href="logout.php"><span class="emoji">🚪</span> Logout</a>
</div>

<script>
function updateNotificationBadge() {
    fetch('get_unread_count.php')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notif-badge');
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(error => {
            console.error("Error fetching notifications:", error);
        });
}

// Initial load
updateNotificationBadge();

// Auto-refresh every 10 seconds
setInterval(updateNotificationBadge, 10000);
</script>
