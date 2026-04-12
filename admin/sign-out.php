<?php
session_start();
include 'backend/conn.php';

if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $query = "UPDATE login_history SET signout_datetime = NOW() 
              WHERE admin_id = '$admin_id' AND signout_datetime IS NULL 
              ORDER BY login_datetime DESC LIMIT 1";
    mysqli_query($conn, $query);
}

$_SESSION = array();
session_destroy();
header("Location: login.php");
exit;
?>