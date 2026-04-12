<?php
include 'backend/auth.php';
include 'backend/conn.php';
include 'backend/egg_inventory.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Egg Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .pagination .page-item {
            margin: 0 5px;
        }

        .pagination .page-item.disabled .page-link {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <?php include 'includes/signout_modal.php'; ?>
    <?php include 'includes/navbar.php'; ?>
     <?php include 'includes/profile.php'; ?>

    <main class="main-content">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h1 style="margin-top: 40px;">Egg Inventory</h1>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Egg Stock</h2>
                <form method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eggInventoryModal">
                            <i class="fas fa-plus me-2"></i> Add Egg Stock
                        </button>
                    </div>
                </form>
            </div>
            <div class="table-responsive inventory-table">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Quality</th>
                            <th>Stock Quantity</th>
                            <th>Production Date</th>
                            <th>Expiry Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($inventoryResult->num_rows > 0) {
                            $row_number = $offset + 1;
                            while ($row = $inventoryResult->fetch_assoc()) {
                                $stock_alert = ($row['stock_quantity'] < 50) ? "text-danger fw-bold" : "";
                                $production_date = date('Y-m-d', strtotime($row['production_date']));
                                $expiry_date = date('Y-m-d', strtotime($row['expiry_date']));

                                echo "<tr>
                                    <td>{$row_number}</td>
                                    <td>{$row['egg_type']}</td>
                                    <td>{$row['size']}</td>
                                    <td>{$row['quality']}</td>
                                    <td class='$stock_alert'>{$row['stock_quantity']}</td>
                                    <td>{$production_date}</td>
                                    <td>{$expiry_date}</td>
                                    <td>
                                        <button class='btn btn-warning btn-sm edit-btn' data-bs-toggle='modal' data-bs-target='#editEggModal' data-id='{$row['batch_id']}'>
                                            <i class='fas fa-edit'></i> Edit
                                        </button>
                                        <button class='btn btn-danger btn-sm delete-btn' data-id='{$row['batch_id']}'>
                                            <i class='fas fa-trash'></i> Delete
                                        </button>
                                    </td>
                                </tr>";
                                $row_number++;
                            }
                        } else {
                            echo "<tr><td colspan='8'>No egg stock found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </main>

    <div class="modal fade" id="eggInventoryModal" tabindex="-1" aria-labelledby="eggInventoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eggInventoryModalLabel">Add Egg Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="eggInventoryForm" method="POST" action="backend/inventory.php">
                        <div class="mb-3">
                            <label for="eggType" class="form-label">Egg Type</label>
                            <select class="form-select" id="eggType" name="egg_type" required>
                                <option value="Organic">Organic</option>
                                <option value="Free-Range">Free-Range</option>
                                <option value="Regular">Regular</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="eggSize" class="form-label">Size</label>
                            <select class="form-select" id="eggSize" name="size" required>
                                <option value="Small">Small</option>
                                <option value="Medium">Medium</option>
                                <option value="Large">Large</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="eggQuality" class="form-label">Quality</label>
                            <select class="form-select" id="eggQuality" name="quality" required>
                                <option value="Grade A">Grade A</option>
                                <option value="Grade B">Grade B</option>
                                <option value="Grade C">Grade C</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="stockQuantity" class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" id="stockQuantity" name="stock_quantity" required min="1">
                        </div>
                        <div class="mb-3">
                            <label for="productionDate" class="form-label">Production Date</label>
                            <input type="date" class="form-control" id="productionDate" name="production_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="expiryDate" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="expiryDate" name="expiry_date" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" name="add_egg_stock">Save</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editEggModal" tabindex="-1" aria-labelledby="editEggModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEggModalLabel">Edit Egg Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editEggForm" method="POST" action="backend/inventory.php">
                        <input type="hidden" id="editBatchId" name="batch_id">
                        <div class="mb-3">
                            <label for="editEggType" class="form-label">Egg Type</label>
                            <select class="form-select" id="editEggType" name="egg_type" required>
                                <option value="Organic">Organic</option>
                                <option value="Free-Range">Free-Range</option>
                                <option value="Regular">Regular</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editEggSize" class="form-label">Size</label>
                            <select class="form-select" id="editEggSize" name="size" required>
                                <option value="Small">Small</option>
                                <option value="Medium">Medium</option>
                                <option value="Large">Large</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editEggQuality" class="form-label">Quality</label>
                            <select class="form-select" id="editEggQuality" name="quality" required>
                                <option value="Grade A">Grade A</option>
                                <option value="Grade B">Grade B</option>
                                <option value="Grade C">Grade C</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editStockQuantity" class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" id="editStockQuantity" name="stock_quantity" required min="1">
                        </div>
                        <div class="mb-3">
                            <label for="editProductionDate" class="form-label">Production Date</label>
                            <input type="date" class="form-control" id="editProductionDate" name="production_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="editExpiryDate" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="editExpiryDate" name="expiry_date" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" name="update_egg_stock">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteStockModal" tabindex="-1" aria-labelledby="deleteStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteStockModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this egg stock? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteStockForm" method="POST" action="backend/inventory.php">
                        <input type="hidden" name="batch_id" id="deleteStockId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="delete_egg_stock">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/inventory.js"></script>
</body>

</html>