<?php
include 'conn.php';

$query = "SELECT al.*, u.full_name, u.role 
        FROM activity_log al
        JOIN users u ON al.user_id = u.id
        WHERE al.table_name IN ('egg_inventory', 'sales', 'users')
        ORDER BY al.action_time DESC
        LIMIT 5";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $count = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $count++ . "</td>";
        echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
        echo "<td><span class='badge bg-" . getActionColor($row['action']) . "'>" . strtoupper($row['action']) . "</span></td>";
        echo "<td>" . htmlspecialchars($row['table_name']) . "</td>";
        echo "<td>" . formatChanges($row['changes']) . "</td>";
        echo "<td>" . date('M d, Y H:i:s', strtotime($row['action_time'])) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7' class='text-center'>No activity found</td></tr>";
}

function getActionColor($action)
{
    switch ($action) {
        case 'add':
            return 'success';
        case 'update':
            return 'warning';
        case 'delete':
            return 'danger';
        default:
            return 'info';
    }
}

function formatChanges($changes)
{
    if (empty($changes)) return 'N/A';

    $changes = json_decode($changes, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return htmlspecialchars($changes);
    }

    $formatted = '<ul class="mb-0">';
    foreach ($changes as $field => $value) {
        $formatted .= '<li><strong>' . $field . '</strong>: ' . htmlspecialchars($value) . '</li>';
    }
    $formatted .= '</ul>';

    return $formatted;
}
