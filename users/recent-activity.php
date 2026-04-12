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
       <link rel="stylesheet" href="../assets/css/users/styles.css">
       <link rel="stylesheet" href="../assets/css/users/sidebar.css">
</head>

<body>
    <?php include '../includes/navbar.php'; ?>
    <?php include '../includes/signout_modal.php'; ?>
    <?php include '../includes/profile.php'; ?>

    <main class="main-content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h1 style="margin-top: 40px; margin-bottom: -20px;">Recent Activity</h1>
        </div>

        <div class="container-fluid mt-4">
            <div class="card">
                <div class="card-header">
                    <h4>Activity Log</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                    <th>Table</th>
                                    <th>Changes</th>
                                    <th>Date/Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php include 'backend/recent-activity.php'; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/sidebar.js"></script>
</body>
</html>