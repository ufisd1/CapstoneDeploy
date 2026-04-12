<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include './functions/conn.php';

$name = "Guest";
$role = "Visitor";
$email = "";
$phone = "";
$profile_pic_path = "img/user.png";
$is_admin = false;


if (isset($_SESSION['admin_id'])) {
    $is_admin = true;
    $admin_id = $_SESSION['admin_id'];

    $sql = "SELECT admin_name, role, email, profile_picture FROM admin WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $name = $admin['admin_name'];
        $role = ucfirst(strtolower($admin['role']));
        $email = $admin['email'];

        if (!empty($admin['profile_picture'])) {
            $profile_pic_path = "./" . $admin['profile_picture'];
        }
    }
} elseif (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT full_name, role, email, phone, profile_picture FROM users WHERE id = ? AND deleted_at IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $name = $user['full_name'];
        $role = ucfirst(strtolower($user['role']));
        $email = $user['email'];
        $phone = $user['phone'];

        if (!empty($user['profile_picture'])) {
            // [FIX] Tiyakin na ang path ay nagsisimula sa ./
            $profile_pic_path = "./" . $user['profile_picture'];
        }
    }
}
?>

<style>
    .user-avatar-img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
        border: 2px solid #eee;
    }

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

    .modal-error-message {
        font-size: 0.9em;
        font-weight: bold;
    }

    .profile-upload-container {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto 15px auto;
    }

    #profilePreview {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #ddd;
    }

    .profile-camera-icon {
        position: absolute;
        bottom: 0px;
        right: 0px;
        width: 30px;
        height: 30px;
        background: #f5f5f5;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #333;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .profile-camera-icon:hover {
        background: #e0e0e0;
    }
    #processingModal .modal-dialog {
        max-width: 300px;
        width: 90%;
    }

    #processingModal .modal-content {
        border-radius: 15px;
    }
</style>

<div class="user-profile">
    <div class="user-info" onclick="toggleDropdown()">
        <img src="<?php echo htmlspecialchars($profile_pic_path); ?>" alt="Profile" class="user-avatar-img"
            id="mainAvatar">
        <div class="user-details">
            <div class="user-name"><?php echo htmlspecialchars($name); ?></div>
            <div class="user-role"><?php echo htmlspecialchars($role); ?></div>
        </div>
        <i class="fas fa-chevron-down dropdown-arrow" style="font-size: 12px;"></i>
    </div>

    <div class="profile-dropdown" id="profileDropdown">
        <a href="#" data-bs-toggle="modal" data-bs-target="#profileSettingsModal"><i class="fas fa-cog"></i> Profile
            Settings</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal"><i class="fas fa-key"></i> Change
            Password</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#signOutModal">
            <i class="fas fa-sign-out-alt"></i> Sign out
        </a>
    </div>
</div>

<div class="modal fade" id="profileSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Profile Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="profileSettingsForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <div class="profile-upload-container">
                            <img src="<?php echo htmlspecialchars($profile_pic_path); ?>" alt="Profile"
                                id="profilePreview">
                            <label for="profilePicture" class="profile-camera-icon">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="profilePicture" name="profile_picture" accept="image/*"
                                style="display: none;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="profileName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="profileName" name="name"
                            value="<?php echo htmlspecialchars($name); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="profileEmail" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="profileEmail" name="email"
                            value="<?php echo htmlspecialchars($email); ?>">
                    </div>
                    <div class="mb-3" <?php if ($is_admin)
                        echo 'style="display:none;"'; ?>>
                        <label for="profilePhone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="profilePhone" name="phone"
                            value="<?php echo htmlspecialchars($phone); ?>">
                    </div>

                    <div id="profileErrorMessage" class="modal-error-message text-danger mb-3"></div>

                    <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="current_password"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password"
                            required>
                    </div>

                    <div id="passwordErrorMessage" class="modal-error-message text-danger mb-3"></div>

                    <button type="submit" class="btn btn-success w-100">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="processingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="mt-3 mb-0">Processing...</h5>
                <p class="mb-0">Please wait while we save your changes.</p>
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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const profileModalEl = document.getElementById('profileSettingsModal');
        const passwordModalEl = document.getElementById('changePasswordModal');
        const processingModalEl = document.getElementById('processingModal');

        const profileErrorDiv = document.getElementById('profileErrorMessage');
        const passwordErrorDiv = document.getElementById('passwordErrorMessage');

        const profileSettingsModal = new bootstrap.Modal(profileModalEl);
        const passwordChangeModal = new bootstrap.Modal(passwordModalEl);

        const processingModal = new bootstrap.Modal(processingModalEl, {
            backdrop: 'static',
            keyboard: false
        });

        const fileInput = document.getElementById('profilePicture');
        const previewImg = document.getElementById('profilePreview');
        const defaultSrc = previewImg.src;

        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                }
                reader.readAsDataURL(file);
            } else {
                previewImg.src = defaultSrc;
            }
        });

        const profileForm = document.getElementById('profileSettingsForm');
        profileForm.addEventListener('submit', function (e) {
            e.preventDefault();
            profileErrorDiv.textContent = '';

            processingModal.show();

            const formData = new FormData(this);

            fetch('./backend/update_profile.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    processingModal.hide();

                    if (data.status === 'success') {
                        profileSettingsModal.hide();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true
                        }).then(() => {
                            location.reload();
                        });

                    } else {
                        profileErrorDiv.textContent = data.message;
                    }
                })
                .catch(error => {
                    processingModal.hide();
                    console.error('Error:', error);
                    profileErrorDiv.textContent = 'An unexpected error occurred. Check console.';
                });
        });

        const passwordForm = document.getElementById('changePasswordForm');
        passwordForm.addEventListener('submit', function (e) {
            e.preventDefault();
            passwordErrorDiv.textContent = '';

            const newPass = document.getElementById('newPassword').value;
            const confirmPass = document.getElementById('confirmPassword').value;

            if (newPass !== confirmPass) {
                passwordErrorDiv.textContent = 'New passwords do not match.';
                return;
            }

            processingModal.show();

            const formData = new FormData(this);

            fetch('./backend/change_password.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    processingModal.hide();

                    if (data.status === 'success') {
                        passwordChangeModal.hide();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        passwordErrorDiv.textContent = data.message;
                    }
                })
                .catch(error => {
                    processingModal.hide();
                    console.error('Error:', error);
                    passwordErrorDiv.textContent = 'An unexpected error occurred. Check console.';
                });
        });

    });
</script>