<?php
include 'conn.php';

$is_admin = isset($_SESSION['admin_id']);

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

$today_start = date('Y-m-d 00:00:00');
$today_end = date('Y-m-d 23:59:59');

$total_logins_today_query = "
    SELECT COUNT(DISTINCT CASE 
        WHEN u.deleted_at IS NULL THEN lh.user_id 
        WHEN a.id IS NOT NULL THEN lh.admin_id 
    END) AS total_logins_today 
    FROM login_history lh
    LEFT JOIN users u ON lh.user_id = u.id
    LEFT JOIN admin a ON lh.admin_id = a.id
    WHERE lh.login_datetime BETWEEN '$today_start' AND '$today_end'";
$total_logins_today_result = mysqli_query($conn, $total_logins_today_query);

if ($total_logins_today_result) {
    $total_logins_today = mysqli_fetch_assoc($total_logins_today_result)['total_logins_today'];
} else {
    die("Error fetching total logins today: " . mysqli_error($conn));
}

$time_24_hours_ago = date('Y-m-d H:i:s', strtotime('-24 hours'));
$successful_logins_query = "
    SELECT COUNT(*) AS successful_logins 
    FROM login_history 
    WHERE status = 'Success' AND login_datetime >= '$time_24_hours_ago'";
$successful_logins_result = mysqli_query($conn, $successful_logins_query);

if ($successful_logins_result) {
    $successful_logins = mysqli_fetch_assoc($successful_logins_result)['successful_logins'];
} else {
    die("Error fetching successful logins: " . mysqli_error($conn));
}

$failed_logins_query = "
    SELECT COUNT(*) AS failed_logins 
    FROM login_history 
    WHERE status = 'Failed' AND login_datetime >= '$time_24_hours_ago'";
$failed_logins_result = mysqli_query($conn, $failed_logins_query);

if ($failed_logins_result) {
    $failed_logins = mysqli_fetch_assoc($failed_logins_result)['failed_logins'];
} else {
    die("Error fetching failed logins: " . mysqli_error($conn));
}

$active_users_time = date('Y-m-d H:i:s', strtotime('-15 minutes'));
$active_users_query = "
    SELECT COUNT(DISTINCT user_id) AS active_users 
    FROM (
        SELECT user_id, MAX(login_datetime) AS latest_login
        FROM login_history
        WHERE login_datetime >= '$active_users_time'
        AND (signout_datetime IS NULL OR signout_datetime >= '$active_users_time')
        GROUP BY user_id
    ) AS latest_logins
    JOIN users u ON latest_logins.user_id = u.id
    WHERE u.deleted_at IS NULL";
$active_users_result = mysqli_query($conn, $active_users_query);

if ($active_users_result) {
    $active_users = mysqli_fetch_assoc($active_users_result)['active_users'];
} else {
    die("Error fetching active users: " . mysqli_error($conn));
}

$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

$login_history_query = "
    SELECT 
        lh.history_id, 
        lh.user_id, 
        u.full_name, 
        lh.login_datetime, 
        lh.signout_datetime,
        lh.status,
        'user' AS user_type,
        u.deleted_at
    FROM login_history lh
    JOIN users u ON lh.user_id = u.id
    
    UNION ALL
    
    SELECT 
        alh.history_id, 
        alh.admin_id AS user_id, 
        a.admin_name, 
        alh.login_datetime, 
        alh.signout_datetime,
        alh.status,
        'admin' AS user_type,
        NULL AS deleted_at
    FROM login_history alh
    JOIN admin a ON alh.admin_id = a.id
    
    ORDER BY login_datetime DESC
    LIMIT $offset, $records_per_page";
$login_history_result = mysqli_query($conn, $login_history_query);

if (!$login_history_result) {
    die("Error fetching login history: " . mysqli_error($conn));
}

$count_query = "
    SELECT COUNT(*) AS total 
    FROM (
        SELECT history_id FROM login_history WHERE user_id IS NOT NULL
        UNION ALL
        SELECT history_id FROM login_history WHERE admin_id IS NOT NULL
    ) AS combined";
$count_result = mysqli_query($conn, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $records_per_page);