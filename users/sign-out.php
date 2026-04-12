<?php
session_start();
include 'backend/conn.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "UPDATE login_history SET signout_datetime = NOW() 
              WHERE user_id = '$user_id' AND signout_datetime IS NULL 
              ORDER BY login_datetime DESC LIMIT 1";
    mysqli_query($conn, $query);
}

$_SESSION = array();
session_destroy();
header("Location: ../index.php");
exit;
?>