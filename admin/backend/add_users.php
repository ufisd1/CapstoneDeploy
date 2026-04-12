<?php
header('Content-Type: application/json');

include 'conn.php';
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$admin_id = $_SESSION['admin_id'];
include 'activity_log.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

try {
    error_log("POST data received: " . print_r($_POST, true));

    if (isset($_POST['add_user']) || (!empty($_POST['fullname']) && !empty($_POST['email']) && !empty($_POST['password']))) {
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass = $_POST['password'] ?? '';


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = "Invalid email format";
            echo json_encode($response);
            exit();
        }

        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        if ($check_stmt) {
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $response['message'] = "Email already exists";
                echo json_encode($response);
                $check_stmt->close();
                exit();
            }
            $check_stmt->close();
        }

        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, email, password, admin_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssi", $fullname, $email, $hashed_password, $admin_id);
            if ($stmt->execute()) {
                $user_id = $conn->insert_id;

                if (function_exists('logActivity')) {
                    $changes = [
                        'full_name' => $fullname,
                        'email' => $email,
                    ];
                    logActivity($conn, $admin_id, 'add', 'users', $user_id, $changes);
                }

                $response = [
                    'status' => 'success',
                    'message' => "User added successfully"
                ];
            } else {
                $response['message'] = "Failed to add user: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = "Database error: " . $conn->error;
        }
        echo json_encode($response);
        exit();
    }

    if (isset($_POST['delete_user']) || (isset($_POST['user_id']) && !empty($_POST['user_id']))) {
        $user_id = intval($_POST['user_id']);

        if ($user_id <= 0) {
            $response['message'] = "Invalid user ID";
            echo json_encode($response);
            exit();
        }

        $get_sql = "SELECT * FROM users WHERE id = ?";
        $get_stmt = $conn->prepare($get_sql);

        if ($get_stmt) {
            $get_stmt->bind_param("i", $user_id);
            $get_stmt->execute();
            $result = $get_stmt->get_result();
            $user_data = $result->fetch_assoc();
            $get_stmt->close();

            if (!$user_data) {
                $response['message'] = "User not found";
                echo json_encode($response);
                exit();
            }
        }

        $check_column_sql = "SHOW COLUMNS FROM users LIKE 'deleted_at'";
        $column_result = $conn->query($check_column_sql);
        $has_deleted_at = ($column_result->num_rows > 0);

        if ($has_deleted_at) {
            $sql = "UPDATE users SET 
                full_name = CONCAT('DELETED_', ?, '_', full_name),
                email = CONCAT('DELETED_', ?, '_', email),
                password = '',
                deleted_at = NOW()
                WHERE id = ?";
        } else {
            $sql = "UPDATE users SET 
                full_name = CONCAT('DELETED_', ?, '_', full_name),
                email = CONCAT('DELETED_', ?, '_', email),
                password = ''
                WHERE id = ?";
        }

        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $random_suffix = bin2hex(random_bytes(4));
            $stmt->bind_param("ssi", $random_suffix, $random_suffix, $user_id);

            if ($stmt->execute()) {
                if (function_exists('logActivity') && $user_data) {
                    $changes = [
                        'full_name' => $user_data['full_name'],
                        'email' => $user_data['email'],
                        'status' => 'deactivated'
                    ];
                    logActivity($conn, $admin_id, 'delete', 'users', $user_id, $changes);
                }

                $response = [
                    'status' => 'success',
                    'message' => "User account deactivated successfully. Related activities in other tables are preserved."
                ];
            } else {
                $response['message'] = "Failed to deactivate user: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = "Database error: " . $conn->error;
        }
        echo json_encode($response);
        exit();
    }

    echo json_encode(['status' => 'error', 'message' => 'Invalid request - no valid action found']);
    exit();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
    exit();
}
