<?php
include 'conn.php';

$result = $conn->query("SELECT SUM(stock_quantity) AS total_produced FROM egg_inventory");
$totalProduced = ($row = $result->fetch_assoc()) ? $row['total_produced'] : 0;

$result = $conn->query("SELECT SUM(quantity) AS total_sold FROM sales");
$totalSold = ($row = $result->fetch_assoc()) ? $row['total_sold'] : 0;

$remainingStock = $totalProduced - $totalSold;

$result = $conn->query("
    (SELECT 'Sold ' AS action, quantity AS amount, DATE_FORMAT(sale_date, '%M %d, %Y') AS date FROM sales ORDER BY sale_date DESC LIMIT 5)
    UNION ALL
    (SELECT 'Added ' AS action, stock_quantity AS amount, DATE_FORMAT(production_date, '%M %d, %Y') AS date FROM egg_inventory ORDER BY production_date DESC LIMIT 5)
    ORDER BY date DESC LIMIT 5
");

$result = $conn->query("
    SELECT 
        DATE_FORMAT(sale_date, '%M %Y') AS month, 
        SUM(quantity * price) AS sales_revenue, 
        (SELECT SUM(amount) FROM expenses WHERE DATE_FORMAT(date, '%M %Y') = DATE_FORMAT(sale_date, '%M %Y')) AS expenses
    FROM sales 
    GROUP BY month 
    ORDER BY MIN(sale_date) ASC
");

$search = isset($_GET['search']) ? $_GET['search'] : '';
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

$sql = "SELECT * FROM egg_inventory ORDER BY production_date DESC LIMIT $offset, $records_per_page";
if (!empty($search)) {
    $sql = "SELECT * FROM egg_inventory WHERE (batch_id LIKE ? OR egg_type LIKE ? OR size LIKE ? OR quality LIKE ?) ORDER BY production_date DESC LIMIT $offset, $records_per_page";
    $searchParam = "%$search%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);
} else {
    $stmt = $conn->prepare($sql);
}
$stmt->execute();
$inventoryResult = $stmt->get_result();

$count_sql = "SELECT COUNT(*) as total FROM egg_inventory";
if (!empty($search)) {
    $count_sql = "SELECT COUNT(*) as total FROM egg_inventory WHERE (batch_id LIKE ? OR egg_type LIKE ? OR size LIKE ? OR quality LIKE ?)";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);
} else {
    $count_stmt = $conn->prepare($count_sql);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);