<?php
include 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Accounts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <?php include 'includes/navbar.php' ?>
    <?php include 'includes/signout_modal.php' ?>
    <?php include 'backend/conn.php' ?>
     <?php include 'includes/profile.php'; ?>

    <main class="main-content">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="header d-flex justify-content-between align-items-center mb-3">
            <h1 style="margin-top: 40px; ">Admin Accounts</h1>
            <div class="d-flex gap-2" style="margin-top: 60px;">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                    <i class="fas fa-plus me-2" ></i>Add New Admin
                </button>
            </div>
        </div>

        <div class="data-table">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>NO.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT id, admin_name, email, created_at FROM admin ORDER BY id DESC";
                        $result = $conn->query($sql);
                        $counter = 1;

                        if (!$result) {
                            die("Query Failed: " . $conn->error);
                        }

                        if ($result->num_rows > 0) {
                            while ($admin = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $counter . "</td>";
                                echo "<td>" . htmlspecialchars($admin['admin_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
                                echo "<td>" . (isset($admin['created_at']) ? date('M d, Y', strtotime($admin['created_at'])) : 'N/A') . "</td>";
                                echo "<td>";
                                if ($admin['id'] != $_SESSION['admin_id']) {
                                    echo "<button class='btn btn-sm btn-outline-danger' onclick='deleteAdmin(" . $admin['id'] . ", \"" . htmlspecialchars($admin['admin_name']) . "\")'>";
                                    echo "<i class='fas fa-trash'></i> Delete";
                                    echo "</button>";
                                } else {
                                    echo "<button class='btn btn-sm btn-outline-danger' disabled>";
                                    echo "<i class='fas fa-trash'></i> Delete";
                                    echo "</button>";
                                }
                                echo "</td>";
                                echo "</tr>";
                                $counter++;
                            }
                        } else {
                            echo "<tr><td colspan='5'>No admin accounts found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="backend/add_admin.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="fullname" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" class="form-control" name="password" minlength="6" required>
                            <div class="form-text">Password must be at least 6 characters long.</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" name="add_admin">Add Admin</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete admin <strong id="deleteAdminName"></strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <form action="backend/add_admin.php" method="POST">
                        <input type="hidden" name="admin_id" id="deleteAdminId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="delete_admin">
                            <i class="fas fa-trash"></i> Delete Admin
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin-accounts.js"></script>


</body>

</html>