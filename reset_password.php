<?php
$pageTitle = 'Reset Password';
require_once 'config.php';

$conn = getDBConnection();
$error = '';
$success = '';

if (!isset($_SESSION['reset_email'])) {
    redirect("forgot_password.php");
}

if (isset($_POST['reset'])) {
    $email = $_SESSION['reset_email'];
    $otp = $_POST['otp'];
    $password = $_POST['password'];

    if (empty($otp) || empty($password)) {
        $error = "Please fill all fields.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $new_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "SELECT * FROM otp_verification WHERE email = ? AND otp_code = ? AND is_used = 0 AND expires_at >= NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $otp);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $update = "UPDATE users SET password = ? WHERE email = ?";
            $stmt2 = $conn->prepare($update);
            $stmt2->bind_param("ss", $new_password, $email);
            $stmt2->execute();

            $mark = "UPDATE otp_verification SET is_used = 1 WHERE email = ?";
            $stmt3 = $conn->prepare($mark);
            $stmt3->bind_param("s", $email);
            $stmt3->execute();

            unset($_SESSION['reset_email']);

            echo "<script>alert('Password reset successful');window.location.href='login.php';</script>";
            exit();
        } else {
            $error = "Invalid or expired OTP";
        }
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
                        <div class="auth-icon"><i class="fas fa-lock-open"></i></div>
                        <h4>Reset Password</h4>
                        <p>Enter the OTP and your new password</p>
                    </div>
                    <div class="auth-body">

                        <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>



                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Enter OTP</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    <input type="text" name="otp" class="form-control otp-input" placeholder="Enter OTP" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="Enter new password" required minlength="6">
                                </div>
                            </div>

                            <button type="submit" name="reset" class="btn btn-primary w-100">
                                <i class="fas fa-sync-alt me-2"></i>Reset Password
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
