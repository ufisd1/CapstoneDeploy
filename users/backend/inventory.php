<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'] ?? false;

if (isset($_POST['add_egg_stock'])) {
    $egg_type = $_POST['egg_type'];
    $size = $_POST['size'];
    $quality = $_POST['quality'];
    $stock_quantity = $_POST['stock_quantity'];
    $production_date = $_POST['production_date'];
    $expiry_date = $_POST['expiry_date'];

    $sql = "INSERT INTO egg_inventory (user_id, egg_type, size, quality, stock_quantity, production_date, expiry_date) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssiss", $user_id, $egg_type, $size, $quality, $stock_quantity, $production_date, $expiry_date);

    if ($stmt->execute()) {
        $batch_id = $conn->insert_id;
        $changes = [
            'egg_type' => $egg_type,
            'size' => $size,
            'quality' => $quality,
            'stock_quantity' => $stock_quantity,
            'production_date' => $production_date,
            'expiry_date' => $expiry_date
        ];
        include 'activity_log.php';
        logActivity($conn, $user_id, 'add', 'egg_inventory', $batch_id, $changes);

        header("Location: ../inventory.php?success=Egg stock added successfully");
    } else {
        header("Location: ../inventory.php?error=Failed to add egg stock");
    }
    exit();
}

// Update Egg Stock
if (isset($_POST['update_egg_stock'])) {
    $batch_id = $_POST['batch_id'];
    $egg_type = $_POST['egg_type'];
    $size = $_POST['size'];
    $quality = $_POST['quality'];
    $stock_quantity = $_POST['stock_quantity'];
    $production_date = $_POST['production_date'];
    $expiry_date = $_POST['expiry_date'];

    $old_sql = "SELECT * FROM egg_inventory WHERE batch_id=?";
    $old_stmt = $conn->prepare($old_sql);
    $old_stmt->bind_param("i", $batch_id);
    $old_stmt->execute();
    $old_result = $old_stmt->get_result();
    
    if ($old_result->num_rows > 0) {
        $old_data = $old_result->fetch_assoc();
        $old_stmt->close();

        $sql = "UPDATE egg_inventory SET egg_type=?, size=?, quality=?, stock_quantity=?, production_date=?, expiry_date=? WHERE batch_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssissi", $egg_type, $size, $quality, $stock_quantity, $production_date, $expiry_date, $batch_id);

        if ($stmt->execute()) {
            $changes = [];
            if ($old_data['egg_type'] != $egg_type) $changes['egg_type'] = $egg_type;
            if ($old_data['size'] != $size) $changes['size'] = $size;
            if ($old_data['quality'] != $quality) $changes['quality'] = $quality;
            if ($old_data['stock_quantity'] != $stock_quantity) $changes['stock_quantity'] = $stock_quantity;
            if ($old_data['production_date'] != $production_date) $changes['production_date'] = $production_date;
            if ($old_data['expiry_date'] != $expiry_date) $changes['expiry_date'] = $expiry_date;

            if (!empty($changes)) {
                include 'activity_log.php';
                logActivity($conn, $user_id, 'update', 'egg_inventory', $batch_id, $changes);
            }

            header("Location: ../inventory.php?success=Egg stock updated successfully");
        } else {
            header("Location: ../inventory.php?error=Failed to update egg stock");
        }
    } else {
        header("Location: ../inventory.php?error=Egg stock not found");
    }
    exit();
}

// Delete Egg Stock
if (isset($_POST['delete_egg_stock'])) {
    $batch_id = $_POST['batch_id'];

    $get_sql = "SELECT * FROM egg_inventory WHERE batch_id=?";
    $get_stmt = $conn->prepare($get_sql);
    $get_stmt->bind_param("i", $batch_id);
    $get_stmt->execute();
    $get_result = $get_stmt->get_result();
    
    if ($get_result->num_rows > 0) {
        $data = $get_result->fetch_assoc();
        $get_stmt->close();

        $sql = "DELETE FROM egg_inventory WHERE batch_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $batch_id);

        if ($stmt->execute()) {
            $changes = [
                'egg_type' => $data['egg_type'],
                'size' => $data['size'],
                'quality' => $data['quality'],
                'stock_quantity' => $data['stock_quantity'],
                'production_date' => $data['production_date'],
                'expiry_date' => $data['expiry_date']
            ];
            include 'activity_log.php';
            logActivity($conn, $user_id, 'delete', 'egg_inventory', $batch_id, $changes);

            header("Location: ../inventory.php?success=Egg stock deleted successfully");
        } else {
            header("Location: ../inventory.php?error=Failed to delete egg stock");
        }
    } else {
        header("Location: ../inventory.php?error=Egg stock not found");
    }
    exit();
}

if (isset($_GET['id'])) {
    $batch_id = $_GET['id'];
    $sql = "SELECT * FROM egg_inventory WHERE batch_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $batch_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode($data);
    exit();
}