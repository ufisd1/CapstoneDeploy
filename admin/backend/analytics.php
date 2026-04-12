<?php
session_start();
include 'conn.php';

$result = $conn->query("SELECT SUM(stock_quantity) AS total_stock FROM egg_inventory");
$totalStock = ($row = $result->fetch_assoc()) ? $row['total_stock'] : 0;

$result = $conn->query("SELECT COUNT(*) AS low_stock_count FROM egg_inventory WHERE stock_quantity < 50");
$lowStockCount = ($row = $result->fetch_assoc()) ? $row['low_stock_count'] : 0;

$result = $conn->query("SELECT SUM(quantity) AS total_returns FROM return_stocks");
$totalReturns = ($row = $result->fetch_assoc()) ? $row['total_returns'] : 0;

$inventoryData = [];
$result = $conn->query("SELECT DATE_FORMAT(production_date, '%Y-%m-%d') AS date, SUM(stock_quantity) AS total FROM egg_inventory GROUP BY date ORDER BY date ASC");
while ($row = $result->fetch_assoc()) {
    $inventoryData[] = $row;
}

$expenseData = [];
$result = $conn->query("SELECT DATE_FORMAT(date, '%Y-%m-%d') AS day, SUM(amount) AS total FROM expenses GROUP BY day ORDER BY day ASC");
while ($row = $result->fetch_assoc()) {
    $expenseData[] = $row;
}

$salesData = [];
$result = $conn->query("SELECT DATE_FORMAT(sale_date, '%Y-%m-%d') AS day, SUM(quantity * price) AS revenue FROM sales GROUP BY day ORDER BY day ASC");
while ($row = $result->fetch_assoc()) {
    $salesData[] = $row;
}

$stockByType = [];
$result = $conn->query("SELECT egg_type, SUM(stock_quantity) AS total FROM egg_inventory GROUP BY egg_type");
while ($row = $result->fetch_assoc()) {
    $stockByType[] = $row;
}

$productionTrends = [];
$result = $conn->query("SELECT DATE_FORMAT(production_date, '%Y-%m-%d') AS date, SUM(stock_quantity) AS total_production FROM egg_inventory GROUP BY date ORDER BY date ASC");
while ($row = $result->fetch_assoc()) {
    $productionTrends[] = $row;
}

$revenueAnalysis = [];
$result = $conn->query("SELECT DATE_FORMAT(sale_date, '%Y-%m-%d') AS date, SUM(quantity * price) AS total_revenue FROM sales GROUP BY date ORDER BY date ASC");
while ($row = $result->fetch_assoc()) {
    $revenueAnalysis[] = $row;
}
?>