<?php
include 'backend/auth.php';
include 'backend/loginHistory.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/signout_modal.php'; ?>
    <?php include 'includes/profile.php'; ?>

    <main class="main-content">
        <div class="container-fluid">
            <h2 class="my-4">Login History</h2>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h6 class="card-title text-center">Total Logins Today</h6>
                            <h2 class="card-text text-center"><?= $total_logins_today ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h6 class="card-title text-center">Successful Logins</h6>
                            <h2 class="card-text text-center"><?= $successful_logins ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h6 class="card-title text-center">Failed Login Attempts</h6>
                            <h2 class="card-text text-center"><?= $failed_logins ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h6 class="card-title text-center">Active Users</h6>
                            <h2 class="card-text text-center"><?= $active_users ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Name</th>
                                    <th>User Type</th>
                                    <th>Login Date & Time</th>
                                    <th>Signout Date & Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $count = ($page - 1) * $records_per_page + 1;
                                while ($row = mysqli_fetch_assoc($login_history_result)) { ?>
                                    <tr>
                                        <td><?= $count++; ?></td>
                                        <td><?= htmlspecialchars($row['full_name']); ?></td>
                                        <td>
                                            <span class="badge bg-<?= ($row['user_type'] == 'admin') ? 'primary' : 'secondary'; ?>">
                                                <?= htmlspecialchars(ucfirst($row['user_type'])); ?>
                                            </span>
                                        </td>
                                        <td><?= $row['deleted_at'] ? '<span class="badge bg-danger">Account Deleted</span>' : htmlspecialchars($row['login_datetime']); ?></td>
                                        <td><?= $row['deleted_at'] ? '<span class="badge bg-danger">Account Deleted</span>' : ($row['signout_datetime'] ? htmlspecialchars($row['signout_datetime']) : '<span class="badge bg-secondary">Still Active</span>'); ?></td>
                                        <td>
                                            <?php if ($row['deleted_at']): ?>
                                                <span class="badge bg-danger">Account Deleted</span>
                                            <?php else: ?>
                                                <span class="badge bg-<?= ($row['status'] == 'Success') ? 'success' : 'danger'; ?>">
                                                    <?= htmlspecialchars($row['status']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        <nav>
                            <ul class="pagination">
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                        <span aria-hidden="true">Prev</span>
                                    </a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= ($page == $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                        <span aria-hidden="true">Next</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>