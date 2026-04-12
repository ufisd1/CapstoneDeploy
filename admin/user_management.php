<?php
include 'backend/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <?php include 'includes/navbar.php' ?>
    <?php include 'includes/signout_modal.php' ?>
    <?php include 'backend/conn.php' ?>
    <?php include 'includes/profile.php'; ?>

    <main class="main-content">
        <div class="header d-flex justify-content-between align-items-center mb-3">
            <h1 style="margin-top: 25px;">Users Account</h1>
            <div class="d-flex gap-2" style="margin-top: 60px;">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus me-2"></i>Add New User
                </button>
                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#trashModal">
                    <i class="fas fa-trash me-2"></i>Trash
                </button>
            </div>
        </div>

        <div class="data-table">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT id, full_name, email, role FROM users WHERE deleted_at IS NULL";
                        $result = $conn->query($sql);

                        if (!$result) {
                            die("Query Failed: " . $conn->error);
                        }

                        if ($result->num_rows > 0) {
                            while ($user = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                                echo "<td>";
                                echo "     <button class='btn btn-danger btn-sm' onclick='deleteUser(" . $user['id'] . ")'>";
                                echo "         <i class='fa fa-trash'></i> Delete";
                                echo "     </button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No active users found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div class="modal fade" id="trashModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Trash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Deleted At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT id, full_name, email, role, deleted_at FROM users WHERE deleted_at IS NOT NULL";
                                $result = $conn->query($sql);

                                if (!$result) {
                                    die("Query Failed: " . $conn->error);
                                }

                                if ($result->num_rows > 0) {
                                    while ($user = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                                        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                                        echo "<td>" . htmlspecialchars($user['deleted_at']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No deleted users found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm" method="POST">
                        <div id="errorContainer" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label class="form-label">Fullname *</label>
                            <input type="text" class="form-control" name="fullname" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" class="form-control" name="password" minlength="6" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" name="add_user">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteUserForm" method="POST">
                        <input type="hidden" name="user_id" id="deleteUserId" value="">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="delete_user">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/user-management.js"></script>
</body>

</html>