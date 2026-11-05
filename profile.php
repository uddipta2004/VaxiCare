<?php
$pageTitle = 'Profile';
require_once 'config.php';
requireLogin();
$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$success = ''; $error = '';
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $age = isset($_POST['age']) ? intval($_POST['age']) : null;
    $gender = isset($_POST['gender']) ? sanitizeInput($_POST['gender']) : null;
    if (empty($name) || empty($email) || empty($phone) || empty($age) || empty($gender)) { $error = "Please fill in all fields."; }
    else {
        $photo_path = $user['photo'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            if (!is_dir('uploads/photos')) { mkdir('uploads/photos', 0755, true); }
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
            $filetype = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['photo']['tmp_name']);
            finfo_close($finfo);
            
            if (in_array($filetype, $allowed_exts) && in_array($mime, $allowed_mimes) && $_FILES['photo']['size'] <= 2097152) {
                $newname = time() . '_' . uniqid() . '.' . $filetype;
                $upload_path = 'uploads/photos/' . $newname;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                    if ($photo_path && file_exists($photo_path)) { unlink($photo_path); }
                    $photo_path = $upload_path;
                }
            } else {
                $error = "Invalid image format or file too large.";
            }
        }
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, age = ?, gender = ?, photo = ? WHERE user_id = ?");
        $stmt->bind_param("sssisss", $name, $email, $phone, $age, $gender, $photo_path, $user_id);
        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
            $_SESSION['user_name'] = $name;
            $user['name'] = $name; $user['email'] = $email; $user['phone'] = $phone; $user['age'] = $age; $user['gender'] = $gender; $user['photo'] = $photo_path;
        } else { $error = "Failed to update profile."; }
        $stmt->close();
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_new_password'];
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) { $error = "Please fill in all password fields."; }
    elseif ($new_password != $confirm_password) { $error = "New passwords do not match."; }
    elseif (strlen($new_password) < 6) { $error = "Password must be at least 6 characters."; }
    else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stored = $stmt->get_result()->fetch_assoc()['password'];
        $stmt->close();
        if (passwordMatches($current_password, $stored)) {
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->bind_param("si", $new_hashed, $user_id);
            if ($stmt->execute()) { $success = "Password changed successfully!"; }
            else { $error = "Failed to change password."; }
            $stmt->close();
        } else { $error = "Current password is incorrect."; }
    }
}
$conn->close();
include 'header.php';
?>

<main>
<div class="container py-5">
    <div class="page-header d-flex justify-content-between align-items-center" data-aos="fade-right">
        <h3><i class="fas fa-user-edit me-2" style="color:#0ea5e9;"></i>My Profile</h3>
        <a href="<?php echo BASE_URL; ?>dashboard.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Dashboard</a>
    </div>

    <?php if ($success): ?><div class="alert alert-success" data-aos="fade-up"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger" data-aos="fade-up"><i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?></div><?php endif; ?>

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-lg-4" data-aos="fade-right">
            <div class="card text-center">
                <div class="card-body p-4">
                    <?php if ($user['photo'] && file_exists($user['photo'])): ?>
                        <img src="<?php echo BASE_URL . $user['photo']; ?>" alt="Profile" class="dashboard-avatar mb-3">
                    <?php else: ?>
                        <div class="dashboard-avatar-placeholder mb-3"><i class="fas fa-user"></i></div>
                    <?php endif; ?>
                    <h5 class="text-white fw-bold"><?php echo htmlspecialchars($user['name']); ?></h5>
                    <p class="text-muted mb-2"><?php echo htmlspecialchars($user['email']); ?></p>
                    <span class="badge bg-<?php echo $user['status']=='approved'?'success':($user['status']=='pending'?'warning':'danger'); ?>">
                        <?php echo ucfirst($user['status']); ?>
                    </span>
                    <div class="mt-3 pt-3" style="border-top:1px solid rgba(255,255,255,.08);">
                        <p class="mb-1" style="font-size:.85rem;color:#64748b;"><i class="fas fa-phone me-2 text-primary"></i><?php echo htmlspecialchars($user['phone']); ?></p>
                        <p class="mb-0" style="font-size:.85rem;color:#64748b;"><i class="fas fa-calendar me-2 text-primary"></i>Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="col-lg-8">
            <div class="card mb-4" data-aos="fade-left">
                <div class="card-header"><h5 class="m-0"><i class="fas fa-edit me-2 text-primary"></i>Edit Profile</h5></div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" maxlength="10" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Age</label>
                                <input type="number" name="age" class="form-control" value="<?php echo htmlspecialchars($user['age'] ?? ''); ?>" min="1" max="120" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-control text-white" required style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);">
                                    <option value="Male" <?php echo ($user['gender'] == 'Male') ? 'selected' : ''; ?> class="text-dark">Male</option>
                                    <option value="Female" <?php echo ($user['gender'] == 'Female') ? 'selected' : ''; ?> class="text-dark">Female</option>
                                    <option value="Other" <?php echo ($user['gender'] == 'Other') ? 'selected' : ''; ?> class="text-dark">Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Profile Photo</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                                <div class="form-text">Max 2MB · JPG, PNG, GIF</div>
                            </div>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary mt-3">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <div class="card" data-aos="fade-left" data-aos-delay="100">
                <div class="card-header"><h5 class="m-0"><i class="fas fa-lock me-2 text-warning"></i>Change Password</h5></div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" minlength="6" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_new_password" class="form-control" minlength="6" required>
                            </div>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-warning mt-3">
                            <i class="fas fa-key me-2"></i>Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<?php include 'footer.php'; ?>
