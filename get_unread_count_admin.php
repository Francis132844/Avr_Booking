<?php
include 'db.php';

$result = $conn->query("SELECT COUNT(*) AS count FROM notifications WHERE is_read = 0");
$row = $result->fetch_assoc();
echo $row['count'];
