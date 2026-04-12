<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

if (!file_exists('conn.php')) {
    $response['message'] = 'CRITICAL: conn.php was not found.';
    echo json_encode($response);
    exit;
}
include 'conn.php';

$user_id = null;
$is_admin = false;
$table_name = '';
$name_column = '';

if (isset($_SESSION['admin_id'])) {
    $user_id = $_SESSION['admin_id'];
    $is_admin = true;
    $table_name = 'admin';
    $name_column = 'admin_name';
} elseif (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $table_name = 'users';
    $name_column = 'full_name';
}

if ($user_id === null) {
    $response['message'] = 'Authentication required.';
    echo json_encode($response);
    exit;
}

$new_name = $_POST['name'] ?? '';
$new_email = $_POST['email'] ?? '';
$new_phone = $_POST['phone'] ?? null;
$profile_pic_path_db = null;

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
    $upload_dir_relative = '../uploads/';
    $upload_dir_db = 'uploads/';
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES['profile_picture']['tmp_name']);

    if (in_array($file_type, $allowed_types)) {
        if (!is_dir($upload_dir_relative)) {
            if (!mkdir($upload_dir_relative, 0755, true)) {
                $response['message'] = 'Upload Failed: Could not create upload directory.';
                echo json_encode($response);
                exit;
            }
        }

        $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $unique_filename = uniqid('profile_', true) . '.' . $file_extension;
        $file_move_path = $upload_dir_relative . $unique_filename;
        $profile_pic_path_db = $upload_dir_db . $unique_filename;

        if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_move_path)) {
            $profile_pic_path_db = null;
            $response['message'] = 'Upload Failed: Could not move file. Check permissions.';
            echo json_encode($response);
            exit;
        }
    } else {
        $response['message'] = 'Invalid file type. Only JPG, PNG, and GIF allowed.';
        echo json_encode($response);
        exit;
    }
} elseif (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] != UPLOAD_ERR_NO_FILE) {
    $response['message'] = 'Upload Failed: File error code ' . $_FILES['profile_picture']['error'];
    echo json_encode($response);
    exit;
}

try {
    $stmt_current = $conn->prepare("SELECT * FROM $table_name WHERE id = ?");
    $stmt_current->bind_param("i", $user_id);
    $stmt_current->execute();
    $current_data = $stmt_current->get_result()->fetch_assoc();
    $stmt_current->close();

    $sql_parts = [];
    $params = [];
    $types = "";

    if ($new_name !== $current_data[$name_column]) {
        $sql_parts[] = "$name_column = ?";
        $params[] = $new_name;
        $types .= "s";
    }

    if ($new_email !== $current_data['email']) {
        $sql_parts[] = "email = ?";
        $params[] = $new_email;
        $types .= "s";
    }

    if (!$is_admin && $new_phone !== $current_data['phone']) {
        $sql_parts[] = "phone = ?";
        $params[] = $new_phone;
        $types .= "s";
    }

    if ($profile_pic_path_db !== null) {
        $sql_parts[] = "profile_picture = ?";
        $params[] = $profile_pic_path_db;
        $types .= "s";
    }

    if (empty($sql_parts)) {
        $response['status'] = 'success';
        $response['message'] = 'No changes detected.';
        $response['new_name'] = $new_name;
        $response['new_avatar_path'] = $current_data['profile_picture'] ?? 'img/user.png';
        echo json_encode($response);
        exit;
    }

    $params[] = $user_id;
    $types .= "i";

    $sql = "UPDATE $table_name SET " . implode(', ', $sql_parts) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Profile updated successfully!';
        $response['new_name'] = $new_name;
        $response['new_avatar_path'] = $profile_pic_path_db ?? $current_data['profile_picture'] ?? 'img/user.png';
    } else {
        $response['message'] = 'Database update failed: ' . $stmt->error;
    }
} catch (Exception $e) {
    $response['message'] = 'PHP Error: ' . $e->getMessage();
}

echo json_encode($response);
?>
