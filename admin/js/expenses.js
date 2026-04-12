document.addEventListener("DOMContentLoaded", function () {
  flatpickr("#exportDateRange", { mode: "range", dateFormat: "Y-m-d" });
  flatpickr("#add_date", { dateFormat: "Y-m-d" });
  flatpickr("#edit_date", { dateFormat: "Y-m-d" });
  const combinedSearch = flatpickr("#combinedSearch", {
    mode: "range",
    dateFormat: "Y-m-d",
    allowInput: true,
    onClose: function () {
      filterExpenses();
    },
  });
  const totalDateRange = flatpickr("#totalDateRange", {
    mode: "range",
    dateFormat: "Y-m-d",
    onChange: function (selectedDates) {
      calculateRangeTotal(selectedDates);
    },
  });
  const searchInput = document.getElementById("combinedSearch");
  const clearSearchBtn = document.getElementById("clearSearch");
  const showTotalsBtn = document.getElementById("showTotalsBtn");
  const totalsCard = document.getElementById("totalsCard");

  showTotalsBtn.addEventListener("click", function () {
    totalsCard.style.display =
      totalsCard.style.display === "none" ? "block" : "none";
    calculateGrandTotal();
  });

  searchInput.addEventListener("input", function () {
    clearSearchBtn.style.display = this.value ? "block" : "none";
    filterExpenses();
  });

  clearSearchBtn.addEventListener("click", function () {
    searchInput.value = "";
    combinedSearch.clear();
    filterExpenses();
    clearSearchBtn.style.display = "none";
  });

  function calculateGrandTotal() {
    let total = 0;
    document.querySelectorAll(".expenses-table tbody tr").forEach((row) => {
      if (row.style.display !== "none") {
        const amountCell = row.querySelector("td:nth-child(5)");
        const amount = parseFloat(
          amountCell.textContent.replace("₱", "").replace(",", "")
        );
        total += amount;
      }
    });
    document.getElementById("grandTotal").textContent = `₱${total.toFixed(2)}`;
  }

  function calculateRangeTotal(selectedDates) {
    if (selectedDates.length !== 2) {
      document.getElementById("rangeTotal").textContent = "₱0.00";
      return;
    }
    const startDate = new Date(selectedDates[0]);
    const endDate = new Date(selectedDates[1]);
    let total = 0;
    document.querySelectorAll(".expenses-table tbody tr").forEach((row) => {
      const dateCell = row.querySelector("td:nth-child(2)");
      const amountCell = row.querySelector("td:nth-child(5)");
      const rowDate = new Date(dateCell.textContent.trim());
      const amount = parseFloat(
        amountCell.textContent.replace("₱", "").replace(",", "")
      );
      if (rowDate >= startDate && rowDate <= endDate) {
        total += amount;
      }
    });
    document.getElementById("rangeTotal").textContent = `₱${total.toFixed(2)}`;
  }

  function filterExpenses() {
    const searchValue = searchInput.value.toLowerCase();
    const selectedDates = combinedSearch.selectedDates;
    const isDateRange = selectedDates && selectedDates.length === 2;
    document.querySelectorAll(".expenses-table tbody tr").forEach((row) => {
      const cells = row.querySelectorAll("td");
      const rowText = Array.from(cells)
        .map((cell) => cell.textContent.toLowerCase())
        .join(" ");
      const rowDate = new Date(cells[1].textContent.trim());
      let matchesSearch = true;
      let matchesDate = true;
      if (searchValue && !isDateRange) {
        matchesSearch = rowText.includes(searchValue);
      }
      if (isDateRange) {
        const startDate = new Date(selectedDates[0]);
        const endDate = new Date(selectedDates[1]);
        matchesDate = rowDate >= startDate && rowDate <= endDate;
      }
      row.style.display = matchesSearch && matchesDate ? "" : "none";
    });
    calculateGrandTotal();
  }

  function resetModal() {
    document.getElementById("addExpenseForm").reset();
  }

  function resetEditModal() {
    document.getElementById("editExpenseForm").reset();
  }

  document.querySelectorAll(".edit-expense").forEach(function (button) {
    button.addEventListener("click", function () {
      const expenseId = this.getAttribute("data-id");
      const date = this.getAttribute("data-date");
      const category = this.getAttribute("data-category");
      const description = this.getAttribute("data-description");
      const amount = this.getAttribute("data-amount");

      document.getElementById("edit_expense_id").value = expenseId;
      document.getElementById("edit_date").value = date;
      document.getElementById("edit_category").value = category;
      document.getElementById("edit_description").value = description;
      document.getElementById("edit_amount").value = amount;

      const modal = new bootstrap.Modal(
        document.getElementById("editExpenseModal")
      );
      modal.show();
    });
  });

  document
    .getElementById("addExpenseModal")
    .addEventListener("hidden.bs.modal", function () {
      resetModal();
    });

  document
    .getElementById("editExpenseModal")
    .addEventListener("hidden.bs.modal", function () {
      resetEditModal();
    });

  document.querySelectorAll(".delete-expense-btn").forEach(function (button) {
    button.addEventListener("click", function () {
      const expenseId = this.getAttribute("data-id");
      document.getElementById("delete_expense_id").value = expenseId;
    });
  });

  const monthlyRadio = document.getElementById("monthlyRadio");
  const annualRadio = document.getElementById("annualRadio");
  const datePickerContainer = document.getElementById("datePickerContainer");
  const exportButton = document.getElementById("exportButton");
  let currentPicker = createMonthPicker();

  monthlyRadio.addEventListener("change", function () {
    if (!monthlyRadio.checked) return;
    currentPicker = createMonthPicker();
    validateSelection();
  });

  annualRadio.addEventListener("change", function () {
    if (!annualRadio.checked) return;
    currentPicker = createYearPicker();
    validateSelection();
  });

  function createMonthPicker() {
    datePickerContainer.innerHTML = `<label for="monthPicker" class="form-label">Select Month:</label><input type="month" class="form-control" id="monthPicker">`;
    const monthPicker = document.getElementById("monthPicker");
    monthPicker.addEventListener("input", validateSelection);
    return monthPicker;
  }

  function createYearPicker() {
    datePickerContainer.innerHTML = `<label for="yearPicker" class="form-label">Select Year:</label><input type="number" class="form-control" id="yearPicker" placeholder="YYYY" min="2000" max="${new Date().getFullYear()}">`;
    const yearPicker = document.getElementById("yearPicker");
    yearPicker.addEventListener("input", validateSelection);
    return yearPicker;
  }

  function validateSelection() {
    const hasValue = currentPicker && currentPicker.value;
    exportButton.disabled = !hasValue;
  }

  document.getElementById("exportPdf").addEventListener("click", function (e) {
    e.preventDefault();
    exportData("pdf");
  });

  document.getElementById("exportCsv").addEventListener("click", function (e) {
    e.preventDefault();
    exportData("csv");
  });

  function exportData(format) {
    let url = "./backend/export.php?format=" + format;
    if (monthlyRadio.checked) {
      const monthValue = document.getElementById("monthPicker")?.value;
      if (!monthValue) return alert("Please select a month.");
      url += "&date_range=monthly&month=" + monthValue;
    } else {
      const yearValue = document.getElementById("yearPicker")?.value;
      if (!yearValue) return alert("Please select a year.");
      url += "&date_range=annually&year=" + yearValue;
    }
    window.location.href = url;
  }

  calculateGrandTotal();
});
