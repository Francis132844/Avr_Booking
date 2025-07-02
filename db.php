<?php
$host = 'localhost';
$db = 'avr_booking';
$user = 'root';  // replace with your DB username
$pass = '';      // replace with your DB password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
