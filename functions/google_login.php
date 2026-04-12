<?php
require_once '../vendor/autoload.php';
require_once 'conn.php';
session_start();

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$clientID = $_ENV['GOOGLE_CLIENT_ID'];
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'];
$redirectUri = $_ENV['GOOGLE_REDIRECT_URI'];

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

if (!isset($_GET['code'])) {
    header("Location: " . $client->createAuthUrl());
    exit();
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    $_SESSION['error'] = "Google authentication failed. Please try again.";
    header("Location: ../index.php");
    exit();
}

$client->setAccessToken($token['access_token']);

$google_oauth = new Google_Service_Oauth2($client);
$google_account_info = $google_oauth->userinfo->get();

$email = $google_account_info->email;
$name = $google_account_info->name;

$stmt = $conn->prepare("SELECT id, fullname FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['fullname'] = $user['fullname'];

    header("Location: ../users/dashboard.php");
    exit();
} else {
    $_SESSION['error'] = "No account found for this Google account.";
    header("Location: ../index.php");
    exit();
}
?>
