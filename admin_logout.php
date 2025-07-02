<?php
session_start();
unset($_SESSION['admin_id']); // Only logs out admin
header("Location: admin_index.php");
exit;

