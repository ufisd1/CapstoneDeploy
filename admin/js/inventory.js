document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".edit-btn").forEach((button) => {
    button.addEventListener("click", function () {
      const batchId = this.getAttribute("data-id");
      fetch(`./backend/inventory.php?id=${batchId}`)
        .then((response) => response.json())
        .then((data) => {
          document.getElementById("editBatchId").value = data.batch_id;
          document.getElementById("editEggType").value = data.egg_type;
          document.getElementById("editEggSize").value = data.size;
          document.getElementById("editEggQuality").value = data.quality;
          document.getElementById("editStockQuantity").value =
            data.stock_quantity;
          document.getElementById("editProductionDate").value =
            data.production_date.split(" ")[0];
          document.getElementById("editExpiryDate").value =
            data.expiry_date.split(" ")[0];
        })
        .catch((error) => console.error("Error:", error));
    });
  });

  document.querySelectorAll(".delete-btn").forEach((button) => {
    button.addEventListener("click", function () {
      const batchId = this.getAttribute("data-id");
      document.getElementById("deleteStockId").value = batchId;
      const deleteModal = new bootstrap.Modal(
        document.getElementById("deleteStockModal")
      );
      deleteModal.show();
    });
  });

  document.getElementById("productionDate").valueAsDate = new Date();

  const expiryDate = new Date();
  expiryDate.setDate(expiryDate.getDate() + 30);
  document.getElementById("expiryDate").valueAsDate = expiryDate;
});