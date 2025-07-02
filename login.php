<?php
include 'db.php';
session_start();

$login_error = '';
$login_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['name'] = $user['name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $login_error = "❌ Invalid email or password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | AVR Booking System</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom right, #006400, #00aa55);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px 30px;
            width: 90%;
            max-width: 400px;
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-in;
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ffffff;
            font-size: 28px;
        }

        .login-box label {
            display: block;
            margin-top: 10px;
            color: #e0ffe0;
            font-size: 14px;
        }

        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 10px 14px;
            margin-top: 5px;
            border: none;
            border-radius: 8px;
            outline: none;
            background: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }

        .login-box input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: white;
            color: #006400;
            font-weight: bold;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .login-box input[type="submit"]:hover {
            background: #00cc66;
            color: white;
        }

        .links {
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: #d4ffd4;
            text-decoration: none;
            margin: 0 6px;
            font-size: 13px;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .error {
            color: #ffbbbb;
            background: rgba(255, 0, 0, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 500px) {
            .login-box {
                padding: 30px 20px;
            }

            .login-box h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>AVR Login</h2>

    <?php if ($login_error): ?>
        <div class="error"><?= $login_error ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="email">Email / ID</label>
        <input type="text" name="email" id="email" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <input type="submit" value="Login">
    </form>

    <div class="links">
        <a href="register.php">Register</a> | 
        <a href="forgot_password.php">Forgot Password?</a>
    </div>
</div>

</body>
</html>
