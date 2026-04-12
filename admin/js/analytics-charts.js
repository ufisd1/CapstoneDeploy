function formatLabel(dateString, timeFilter) {
  if (timeFilter === "weekly") {
    const parts = dateString.split("-");
    return `Week ${parts[1]} ${parts[0]}`;
  } else if (timeFilter === "monthly") {
    const [year, month] = dateString.split("-");
    const monthNames = [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "May",
      "Jun",
      "Jul",
      "Aug",
      "Sep",
      "Oct",
      "Nov",
      "Dec",
    ];
    return `${monthNames[parseInt(month) - 1]} ${year}`;
  } else if (timeFilter === "yearly") {
    return dateString;
  }
  return dateString;
}

document.addEventListener("DOMContentLoaded", function () {
  const timeFilter = chartData.timeFilter;

  const salesLabels = chartData.salesData.map((item) =>
    formatLabel(item.day, timeFilter)
  );
  const expenseLabels = chartData.expenseData.map((item) =>
    formatLabel(item.day, timeFilter)
  );
  const productionLabels = chartData.productionTrends.map((item) =>
    formatLabel(item.date, timeFilter)
  );
  const revenueLabels = chartData.revenueAnalysis.map((item) =>
    formatLabel(item.date, timeFilter)
  );

  const salesExpensesCtx = document
    .getElementById("dailySalesExpensesChart")
    .getContext("2d");
  new Chart(salesExpensesCtx, {
    type: "line",
    data: {
      labels: expenseLabels,
      datasets: [
        {
          label: "Expenses",
          data: chartData.expenseData.map((item) => item.total),
          backgroundColor: "rgba(255, 99, 132, 0.5)",
          borderColor: "rgba(255, 99, 132, 1)",
          borderWidth: 2,
          tension: 0.1,
        },
        {
          label: "Sales",
          data: chartData.salesData.map((item) => item.revenue),
          backgroundColor: "rgba(54, 162, 235, 0.5)",
          borderColor: "rgba(54, 162, 235, 1)",
          borderWidth: 2,
          tension: 0.1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        tooltip: {
          mode: "index",
          intersect: false,
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: "Amount",
          },
        },
        x: {
          title: {
            display: true,
            text: "Time Period",
          },
        },
      },
    },
  });

  const revenueCtx = document.getElementById("revenueChart").getContext("2d");
  new Chart(revenueCtx, {
    type: "bar",
    data: {
      labels: revenueLabels,
      datasets: [
        {
          label: "Total Revenue",
          data: chartData.revenueAnalysis.map((item) => item.total_revenue),
          backgroundColor: "rgba(54, 162, 235, 0.5)",
          borderColor: "rgba(54, 162, 235, 1)",
          borderWidth: 2,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: "Revenue",
          },
        },
        x: {
          title: {
            display: true,
            text: "Time Period",
          },
        },
      },
    },
  });

  const productionTrendsCtx = document
    .getElementById("productionTrendsChart")
    .getContext("2d");
  new Chart(productionTrendsCtx, {
    type: "line",
    data: {
      labels: productionLabels,
      datasets: [
        {
          label: "Total Production",
          data: chartData.productionTrends.map((item) => item.total_production),
          backgroundColor: "rgba(255, 206, 86, 0.5)",
          borderColor: "rgba(255, 206, 86, 1)",
          borderWidth: 2,
          tension: 0.1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: "Quantity",
          },
        },
        x: {
          title: {
            display: true,
            text: "Time Period",
          },
        },
      },
    },
  });

  const stockByTypeCtx = document
    .getElementById("stockByTypeChart")
    .getContext("2d");
  new Chart(stockByTypeCtx, {
    type: "pie",
    data: {
      labels: chartData.stockByType.map((item) => item.egg_type),
      datasets: [
        {
          data: chartData.stockByType.map((item) => item.total),
          backgroundColor: [
            "rgba(255, 99, 132, 0.5)",
            "rgba(54, 162, 235, 0.5)",
            "rgba(255, 206, 86, 0.5)",
            "rgba(75, 192, 192, 0.5)",
            "rgba(153, 102, 255, 0.5)",
            "rgba(255, 159, 64, 0.5)",
          ],
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "right",
          labels: {
            boxWidth: 12,
            padding: 10,
          },
        },
      },
    },
  });
});