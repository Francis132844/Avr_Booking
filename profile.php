<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// Fetch user info
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$name || !$email) {
        $error = "⚠️ Full Name and Email are required.";
    } else {
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $error = "⚠️ Password must be at least 6 characters.";
            } else {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
                $stmt->bind_param("sssi", $name, $email, $hashed, $user_id);
            }
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $email, $user_id);
        }

        if (empty($error)) {
            if ($stmt->execute()) {
                $success = "✅ Profile updated successfully.";
                $user['name'] = $name;
                $user['email'] = $email;
            } else {
                $error = "❌ Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 50px 15px;
            color: #333;
        }
        .container {
            background: #fff;
            padding: 35px 30px;
            max-width: 600px;
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.07);
            animation: fadeIn 0.6s ease-in-out;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }
        h1 {
            font-weight: 600;
            font-size: 2rem;
            color: #007a33;
            margin-bottom: 25px;
            text-align: center;
        }
        #clock {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        form label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            color: #444;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px 15px;
            margin-bottom: 20px;
            border: 1.5px solid #ccc;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }
        input:focus {
            border-color: #007a33;
            outline: none;
        }
        .note {
            font-size: 13px;
            color: #999;
            margin-top: -18px;
            margin-bottom: 20px;
        }
        button {
            background: #007a33;
            color: white;
            border: none;
            padding: 14px 0;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        button:hover {
            background-color: #005822;
        }
        .msg-success, .msg-error {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
            animation: fadeIn 0.4s ease-in;
        }
        .msg-success {
            color: #2ecc71;
            background: #e8f6e8;
        }
        .msg-error {
            color: #e74c3c;
            background: #fdecea;
        }
        .warning {
            font-size: 13px;
            color: #e67e22;
            margin-top: -12px;
            margin-bottom: 15px;
        }
        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }
            h1 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="container">
    <h1>🙋‍♂️ Profile</h1>
    <div id="clock"></div>

    <?php if ($success): ?>
        <div class="msg-success"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="msg-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" id="profileForm" novalidate>
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($user['name']); ?>">

        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($user['email']); ?>">

        <label for="password">New Password</label>
        <input type="password" name="password" id="password" placeholder="Leave blank to keep current">
        <div class="warning" id="passWarning" style="display:none;">⚠️ Weak password. Use at least 6 characters.</div>

        <button type="submit">Update Profile</button>
    </form>
</div>

<script>
    // Live Clock
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent = "🕒 " + now.toLocaleTimeString();
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Password Strength Warning
    const passwordField = document.getElementById('password');
    const passWarning = document.getElementById('passWarning');

    passwordField.addEventListener('input', function () {
        if (passwordField.value && passwordField.value.length < 6) {
            passWarning.style.display = 'block';
        } else {
            passWarning.style.display = 'none';
        }
    });

    // Form Validation
    document.getElementById('profileForm').addEventListener('submit', function (e) {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = passwordField.value;

        if (!name || !email) {
            alert("⚠️ Full name and email are required.");
            e.preventDefault();
        } else if (password && password.length < 6) {
            alert("⚠️ Password should be at least 6 characters.");
            e.preventDefault();
        }
    });
</script>

</body>
</html>
