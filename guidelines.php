<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Guidelines</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background-color: #f4f4f4; }
        .content { margin-left: 240px; padding: 20px; }
        .card {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1 { font-size: 28px; color: #2c3e50; }
        h2 { font-size: 20px; margin-top: 25px; color: #2980b9; }
        ul { margin-left: 20px; line-height: 1.8; }
        li::marker { color: #3498db; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <div class="card">
        <h1>📖 Booking Guidelines</h1>

        <h2>✅ Do's</h2>
        <ul>
            <li>Submit your AVR booking at least 3 days in advance.</li>
            <li>Provide accurate details for your event including time and equipment needed.</li>
            <li>Ensure the room is clean and equipment is returned after use.</li>
            <li>Notify the admin in case of any changes or cancellations.</li>
        </ul>

        <h2>❌ Don'ts</h2>
        <ul>
            <li>Do not eat or drink near AV equipment.</li>
            <li>Do not leave the AVR unattended during your booking.</li>
            <li>Do not tamper with the system or rearrange AV equipment.</li>
            <li>Do not extend your time without approval.</li>
        </ul>

        <h2>📜 Usage Policies</h2>
        <ul>
            <li>All bookings are subject to approval by the AVR administrator.</li>
            <li>Users with repeated violations may be restricted from future bookings.</li>
            <li>In case of technical issues, immediately contact the admin or support staff.</li>
            <li>AVR is intended for academic and school-related functions only.</li>
        </ul>
    </div>
</div>

</body>
</html>
