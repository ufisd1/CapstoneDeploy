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
    <link rel="stylesheet" href="../assets/css/users/styles.css">
     <link rel="stylesheet" href="../assets/css/users/activity-log.css">
      <link rel="stylesheet" href="../assets/css/users/sidebar.css">
    
</head>

<body>
    <?php include '../includes/navbar.php'; ?>
    <?php include '../includes/signout_modal.php'; ?>
    <?php include '../includes/profile.php'; ?>

    <main class="main-content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h1  style="margin-top: 35px; margin-bottom: -20px;">Activity Log</h1>
        </div>

        <div class="container-fluid mt-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Activity Log</h5>
                        <div class="search-container">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search activities...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <?php
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $per_page = 10;
                    displayActivityTable($page, $per_page);
                    ?>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/activity-log.js"></script>
    <script src="../assets/js/sidebar.js"></script>
</body>

</html>