document.addEventListener("DOMContentLoaded", function () {
  if (document.getElementById("productionChart")) {
    var ctx = document.getElementById("productionChart").getContext("2d");
    var productionChart = new Chart(ctx, {
      type: "line",
      data: {
        labels: [],
        datasets: [
          {
            label: "Eggs Produced",
            data: [],
            borderColor: "rgba(255, 99, 132, 1)",
            backgroundColor: "rgba(255, 99, 132, 0.2)",
            fill: true,
            tension: 0.1,
          },
        ],
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: "top",
          },
          title: {
            display: true,
            text: "Monthly Production",
          },
        },
      },
    });
  }

  if (document.getElementById("cashFlowChart")) {
    var cashCtx = document.getElementById("cashFlowChart").getContext("2d");
    var cashFlowChart = new Chart(cashCtx, {
      type: "bar",
      data: {
        labels: [],
        datasets: [
          {
            label: "Sales Revenue",
            data: [],
            backgroundColor: "rgba(75, 192, 192, 0.6)",
            borderColor: "rgba(75, 192, 192, 1)",
            borderWidth: 1,
          },
          {
            label: "Expenses",
            data: [],
            backgroundColor: "rgba(255, 99, 132, 0.6)",
            borderColor: "rgba(255, 99, 132, 1)",
            borderWidth: 1,
          },
          {
            label: "Net Cash Flow",
            data: [],
            type: "line",
            backgroundColor: "rgba(54, 162, 235, 0.2)",
            borderColor: "rgba(54, 162, 235, 1)",
            borderWidth: 2,
            pointBackgroundColor: "rgba(54, 162, 235, 1)",
            pointRadius: 4,
            fill: false,
          },
        ],
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: "Amount (PHP)",
            },
          },
        },
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: "Monthly Cash Flow",
          },
          legend: {
            position: "top",
          },
        },
      },
    });
  }

  function toggleSidebar() {
    document.querySelector(".sidebar").classList.toggle("active");
  }
});
