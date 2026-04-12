<?php
include 'conn.php';

$totalProduced = 0;
$totalSold = 0;
$remainingStock = 0;
$chartData = [];
$inventoryData = [];
$recentActivity = [];
$productTrendData = [];
$totalRevenue = 0;
$totalExpenses = 0;
$netCashFlow = 0;
$cashFlowData = [];

$result = $conn->query("SELECT SUM(stock_quantity) AS total_produced FROM egg_inventory");
$totalProduced = ($row = $result->fetch_assoc()) ? $row['total_produced'] : 0;

$result = $conn->query("SELECT SUM(quantity) AS total_sold FROM sales");
$totalSold = ($row = $result->fetch_assoc()) ? $row['total_sold'] : 0;

$remainingStock = $totalProduced - $totalSold;

$result = $conn->query("SELECT DATE_FORMAT(production_date, '%M %Y') AS month, SUM(stock_quantity) AS count FROM egg_inventory GROUP BY month ORDER BY production_date ASC");
while ($row = $result->fetch_assoc()) {
    $chartData[] = $row;
}

$result = $conn->query("SELECT egg_type, stock_quantity FROM egg_inventory");
while ($row = $result->fetch_assoc()) {
    $inventoryData[] = $row;
}

$result = $conn->query("
    SELECT 
        al.action, 
        al.table_name, 
        al.changes, 
        al.action_time, 
        u.full_name 
    FROM 
        activity_log al
    JOIN 
        users u ON al.user_id = u.id
    WHERE 
        al.action IN ('add', 'update')
        AND al.table_name IN ('sales', 'egg_inventory')
    ORDER BY 
        al.action_time DESC 
    LIMIT 5
");

while ($row = $result->fetch_assoc()) {
    $changes = json_decode($row['changes'], true);
    $action = $row['action'] == 'add' ? 'added' : 'updated';
    $table = $row['table_name'] == 'sales' ? 'sale' : 'inventory';
    
    $details = '';
    if ($row['action'] == 'add') {
        if ($row['table_name'] == 'sales') {
            $details = "Added sale of " . ($changes['product_name'] ?? 'N/A') . 
                      " (Qty: " . ($changes['quantity'] ?? 0) . 
                      ", Price: " . (isset($changes['price']) ? '₱' . number_format($changes['price'], 2) : 'N/A') . ")";
        } else {
            $details = "Added " . ($changes['stock_quantity'] ?? 0) . " " . ($changes['egg_type'] ?? 'N/A') . 
                      " eggs (" . ($changes['size'] ?? 'N/A') . ") to inventory";
        }
    } else {
        $changeDetails = [];
        foreach ($changes as $field => $newValue) {
            if ($field == 'price' || $field == 'total') {
                $newValue = '₱' . number_format($newValue, 2);
            }
            $changeDetails[] = "$field to $newValue";
        }
        $details = "Updated " . implode(', ', $changeDetails) . " in $table";
    }
    
    $timestamp = date('M d, Y h:i A', strtotime($row['action_time']));
    
    $recentActivity[] = [
        'user' => $row['full_name'],
        'action' => $action,
        'table' => $table,
        'details' => $details,
        'timestamp' => $timestamp
    ];
}

$result = $conn->query("
    SELECT DATE_FORMAT(sale_date, '%M %Y') AS month, SUM(quantity) AS total_sold 
    FROM sales 
    GROUP BY month 
    ORDER BY MIN(sale_date) ASC
");

while ($row = $result->fetch_assoc()) {
    $productTrendData[] = $row;
}

$salesTableCheck = $conn->query("SHOW TABLES LIKE 'sales'");
if ($salesTableCheck && $salesTableCheck->num_rows > 0) {
    $salesStructureQuery = "DESCRIBE sales";
    $salesStructureResult = $conn->query($salesStructureQuery);

    if ($salesStructureResult) {
        $salesColumns = [];
        while ($row = $salesStructureResult->fetch_assoc()) {
            $salesColumns[] = $row['Field'];
        }

        if (in_array('total', $salesColumns)) {
            $revenueQuery = "SELECT SUM(total) AS total_revenue FROM sales";
        } elseif (in_array('quantity', $salesColumns)) {
            $priceColumn = '';
            $possiblePriceColumns = ['price', 'unit_price', 'price_per_unit', 'price_each', 'rate'];
            foreach ($possiblePriceColumns as $col) {
                if (in_array($col, $salesColumns)) {
                    $priceColumn = $col;
                    break;
                }
            }

            if ($priceColumn) {
                $revenueQuery = "SELECT SUM(quantity * $priceColumn) AS total_revenue FROM sales";
            } else {
                $revenueQuery = "SELECT SUM(quantity) AS total_quantity FROM sales";
            }
        } else {
            $revenueQuery = "SELECT COUNT(*) AS sales_count FROM sales";
        }

        $revenueResult = $conn->query($revenueQuery);
        if ($revenueResult && $row = $revenueResult->fetch_assoc()) {
            $totalRevenue = isset($row['total_revenue']) ? $row['total_revenue'] : 
                          (isset($row['total_quantity']) ? $row['total_quantity'] : 
                          (isset($row['sales_count']) ? $row['sales_count'] : 0));
        }
    }
}

$expensesTableCheck = $conn->query("SHOW TABLES LIKE 'expenses'");
if ($expensesTableCheck && $expensesTableCheck->num_rows > 0) {
    $expensesStructureQuery = "DESCRIBE expenses";
    $expensesStructureResult = $conn->query($expensesStructureQuery);

    if ($expensesStructureResult) {
        $expensesColumns = [];
        while ($row = $expensesStructureResult->fetch_assoc()) {
            $expensesColumns[] = $row['Field'];
        }

        $amountColumn = '';
        $possibleAmountColumns = ['amount', 'total', 'cost', 'expense_amount'];
        foreach ($possibleAmountColumns as $col) {
            if (in_array($col, $expensesColumns)) {
                $amountColumn = $col;
                break;
            }
        }

        if ($amountColumn) {
            $expensesQuery = "SELECT SUM($amountColumn) AS total_expenses FROM expenses";
            $expensesResult = $conn->query($expensesQuery);

            if ($expensesResult && $row = $expensesResult->fetch_assoc()) {
                $totalExpenses = $row['total_expenses'] ?? 0;
            }
        }
    }
}

$netCashFlow = $totalRevenue - $totalExpenses;

$result = $conn->query("
    SELECT 
        DATE_FORMAT(sale_date, '%M %Y') AS month, 
        SUM(quantity * price) AS sales_revenue, 
        (SELECT SUM(amount) FROM expenses WHERE DATE_FORMAT(date, '%M %Y') = DATE_FORMAT(sale_date, '%M %Y')) AS expenses
    FROM sales 
    GROUP BY month 
    ORDER BY MIN(sale_date) ASC
");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['net'] = $row['sales_revenue'] - ($row['expenses'] ?? 0);
        $cashFlowData[] = $row;
    }
}