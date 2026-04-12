<?php
include 'conn.php';
include 'activity_log.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

if (isset($_POST['add_admin'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    if (empty($fullname) || empty($email) || empty($pass)) {
        header("Location: ../admin-accounts.php?error=All fields are required");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../admin-accounts.php?error=Invalid email format");
        exit();
    }

    $check_sql = "SELECT id FROM admin WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    if ($check_stmt) {
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            header("Location: ../admin-accounts.php?error=Email already exists");
            $check_stmt->close();
            exit();
        }
        $check_stmt->close();
    }

    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
    $sql = "INSERT INTO admin (admin_name, email, password, created_by) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssi", $fullname, $email, $hashed_password, $admin_id);
        if ($stmt->execute()) {
            $new_admin_id = $conn->insert_id;
            
            $changes = [
                'admin_name' => $fullname,
                'email' => $email,
                'created_by' => $admin_id
            ];
            logActivity($conn, $admin_id, 'add', 'admin', $new_admin_id, $changes);
            
            header("Location: ../admin-accounts.php?success=Admin added successfully");
        } else {
            header("Location: ../admin-accounts.php?error=Failed to add Admin: " . $stmt->error);
        }
        $stmt->close();
    } else {
        header("Location: ../admin-accounts.php?error=Database error: " . $conn->error);
    }
    exit();
}

if (isset($_POST['delete_admin'])) {
    $delete_admin_id = intval($_POST['admin_id'] ?? 0);
    
    if ($delete_admin_id <= 0) {
        header("Location: ../admin-accounts.php?error=Invalid admin ID");
        exit();
    }

    if ($delete_admin_id == $admin_id) {
        header("Location: ../admin-accounts.php?error=You cannot delete your own account");
        exit();
    }

    $get_sql = "SELECT * FROM admin WHERE id = ?";
    $get_stmt = $conn->prepare($get_sql);
    $get_stmt->bind_param("i", $delete_admin_id);
    $get_stmt->execute();
    $result = $get_stmt->get_result();
    $admin_data = $result->fetch_assoc();
    $get_stmt->close();

    if (!$admin_data) {
        header("Location: ../admin-accounts.php?error=Admin not found");
        exit();
    }

    $sql = "DELETE FROM admin WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_admin_id);
    
    if ($stmt && $stmt->execute()) {
        $changes = [
            'admin_name' => $admin_data['admin_name'],
            'email' => $admin_data['email'],
            'deleted_by' => $admin_id
        ];
        logActivity($conn, $admin_id, 'delete', 'admin', $delete_admin_id, $changes);
        
        header("Location: ../admin-accounts.php?success=Admin deleted successfully");
    } else {
        header("Location: ../admin-accounts.php?error=Failed to delete admin: " . ($stmt ? $stmt->error : $conn->error));
    }
    
    if ($stmt) $stmt->close();
    exit();
}

header("Location: ../admin-accounts.php?error=Invalid request");
exit();
?>