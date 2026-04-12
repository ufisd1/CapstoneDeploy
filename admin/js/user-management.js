$(document).ready(function () {
  $("#addUserForm").on("submit", function (e) {
    e.preventDefault();

    var form = $(this);
    var errorContainer = $("#errorContainer");
    errorContainer.addClass("d-none").empty();

    var submitBtn = form.find('button[type="submit"]');
    submitBtn.prop("disabled", true).text("Adding...");

    $.ajax({
      url: "./backend/add_users.php",
      type: "POST",
      data: form.serialize(),
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          $("#addUserModal").modal("hide");
          alert(response.message);
          window.location.reload();
        } else {
          errorContainer.removeClass("d-none").text(response.message);
        }
      },
      error: function (xhr, status, error) {
        console.log("XHR Response:", xhr.responseText);
        try {
          var response = JSON.parse(xhr.responseText);
          errorContainer
            .removeClass("d-none")
            .text(response.message || "An error occurred");
        } catch (e) {
          errorContainer
            .removeClass("d-none")
            .text("An error occurred: " + xhr.responseText.substring(0, 100));
        }
      },
      complete: function () {
        submitBtn.prop("disabled", false).text("Add User");
      },
    });
  });

  $("#deleteUserForm").on("submit", function (e) {
    e.preventDefault();

    var form = $(this);
    var formData = form.serialize();

    console.log("Delete form data:", formData);

    var submitBtn = form.find('button[type="submit"]');
    submitBtn.prop("disabled", true).text("Deleting...");

    $.ajax({
      url: "./backend/add_users.php",
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        console.log("Delete response:", response);
        if (response.status === "success") {
          $("#deleteUserModal").modal("hide");
          alert(response.message);
          window.location.reload();
        } else {
          alert(response.message);
        }
      },
      error: function (xhr, status, error) {
        console.log("Delete error - XHR Response:", xhr.responseText);
        try {
          var response = JSON.parse(xhr.responseText);
          alert(response.message || "An error occurred");
        } catch (e) {
          alert("An error occurred: " + xhr.responseText.substring(0, 100));
        }
      },
      complete: function () {
        submitBtn.prop("disabled", false).text("Delete");
      },
    });
  });

  $("#addUserModal").on("hidden.bs.modal", function () {
    $("#errorContainer").addClass("d-none").empty();
    $(this).find("form")[0].reset();
  });
});

function deleteUser(userId) {
  console.log("Delete user called with ID:", userId);
  document.getElementById("deleteUserId").value = userId;
  var deleteModal = new bootstrap.Modal(
    document.getElementById("deleteUserModal")
  );
  deleteModal.show();
}