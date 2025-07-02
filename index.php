<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome | AVR Booking System</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom right, #005522, #007744, #00aa55);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #ffffff;
            animation: fadeIn 1s ease-in;
        }

        .welcome-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 50px 30px;
            border-radius: 20px;
            text-align: center;
            width: 90%;
            max-width: 520px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.8s ease;
        }

        .logo {
            width: 90px;
            margin-bottom: 20px;
        }

        .welcome-box h1 {
            font-size: 34px;
            color: #ffffff;
            margin-bottom: 10px;
        }

        .welcome-box p {
            font-size: 16px;
            color: #e0ffe0;
            margin-bottom: 25px;
        }

        .welcome-box a {
            display: inline-block;
            margin: 10px 8px;
            padding: 12px 28px;
            background-color: #ffffff;
            color: #006400;
            text-decoration: none;
            font-weight: bold;
            border-radius: 50px;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .welcome-box a:hover {
            background-color: #00cc66;
            color: white;
            transform: scale(1.05);
        }

        @media (max-width: 500px) {
            .welcome-box {
                padding: 30px 20px;
            }

            .welcome-box h1 {
                font-size: 24px;
            }

            .welcome-box a {
                display: block;
                width: 100%;
                margin: 10px 0;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <div class="welcome-box">
        <img src="assets/AVR LOGO.jpg" alt="DLSJBC Logo" class="logo">
        <h1>AVR Booking System</h1>
        <p>Welcome to the official AVR Booking platform of<br><strong>De La Salle John Bosco College</strong></p>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </div>

</body>
</html>
