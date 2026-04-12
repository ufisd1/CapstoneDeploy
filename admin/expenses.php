<?php
include 'backend/auth.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/expenses.css">
</head>

<body>
    <?php include 'includes/navbar.php' ?>
    <?php include 'includes/signout_modal.php' ?>
    <?php include 'includes/profile.php'; ?>

    <main class="main-content">
        <div class="page-header mb-3">
            <div class="row">
                <div class="col-md-6">
                    <h2 style="margin-top: 45px;">Expenses</h2>
                </div>
                <div class="col-md-6">
                    <div class="text-end">
                        <button class="btn btn-sm" style="background-color: #28a745; color: white;" id="showTotalsBtn">
                            <i class="fas fa-calculator me-1"></i> Show Totals
                        </button>
                        <button class="btn btn-sm w-auto" style="background-color: #0d6efd; color: white;" type="button" data-bs-toggle="modal" data-bs-target="#expenseModal">
                            <i class="fas fa-file-export me-1"></i> Expense
                        </button>
                        <button class="btn btn-sm w-auto" style="background-color: #0d6efd; color: white;" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                            <i class="fas fa-plus me-1"></i> Add Expense
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-3 mb-3" id="totalsCard" style="display: none;">
            <div class="row">
                <div class="col-md-6">
                    <div class="total-summary">
                        <span>Total Expenses: </span>
                        <span class="total-amount" id="grandTotal">₱0.00</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="date-range-total">
                        <div class="input-group">
                            <input type="text" class="form-control" id="totalDateRange" placeholder="Select date range">
                            <span class="input-group-text">Total: </span>
                            <span class="input-group-text total-amount" id="rangeTotal">₱0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header" style="background-color: #FFC107; color: #212529;">
                        <i class="fas fa-calendar me-2"></i> Monthly Expenses
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Month</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include 'backend/conn.php';
                                    $monthly_query = "SELECT DATE_FORMAT(date, '%Y-%m') as month_year, DATE_FORMAT(date, '%b %Y') as month_name, SUM(amount) as total FROM expenses GROUP BY month_year ORDER BY month_year DESC LIMIT 6";
                                    $monthly_result = $conn->query($monthly_query);
                                    while ($month = $monthly_result->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td><?php echo $month['month_name']; ?></td>
                                            <td>₱<?php echo number_format($month['total'], 2); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header" style="background-color: #FFC107; color: #212529;">
                        <i class="fas fa-calendar-alt me-2"></i> Annual Expenses
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Year</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $annual_query = "SELECT YEAR(date) as year, SUM(amount) as total FROM expenses GROUP BY year ORDER BY year DESC";
                                    $annual_result = $conn->query($annual_query);
                                    while ($year = $annual_result->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td><?php echo $year['year']; ?></td>
                                            <td>₱<?php echo number_format($year['total'], 2); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-3">
            <div class="table-responsive">
                <table class="table table-bordered expenses-table">
                    <thead class="table-light">
                        <tr>
                            <th colspan="6">
                                <div class="row g-2">
                                    <div class="col-md-12">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" class="form-control form-control-sm" placeholder="Search expenses or select date range..." id="combinedSearch">
                                            <button class="btn btn-outline-secondary" type="button" id="clearSearch" style="display: none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th>No.</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $limit = 10;
                        $page = isset($_GET['page']) ? $_GET['page'] : 1;
                        $offset = ($page - 1) * $limit;
                        $sql = "SELECT * FROM expenses ORDER BY date DESC LIMIT $offset, $limit";
                        $result = $conn->query($sql);
                        $row_number = $offset + 1;
                        while ($row = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?php echo $row_number++; ?></td>
                                <td><?php echo $row['date']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><?php echo $row['description']; ?></td>
                                <td>₱<?php echo number_format($row['amount'], 2); ?></td>
                                <td>
                                    <button class="btn btn-link text-primary p-0 border-0 edit-expense"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-date="<?php echo $row['date']; ?>"
                                        data-category="<?php echo htmlspecialchars($row['category']); ?>"
                                        data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                        data-amount="<?php echo $row['amount']; ?>">
                                        <i class="fas fa-edit action-icon"></i>
                                    </button>
                                    <button class="btn btn-link text-danger p-0 border-0 delete-expense-btn" data-id="<?php echo $row['id']; ?>" data-bs-toggle="modal" data-bs-target="#deleteExpenseModal">
                                        <i class="fas fa-trash action-icon delete-icon"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php
                        $total_records = $conn->query("SELECT COUNT(*) as count FROM expenses")->fetch_assoc()['count'];
                        $total_pages = ceil($total_records / $limit);
                        echo '<li class="page-item' . ($page == 1 ? ' disabled' : '') . '"><a class="page-link" href="?page=' . ($page - 1) . '"' . ($page == 1 ? ' tabindex="-1" aria-disabled="true"' : '') . '>Previous</a></li>';
                        for ($i = 1; $i <= $total_pages; $i++) {
                            echo '<li class="page-item' . ($i == $page ? ' active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                        }
                        echo '<li class="page-item' . ($page == $total_pages ? ' disabled' : '') . '"><a class="page-link" href="?page=' . ($page + 1) . '"' . ($page == $total_pages ? ' tabindex="-1" aria-disabled="true"' : '') . '>Next</a></li>';
                        ?>
                    </ul>
                </nav>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="backend/expenses.php" method="POST" id="addExpenseForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="add_date" class="form-label">Date</label>
                            <input type="text" class="form-control" name="date" id="add_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_category" class="form-label">Category</label>
                            <input type="text" class="form-control" name="category" id="add_category" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="add_description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="add_amount" class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control" name="amount" id="add_amount" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_expense" class="btn btn-primary">Add Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="backend/expenses.php" method="POST" id="editExpenseForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="expense_id" id="edit_expense_id">
                        <div class="mb-3">
                            <label for="edit_date" class="form-label">Date</label>
                            <input type="text" class="form-control" name="date" id="edit_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_category" class="form-label">Category</label>
                            <input type="text" class="form-control" name="category" id="edit_category" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_amount" class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control" name="amount" id="edit_amount" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update_expense" class="btn btn-primary">Update Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteExpenseModal" tabindex="-1" aria-labelledby="deleteExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="backend/expenses.php" method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteExpenseModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this expense?
                    <input type="hidden" name="expense_id" id="delete_expense_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_expense" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="expenseModalLabel">Export Expense Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Period:</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="periodType" id="monthlyRadio" autocomplete="off" checked>
                            <label class="btn btn-outline-primary" for="monthlyRadio">Monthly</label>
                            <input type="radio" class="btn-check" name="periodType" id="annualRadio" autocomplete="off">
                            <label class="btn btn-outline-primary" for="annualRadio">Annual</label>
                        </div>
                    </div>
                    <div class="mb-3" id="datePickerContainer">
                        <label for="monthPicker" class="form-label">Select Month:</label>
                        <input type="month" class="form-control" id="monthPicker">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="exportButton" disabled>
                            Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" id="exportPdf">Export as PDF</a></li>
                            <li><a class="dropdown-item" href="#" id="exportCsv">Export as CSV</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="js/expenses.js"></script>
</body>

</html>