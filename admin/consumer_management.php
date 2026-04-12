<?php include 'backend/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consumer Management</title>
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
      <h1 style="margin-top: 45px;">Consumer Management</h1>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#supplierModal" style="margin-top: 45px;">
          <i class="fas fa-user-plus me-2"></i> Add Consumer
        </button>
      </div>
    </div>

    <div class="card">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th colspan="8">
                <div class="input-group input-group-sm mb-0">
                  <span class="input-group-text">
                    <i class="fas fa-search"></i>
                  </span>
                  <input type="text" class="form-control" placeholder="Search consumers..." id="supplierSearch">
                </div>

              </th>
            </tr>
            <tr>
              <th>No.</th>
              <th>Name</th>
              <th>Contact Number</th>
              <th>Email</th>
              <th>Address</th>
              <th>Last Purchase</th>
              <th>Next Delivery</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $records_per_page = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $records_per_page;

            $sql = "SELECT COUNT(*) AS total FROM consumers";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $total_records = $row['total'];
            $total_pages = ceil($total_records / $records_per_page);

            $sql = "SELECT * FROM consumers ORDER BY supplier_id DESC LIMIT $offset, $records_per_page";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
              $count = ($page - 1) * $records_per_page + 1;
              while ($row = $result->fetch_assoc()) {
                echo "<tr>
                  <td>$count</td>
                  <td>{$row['supplier_name']}</td>
                  <td>{$row['contact_number']}</td>
                  <td>{$row['email']}</td>
                  <td>{$row['address']}</td>
                  <td>{$row['last_purchase']}</td>
                  <td>{$row['next_delivery']}</td>
                  <td>
                    <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editSupplierModal' data-id='{$row['supplier_id']}'>
                      <i class='fa fa-edit'></i>
                    </button>
                    <button class='btn btn-danger btn-sm btn-delete-supplier' data-id='{$row['supplier_id']}'>
                      <i class='fa fa-trash'></i>
                    </button>
                  </td>
                </tr>";
                $count++;
              }
            } else {
              echo "<tr><td colspan='8'>No suppliers found</td></tr>";
            }
            ?>
          </tbody>
        </table>

        <div class="d-flex justify-content-center p-3">
          <nav aria-label="Page navigation">
            <ul class="pagination">

              <?php if ($page > 1): ?>
                <li class="page-item">
                  <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                </li>
              <?php else: ?>
                <li class="page-item disabled">
                  <span class="page-link">Previous</span>
                </li>
              <?php endif; ?>

              <?php

              $start_page = max(1, $page - 2);
              $end_page = min($total_pages, $page + 2);

              if ($start_page > 1): ?>
                <li class="page-item">
                  <a class="page-link" href="?page=1">1</a>
                </li>
                <?php if ($start_page > 2): ?>
                  <li class="page-item disabled">
                    <span class="page-link">...</span>
                  </li>
                <?php endif; ?>
              <?php endif; ?>

              <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                <?php if ($i == $page): ?>
                  <li class="page-item active">
                    <span class="page-link"><?php echo $i; ?></span>
                  </li>
                <?php else: ?>
                  <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                  </li>
                <?php endif; ?>
              <?php endfor; ?>

              <?php if ($end_page < $total_pages): ?>
                <?php if ($end_page < $total_pages - 1): ?>
                  <li class="page-item disabled">
                    <span class="page-link">...</span>
                  </li>
                <?php endif; ?>
                <li class="page-item">
                  <a class="page-link" href="?page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a>
                </li>
              <?php endif; ?>

              <?php if ($page < $total_pages): ?>
                <li class="page-item">
                  <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                </li>
              <?php else: ?>
                <li class="page-item disabled">
                  <span class="page-link">Next</span>
                </li>
              <?php endif; ?>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="supplierModalLabel">Add Consumer</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="supplierForm" method="POST" action="backend/consumer.php">
              <div class="mb-3">
                <label for="supplierName" class="form-label">Name</label>
                <input type="text" class="form-control" id="supplierName" name="supplier_name" required>
              </div>
              <div class="mb-3">
                <label for="contactNumber" class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="contactNumber" name="contact_number" required>
              </div>
              <div class="mb-3">
                <label for="supplierEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="supplierEmail" name="email" required>
              </div>
              <div class="mb-3">
                <label for="supplierAddress" class="form-label">Address</label>
                <input type="text" class="form-control" id="supplierAddress" name="address" required>
              </div>
              <div class="mb-3">
                <label for="lastPurchase" class="form-label">Last Purchase Date</label>
                <input type="date" class="form-control" id="lastPurchase" name="last_purchase">
              </div>
              <div class="mb-3">
                <label for="nextDelivery" class="form-label">Next Delivery Date</label>
                <input type="date" class="form-control" id="nextDelivery" name="next_delivery">
              </div>
              <div class="mb-3">
                <label for="restockReminder" class="form-label">Restock Reminder Date</label>
                <input type="date" class="form-control" id="restockReminder" name="restock_reminder">
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success" name="add_supplier">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form id="editSupplierForm" method="POST" action="backend/consumer.php">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editSupplierModalLabel">Edit Consumer</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" id="editSupplierId" name="supplier_id">
              <div class="mb-3">
                <label for="editSupplierName" class="form-label">Name</label>
                <input type="text" class="form-control" id="editSupplierName" name="supplier_name">
              </div>
              <div class="mb-3">
                <label for="editContactNumber" class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="editContactNumber" name="contact_number">
              </div>
              <div class="mb-3">
                <label for="editSupplierEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="editSupplierEmail" name="email">
              </div>
              <div class="mb-3">
                <label for="editSupplierAddress" class="form-label">Address</label>
                <input type="text" class="form-control" id="editSupplierAddress" name="address">
              </div>
              <div class="mb-3">
                <label for="editLastPurchase" class="form-label">Last Purchase Date</label>
                <input type="date" class="form-control" id="editLastPurchase" name="last_purchase">
              </div>
              <div class="mb-3">
                <label for="editNextDelivery" class="form-label">Next Delivery Date</label>
                <input type="date" class="form-control" id="editNextDelivery" name="next_delivery">
              </div>
              <div class="mb-3">
                <label for="editRestockReminder" class="form-label">Restock Reminder Date</label>
                <input type="date" class="form-control" id="editRestockReminder" name="restock_reminder">
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success" name="update_supplier">Save Changes</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="modal fade" id="deleteSupplierModal" tabindex="-1" aria-labelledby="deleteSupplierModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form id="deleteSupplierForm" method="POST" action="backend/consumer.php">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="deleteSupplierModalLabel">Confirm Deletion</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p>Are you sure you want to delete this consumer?</p>
              <input type="hidden" name="supplier_id" id="deleteSupplierId">
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-danger" name="delete_supplier">Delete</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/consumer_management.js"></script>
</body>

</html>