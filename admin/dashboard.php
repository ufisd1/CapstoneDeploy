<?php
include 'backend/auth.php';
include 'backend/conn.php';
include 'backend/dashboard-results.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dashboard.css">

</head>

<body>
    <?php include 'includes/signout_modal.php'; ?>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/profile.php'; ?>

    <main class="main-content">
        <div class="container-fluid">
            <h2 class="my-4">Dashboard</h2>

            <div class="row">
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3 card-height">
                        <a href="inventory.php" class="card-header text-white" style="text-decoration: none;">
                            <i class="fas fa-egg me-2"></i>Total Eggs Produced
                        </a>
                        <div class="card-body card-body-fixed">
                            <h5 class="card-title"><?php echo number_format($totalProduced); ?></h5>
                            <p class="card-text">Total number of eggs produced.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3 card-height">
                        <a href="sales.php" class="card-header text-white" style="text-decoration: none;">
                            <i class="fas fa-shopping-cart me-2"></i>Eggs Sold
                        </a>
                        <div class="card-body card-body-fixed">
                            <h5 class="card-title"><?php echo number_format($totalSold); ?></h5>
                            <p class="card-text">Total eggs sold so far.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3 card-height">
                        <a href="inventory.php" class="card-header text-white" style="text-decoration: none;">
                            <i class="fas fa-boxes me-2"></i>Remaining Stock
                        </a>
                        <div class="card-body card-body-fixed">
                            <h5 class="card-title"><?php echo number_format($remainingStock); ?></h5>
                            <p class="card-text">Current stock available.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-white bg-info mb-3 card-height">
                        <a href="recent-activity.php" class="card-header text-white" style="text-decoration: none;">
                            <i class="fas fa-history me-2"></i>Recent Activity
                        </a>
                        <div class="card-body card-body-fixed">
                            <?php if (!empty($recentActivity)): ?>
                                <div class="activity-feed activity-scroll">
                                    <?php foreach ($recentActivity as $activity): ?>
                                        <div class="activity-item">
                                            <div class="activity-text text-light">
                                                <?php echo htmlspecialchars($activity['details']); ?>
                                            </div>
                                            <div class="activity-time">
                                                <?php echo $activity['timestamp']; ?> by <?php echo htmlspecialchars($activity['admin']); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-3">No recent activity found</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3 mt-3">
                        <div class="card-header">
                            <i class="fas fa-money-bill-wave me-2"></i>Cash Flow Analysis
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Total Revenue</h6>
                                            <h4 class="text-success">₱<?php echo number_format($totalRevenue, 2); ?></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Total Expenses</h6>
                                            <h4 class="text-danger">₱<?php echo number_format($totalExpenses, 2); ?></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Net Profit</h6>
                                            <h4 class="<?php echo $netCashFlow >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                ₱<?php echo number_format($netCashFlow, 2); ?>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($cashFlowData)): ?>
                                <canvas id="cashFlowChart"></canvas>
                            <?php else: ?>
                                <p>No cash flow data available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <i class="fas fa-chart-line me-2"></i>Production Trend
                        </div>
                        <div class="card-body">
                            <?php if (!empty($chartData)): ?>
                                <canvas id="productionChart"></canvas>
                            <?php else: ?>
                                <p>No production data available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <i class="fas fa-box-open me-2"></i>Inventory Overview
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Egg Type</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($inventoryData)) {
                                        foreach ($inventoryData as $item) {
                                            echo "<tr><td>{$item['egg_type']}</td><td>{$item['stock_quantity']}</td></tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='2'>No inventory available.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dashboard.js"></script>
</body>

</html>