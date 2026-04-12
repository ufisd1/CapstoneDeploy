<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

include 'activity_log.php';

if (isset($_POST['add_expense'])) {
    $date = $_POST['date'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];

    $sql = "INSERT INTO expenses (date, category, description, amount, admin_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdi", $date, $category, $description, $amount, $admin_id);

    if ($stmt->execute()) {
        $expense_id = $conn->insert_id;
        $changes = [
            'date' => $date,
            'category' => $category,
            'description' => $description,
            'amount' => $amount
        ];
        
        if (!logActivity($conn, $admin_id, 'add', 'expenses', $expense_id, $changes)) {
            error_log("Failed to log add activity for expense ID: $expense_id");
        }
        
        $_SESSION['success'] = "Expense added successfully";
    } else {
        $_SESSION['error'] = "Failed to add expense: " . $conn->error;
    }
    header("Location: ../expenses.php");
    exit();
}

if (isset($_POST['update_expense'])) {
    $expense_id = $_POST['expense_id'];
    $date = $_POST['date'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];

    $old_sql = "SELECT * FROM expenses WHERE id=?";
    $old_stmt = $conn->prepare($old_sql);
    $old_stmt->bind_param("i", $expense_id);
    $old_stmt->execute();
    $old_result = $old_stmt->get_result();
    $old_data = $old_result->fetch_assoc();
    $old_stmt->close();

    $sql = "UPDATE expenses SET date=?, category=?, description=?, amount=?, admin_id=?, updated_at=NOW() WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdii", $date, $category, $description, $amount, $admin_id, $expense_id);

    if ($stmt->execute()) {
        $changes = [];
        if ($old_data['date'] != $date) $changes['date'] = $date;
        if ($old_data['category'] != $category) $changes['category'] = $category;
        if ($old_data['description'] != $description) $changes['description'] = $description;
        if ($old_data['amount'] != $amount) $changes['amount'] = $amount;
        
        if (!empty($changes)) {
            if (!logActivity($conn, $admin_id, 'update', 'expenses', $expense_id, $changes)) {
                error_log("Failed to log update activity for expense ID: $expense_id");
            }
        }
        
        $_SESSION['success'] = "Expense updated successfully";
    } else {
        $_SESSION['error'] = "Failed to update expense: " . $conn->error;
    }
    header("Location: ../expenses.php");
    exit();
}

if (isset($_POST['delete_expense'])) {
    $expense_id = $_POST['expense_id'];
    
    $get_sql = "SELECT * FROM expenses WHERE id=?";
    $get_stmt = $conn->prepare($get_sql);
    $get_stmt->bind_param("i", $expense_id);
    $get_stmt->execute();
    $result = $get_stmt->get_result();
    $expense_data = $result->fetch_assoc();
    $get_stmt->close();
    
    $sql = "DELETE FROM expenses WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $expense_id);
    
    if ($stmt->execute()) {
        $changes = [
            'date' => $expense_data['date'],
            'category' => $expense_data['category'],
            'description' => $expense_data['description'],
            'amount' => $expense_data['amount']
        ];
        
        if (!logActivity($conn, $admin_id, 'delete', 'expenses', $expense_id, $changes)) {
            error_log("Failed to log delete activity for expense ID: $expense_id");
        }
        
        $_SESSION['success'] = "Expense deleted successfully";
    } else {
        $_SESSION['error'] = "Failed to delete expense: " . $conn->error;
    }
    header("Location: ../expenses.php");
    exit();
}

if (isset($_GET['expense_id'])) {
    $expense_id = $_GET['expense_id'];
    
    $sql = "SELECT * FROM expenses WHERE id=? AND admin_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $expense_id, $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Expense not found or unauthorized']);
    }
    exit();
}

header("Location: ../expenses.php");
exit();
?>