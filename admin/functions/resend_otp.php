<?php
session_start();
require_once 'conn.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

date_default_timezone_set('Asia/Manila');

$secret_key = $_ENV['SECRET_KEY'] ?? 'fallback_secret_key';

function generateOTP()
{
    return rand(100000, 999999);
}

function sendOTPEmail($email, $otp)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'];
        $mail->Password   = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $_ENV['SMTP_PORT'];

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom($_ENV['SMTP_FROM'], $_ENV['SMTP_NAME']);
        $mail->addAddress($email);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "Your OTP code is: $otp. It expires in 5 minutes.";

        return $mail->send();
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

// --- Session checks ---
if (!isset($_SESSION['temp_admin_id']) || !isset($_SESSION['temp_email'])) {
    $_SESSION['error'] = "You must be logged in to resend OTP.";
    header("Location: ../verify_otp.php");
    exit();
}

if (!isset($_SESSION['resend_attempts'])) {
    $_SESSION['resend_attempts'] = 0;
}

if ($_SESSION['resend_attempts'] >= 5) {
    $_SESSION['error'] = "You have exceeded the maximum number of OTP resend attempts.";
    header("Location: ../verify_otp.php");
    exit();
}

$otp = generateOTP();
$hashed_otp = hash_hmac('sha256', $otp, $secret_key);
$expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));

$admin_id = $_SESSION['temp_admin_id'];
$email = $_SESSION['temp_email'];

$stmt = $conn->prepare("INSERT INTO otp_verifications (admin_id, email, otp, expires_at, verified) VALUES (?, ?, ?, ?, 0)");
$stmt->bind_param("isss", $admin_id, $email, $hashed_otp, $expires_at);

if ($stmt->execute()) {
    if (sendOTPEmail($email, $otp)) {
        $_SESSION['success'] = "A new OTP has been sent to your email successfully.";
        $_SESSION['last_otp_sent'] = time();
        $_SESSION['resend_attempts']++;
    } else {
        $_SESSION['error'] = "Failed to send the new OTP. Please try again.";
    }
} else {
    $_SESSION['error'] = "Failed to insert OTP in the database. Please try again.";
}

header("Location: ../verify_otp.php");
exit();
