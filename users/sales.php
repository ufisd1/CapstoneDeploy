<?php
include 'backend/auth.php';
include 'backend/conn.php';
include 'backend/sales-record.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/sales.css">
    <link rel="stylesheet" href="../assets/css/users/sidebar.css">

</head>

<body>
    <?php include '../includes/navbar.php'; ?>
    <?php include '../includes/signout_modal.php'; ?>
    <?php include '../includes/profile.php'; ?>

  <main class="main-content">
        <div class="container-fluid">
            <h2 class="my-4" style="margin-top: 30px;">Sales</h2>

            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card summary-card">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted">Total Sales</h6>
                            <h3 class="summary-value text-primary"><?= $totalSalesData['total_sales'] ?? 0 ?></h3>
                            <i class="fas fa-shopping-cart fa-2x text-primary mt-2"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card summary-card">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted">Total Revenue</h6>
                            <h3 class="summary-value text-success">₱<?= number_format($totalSalesData['total_revenue'] ?? 0, 2) ?></h3>
                            <i class="fas fa-money-bill-wave fa-2x text-success mt-2"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card summary-card">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted">Avg. Sale Value</h6>
                            <h3 class="summary-value text-info">₱<?= number_format($avgSaleData['avg_sale'] ?? 0, 2) ?></h3>
                            <i class="fas fa-chart-line fa-2x text-info mt-2"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sales Records</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#salesModal">
                        <i class="fas fa-plus me-2"></i>Add Sales Record
                    </button>
                </div>
                
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $counter = ($page - 1) * $records_per_page + 1;
                                if (!empty($sales_data)) {
                                    foreach ($sales_data as $row) {
                                        // Try different possible column names
                                        $product_name = $row['product_name'] ?? $row['product'] ?? $row['name'] ?? 'N/A';
                                        $quantity = $row['quantity'] ?? $row['qty'] ?? '0';
                                        $price = $row['price'] ?? $row['unit_price'] ?? 0;
                                        $total = $row['total'] ?? $row['total_amount'] ?? 0;
                                        $sale_date = $row['sale_date'] ?? $row['date'] ?? $row['created_at'] ?? null;
                                        $id = $row['id'] ?? $row['sale_id'] ?? '';
                                        
                                        // Format date
                                        $formatted_date = 'N/A';
                                        if ($sale_date) {
                                            try {
                                                $formatted_date = date('M d, Y', strtotime($sale_date));
                                            } catch (Exception $e) {
                                                $formatted_date = $sale_date;
                                            }
                                        }
                                        
                                        echo "<tr>
                                            <td>$counter</td>
                                            <td>" . htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8') . "</td>
                                            <td>" . htmlspecialchars($quantity, ENT_QUOTES, 'UTF-8') . "</td>
                                            <td>₱" . number_format($price, 2) . "</td>
                                            <td>₱" . number_format($total, 2) . "</td>
                                            <td>$formatted_date</td>
                                            <td>
                                                <button class='btn btn-sm btn-outline-warning editSale' data-bs-toggle='modal' data-bs-target='#editSalesModal' 
                                                    data-id='" . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "' 
                                                    data-product='" . htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8') . "' 
                                                    data-quantity='" . htmlspecialchars($quantity, ENT_QUOTES, 'UTF-8') . "' 
                                                    data-price='$price' 
                                                    data-total='$total' 
                                                    data-date='" . htmlspecialchars($sale_date, ENT_QUOTES, 'UTF-8') . "'>
                                                    <i class='fas fa-edit'></i>
                                                </button>
                                                <button class='btn btn-sm btn-outline-danger ms-1' onclick='deleteSale($id)'>
                                                    <i class='fas fa-trash'></i>
                                                </button>
                                            </td>
                                        </tr>";
                                        $counter++;
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center py-4'>No sales records found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                   
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= max(1, $page - 1) ?>">Previous</a>
                            </li>
                            <?php 
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $page + 2);
                            
                            if ($start > 1): ?>
                                <li class="page-item"><a class="page-link" href="?page=1">1</a></li>
                                <?php if ($start > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($end < $total_pages): ?>
                                <?php if ($end < $total_pages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item"><a class="page-link" href="?page=<?= $total_pages ?>"><?= $total_pages ?></a></li>
                            <?php endif; ?>
                            
                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= min($total_pages, $page + 1) ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="salesModal" tabindex="-1" aria-labelledby="salesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salesModalLabel">Add Sales Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="salesForm" method="POST" action="backend/sales.php">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="productName" name="product" required>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="add_sales">Save</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSalesModal" tabindex="-1" aria-labelledby="editSalesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSalesModalLabel">Edit Sales Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editSalesForm" method="POST" action="backend/sales.php">
                        <input type="hidden" id="editTransactionId" name="transaction_id">
                        <div class="mb-3">
                            <label for="editProduct" class="form-label">Product</label>
                            <input type="text" class="form-control" id="editProduct" name="product" required>
                        </div>
                        <div class="mb-3">
                            <label for="editQuantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="editQuantity" name="quantity" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPrice" class="form-label">Price</label>
                            <input type="number" class="form-control" id="editPrice" name="price" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="editDate" name="date" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="update_sale">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

     <div class="modal fade" id="deleteSaleModal" tabindex="-1" aria-labelledby="deleteSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSaleModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this sale record? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form id="deleteSaleForm" method="POST" action="backend/sales.php">
                    <input type="hidden" name="sale_id" id="deleteSaleId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" name="delete_sale">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/sales.js"></script>
    <script src="../assets/js/sidebar.js"></script>
</body>

</html>