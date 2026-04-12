document.querySelectorAll(".editSale").forEach((button) => {
  button.addEventListener("click", function () {
    document.getElementById("editTransactionId").value = this.dataset.id;
    document.getElementById("editProduct").value = this.dataset.product;
    document.getElementById("editQuantity").value = this.dataset.quantity;
    document.getElementById("editPrice").value = this.dataset.price;
    document.getElementById("editTotal").value = this.dataset.total;
    document.getElementById("editDate").value = this.dataset.date;
  });
});

function deleteSale(saleId) {
    document.getElementById("deleteSaleId").value = saleId;

    var deleteModal = new bootstrap.Modal(
        document.getElementById("deleteSaleModal")
    );
    deleteModal.show();
}

document.getElementById('deleteSaleForm').addEventListener('submit', function(e) {
    const saleId = document.getElementById('deleteSaleId').value;
    if (!saleId || saleId.trim() === '') {
        e.preventDefault();
        alert('Error: Sale ID is missing. Please try again.');
        return false;
    }
});