<?php
require_once 'config.php';
$pageTitle = 'Register';
if (isLoggedIn()) { redirect('dashboard.php'); }
include 'header.php';
$conn = getDBConnection();
$error = '';
if (!isset($_SESSION['captcha'])) { $_SESSION['captcha'] = generateCaptcha(); }
if (isset($_POST['register'])) {
    // CSRF check
    requireCSRF();
    $name             = sanitizeInput($_POST['name']);
    $email            = sanitizeInput($_POST['email']);
    $phone            = sanitizeInput($_POST['phone']);
    $age              = isset($_POST['age']) ? intval($_POST['age']) : null;
    $gender           = isset($_POST['gender']) ? sanitizeInput($_POST['gender']) : null;
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $captcha_input    = strtoupper(sanitizeInput($_POST['captcha']));
    $captchaExpected  = strtoupper($_SESSION['captcha'] ?? '');
    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif ($captcha_input !== $captchaExpected) {
        $error = "Invalid captcha. Please try again."; $_SESSION['captcha'] = generateCaptcha();
    } elseif (!preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
        $error = "Enter a valid phone number (7-15 digits).";
    } else {
        // Check email uniqueness
        $check = $conn->prepare("SELECT user_id FROM users WHERE email=?");
        $check->bind_param("s", $email); $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "Email already registered. Please login or use a different email.";
        } else {
            // Check phone uniqueness
            $checkPhone = $conn->prepare("SELECT user_id FROM users WHERE phone=?");
            $checkPhone->bind_param("s", $phone); $checkPhone->execute();
            if ($checkPhone->get_result()->num_rows > 0) {
                $error = "Phone number already registered.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $insert = $conn->prepare("INSERT INTO users (name,email,phone,age,gender,password) VALUES (?,?,?,?,?,?)");
                $insert->bind_param("sssiss", $name, $email, $phone, $age, $gender, $hashed);
                if ($insert->execute()) {
                    $otp    = generateOTP();
                    $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));
                    $_SESSION['otp_email'] = $email;
                    $otp_insert = $conn->prepare("INSERT INTO otp_verification (email,otp_code,expires_at) VALUES (?,?,?)");
                    $otp_insert->bind_param("sss", $email, $otp, $expiry);
                    $otp_insert->execute();
                    $otp_insert->close();
                    sendOTPEmail($email, $otp);
                    $conn->close();
                    $_SESSION['success'] = 'Account created! Please verify your email with the OTP sent.';
                    redirect('verify_otp.php');
                } else {
                    $error = "Registration failed. Please try again. (" . $conn->error . ")";
                }
                $insert->close();
            }
            $checkPhone->close();
        }
        $check->close();
    }
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
                <h2 style="font-weight:900;background:linear-gradient(135deg,#0ea5e9,#6366f1);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:4px;">Join VaxiCare</h2>
                <p style="color:rgba(255,255,255,.5);font-size:.85rem;margin-bottom:40px;">Free Registration — Takes 2 minutes</p>
            </div>
            <div class="split-benefits">
                <div class="split-benefit-item">
                    <div class="split-benefit-icon" style="background:rgba(14,165,233,.15);color:#0ea5e9;"><i class="fas fa-user-plus"></i></div>
                    <div><h6>Step 1: Register</h6><p>Create your free account</p></div>
                </div>
                <div class="split-benefit-item">
                    <div class="split-benefit-icon" style="background:rgba(34,197,94,.15);color:#22c55e;"><i class="fas fa-envelope-open-text"></i></div>
                    <div><h6>Step 2: Verify OTP</h6><p>Confirm your email address</p></div>
                </div>
                <div class="split-benefit-item">
                    <div class="split-benefit-icon" style="background:rgba(99,102,241,.15);color:#6366f1;"><i class="fas fa-calendar-plus"></i></div>
                    <div><h6>Step 3: Book Slot</h6><p>Choose your vaccination center</p></div>
                </div>
                <div class="split-benefit-item">
                    <div class="split-benefit-icon" style="background:rgba(245,158,11,.15);color:#f59e0b;"><i class="fas fa-download"></i></div>
                    <div><h6>Step 4: Get Certificate</h6><p>Download your digital proof</p></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Right Form Panel -->
    <div class="split-right" data-aos="fade-left">
        <div class="split-form-wrap">
            <div class="auth-header" style="text-align:left;padding:0 0 24px;border:none;background:none;">
                <div class="auth-icon" style="margin:0 0 16px;"><i class="fas fa-user-plus"></i></div>
                <h4 style="font-size:1.5rem;font-weight:800;">Create Account</h4>
                <p style="margin:0;">Join VaxiCare — Free &amp; Quick</p>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger mb-3"><i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="float-group">
                            <input type="text" name="name" id="regName" class="form-control float-input" placeholder=" " required>
                            <label for="regName" class="float-label"><i class="fas fa-user me-2"></i>Full Name</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="float-group">
                            <input type="email" name="email" id="regEmail" class="form-control float-input" placeholder=" " required>
                            <label for="regEmail" class="float-label"><i class="fas fa-envelope me-2"></i>Email</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="float-group">
                            <input type="text" name="phone" id="regPhone" class="form-control float-input" placeholder=" " required>
                            <label for="regPhone" class="float-label"><i class="fas fa-phone me-2"></i>Phone Number</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="float-group">
                            <input type="number" name="age" id="regAge" class="form-control float-input" placeholder=" " required min="1" max="120">
                            <label for="regAge" class="float-label"><i class="fas fa-birthday-cake me-2"></i>Age</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="float-group">
                            <select name="gender" id="regGender" class="form-control float-input text-white" required style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);">
                                <option value="" disabled selected class="text-dark">Gender</option>
                                <option value="Male" class="text-dark">Male</option>
                                <option value="Female" class="text-dark">Female</option>
                                <option value="Other" class="text-dark">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="float-group">
                            <input type="password" name="password" id="regPass" class="form-control float-input" placeholder=" " required style="padding-right:50px;">
                            <label for="regPass" class="float-label"><i class="fas fa-lock me-2"></i>Password</label>
                            <button class="toggle-password float-eye" type="button" data-target="#regPass"><i class="fas fa-eye-slash"></i></button>
                        </div>
                        <div class="mt-2">
                            <div style="height:4px;background:rgba(255,255,255,.08);border-radius:3px;overflow:hidden;">
                                <div id="strengthBar" style="height:100%;width:0;border-radius:3px;transition:all .4s;"></div>
                            </div>
                            <span id="strengthLabel" style="font-size:.72rem;color:#64748b;"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="float-group">
                            <input type="password" name="confirm_password" id="regConfirm" class="form-control float-input" placeholder=" " required>
                            <label for="regConfirm" class="float-label"><i class="fas fa-lock me-2"></i>Confirm Password</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" style="font-size:.82rem;color:rgba(255,255,255,.7);">Security Captcha</label>
                        <div class="auth-captcha mb-2"><?php echo $_SESSION['captcha']; ?></div>
                        <div class="float-group">
                            <input type="text" name="captcha" id="regCaptcha" class="form-control float-input" placeholder=" " required>
                            <label for="regCaptcha" class="float-label"><i class="fas fa-keyboard me-2"></i>Enter Captcha</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="register" class="btn btn-primary w-100 py-3" style="font-size:1rem;border-radius:14px;">
                            <i class="fas fa-user-check me-2"></i>Create Account &amp; Send OTP
                        </button>
                    </div>
                    <div class="col-12 text-center">
                        <?php echo csrfField(); ?>
                    </div>
                </div>
            </form>
            <p class="text-center mt-3 mb-0" style="font-size:.9rem;">
                <span class="auth-divider">Already registered? </span>
                <a href="login.php" class="auth-link fw-700">Sign In</a>
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>