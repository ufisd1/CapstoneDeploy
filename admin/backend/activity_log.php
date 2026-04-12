<?php
function logActivity($conn, $admin_id, $action, $table_name, $record_id, $changes = null) {
    $sql = "INSERT INTO activity_log (admin_id, action, table_name, record_id, changes) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    $changes_json = is_array($changes) ? json_encode($changes) : $changes;
    
    $stmt->bind_param("issss", $admin_id, $action, $table_name, $record_id, $changes_json);
    $stmt->execute();
    $stmt->close();
}
?>