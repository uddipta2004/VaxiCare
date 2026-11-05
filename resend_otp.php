<?php

include 'config.php';

$conn = getDBConnection();

if (!isset($_SESSION['otp_email'])) {

    redirect("register.php");

}

$email = $_SESSION['otp_email'];

$otp = generateOTP();

$sql = "
INSERT INTO otp_verification
(email, otp_code, created_at, expires_at, is_used)
VALUES
(?, ?, NOW(),
DATE_ADD(NOW(), INTERVAL 5 MINUTE),
0)
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
"ss",
$email,
$otp
);

$stmt->execute();

if (sendOTPEmail($email, $otp)) {
    $_SESSION['success'] = 'New OTP sent successfully.';
} else {
    $_SESSION['error'] = 'Email could not be sent. Please check your SMTP settings.';
}

$conn->close();
redirect('verify_otp.php');

?>
