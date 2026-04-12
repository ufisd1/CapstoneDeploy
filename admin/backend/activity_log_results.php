<?php
function logActivity($conn, $user_id, $action, $table_name, $record_id, $changes) {
    $is_admin = isset($_SESSION['admin_id']);
    $admin_id = $is_admin ? $user_id : null;
    $user_id = $is_admin ? null : $user_id;
    
    $changes_json = json_encode($changes);
    
    $sql = "INSERT INTO activity_log (admin_id, user_id, action, table_name, changes) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $admin_id, $user_id, $action, $table_name, $changes_json);
    
    return $stmt->execute();
}

function displayActivityTable($type) {
    include 'conn.php';
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;
    
    $count_query = "SELECT COUNT(*) as total FROM activity_log 
                   WHERE table_name IN ('egg_inventory', 'sales', 'users', 'admin', 'consumers', 'expenses', 'return_stocks')";
    
    $query = "SELECT al.*, 
              u.full_name, u.role as user_role,
              a.admin_name, a.role as admin_role
              FROM activity_log al
              LEFT JOIN users u ON al.user_id = u.id
              LEFT JOIN admin a ON al.admin_id = a.id
              WHERE al.table_name IN ('egg_inventory', 'sales', 'users', 'admin', 'consumers', 'expenses', 'return_stocks')";
    
    switch($type) {
        case 'admin':
            $count_query .= " AND admin_id IS NOT NULL";
            $query .= " AND al.admin_id IS NOT NULL";
            break;
        case 'users':
            $count_query .= " AND user_id IS NOT NULL";
            $query .= " AND al.user_id IS NOT NULL";
            break;
    }
    
    $count_result = mysqli_query($conn, $count_query);
    $total_rows = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_rows / $per_page);
    
    $query .= " ORDER BY al.action_time DESC LIMIT $offset, $per_page";
    
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0) {
        echo '<div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Action</th>
                            <th>Table</th>
                            <th>Changes</th>
                            <th>Date/Time</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        $count = $offset + 1;
        while($row = mysqli_fetch_assoc($result)) {
            $name = !empty($row['full_name']) ? $row['full_name'] : $row['admin_name'];
            $role = !empty($row['user_role']) ? $row['user_role'] : $row['admin_role'];
            $table_name = ucwords(str_replace('_', ' ', $row['table_name']));
            
            echo "<tr>";
            echo "<td>".$count++."</td>";
            echo "<td>".htmlspecialchars($name)."</td>";
            echo "<td>".htmlspecialchars($role)."</td>";
            echo "<td><span class='badge bg-".getActionColor($row['action'])."'>".strtoupper($row['action'])."</span></td>";
            echo "<td>".htmlspecialchars($table_name)."</td>";
            echo "<td>".formatChanges($row['changes'], $row['table_name'])."</td>";
            echo "<td>".date('M d, Y H:i:s', strtotime($row['action_time']))."</td>";
            echo "</tr>";
        }
        
        echo '</tbody></table></div>';
        
        echo '<nav aria-label="Page navigation">
                <ul class="pagination">';
        
        if ($page > 1) {
            echo '<li class="page-item">
                    <a class="page-link" href="?page='.($page - 1).'&tab='.$type.'" aria-label="Previous">
                        <span aria-hidden="true">&laquo; Previous</span>
                    </a>
                  </li>';
        } else {
            echo '<li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                  </li>';
        }
        
        if ($page < $total_pages) {
            echo '<li class="page-item">
                    <a class="page-link" href="?page='.($page + 1).'&tab='.$type.'" aria-label="Next">
                        <span aria-hidden="true">Next &raquo;</span>
                    </a>
                  </li>';
        } else {
            echo '<li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Next</a>
                  </li>';
        }
        
        echo '</ul></nav>';
        
        echo '<div class="text-center text-muted">Page '.$page.' of '.$total_pages.'</div>';
        
    } else {
        echo '<div class="alert alert-info">No activity found</div>';
    }
}

function getActionColor($action) {
    switch($action) {
        case 'add': return 'success';
        case 'update': return 'warning';
        case 'delete': return 'danger';
        default: return 'info';
    }
}

function formatChanges($changes, $table_name) {
    if(empty($changes)) return 'N/A';
    
    $changes = json_decode($changes, true);
    if(json_last_error() !== JSON_ERROR_NONE) {
        return htmlspecialchars($changes);
    }
    
    $formatted = '<ul class="changes-list mb-0">';
    
    foreach($changes as $field => $value) {
        $display_field = ucwords(str_replace('_', ' ', $field));
        $display_value = htmlspecialchars($value);
        
        switch($table_name) {
            case 'consumers':
                if ($field === 'restock_reminder') {
                    $display_value = $value ? 'Enabled' : 'Disabled';
                }
                break;
            case 'expenses':
                if ($field === 'amount') {
                    $display_value = '₱' . number_format($value, 2);
                }
                break;
            case 'return_stocks':
                if ($field === 'quantity') {
                    $display_value = number_format($value) . ' pcs';
                }
                break;
        }
        
        $formatted .= '<li><strong>'.$display_field.'</strong>: '.$display_value.'</li>';
    }
    
    $formatted .= '</ul>';
    
    return $formatted;
}
?>