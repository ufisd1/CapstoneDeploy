function deleteAdmin(adminId, adminName) {
  document.getElementById("deleteAdminId").value = adminId;
  document.getElementById("deleteAdminName").textContent = adminName;

  var deleteModal = new bootstrap.Modal(
    document.getElementById("deleteAdminModal")
  );
  deleteModal.show();
}

document.addEventListener("DOMContentLoaded", function () {
  const alerts = document.querySelectorAll(".alert");
  alerts.forEach(function (alert) {
    setTimeout(function () {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }, 5000);
  });
});
