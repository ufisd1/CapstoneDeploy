<?php 
include 'conn.php';

function debugLog($message) {
    file_put_contents('debug.txt', date("[Y-m-d H:i:s] ") . $message . PHP_EOL, FILE_APPEND);
}

try {
    $result = $conn->query("SELECT SUM(stock_quantity) AS total_produced FROM egg_inventory");
    if (!$result) throw new Exception("Failed to fetch total_produced: " . $conn->error);
    $totalProduced = ($row = $result->fetch_assoc()) ? $row['total_produced'] : 0;
    debugLog("Total Produced: $totalProduced");

    $result = $conn->query("SELECT SUM(quantity) AS total_sold FROM sales");
    if (!$result) throw new Exception("Failed to fetch total_sold: " . $conn->error);
    $totalSold = ($row = $result->fetch_assoc()) ? $row['total_sold'] : 0;
    debugLog("Total Sold: $totalSold");

    $remainingStock = $totalProduced - $totalSold;
    debugLog("Remaining Stock: $remainingStock");

    $chartData = [];
    $result = $conn->query("SELECT DATE_FORMAT(production_date, '%M %Y') AS month, SUM(stock_quantity) AS count FROM egg_inventory GROUP BY month ORDER BY production_date ASC");
    if (!$result) throw new Exception("Chart data query failed: " . $conn->error);
    while ($row = $result->fetch_assoc()) {
        $chartData[] = $row;
    }

    $inventoryData = [];
    $result = $conn->query("SELECT egg_type, stock_quantity FROM egg_inventory");
    if (!$result) throw new Exception("Inventory data query failed: " . $conn->error);
    while ($row = $result->fetch_assoc()) {
        $inventoryData[] = $row;
    }

    $recentActivity = [];
    $result = $conn->query("
        (SELECT 'Sold ' AS action, quantity AS amount, DATE_FORMAT(sale_date, '%M %d, %Y') AS date FROM sales ORDER BY sale_date DESC LIMIT 5)
        UNION ALL
        (SELECT 'Added ' AS action, stock_quantity AS amount, DATE_FORMAT(production_date, '%M %d, %Y') AS date FROM egg_inventory ORDER BY production_date DESC LIMIT 5)
        ORDER BY date DESC LIMIT 5
    ");
    if (!$result) throw new Exception("Recent activity query failed: " . $conn->error);
    while ($row = $result->fetch_assoc()) {
        $recentActivity[] = "{$row['action']}{$row['amount']} eggs on {$row['date']}";
    }

    $productTrendData = [];
    $result = $conn->query("
        SELECT DATE_FORMAT(sale_date, '%M %Y') AS month, SUM(quantity) AS total_sold 
        FROM sales 
        GROUP BY month 
        ORDER BY MIN(sale_date) ASC
    ");
    if (!$result) throw new Exception("Product trend query failed: " . $conn->error);
    while ($row = $result->fetch_assoc()) {
        $productTrendData[] = $row;
    }

    $totalRevenue = 0;
    $totalExpenses = 0;

    $salesTableCheck = $conn->query("SHOW TABLES LIKE 'sales'");
    if ($salesTableCheck && $salesTableCheck->num_rows > 0) {
        $salesStructureResult = $conn->query("DESCRIBE sales");
        if ($salesStructureResult) {
            $salesColumns = [];
            while ($row = $salesStructureResult->fetch_assoc()) {
                $salesColumns[] = $row['Field'];
            }

            if (in_array('total', $salesColumns)) {
                $revenueQuery = "SELECT SUM(total) AS total_revenue FROM sales";
            } else {
                $priceColumn = '';
                foreach (['price', 'unit_price', 'price_per_unit', 'price_each', 'rate'] as $col) {
                    if (in_array($col, $salesColumns)) {
                        $priceColumn = $col;
                        break;
                    }
                }
                $revenueQuery = $priceColumn 
                    ? "SELECT SUM(quantity * $priceColumn) AS total_revenue FROM sales" 
                    : "SELECT SUM(quantity) AS total_quantity FROM sales";
            }

            $revenueResult = $conn->query($revenueQuery);
            if ($revenueResult && $row = $revenueResult->fetch_assoc()) {
                $totalRevenue = $row['total_revenue'] ?? ($row['total_quantity'] ?? 0);
            }
        }
    }
    debugLog("Total Revenue: $totalRevenue");

    $expensesTableCheck = $conn->query("SHOW TABLES LIKE 'expenses'");
    if ($expensesTableCheck && $expensesTableCheck->num_rows > 0) {
        $expensesStructureResult = $conn->query("DESCRIBE expenses");
        if ($expensesStructureResult) {
            $expensesColumns = [];
            while ($row = $expensesStructureResult->fetch_assoc()) {
                $expensesColumns[] = $row['Field'];
            }

            $amountColumn = '';
            foreach (['amount', 'total', 'cost', 'expense_amount'] as $col) {
                if (in_array($col, $expensesColumns)) {
                    $amountColumn = $col;
                    break;
                }
            }

            if ($amountColumn) {
                $expensesResult = $conn->query("SELECT SUM($amountColumn) AS total_expenses FROM expenses");
                if ($expensesResult && $row = $expensesResult->fetch_assoc()) {
                    $totalExpenses = $row['total_expenses'] ?? 0;
                }
            }
        }
    }
    debugLog("Total Expenses: $totalExpenses");

    $netCashFlow = $totalRevenue - $totalExpenses;
    debugLog("Net Cash Flow: $netCashFlow");

    $cashFlowData = [];
    $result = $conn->query("
        SELECT 
            DATE_FORMAT(sale_date, '%M %Y') AS month, 
            SUM(quantity * price) AS sales_revenue, 
            (SELECT SUM(amount) FROM expenses WHERE DATE_FORMAT(date, '%M %Y') = DATE_FORMAT(sale_date, '%M %Y')) AS expenses
        FROM sales 
        GROUP BY month 
        ORDER BY MIN(sale_date) ASC
    ");
    if (!$result) throw new Exception("Final cash flow query failed: " . $conn->error);

    while ($row = $result->fetch_assoc()) {
        $row['net'] = $row['sales_revenue'] - $row['expenses'];
        $cashFlowData[] = $row;
    }
    debugLog("Cash flow data successfully generated.");

} catch (Exception $e) {
    debugLog("Exception caught: " . $e->getMessage());
    die("An error occurred. Check debug.txt for more info.");
}
?>
