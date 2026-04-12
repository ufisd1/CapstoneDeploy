<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASSWORD'];
$name = $_ENV['DB_NAME'];

$conn = mysqli_connect($host, $user, $pass, $name);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
