
<?php
function displayActivityTable($page = 1, $per_page = 10)
{
    include 'conn.php';

    $offset = ($page - 1) * $per_page;

    $count_query = "SELECT COUNT(*) as total FROM activity_log 
                   WHERE table_name IN ('egg_inventory', 'sales', 'users')
                   AND user_id IS NOT NULL";
    $count_result = mysqli_query($conn, $count_query);
    $total_rows = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_rows / $per_page);

    $query = "SELECT al.*, u.full_name, u.role as user_role
              FROM activity_log al
              LEFT JOIN users u ON al.user_id = u.id
              WHERE al.table_name IN ('egg_inventory', 'sales', 'users')
              AND al.user_id IS NOT NULL
              ORDER BY al.action_time DESC 
              LIMIT $offset, $per_page";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
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
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $count++ . "</td>";
            echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['user_role']) . "</td>";
            echo "<td><span class='badge bg-" . getActionColor($row['action']) . "'>" . strtoupper($row['action']) . "</span></td>";
            echo "<td>" . htmlspecialchars($row['table_name']) . "</td>";
            echo "<td>" . formatChanges($row['changes']) . "</td>";
            echo "<td>" . date('M d, Y H:i:s', strtotime($row['action_time'])) . "</td>";
            echo "</tr>";
        }

        echo '</tbody></table></div>';

        echo '<nav aria-label="Page navigation">
                <ul class="pagination">';

        if ($page > 1) {
            echo '<li class="page-item">
                    <a class="page-link" href="?page=' . ($page - 1) . '" aria-label="Previous">
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
                    <a class="page-link" href="?page=' . ($page + 1) . '" aria-label="Next">
                        <span aria-hidden="true">Next &raquo;</span>
                    </a>
                  </li>';
        } else {
            echo '<li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Next</a>
                  </li>';
        }

        echo '</ul></nav>';

        echo '<div class="text-center text-muted">Page ' . $page . ' of ' . $total_pages . '</div>';
    } else {
        echo '<div class="alert alert-info">No activity found</div>';
    }
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
?>