<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$booking_id = $_GET['id'] ?? null;

if ($booking_id) {
    // Allow deletion only if booking is pending
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
}

header("Location: my_bookings.php");
exit;
