document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchInput");
  const table = document.querySelector(".table");

  searchInput.addEventListener("keyup", function () {
    const searchTerm = this.value.toLowerCase();
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
