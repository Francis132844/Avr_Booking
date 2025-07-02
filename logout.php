<?php
session_start();
unset($_SESSION['user_id']); // Only logs out user
header("Location: index.php");
exit;
?>
