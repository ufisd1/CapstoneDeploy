<?php
session_start();
include 'backend/conn.php';
include 'backend/analytics-results.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/analytics.css">

</head>

<body>
    <?php include 'includes/navbar.php' ?>
    <?php include 'includes/signout_modal.php' ?>
     <?php include 'includes/profile.php'; ?>

    <main class="main-content">
        <div class="container-fluid">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <h2 class="mb-2 mb-md-0" style="margin-top: 40px; margin-bottom: -30px;">Analytics</h2>

                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2 gap-md-3">
                    <div class="btn-group" id="btn-filter">
                        <a href="?time_filter=daily" class="btn btn-sm btn-outline-primary <?php echo $timeFilter == 'daily' ? 'active' : ''; ?>">Daily</a>
                        <a href="?time_filter=weekly" class="btn btn-sm btn-outline-primary <?php echo $timeFilter == 'weekly' ? 'active' : ''; ?>">Weekly</a>
                        <a href="?time_filter=monthly" class="btn btn-sm btn-outline-primary <?php echo $timeFilter == 'monthly' ? 'active' : ''; ?>">Monthly</a>
                        <a href="?time_filter=yearly" class="btn btn-sm btn-outline-primary <?php echo $timeFilter == 'yearly' ? 'active' : ''; ?>">Yearly</a>
                        <a href="?time_filter=custom" class="btn btn-sm btn-outline-primary <?php echo $timeFilter == 'custom' ? 'active' : ''; ?>">Custom</a>
                    </div>

                    <?php if ($timeFilter == 'custom'): ?>
                        <div class="date-range-container d-flex gap-2 mt-2 mt-md-0">
                            <input type="text"  class="form-control form-control-sm date-range-input" id="dateRangePicker" value="<?php echo $startDate && $endDate ? $startDate . ' - ' . $endDate : ''; ?>">
                            <button class="btn btn-sm btn-primary"  id="applyDateRange">Apply</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>


            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card kpi-card">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <h5>Total Stock</h5>
                            <?php if ($totalStock > 0): ?>
                                <p class="h3"><?php echo $totalStock; ?></p>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-box-open"></i>
                                    <span>No stock data available</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card kpi-card">
                        <div class="card-body text-center text-warning d-flex flex-column justify-content-center">
                            <h5>Low Stock Items</h5>
                            <?php if ($lowStockCount > 0): ?>
                                <p class="h3"><?php echo $lowStockCount; ?></p>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-check-circle"></i>
                                    <span>No low stock items</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card kpi-card">
                        <div class="card-body text-center text-danger d-flex flex-column justify-content-center">
                            <h5>Return Stocks</h5>
                            <?php if ($totalReturns > 0): ?>
                                <p class="h3"><?php echo $totalReturns; ?></p>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-undo"></i>
                                    <span>No returned stocks</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card chart-card">
                        <div class="card-body">
                            <h5 class="text-center">Sales vs Expenses (<?php echo ucfirst($timeFilter); ?>)</h5>
                            <div class="chart-container">
                                <canvas id="dailySalesExpensesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card chart-card">
                        <div class="card-body">
                            <h5 class="text-center">Revenue Analysis (<?php echo ucfirst($timeFilter); ?>)</h5>
                            <div class="chart-container">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card chart-card">
                        <div class="card-body">
                            <h5 class="text-center">Production Trends (<?php echo ucfirst($timeFilter); ?>)</h5>
                            <div class="chart-container">
                                <canvas id="productionTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card small-chart-card">
                        <div class="card-body">
                            <h5 class="text-center mb-3">Stock by Egg Type</h5>
                            <div class="chart-container">
                                <canvas id="stockByTypeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>

    <script>
        const chartData = {
            timeFilter: '<?php echo $timeFilter; ?>',
            salesData: <?php echo json_encode($salesData); ?>,
            expenseData: <?php echo json_encode($expenseData); ?>,
            productionTrends: <?php echo json_encode($productionTrends); ?>,
            revenueAnalysis: <?php echo json_encode($revenueAnalysis); ?>,
            stockByType: <?php echo json_encode($stockByType); ?>
        };

        $(document).ready(function() {
            $('#dateRangePicker').daterangepicker({
                opens: 'right',
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            });

            $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });

            $('#applyDateRange').click(function() {
                const dateRange = $('#dateRangePicker').val();
                if (dateRange) {
                    const dates = dateRange.split(' - ');
                    window.location.href = `?time_filter=custom&start_date=${dates[0]}&end_date=${dates[1]}`;
                }
            });
        });
    </script>

    <script src="js/analytics-charts.js"></script>
</body>

</html>