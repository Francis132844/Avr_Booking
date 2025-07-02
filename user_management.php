<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Update user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id = intval($_POST['user_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $id);
    $stmt->execute();
    header("Location: user_management.php");
    exit;
}

// Toggle active/inactive
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $new_status = ($_GET['status'] === 'active') ? 'inactive' : 'active';
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $id);
    $stmt->execute();
    header("Location: user_management.php");
    exit;
}

// Edit fetch
$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_user = $stmt->get_result()->fetch_assoc();
}

// All users
$users = $conn->query("SELECT * FROM users ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f8;
            margin: 0;
        }

        .admin-content {
            margin-left: 260px;
            padding: 40px;
        }

        h1 {
            color: #2c3e50;
            font-size: 26px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            background: #fff;
            border-collapse: collapse;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.07);
            overflow: hidden;
        }

        th, td {
            padding: 14px 20px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background:linear-gradient(180deg, #004225, #00793f);
            color: #fff;
            font-size: 14px;
            text-transform: uppercase;
        }

        tr:hover {
            background-color: #f9fbff;
        }

        .btn {
            padding: 7px 12px;
            font-size: 13px;
            font-weight: 500;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            margin: 2px;
            display: inline-block;
        }

        .edit-btn {
            background-color: #f39c12;
            color: white;
        }

        .deact-btn {
            background-color: #e74c3c;
            color: white;
        }

        .react-btn {
            background-color: #2ecc71;
            color: white;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
        }

        .status-badge.active {
            background-color: #2ecc71;
        }

        .status-badge.inactive {
            background-color: #e74c3c;
        }

        .form-box {
            background: white;
            padding: 25px 30px;
            margin-top: 30px;
            border-radius: 10px;
            max-width: 600px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        input {
            padding: 12px;
            margin: 10px 0;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        button {
            background: #3498db;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }

        a.cancel-link {
            margin-left: 15px;
            color: #2980b9;
            font-size: 14px;
            text-decoration: none;
        }

        a.cancel-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .admin-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="admin-content">
    <h1>👥 User Management</h1>

    <?php if ($edit_user): ?>
        <div class="form-box">
            <h3>✏️ Edit User</h3>
            <form method="post">
                <input type="hidden" name="user_id" value="<?= $edit_user['id']; ?>">
                <label>Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($edit_user['name']); ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($edit_user['email']); ?>" required>

                <button type="submit" name="update_user">Update User</button>
                <a href="user_management.php" class="cancel-link">Cancel</a>
            </form>
        </div>
    <?php endif; ?>

    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($user = $users->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($user['name']); ?></td>
                <td><?= htmlspecialchars($user['email']); ?></td>
                <td>
                    <span class="status-badge <?= $user['status'] ?>">
                        <?= ucfirst($user['status']) ?>
                    </span>
                </td>
                <td>
                    <a class="btn edit-btn" href="?edit=<?= $user['id']; ?>">Edit</a>
                    <?php if ($user['status'] === 'active'): ?>
                        <a class="btn deact-btn" href="?toggle=<?= $user['id']; ?>&status=active" onclick="return confirm('Deactivate this account?')">Deactivate</a>
                    <?php else: ?>
                        <a class="btn react-btn" href="?toggle=<?= $user['id']; ?>&status=inactive" onclick="return confirm('Reactivate this account?')">Reactivate</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
