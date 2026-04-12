<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

include 'activity_log.php';

// Add Consumer
if (isset($_POST['add_supplier'])) {
    $supplier_name = $_POST['supplier_name'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $last_purchase = $_POST['last_purchase'];
    $next_delivery = $_POST['next_delivery'];
    $restock_reminder = isset($_POST['restock_reminder']) ? 1 : 0;

    $sql = "INSERT INTO consumers (supplier_name, contact_number, email, address, last_purchase, next_delivery, restock_reminder, admin_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssii", $supplier_name, $contact_number, $email, $address, $last_purchase, $next_delivery, $restock_reminder, $admin_id);

    if ($stmt->execute()) {
        $supplier_id = $conn->insert_id;
        $changes = [
            'supplier_name' => $supplier_name,
            'contact_number' => $contact_number,
            'email' => $email,
            'address' => $address,
            'last_purchase' => $last_purchase,
            'next_delivery' => $next_delivery,
            'restock_reminder' => $restock_reminder ? 'Enabled' : 'Disabled'
        ];
        
        if (!logActivity($conn, $admin_id, 'add', 'consumers', $supplier_id, $changes)) {
            error_log("Failed to log activity for adding consumer ID: $supplier_id");
        }
        
        header("Location: ../consumer_management.php?success=Supplier added successfully");
    } else {
        header("Location: ../consumer_management.php?error=Failed to add supplier");
    }
    exit();
}

// Update Consumer
if (isset($_POST['update_supplier'])) {
    $supplier_id = $_POST['supplier_id'];
    $supplier_name = $_POST['supplier_name'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $last_purchase = $_POST['last_purchase'];
    $next_delivery = $_POST['next_delivery'];
    $restock_reminder = isset($_POST['restock_reminder']) ? 1 : 0;

    $old_sql = "SELECT * FROM consumers WHERE supplier_id=?";
    $old_stmt = $conn->prepare($old_sql);
    $old_stmt->bind_param("i", $supplier_id);
    $old_stmt->execute();
    $old_result = $old_stmt->get_result();
    $old_data = $old_result->fetch_assoc();
    $old_stmt->close();

    $sql = "UPDATE consumers SET 
            supplier_name=?, 
            contact_number=?, 
            email=?, 
            address=?, 
            last_purchase=?, 
            next_delivery=?, 
            restock_reminder=?, 
            admin_id=?, 
            updated_at=NOW() 
            WHERE supplier_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssiii", $supplier_name, $contact_number, $email, $address, $last_purchase, $next_delivery, $restock_reminder, $admin_id, $supplier_id);

    if ($stmt->execute()) {
        $changes = [];
        if ($old_data['supplier_name'] != $supplier_name) $changes['supplier_name'] = $supplier_name;
        if ($old_data['contact_number'] != $contact_number) $changes['contact_number'] = $contact_number;
        if ($old_data['email'] != $email) $changes['email'] = $email;
        if ($old_data['address'] != $address) $changes['address'] = $address;
        if ($old_data['last_purchase'] != $last_purchase) $changes['last_purchase'] = $last_purchase;
        if ($old_data['next_delivery'] != $next_delivery) $changes['next_delivery'] = $next_delivery;
        if ($old_data['restock_reminder'] != $restock_reminder) $changes['restock_reminder'] = $restock_reminder ? 'Enabled' : 'Disabled';
        
        if (!empty($changes)) {
            if (!logActivity($conn, $admin_id, 'update', 'consumers', $supplier_id, $changes)) {
                error_log("Failed to log activity for updating consumer ID: $supplier_id");
            }
        }
        
        header("Location: ../consumer_management.php?success=Consumer updated successfully");
    } else {
        header("Location: ../consumer_management.php?error=Failed to update consumer");
    }
    exit();
}

if (isset($_POST['delete_supplier'])) {
    $supplier_id = $_POST['supplier_id'];

    $get_sql = "SELECT * FROM consumers WHERE supplier_id=?";
    $get_stmt = $conn->prepare($get_sql);
    $get_stmt->bind_param("i", $supplier_id);
    $get_stmt->execute();
    $result = $get_stmt->get_result();
    $supplier_data = $result->fetch_assoc();
    $get_stmt->close();

    $sql = "DELETE FROM consumers WHERE supplier_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supplier_id);
    
    if ($stmt->execute()) {
        $changes = [
            'supplier_name' => $supplier_data['supplier_name'],
            'contact_number' => $supplier_data['contact_number'],
            'email' => $supplier_data['email'],
            'address' => $supplier_data['address'],
            'last_purchase' => $supplier_data['last_purchase'],
            'next_delivery' => $supplier_data['next_delivery'],
            'restock_reminder' => $supplier_data['restock_reminder'] ? 'Enabled' : 'Disabled'
        ];
        
        if (!logActivity($conn, $admin_id, 'delete', 'consumers', $supplier_id, $changes)) {
            error_log("Failed to log activity for deleting consumer ID: $supplier_id");
        }
        
        header("Location: ../consumer_management.php?success=Consumer deleted permanently");
    } else {
        header("Location: ../consumer_management.php?error=Failed to delete consumer");
    }
    exit();
}

if (isset($_GET['supplier_id'])) {
    $supplier_id = $_GET['supplier_id'];
    
    $sql = "SELECT * FROM consumers WHERE supplier_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Consumer not found']);
    }
    exit();
}

header("Location: ../consumer_management.php");
exit();