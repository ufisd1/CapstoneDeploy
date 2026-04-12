<?php
include 'backend/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Activity</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <?php include 'includes/signout_modal.php'; ?>
    <?php include 'includes/navbar.php'; ?>

    <main class="main-content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h1>Recent Activity</h1>
        </div>

        <div class="container-fluid mt-4">
            <div class="card">
                <div class="card-header">
                    <h4>Recent Activity</h4>
                </div>
                <div class="card-body">
                    <?php
                    include 'backend/conn.php';

                    $query = "SELECT al.*, a.admin_name, a.role AS admin_role
                              FROM activity_log al
                              JOIN admin a ON al.admin_id = a.id
                              WHERE al.admin_id IS NOT NULL
                              ORDER BY al.action_time DESC
                              LIMIT 10";

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

                        $count = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            $name = $row['admin_name'];
                            $role = $row['admin_role'];

                            echo "<tr>";
                            echo "<td>" . $count++ . "</td>";
                            echo "<td>" . htmlspecialchars($name) . "</td>";
                            echo "<td>" . htmlspecialchars($role) . "</td>";
                            echo "<td><span class='badge bg-" . getActionColor($row['action']) . "'>" . strtoupper($row['action']) . "</span></td>";
                            echo "<td>" . htmlspecialchars($row['table_name']) . "</td>";
                            echo "<td>" . formatChanges($row['changes']) . "</td>";
                            echo "<td>" . date('M d, Y H:i:s', strtotime($row['action_time'])) . "</td>";
                            echo "</tr>";
                        }

                        echo '</tbody></table></div>';
                    } else {
                        echo '<div class="alert alert-info">No activity found</div>';
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
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>