<?php
date_default_timezone_set('Asia/Manila');

require '../vendor/autoload.php';
include 'conn.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function recordUserLogin($conn, $user_id, $status, $password = null)
{
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $login_time = date('Y-m-d H:i:s');

    $query = "INSERT INTO login_history 
              (user_id, login_datetime, ip_address, user_agent, status, attempt_password) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $user_id, $login_time, $ip_address, $user_agent, $status, $password);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        $_SESSION['error'] = 'Wrong Email or Password';
        header("Location: ../index.php");
        exit();
    }

    if (password_verify($password, $user['password'])) {
        $_SESSION['temp_email'] = $email;
        $_SESSION['temp_user_id'] = $user['id'];

        $otp = rand(100000, 999999);
        $expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $secret_key = $_ENV['SECRET_KEY'];

        $hashed_otp = hash_hmac('sha256', $otp, $secret_key);

        mysqli_query($conn, "INSERT INTO otp_verifications (user_id, email, otp, expires_at) 
        VALUES ('{$user['id']}', '$email', '$hashed_otp', '$expires_at')");

        if (sendOTPEmail($email, $otp)) {
            $_SESSION['success'] = 'OTP sent to your email.';
        } else {
            $_SESSION['error'] = 'OTP email could not be sent.';
        }

        header("Location: ../verify_otp.php");
        exit();
    } else {
        recordUserLogin($conn, $user['id'], 'Failed', $password);
        $_SESSION['error'] = 'Wrong Email or Password';
        header("Location: ../index.php");
        exit();
    }
}

function sendOTPEmail($email, $otp)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER'];
        $mail->Password = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['SMTP_PORT'];

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

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

if (isset($_POST['verify_otp'])) {
    $otp = $_POST['otp'];
    $email = $_SESSION['temp_email'];
    $user_id = $_SESSION['temp_user_id'];

    $data = ['email' => $email, 'otp' => $otp];

    $ch = curl_init($_ENV['VERIFY_OTP_API']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $response_data = json_decode($response, true);

    if ($http_code == 200 && isset($response_data['status']) && $response_data['status'] === 'success') {
        recordUserLogin($conn, $user_id, 'Success');

        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $email;
        $_SESSION['logged_in'] = true;
        unset($_SESSION['temp_email']);
        unset($_SESSION['temp_user_id']);
        unset($_SESSION['last_otp_sent']);
        $_SESSION['success'] = 'OTP verified successfully.';
        header("Location: ../users/dashboard.php");
        exit();
    } else {
        $error_message = $response_data['message'] ?? 'Invalid or expired OTP.';
        $_SESSION['error'] = $error_message;
        header("Location: ../verify_otp.php");
        exit();
    }
}

if (isset($_POST['resend_otp'])) {
    if (isset($_SESSION['temp_email']) && isset($_SESSION['temp_user_id'])) {
        $email = $_SESSION['temp_email'];
        $user_id = $_SESSION['temp_user_id'];
        $new_otp = rand(100000, 999999);
        $expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $secret_key = $_ENV['SECRET_KEY'];
        $hashed_new_otp = hash_hmac('sha256', $new_otp, $secret_key);

        $query = "UPDATE otp_verifications 
                  SET otp='$hashed_new_otp', expires_at='$expires_at' 
                  WHERE user_id='$user_id' AND email='$email' AND verified=0";
        mysqli_query($conn, $query);

        sendOTPEmail($email, $new_otp);

        $_SESSION['success'] = 'New OTP sent to your email.';
        header("Location: ../verify_otp.php");
        exit();
    }
}


if (isset($_GET['logout'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $query = "UPDATE login_history SET signout_datetime = NOW() 
                  WHERE user_id = '$user_id' AND signout_datetime IS NULL 
                  ORDER BY login_datetime DESC LIMIT 1";
        mysqli_query($conn, $query);
    }

    session_destroy();
    header("Location: ../index.php");
    exit();
}
