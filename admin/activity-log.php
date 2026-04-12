<?php
include 'backend/auth.php';
include 'backend/activity_log_results.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/activity_log.css">

</head>

<body>
    <?php include 'includes/signout_modal.php'; ?>
    <?php include 'includes/navbar.php'; ?>
     <?php include 'includes/profile.php'; ?>

    <main class="main-content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h1 style="margin-top: 20px;">Activity Log</h1>
        </div>

        <div class="container-fluid mt-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <ul class="nav nav-tabs card-header-tabs" id="activityTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">All</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab">Admin</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">Users</button>
                            </li>
                        </ul>
                        <div class="search-container">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" class="form-control" id="searchInput" placeholder="Search activities...">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="activityTabContent">
                        <div class="tab-pane fade show active" id="all" role="tabpanel">
                            <?php displayActivityTable('all'); ?>
                        </div>
                        <div class="tab-pane fade" id="admin" role="tabpanel">
                            <?php displayActivityTable('admin'); ?>
                        </div>
                        <div class="tab-pane fade" id="users" role="tabpanel">
                            <?php displayActivityTable('users'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/activity-log.js"></script>
</body>

</html>