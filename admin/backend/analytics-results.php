<?php
include 'conn.php';

$timeFilter = isset($_GET['time_filter']) ? $_GET['time_filter'] : 'daily';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

function getDateFilterParams($filter, $startDate, $endDate)
{
    switch ($filter) {
        case 'weekly':
            return ['%Y-%u', '1 WEEK'];
        case 'monthly':
            return ['%Y-%m', '1 MONTH'];
        case 'yearly':
            return ['%Y', '1 YEAR'];
        case 'custom':
            return ['%Y-%m-%d', null];
        default:
            return ['%Y-%m-%d', '1 DAY'];
    }
}

list($dateFormat, $interval) = getDateFilterParams($timeFilter, $startDate, $endDate);

function buildWhereClause($timeFilter, $startDate, $endDate, $dateField)
{
    if ($timeFilter === 'custom' && $startDate && $endDate) {
        return " WHERE $dateField BETWEEN '$startDate' AND '$endDate'";
    }
    return '';
}

$whereClause = buildWhereClause($timeFilter, $startDate, $endDate, 'production_date');
$result = $conn->query("SELECT SUM(stock_quantity) AS total_stock FROM egg_inventory" . $whereClause);
$totalStock = ($row = $result->fetch_assoc()) ? $row['total_stock'] : 0;

$whereClause = buildWhereClause($timeFilter, $startDate, $endDate, 'production_date');
$lowStockWhere = " WHERE stock_quantity < 50";
if ($whereClause) {
    $lowStockWhere = str_replace('WHERE', 'AND', $whereClause);
    $lowStockWhere = " WHERE stock_quantity < 50" . $lowStockWhere;
}
$result = $conn->query("SELECT COUNT(*) AS low_stock_count FROM egg_inventory" . $lowStockWhere);
$lowStockCount = ($row = $result->fetch_assoc()) ? $row['low_stock_count'] : 0;

$whereClause = buildWhereClause($timeFilter, $startDate, $endDate, 'return_date');
$result = $conn->query("SELECT SUM(quantity) AS total_returns FROM return_stocks" . $whereClause);
$totalReturns = ($row = $result->fetch_assoc()) ? $row['total_returns'] : 0;

$inventoryData = [];
$whereClause = buildWhereClause($timeFilter, $startDate, $endDate, 'production_date');
$result = $conn->query("SELECT DATE_FORMAT(production_date, '$dateFormat') AS date, SUM(stock_quantity) AS total FROM egg_inventory" . $whereClause . " GROUP BY date ORDER BY date ASC");
while ($row = $result->fetch_assoc()) {
    $inventoryData[] = $row;
}

$expenseData = [];
$whereClause = buildWhereClause($timeFilter, $startDate, $endDate, 'date');
$result = $conn->query("SELECT DATE_FORMAT(date, '$dateFormat') AS day, SUM(amount) AS total FROM expenses" . $whereClause . " GROUP BY day ORDER BY day ASC");
while ($row = $result->fetch_assoc()) {
    $expenseData[] = $row;
}

$salesData = [];
$whereClause = buildWhereClause($timeFilter, $startDate, $endDate, 'sale_date');
$result = $conn->query("SELECT DATE_FORMAT(sale_date, '$dateFormat') AS day, SUM(quantity * price) AS revenue FROM sales" . $whereClause . " GROUP BY day ORDER BY day ASC");
while ($row = $result->fetch_assoc()) {
    $salesData[] = $row;
}

$stockByType = [];
$whereClause = buildWhereClause($timeFilter, $startDate, $endDate, 'production_date');
$result = $conn->query("SELECT egg_type, SUM(stock_quantity) AS total FROM egg_inventory" . $whereClause . " GROUP BY egg_type");
while ($row = $result->fetch_assoc()) {
    $stockByType[] = $row;
}

$productionTrends = [];
$whereClause = buildWhereClause($timeFilter, $startDate, $endDate, 'production_date');
$result = $conn->query("SELECT DATE_FORMAT(production_date, '$dateFormat') AS date, SUM(stock_quantity) AS total_production FROM egg_inventory" . $whereClause . " GROUP BY date ORDER BY date ASC");
while ($row = $result->fetch_assoc()) {
    $productionTrends[] = $row;
}

$revenueAnalysis = [];
$whereClause = buildWhereClause($timeFilter, $startDate, $endDate, 'sale_date');
$result = $conn->query("SELECT DATE_FORMAT(sale_date, '$dateFormat') AS date, SUM(quantity * price) AS total_revenue FROM sales" . $whereClause . " GROUP BY date ORDER BY date ASC");
while ($row = $result->fetch_assoc()) {
    $revenueAnalysis[] = $row;
}