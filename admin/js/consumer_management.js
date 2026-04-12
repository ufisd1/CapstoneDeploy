document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("supplierSearch");
  const tableRows = document.querySelectorAll("table tbody tr");

  searchInput.addEventListener("input", function () {
    const searchTerm = this.value.toLowerCase();
    tableRows.forEach((row) => {
      const rowText = row.textContent.toLowerCase();
      row.style.display = rowText.includes(searchTerm) ? "" : "none";
    });
  });

  document.querySelectorAll(".btn-delete-supplier").forEach((button) => {
    button.addEventListener("click", function () {
      const supplierId = this.getAttribute("data-id");
      document.getElementById("deleteSupplierId").value = supplierId;
      const deleteModal = new bootstrap.Modal(
        document.getElementById("deleteSupplierModal")
      );
      deleteModal.show();
    });
  });

  document
    .querySelectorAll("[data-bs-target='#editSupplierModal']")
    .forEach((button) => {
      button.addEventListener("click", function () {
        const supplierId = this.getAttribute("data-id");
        fetch(`./backend/consumer.php?supplier_id=${supplierId}`)
          .then((response) => response.json())
          .then((data) => {
            if (data) {
              document.getElementById("editSupplierId").value =
                data.supplier_id;
              document.getElementById("editSupplierName").value =
                data.supplier_name;
              document.getElementById("editContactNumber").value =
                data.contact_number;
              document.getElementById("editSupplierEmail").value = data.email;
              document.getElementById("editSupplierAddress").value =
                data.address;
              document.getElementById("editLastPurchase").value =
                data.last_purchase;
              document.getElementById("editNextDelivery").value =
                data.next_delivery;
              document.getElementById("editRestockReminder").value =
                data.restock_reminder;
            }
          })
          .catch((error) => {
            console.error("Error fetching supplier data:", error);
          });
      });
    });
});
