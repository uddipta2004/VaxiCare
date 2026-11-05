<?php
$pageTitle = 'Verify Login OTP';
require_once 'config.php';

if (!isset($_SESSION['otp_email'])) {
    redirect('login.php');
}

$conn = getDBConnection();
$error = '';

if (isset($_POST['verify'])) {
    $entered_otp = sanitizeInput($_POST['otp']);
    $email = $_SESSION['otp_email'];

    $sql = "SELECT u.user_id, u.name
            FROM users u
            JOIN otp_verification o ON u.email = o.email
            WHERE o.email = ?
            AND o.otp_code = ?
            AND o.expires_at >= NOW()
            AND o.is_used = 0";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $entered_otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['name'];
        unset($_SESSION['otp_email']);

        $stmt2 = $conn->prepare("UPDATE otp_verification SET is_used = 1 WHERE email = ?");
        $stmt2->bind_param("s", $email);
        $stmt2->execute();
        $stmt2->close();

        redirect('dashboard.php');
    } else {
        $error = "Invalid or expired OTP.";
    }

    $stmt->close();
}

include 'header.php';
?>

<main>
<div class="container py-5">
<div class="row justify-content-center">
<div class="col-lg-5">
<div class="card">
<div class="card-header"><h4>Verify Login OTP</h4></div>
<div class="card-body">
<?php if ($error): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<form method="POST">
<label>Enter OTP</label>
<input type="text" name="otp" class="form-control" maxlength="6" pattern="[0-9]{6}" required>
<br>
<button type="submit" name="verify" class="btn btn-primary w-100">Verify</button>
</form>
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
