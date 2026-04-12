<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

if (isset($_POST['add_return_stock'])) {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../login.php");
        exit();
    }

    $egg_type = $_POST['egg_type'];
    $quantity = $_POST['quantity'];
    $return_reason = $_POST['return_reason'];
    $return_date = date('Y-m-d');
    $admin_id = $_SESSION['admin_id'];
    
    $sql = "INSERT INTO return_stocks (egg_type, quantity, return_reason, return_date, admin_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissi", $egg_type, $quantity, $return_reason, $return_date, $admin_id);

    if ($stmt->execute()) {
        $return_id = $conn->insert_id;
        $changes = [
            'egg_type' => $egg_type,
            'quantity' => $quantity,
            'return_reason' => $return_reason,
            'return_date' => $return_date
        ];
        include 'activity_log.php';
        logActivity($conn, $admin_id, 'add', 'return_stocks', $return_id, $changes);
        
        header("Location: ../return-stocks.php?success=Stock returned successfully");
    } else {
        header("Location: ../return-stocks.php?error=Failed to return stock");
    }
    exit();
}

if (isset($_POST['edit_return_stock'])) {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../login.php");
        exit();
    }

    $return_id = $_POST['id'];  
    $egg_type = $_POST['egg_type']; 
    $quantity = $_POST['quantity']; 
    $return_reason = $_POST['return_reason']; 
    $admin_id = $_SESSION['admin_id'];

    $old_sql = "SELECT * FROM return_stocks WHERE id=?";
    $old_stmt = $conn->prepare($old_sql);
    $old_stmt->bind_param("i", $return_id);
    $old_stmt->execute();
    $old_result = $old_stmt->get_result();
    $old_data = $old_result->fetch_assoc();
    $old_stmt->close();

    $sql = "UPDATE return_stocks SET egg_type=?, quantity=?, return_reason=?, admin_id=?, updated_at=NOW() WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisii", $egg_type, $quantity, $return_reason, $admin_id, $return_id);

    if ($stmt->execute()) {
        $changes = [];
        if ($old_data['egg_type'] != $egg_type) $changes['egg_type'] = $egg_type;
        if ($old_data['quantity'] != $quantity) $changes['quantity'] = $quantity;
        if ($old_data['return_reason'] != $return_reason) $changes['return_reason'] = $return_reason;
        
        if (!empty($changes)) {
            include 'activity_log.php';
            logActivity($conn, $admin_id, 'update', 'return_stocks', $return_id, $changes);
        }
        
        header("Location: ../return-stocks.php?success=Return Stock updated successfully");
    } else {
        header("Location: ../return-stocks.php?error=Failed to update return stock");
    }
    exit();
}

if (isset($_POST['delete_return_stock'])) {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../login.php");
        exit();
    }

    $id = $_POST['delete_return_stock_id'];
    $admin_id = $_SESSION['admin_id'];

    $get_sql = "SELECT * FROM return_stocks WHERE id=?";
    $get_stmt = $conn->prepare($get_sql);
    $get_stmt->bind_param("i", $id);
    $get_stmt->execute();
    $result = $get_stmt->get_result();
    $return_data = $result->fetch_assoc();
    $get_stmt->close();
    
    if ($return_data) {
        $sql = "DELETE FROM return_stocks WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $changes = [
                'egg_type' => $return_data['egg_type'],
                'quantity' => $return_data['quantity'],
                'return_reason' => $return_data['return_reason'],
                'return_date' => $return_data['return_date']
            ];
            include 'activity_log.php';
            logActivity($conn, $admin_id, 'delete', 'return_stocks', $id, $changes);
            
            header("Location: ../return-stocks.php?success=Return stock deleted successfully");
        } else {
            header("Location: ../return-stocks.php?error=Failed to delete return stock");
        }
    } else {
        header("Location: ../return-stocks.php?error=Return stock not found");
    }
    exit();
}

if (isset($_GET['id'])) {
    if (!isset($_SESSION['admin_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }

    $id = $_GET['id'];

    $sql = "SELECT * FROM return_stocks WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    header('Content-Type: application/json');
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Return stock not found']);
    }
    exit();
}

header("Location: ../return-stocks.php");
exit();
?>