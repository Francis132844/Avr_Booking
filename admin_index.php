<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel – De La Salle John Bosco College</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #00532a, #00351c);
            margin: 0;
            padding: 0;
            color: #ffffff;
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 40px 20px;
            text-align: center;
        }

        .logo {
            width: 120px;
            margin-bottom: 20px;
            border-radius: 50%;
            border: 3px solid #f1c40f;
            background-color: white;
        }

        h1 {
            font-size: 40px;
            font-weight: 600;
            margin: 10px 0 5px;
            color: #f1c40f;
        }

        h2 {
            font-size: 24px;
            font-weight: 400;
            margin-bottom: 20px;
            color: #dfffe0;
        }

        p {
            font-size: 16px;
            max-width: 600px;
            color: #e6e6e6;
            margin-bottom: 30px;
        }

        .btn {
            background-color: #f1c40f;
            color: #000;
            padding: 14px 30px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .btn:hover {
            background-color: #d4ac0d;
        }

        .footer {
            margin-top: 50px;
            font-size: 13px;
            color: #ccc;
        }

        @media (max-width: 600px) {
            h1 { font-size: 30px; }
            h2 { font-size: 18px; }
            .btn {
                width: 90%;
                padding: 12px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <img src="assets/AVR LOGO.jpg" alt="School Logo" class="logo">
    
    <h1>Admin Panel</h1>
    <h2>De La Salle John Bosco College</h2>

    <p>Welcome to the AVR Booking System admin interface.<br>
    Log in to manage room reservations, schedules, users, announcements, and more.</p>

    <a href="admin_login.php" class="btn">🔐 Admin Login</a>

    <div class="footer">
        &copy; <?php echo date('Y'); ?> De La Salle John Bosco College – IT Department. All Rights Reserved.
    </div>
</div>

</body>
</html>
