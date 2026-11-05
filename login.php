<?php
require_once 'config.php';
$pageTitle = 'Login';
if (isLoggedIn()) { redirect('dashboard.php'); }
include 'header.php';
$error = '';
if (isset($_POST['login'])) {
    requireCSRF();
    $email    = sanitizeInput($_POST['email']);
    $password = $_POST['password'] ?? '';  // Do NOT sanitize — htmlspecialchars breaks hash verify
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT user_id, name, password, status FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (passwordMatches($password, $user['password'])) {
            if ($user['status'] === 'rejected') {
                $error = "Your account has been rejected. Please contact support.";
            } elseif ($user['status'] === 'pending') {
                $error = "Your account is pending admin approval. You'll be notified by email once approved.";
            } else {
                $_SESSION['user_id']   = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                $stmt->close(); $conn->close();
                redirect('dashboard.php');
            }
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "No account found with that email. <a href='register.php' class='auth-link'>Register?</a>";
    }
    $stmt->close();
    $conn->close();
}
?>

<div class="split-auth-page">
    <!-- Left Branding Panel -->
    <div class="split-left" data-aos="fade-right">
        <div class="split-left-inner">
            <div class="split-brand">
                <div style="width:54px;height:54px;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <img src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="VaxiCare" style="width:100%;height:100%;object-fit:contain;filter:drop-shadow(0 0 25px rgba(14,165,233,.5));">
                </div>
                <h2 style="font-weight:900;background:linear-gradient(135deg,#0ea5e9,#6366f1);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:4px;">VaxiCare</h2>
                <p style="color:rgba(255,255,255,.5);font-size:.85rem;margin-bottom:40px;">India's Trusted Vaccination Portal</p>
            </div>
            <div class="split-benefits">
                <div class="split-benefit-item">
                    <div class="split-benefit-icon" style="background:rgba(14,165,233,.15);color:#0ea5e9;"><i class="fas fa-shield-alt"></i></div>
                    <div><h6>Secure & Verified</h6><p>Government-grade data security</p></div>
                </div>
                <div class="split-benefit-item">
                    <div class="split-benefit-icon" style="background:rgba(34,197,94,.15);color:#22c55e;"><i class="fas fa-calendar-check"></i></div>
                    <div><h6>Easy Slot Booking</h6><p>Book your preferred time slot</p></div>
                </div>
                <div class="split-benefit-item">
                    <div class="split-benefit-icon" style="background:rgba(99,102,241,.15);color:#6366f1;"><i class="fas fa-certificate"></i></div>
                    <div><h6>Digital Certificate</h6><p>Download instantly after vaccination</p></div>
                </div>
                <div class="split-benefit-item">
                    <div class="split-benefit-icon" style="background:rgba(245,158,11,.15);color:#f59e0b;"><i class="fas fa-bell"></i></div>
                    <div><h6>Instant Alerts</h6><p>Real-time slot availability updates</p></div>
                </div>
            </div>
            <div class="split-visual-card">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;background:linear-gradient(135deg,#22c55e,#0ea5e9);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;"><i class="fas fa-check"></i></div>
                    <div><div style="font-weight:700;color:#fff;font-size:.9rem;">Live & Secure System</div><div style="color:rgba(255,255,255,.5);font-size:.75rem;">All data encrypted & protected</div></div>
                    <div style="margin-left:auto;"><span style="width:8px;height:8px;background:#22c55e;border-radius:50%;display:inline-block;box-shadow:0 0 8px #22c55e;animation:pulseDot 2s infinite;"></span></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Right Form Panel -->
    <div class="split-right" data-aos="fade-left">
        <div class="split-form-wrap">
            <div class="auth-header" style="text-align:left;padding:0 0 28px;border:none;background:none;">
                <div class="auth-icon" style="margin:0 0 16px;"><i class="fas fa-user-shield"></i></div>
                <h4 style="font-size:1.6rem;font-weight:800;">Welcome Back</h4>
                <p style="margin:0;">Sign in to your VaxiCare account</p>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger mb-4"><i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <?php echo csrfField(); ?>
                <div class="float-group mb-4">
                    <input type="email" name="email" id="loginEmail" class="form-control float-input" placeholder=" " required>
                    <label for="loginEmail" class="float-label"><i class="fas fa-envelope me-2"></i>Email Address</label>
                </div>
                <div class="float-group mb-2">
                    <input type="password" name="password" id="loginPassword" class="form-control float-input" placeholder=" " required style="padding-right:50px;">
                    <label for="loginPassword" class="float-label"><i class="fas fa-lock me-2"></i>Password</label>
                    <button class="toggle-password float-eye" type="button" data-target="#loginPassword"><i class="fas fa-eye-slash"></i></button>
                </div>
                <div class="text-end mb-4">
                    <a href="forgot_password.php" class="auth-link" style="font-size:.85rem;">Forgot Password?</a>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 py-3 fw-700" style="font-size:1rem;border-radius:14px;">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>
            <hr style="border-color:rgba(255,255,255,.1);margin:24px 0;">
            <p class="text-center mb-0" style="font-size:.9rem;">
                <span class="auth-divider">New to VaxiCare? </span>
                <a href="register.php" class="auth-link fw-700">Create Account</a>
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>