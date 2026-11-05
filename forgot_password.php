<?php
$pageTitle = 'Forgot Password';
require_once 'config.php';

$conn = getDBConnection();
$message = '';
$error = '';

if (isset($_POST['submit'])) {
    $email = sanitizeInput($_POST['email']);

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $userExists = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if (!$userExists) {
        $error = "No user account found with that email.";
    } else {
        $otp = generateOTP();

        $sql = "INSERT INTO otp_verification (email, otp_code, created_at, expires_at, is_used)
                VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 5 MINUTE), 0)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $otp);
        $stmt->execute();
        $stmt->close();

        $_SESSION['reset_email'] = $email;

        if (sendOTPEmail($email, $otp)) {
            $_SESSION['success'] = "OTP sent to your email.";
        } else {
            $_SESSION['error'] = "Email could not be sent. Please check your SMTP configuration.";
        }

        redirect('reset_password.php');
    }
}

include 'header.php';
?>

<main>
<div class="auth-page">
    <i class="fas fa-heartbeat auth-bg-icon auth-bg-icon-1"></i>
    <i class="fas fa-shield-virus auth-bg-icon auth-bg-icon-2"></i>
    <i class="fas fa-syringe auth-bg-icon auth-bg-icon-3"></i>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 auth-shell" data-aos="fade-up">
                <div class="auth-card">
                    <div class="auth-header">
                        <div class="auth-icon"><i class="fas fa-key"></i></div>
                        <h4>Forgot Password</h4>
                        <p>Enter your email to receive a reset OTP</p>
                    </div>
                    <div class="auth-body">

                        <?php if ($message): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                                </div>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i>Send OTP
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="login.php" class="auth-link"><i class="fas fa-arrow-left me-1"></i>Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<?php
$conn->close();
include 'footer.php';
?>
