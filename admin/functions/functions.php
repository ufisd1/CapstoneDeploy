<?php
date_default_timezone_set('Asia/Manila');

require '../../vendor/autoload.php';
include 'conn.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$secret_key = $_ENV['SECRET_KEY'] ?? 'default_fallback_secret_key';

function logActivity($conn, $admin_id, $action, $table_name, $record_id, $changes = null)
{
    $sql = "INSERT INTO activity_log (admin_id, action, table_name, record_id, changes) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $changes_json = is_array($changes) ? json_encode($changes) : $changes;
    $stmt->bind_param("issss", $admin_id, $action, $table_name, $record_id, $changes_json);
    $stmt->execute();
    $stmt->close();
}

function recordAdminLogin($conn, $admin_id, $status, $password = null)
{
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $login_time = date('Y-m-d H:i:s');

    $query = "INSERT INTO login_history 
              (admin_id, login_datetime, ip_address, user_agent, status, attempt_password) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $admin_id, $login_time, $ip_address, $user_agent, $status, $password);
    $stmt->execute();
    $stmt->close();
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

// --- Admin Login ---
if (isset($_POST['admin_login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM admin WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $admin = mysqli_fetch_assoc($result);

    if (!$admin) {
        $_SESSION['error'] = 'Wrong Email or Password';
        header("Location: ../login.php");
        exit();
    }

    if (password_verify($password, $admin['password'])) {
        $_SESSION['temp_email'] = $email;
        $_SESSION['temp_admin_id'] = $admin['id'];

        $otp = rand(100000, 999999);
        $expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        $hashed_otp = hash_hmac('sha256', $otp, $_ENV['SECRET_KEY']);

        mysqli_query($conn, "INSERT INTO otp_verifications (admin_id, email, otp, expires_at) 
            VALUES ('{$admin['id']}', '$email', '$hashed_otp', '$expires_at')");

        if (sendOTPEmail($email, $otp)) {
            $_SESSION['success'] = 'OTP sent to your email.';
        } else {
            $_SESSION['error'] = 'OTP email could not be sent.';
        }

        header("Location: ../verify_otp.php");
        exit();
    } else {
        recordAdminLogin($conn, $admin['id'], 'Failed', $password);
        $_SESSION['error'] = 'Wrong Email or Password';
        header("Location: ../login.php");
        exit();
    }
}

// --- OTP Verification ---
if (isset($_POST['admin_verify_otp'])) {
    $otp = $_POST['otp'];
    $email = $_SESSION['temp_email'];
    $admin_id = $_SESSION['temp_admin_id'];

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
        recordAdminLogin($conn, $admin_id, 'Success');
        $_SESSION['admin_id'] = $admin_id;
        $_SESSION['email'] = $email;
        $_SESSION['logged_in'] = true;
        unset($_SESSION['temp_email'], $_SESSION['temp_admin_id'], $_SESSION['last_otp_sent']);
        $_SESSION['success'] = 'OTP verified successfully.';
        header("Location: ../dashboard.php");
        exit();
    } else {
        $error_message = $response_data['message'] ?? 'Invalid or expired OTP.';
        $_SESSION['error'] = $error_message;
        header("Location: ../verify_otp.php");
        exit();
    }
}

// --- Resend OTP ---
if (isset($_POST['resend_otp'])) {
    if (isset($_SESSION['temp_email']) && isset($_SESSION['temp_admin_id'])) {
        $email = $_SESSION['temp_email'];
        $admin_id = $_SESSION['temp_admin_id'];
        $new_otp = rand(100000, 999999);
        $expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        $hashed_new_otp = hash_hmac('sha256', $new_otp, $_ENV['SECRET_KEY']);

        $query = "UPDATE otp_verifications 
                  SET otp='$hashed_new_otp', expires_at='$expires_at' 
                  WHERE admin_id='$admin_id' AND email='$email' AND verified=0";
        mysqli_query($conn, $query);

        sendOTPEmail($email, $new_otp);

        $_SESSION['success'] = 'New OTP sent to your email.';
        header("Location: ../verify_otp.php");
        exit();
    } else {
        $_SESSION['error'] = 'Session expired. Please log in again.';
        header("Location: ../login.php");
        exit();
    }
}

if (isset($_POST['add_production'])) {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../login.php");
        exit();
    }

    $quantity = $_POST['quantity'];
    $grade = $_POST['grade'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];
    $date = $_POST['date'];
    $admin_id = $_SESSION['admin_id'];

    $insert = mysqli_query($conn, "INSERT INTO production_records (production_date, quantity, grade, status, notes, created_by) 
        VALUES('$date', '$quantity', '$grade', '$status', '$notes', '$admin_id')");

    if ($insert) {
        $record_id = mysqli_insert_id($conn);
        $changes = compact('quantity', 'grade', 'status', 'notes', 'date');
        logActivity($conn, $admin_id, 'insert', 'production_records', $record_id, $changes);
        echo "<script>alert('Product added Successfully');window.location.href='../production.php';</script>";
    } else {
        echo "<script>alert('Product Not added');window.location.href='../production.php';</script>";
    }
    exit();
}

$conn->close();
