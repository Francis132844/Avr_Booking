<?php
include 'db.php';

$register_error = '';
$register_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $user_type = $_POST['user_type'] ?? 'student';

    if ($name && $email && $password) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $register_error = "❌ Email/ID is already registered.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $user_type);

            if ($stmt->execute()) {
                $register_success = "✅ Registration successful. <a href='login.php'>Login here</a>";
            } else {
                $register_error = "❌ Error: " . $stmt->error;
            }

            $stmt->close();
        }
        $check->close();
    } else {
        $register_error = "⚠️ Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | AVR Booking System</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom right, #006400, #00aa55);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
        }

        .register-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px 30px;
            width: 90%;
            max-width: 450px;
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.8s ease-in;
        }

        .register-box h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px;
            color: #ffffff;
        }

        .register-box label {
            display: block;
            margin-top: 10px;
            font-size: 14px;
            color: #e0ffe0;
        }

        .register-box input[type="text"],
        .register-box input[type="email"],
        .register-box input[type="password"],
        .register-box select {
            width: 100%;
            padding: 10px 14px;
            margin-top: 5px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.95);
            font-size: 14px;
            color: #333;
        }

        .register-box input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: white;
            color: #006400;
            font-weight: bold;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.3s ease;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .register-box input[type="submit"]:hover {
            background-color: #00cc66;
            color: white;
        }

        .links {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .links a {
            color: #d4ffd4;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .error, .success {
            text-align: center;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .error {
            background: rgba(255, 0, 0, 0.1);
            color: #ffbbbb;
        }

        .success {
            background: rgba(0, 255, 0, 0.1);
            color: #b5ffb5;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 500px) {
            .register-box {
                padding: 30px 20px;
            }

            .register-box h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<div class="register-box">
    <h2>Register Account</h2>

    <?php if ($register_error): ?>
        <div class="error"><?= $register_error ?></div>
    <?php endif; ?>

    <?php if ($register_success): ?>
        <div class="success"><?= $register_success ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" required>

        <label for="email">Email / ID</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <label for="user_type">User Type</label>
        <select name="user_type" id="user_type">
            <option value="student">Student</option>
            <option value="faculty">Faculty</option>
        </select>

        <input type="submit" value="Register">
    </form>

    <div class="links">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</div>

</body>
</html>
