<?php
include 'conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];


$table_structure_query = "DESCRIBE sales";
$table_structure_result = $conn->query($table_structure_query);
$columns = [];
if ($table_structure_result) {
    while ($row = $table_structure_result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
}

$sample_query = "SELECT * FROM sales LIMIT 1";
$sample_result = $conn->query($sample_query);
$sample_data = null;
if ($sample_result && $sample_result->num_rows > 0) {
    $sample_data = $sample_result->fetch_assoc();
}

$totalSalesQuery = "SELECT COUNT(*) as total_sales, COALESCE(SUM(total), 0) as total_revenue FROM sales";
$totalSalesResult = $conn->query($totalSalesQuery);
if (!$totalSalesResult) {
    die("Error retrieving total sales: " . $conn->error);
}
$totalSalesData = $totalSalesResult->fetch_assoc();

$avgSaleQuery = "SELECT COALESCE(AVG(total), 0) as avg_sale FROM sales";
$avgSaleResult = $conn->query($avgSaleQuery);
if (!$avgSaleResult) {
    die("Error retrieving average sale: " . $conn->error);
}
$avgSaleData = $avgSaleResult->fetch_assoc();

$recentSalesQuery = "SELECT COALESCE(SUM(total), 0) as daily_sales, DATE(sale_date) as sale_day FROM sales WHERE sale_date IS NOT NULL GROUP BY DATE(sale_date) ORDER BY sale_day DESC LIMIT 7";
$recentSalesResult = $conn->query($recentSalesQuery);
if (!$recentSalesResult) {
    die("Error retrieving recent sales: " . $conn->error);
}
$recentSalesData = [];
while ($row = $recentSalesResult->fetch_assoc()) {
    $recentSalesData[] = $row;
}

$records_per_page = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $records_per_page;

$stmt = $conn->prepare("SELECT * FROM sales ORDER BY sale_date DESC LIMIT ?, ?");
$stmt->bind_param("ii", $offset, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error retrieving sales records: " . $conn->error);
}

$total_records_query = "SELECT COUNT(*) as total FROM sales";
$total_records_result = $conn->query($total_records_query);
if (!$total_records_result) {
    die("Error retrieving total records: " . $conn->error);
}
$total_records = $total_records_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

$sales_data = [];
while ($row = $result->fetch_assoc()) {
    $sales_data[] = $row;
}