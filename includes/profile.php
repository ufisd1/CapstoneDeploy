<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../functions/conn.php';

$name = "Guest";
$role = "Visitor";
$profile_icon = "fa-user";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT full_name, role FROM users WHERE id = ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $name = $user['full_name'];
        $role = ucfirst(strtolower($user['role']));
    }
} else if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $sql = "SELECT admin_name, role FROM admin WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $name = $admin['admin_name'];
        $role = ucfirst(strtolower($admin['role']));
        $profile_icon = "fa-user-shield";
    }
}
?>

<style>
    .profile-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        min-width: 200px;
        background: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border-radius: 4px;
        z-index: 100;
        display: none;
    }

    .profile-dropdown a {
        display: block;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        text-align: left;
        white-space: nowrap;
    }

    .profile-dropdown a:hover {
        background: #f5f5f5;
    }

    .profile-dropdown.show {
        display: block;
    }

    .dropdown-arrow {
        transition: transform 0.2s;
    }

    .profile-dropdown.show~.user-info .dropdown-arrow {
        transform: rotate(180deg);
    }
</style>

<div class="user-profile">
    <div class="user-info" onclick="toggleDropdown()">
        <div class="user-avatar">
            <i class="fas <?php echo $profile_icon; ?>"></i>
        </div>
        <div class="user-details">
            <div class="user-name"><?php echo htmlspecialchars($name); ?></div>
            <div class="user-role"><?php echo htmlspecialchars($role); ?></div>
        </div>
        <i class="fas fa-chevron-down dropdown-arrow" style="font-size: 12px;"></i>
    </div>

    <div class="profile-dropdown" id="profileDropdown">
        <a href="#" data-bs-toggle="modal" data-bs-target="#profileSettingsModal"><i class="fas fa-cog"></i> Profile Settings</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal"><i class="fas fa-key"></i> Change Password</a>
        <?php if ($role === 'Admin'): ?>
            <a href="system-settings.php"><i class="fas fa-sliders-h"></i> System Settings</a>
        <?php endif; ?>
        <a href="#" data-bs-toggle="modal" data-bs-target="#signOutModal">
            <i class="fas fa-sign-out-alt"></i> Sign out
        </a>
    </div>
</div>

<div class="modal fade" id="profileSettingsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Profile Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="profileSettingsForm" enctype="multipart/form-data">
                    <div class="mb-3 text-center">
                        <img src="../assets/img/user.png" alt="Profile" id="profilePreview" class="rounded-circle mb-2" width="100" height="100">
                        <input type="file" class="form-control" id="profilePicture" name="profile_picture" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="profileName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="profileName" name="full_name" value="<?php echo htmlspecialchars($name); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="profileEmail" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="profileEmail" name="email" value="">
                    </div>
                    <div class="mb-3">
                        <label for="profilePhone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="profilePhone" name="phone" value="">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="current_password">
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password">
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        dropdown.classList.toggle('show');
        document.addEventListener('click', function closeDropdown(e) {
            if (!e.target.closest('.user-profile')) {
                dropdown.classList.remove('show');
                document.removeEventListener('click', closeDropdown);
            }
        });
    }
</script>
