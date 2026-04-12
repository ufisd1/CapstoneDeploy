<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Add Sale
if (isset($_POST['add_sales'])) {
    $admin_id = $_POST['admin_id'];
    $product = $_POST['product'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $total = $quantity * $price;
    $date = $_POST['date'];

    $sql = "INSERT INTO sales (product_name, quantity, price, total, sale_date, admin_id) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        header("Location: ../sales.php?error=Database error: " . urlencode($conn->error));
        exit();
    }

    $stmt->bind_param("siddsi", $product, $quantity, $price, $total, $date, $admin_id);

    if ($stmt->execute()) {
        $sale_id = $conn->insert_id;
        $changes = [
            'product_name' => $product,
            'quantity' => $quantity,
            'price' => $price,
            'total' => $total,
            'sale_date' => $date
        ];
        
        $log_sql = "INSERT INTO activity_log (admin_id, action, table_name, record_id, changes) 
                    VALUES (?, 'add', 'sales', ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param("iis", $admin_id, $sale_id, json_encode($changes));
        $log_stmt->execute();
        $log_stmt->close();
        
        header("Location: ../sales.php?success=Sale added successfully");
    } else {
        header("Location: ../sales.php?error=Failed to add sale: " . urlencode($stmt->error));
    }
    $stmt->close();
    exit();
}

// Update Sale
if (isset($_POST['update_sale'])) {
    $id = $_POST['transaction_id'];
    $product = $_POST['product'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $total = $quantity * $price;
    $date = $_POST['date'];

    $check_sql = "SELECT * FROM sales WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        header("Location: ../sales.php?error=Database error: " . urlencode($conn->error));
        exit();
    }
    
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $old_data = $result->fetch_assoc();
        
        $update_sql = "UPDATE sales SET 
                      product_name = ?, 
                      quantity = ?, 
                      price = ?, 
                      total = ?, 
                      sale_date = ? 
                      WHERE id = ?";
        
        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            header("Location: ../sales.php?error=Database error: " . urlencode($conn->error));
            $check_stmt->close();
            exit();
        }
        
        $update_stmt->bind_param("siddsi", $product, $quantity, $price, $total, $date, $id);

        if ($update_stmt->execute()) {
            $changes = [];
            if ($old_data['product_name'] != $product) $changes['product_name'] = $product;
            if ($old_data['quantity'] != $quantity) $changes['quantity'] = $quantity;
            if ($old_data['price'] != $price) $changes['price'] = $price;
            if ($old_data['total'] != $total) $changes['total'] = $total;
            if ($old_data['sale_date'] != $date) $changes['sale_date'] = $date;

            if (!empty($changes)) {
                $log_sql = "INSERT INTO activity_log (admin_id, action, table_name, record_id, changes) 
                            VALUES (?, 'update', 'sales', ?, ?)";
                $log_stmt = $conn->prepare($log_sql);
                $log_stmt->bind_param("iis", $admin_id, $id, json_encode($changes));
                $log_stmt->execute();
                $log_stmt->close();
            }
            
            header("Location: ../sales.php?success=Sale updated successfully");
        } else {
            header("Location: ../sales.php?error=Failed to update sale: " . urlencode($update_stmt->error));
        }
        $update_stmt->close();
    } else {
        header("Location: ../sales.php?error=Sale not found");
    }
    $check_stmt->close();
    exit();
}

if (isset($_POST['delete_sale'])) {
    $sale_id = $_POST['sale_id'];

    $check_sql = "SELECT * FROM sales WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        header("Location: ../sales.php?error=Database error: " . urlencode($conn->error));
        exit();
    }
    
    $check_stmt->bind_param("i", $sale_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        
        $delete_sql = "DELETE FROM sales WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        if (!$delete_stmt) {
            header("Location: ../sales.php?error=Database error: " . urlencode($conn->error));
            $check_stmt->close();
            exit();
        }
        
        $delete_stmt->bind_param("i", $sale_id);

        if ($delete_stmt->execute()) {
            $changes = [
                'product_name' => $data['product_name'],
                'quantity' => $data['quantity'],
                'price' => $data['price'],
                'total' => $data['total'],
                'sale_date' => $data['sale_date']
            ];
            
            $log_sql = "INSERT INTO activity_log (admin_id, action, table_name, record_id, changes) 
                        VALUES (?, 'delete', 'sales', ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("iis", $admin_id, $sale_id, json_encode($changes));
            $log_stmt->execute();
            $log_stmt->close();
            
            header("Location: ../sales.php?success=Sale deleted successfully");
        } else {
            header("Location: ../sales.php?error=Failed to delete sale: " . urlencode($delete_stmt->error));
        }
        $delete_stmt->close();
    } else {
        header("Location: ../sales.php?error=Sale not found");
    }
    $check_stmt->close();
    exit();
}
?>