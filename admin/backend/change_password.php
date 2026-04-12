<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conn.php';

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

$user_id = null;
$table_name = '';

if (isset($_SESSION['admin_id'])) {
    $user_id = $_SESSION['admin_id'];
    $table_name = 'admin';
} elseif (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $table_name = 'users';
}

if ($user_id === null) {
    $response['message'] = 'Authentication required.';
    echo json_encode($response);
    exit;
}

$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $response['message'] = 'Please fill in all password fields.';
    echo json_encode($response);
    exit;
}

if ($new_password !== $confirm_password) {
    $response['message'] = 'New passwords do not match.';
    echo json_encode($response);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT password FROM $table_name WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $response['message'] = 'User not found.';
        echo json_encode($response);
        exit;
    }
    
    $user = $result->fetch_assoc();
    $hashed_password = $user['password'];

    if (password_verify($current_password, $hashed_password)) {
        $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $update_stmt = $conn->prepare("UPDATE $table_name SET password = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_hashed_password, $user_id);
        
        if ($update_stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Password updated successfully!';
        } else {
            $response['message'] = 'Failed to update password.';
        }
    } else {
        $response['message'] = 'Incorrect current password.';
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
