const editModal = document.getElementById('editReturnModal');
  editModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');

    fetch('./backend/return-stocks.php?id=' + id)
      .then(response => response.json())
      .then(data => {
        document.getElementById('editId').value = data.id;
        document.getElementById('editEggType').value = data.egg_type;
        document.getElementById('editQuantity').value = data.quantity;
        document.getElementById('editReturnReason').value = data.return_reason;
      })
      .catch(error => console.error('Error:', error));
  });

  const deleteModal = document.getElementById('deleteReturnModal');
  deleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    document.getElementById('deleteReturnId').value = id;
  });