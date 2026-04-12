document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchInput");
  const tables = document.querySelectorAll(".table");

  searchInput.addEventListener("keyup", function () {
    const searchTerm = this.value.toLowerCase();

    tables.forEach((table) => {
      const rows = table.querySelectorAll("tbody tr");
      let hasMatches = false;

      rows.forEach((row) => {
        const rowText = row.textContent.toLowerCase();
        if (rowText.includes(searchTerm)) {
          row.style.display = "";
          hasMatches = true;
        } else {
          row.style.display = "none";
        }
      });

      const noResults = table.parentElement.querySelector(".no-results");
      if (!hasMatches) {
        if (!noResults) {
          const message = document.createElement("div");
          message.className = "alert alert-warning no-results";
          message.textContent = "No matching activities found";
          table.parentElement.appendChild(message);
        }
      } else if (noResults) {
        noResults.remove();
      }
    });
  });

  document.querySelectorAll('[data-bs-toggle="tab"]').forEach((tab) => {
    tab.addEventListener("click", function () {
      searchInput.value = "";
      tables.forEach((table) => {
        const rows = table.querySelectorAll("tbody tr");
        rows.forEach((row) => {
          row.style.display = "";
        });
        const noResults = table.parentElement.querySelector(".no-results");
        if (noResults) noResults.remove();
      });
    });
  });
});