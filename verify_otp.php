<?php
$pageTitle = 'Verify OTP';
require_once 'config.php';
$conn = getDBConnection();
$error = '';
if (!isset($_SESSION['otp_email'])) { redirect('register.php'); }
if (isset($_POST['verify'])) {
    requireCSRF();
    $entered_otp = preg_replace('/\D/', '', $_POST['otp'] ?? '');
    $email = $_SESSION['otp_email'];
    $stmt = $conn->prepare("SELECT otp_id FROM otp_verification WHERE email=? AND otp_code=? AND expires_at>=NOW() AND is_used=0");
    $stmt->bind_param('ss', $email, $entered_otp); $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        // Mark OTP as used
        $stmt2 = $conn->prepare("UPDATE otp_verification SET is_used=1 WHERE email=?");
        $stmt2->bind_param('s', $email); $stmt2->execute(); $stmt2->close();
        // Approve user after email verification
        $stmt3 = $conn->prepare("UPDATE users SET status='approved' WHERE email=? AND status='pending'");
        $stmt3->bind_param('s', $email); $stmt3->execute(); $stmt3->close();
        unset($_SESSION['otp_email']);
        $conn->close();
        $_SESSION['success'] = 'Email verified! Your account is now active. Please login.';
        redirect('login.php');
    } else {
        $error = 'Invalid or expired OTP. Please try again or request a new code.';
    }
    $stmt->close();
}
if (isset($_POST['resend'])) { $conn->close(); redirect('resend_otp.php'); }
$conn->close();
include 'header.php';
?>

<div class="split-auth-page">
  <div class="split-left" data-aos="fade-right">
    <div class="split-left-inner">
      <div class="split-brand">
        <div style="width:54px;height:54px;background:linear-gradient(135deg,#0ea5e9,#6366f1);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#fff;margin-bottom:16px;"><i class="fas fa-syringe"></i></div>
        <h2 style="font-weight:900;background:linear-gradient(135deg,#0ea5e9,#6366f1);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">VaxiCare</h2>
        <p style="color:rgba(255,255,255,.5);font-size:.85rem;margin-bottom:40px;">Almost there! Verify your email.</p>
      </div>
      <div class="text-center">
        <div style="width:120px;height:120px;background:rgba(14,165,233,.1);border:1px solid rgba(14,165,233,.3);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:3rem;color:#0ea5e9;"><i class="fas fa-envelope-open-text"></i></div>
        <p style="color:rgba(255,255,255,.6);max-width:280px;margin:0 auto;">We sent a 6-digit code to your email. It expires in 5 minutes.</p>
      </div>
    </div>
  </div>
  <div class="split-right" data-aos="fade-left">
    <div class="split-form-wrap">
      <div class="auth-header" style="text-align:left;padding:0 0 28px;border:none;background:none;">
        <div class="auth-icon" style="margin:0 0 16px;"><i class="fas fa-shield-alt"></i></div>
        <h4 style="font-size:1.6rem;font-weight:800;">Verify Your Email</h4>
        <p style="margin:0;">Code sent to <strong style="color:#0ea5e9;"><?php echo htmlspecialchars($_SESSION['otp_email'] ?? ''); ?></strong></p>
      </div>
      <?php if ($error): ?><div class="alert alert-danger mb-4"><i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?></div><?php endif; ?>

      <form method="POST" id="otpForm">
        <?php echo csrfField(); ?>
        <label class="form-label mb-3" style="font-size:.85rem;color:rgba(255,255,255,.7);">Enter the 6-digit verification code</label>
        <div class="d-flex gap-2 justify-content-center mb-3">
          <input class="otp-box form-control text-center fw-bold" maxlength="1" inputmode="numeric" pattern="[0-9]" style="width:52px;height:60px;font-size:1.5rem;border-radius:12px;">
          <input class="otp-box form-control text-center fw-bold" maxlength="1" inputmode="numeric" pattern="[0-9]" style="width:52px;height:60px;font-size:1.5rem;border-radius:12px;">
          <input class="otp-box form-control text-center fw-bold" maxlength="1" inputmode="numeric" pattern="[0-9]" style="width:52px;height:60px;font-size:1.5rem;border-radius:12px;">
          <input class="otp-box form-control text-center fw-bold" maxlength="1" inputmode="numeric" pattern="[0-9]" style="width:52px;height:60px;font-size:1.5rem;border-radius:12px;">
          <input class="otp-box form-control text-center fw-bold" maxlength="1" inputmode="numeric" pattern="[0-9]" style="width:52px;height:60px;font-size:1.5rem;border-radius:12px;">
          <input class="otp-box form-control text-center fw-bold" maxlength="1" inputmode="numeric" pattern="[0-9]" style="width:52px;height:60px;font-size:1.5rem;border-radius:12px;">
        </div>
        <input type="hidden" name="otp" id="otpHidden">
        <div class="text-center mb-4"><span style="color:rgba(255,255,255,.5);font-size:.85rem;">Code expires in </span><span id="otpCountdown" style="color:#f59e0b;font-weight:700;">5:00</span></div>
        <button type="submit" name="verify" class="btn btn-primary w-100 py-3 mb-3" style="border-radius:14px;"><i class="fas fa-check-circle me-2"></i>Verify OTP</button>
      </form>
      <form method="POST"><?php echo csrfField(); ?><button type="submit" name="resend" class="btn btn-secondary w-100" style="border-radius:14px;"><i class="fas fa-redo me-2"></i>Resend OTP</button></form>
      <p class="text-center mt-4 mb-0" style="font-size:.85rem;"><a href="register.php" class="auth-link"><i class="fas fa-arrow-left me-1"></i>Back to Register</a></p>
    </div>
  </div>
</div>
<script>
let sec=300; const cd=document.getElementById('otpCountdown');
const t=setInterval(()=>{ sec--; if(sec<=0){clearInterval(t);cd.textContent='Expired';cd.style.color='#ef4444';return;} const m=Math.floor(sec/60),s=sec%60; cd.textContent=m+':'+(s<10?'0':'')+s; if(sec<60)cd.style.color='#ef4444'; },1000);
</script>
<?php include 'footer.php'; ?>
