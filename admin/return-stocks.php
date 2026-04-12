<?php
include 'backend/auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Return Stocks</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
</head>

<body>
  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/signout_modal.php'; ?>
  <?php include 'backend/conn.php'; ?>
   <?php include 'includes/profile.php'; ?>

  <main class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center mb-3">
      <h1 style="margin-top: 45px;">Return Stocks</h1>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#returnStockModal" style="margin-top: 50px;">
          <i class="fas fa-plus me-2"></i> Add Return Stock
        </button>
      </div>
    </div>

    <div class="card">
      <div class="table-responsive inventory-table">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>No.</th>
              <th>Egg Type</th>
              <th>Quantity</th>
              <th>Return Reason</th>
              <th>Return Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $records_per_page = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $records_per_page;

            $sql = "SELECT COUNT(*) AS total FROM return_stocks";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $total_records = $row['total'];
            $total_pages = ceil($total_records / $records_per_page);

            $sql = "SELECT * FROM return_stocks ORDER BY return_date DESC, id DESC LIMIT $offset, $records_per_page";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
              $count = $offset + 1;
              while ($row = $result->fetch_assoc()) {
                echo "<tr>
              <td>$count</td>
              <td>{$row['egg_type']}</td>
              <td>{$row['quantity']}</td>
              <td>{$row['return_reason']}</td>
              <td>{$row['return_date']}</td>
              <td>
                <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editReturnModal' data-id='{$row['id']}'>
                  <i class='fa fa-edit'></i> Edit
                </button>
                <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deleteReturnModal' data-id='{$row['id']}'>
                  <i class='fa fa-trash'></i> Delete
                </button>
              </td>
            </tr>";
                $count++;
              }
            } else {
              echo "<tr><td colspan='6'>No return stocks found</td></tr>";
            }
            ?>
          </tbody>
        </table>

        <div class="d-flex justify-content-center p-3">
          <nav aria-label="Page navigation">
            <ul class="pagination">
              <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
              <?php else: ?>
                <li class="page-item disabled"><span class="page-link">Previous</span></li>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                  <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
              <?php endfor; ?>

              <?php if ($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
              <?php else: ?>
                <li class="page-item disabled"><span class="page-link">Next</span></li>
              <?php endif; ?>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </main>

  <div class="modal fade" id="returnStockModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="backend/return-stocks.php">
          <div class="modal-header">
            <h5 class="modal-title">Add Return Stock</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Egg Type</label>
              <select class="form-select" name="egg_type" required>
                <option value="Organic">Organic</option>
                <option value="Free-Range">Free-Range</option>
                <option value="Regular">Regular</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Quantity</label>
              <input type="number" class="form-control" name="quantity" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Return Reason</label>
              <textarea class="form-control" name="return_reason" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success" name="add_return_stock">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editReturnModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="backend/return-stocks.php">
          <div class="modal-header">
            <h5 class="modal-title">Edit Return Stock</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="editId" name="id">
            <div class="mb-3">
              <label class="form-label">Egg Type</label>
              <select class="form-select" id="editEggType" name="egg_type" required>
                <option value="Organic">Organic</option>
                <option value="Free-Range">Free-Range</option>
                <option value="Regular">Regular</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Quantity</label>
              <input type="number" class="form-control" id="editQuantity" name="quantity" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Return Reason</label>
              <textarea class="form-control" id="editReturnReason" name="return_reason" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary" name="edit_return_stock">Save Changes</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="deleteReturnModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="backend/return-stocks.php">
          <div class="modal-header">
            <h5 class="modal-title">Delete Confirmation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="delete_return_stock_id" id="deleteReturnId">
            <p>Are you sure you want to delete this return stock?</p>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-danger" name="delete_return_stock">Delete</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/return-stocks.js"></script>
</body>

</html>