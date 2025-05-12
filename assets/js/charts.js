const speciesCtx = document.getElementById("speciesPieChart").getContext("2d");

const speciesLabels = JSON.parse(
  document.getElementById("speciesPieChart").dataset.labels
);
const speciesCounts = JSON.parse(
  document.getElementById("speciesPieChart").dataset.counts
);

new Chart(speciesCtx, {
  type: "pie",
  data: {
    labels: speciesLabels,
    datasets: [
      {
        label: "Species Distribution",
        data: speciesCounts,
        backgroundColor: [
          "rgba(255, 99, 132, 0.6)",
          "rgba(54, 162, 235, 0.6)",
          "rgba(255, 206, 86, 0.6)",
          "rgba(75, 192, 192, 0.6)",
        ],
        borderColor: [
          "rgba(255, 99, 132, 1)",
          "rgba(54, 162, 235, 1)",
          "rgba(255, 206, 86, 1)",
          "rgba(75, 192, 192, 1)",
        ],
        borderWidth: 1,
      },
    ],
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
  },
});
document.addEventListener("DOMContentLoaded", function () {
  // Sample appointment data - you would replace this with your PHP data
  const appointmentData = {
    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
    datasets: [
      {
        label: "Appointments",
        data: [12, 19, 15, 17, 14, 20],
        backgroundColor: "rgba(54, 162, 235, 0.2)",
        borderColor: "rgba(54, 162, 235, 1)",
        borderWidth: 1,
      },
    ],
  };

  // Appointment Chart
  const apptCtx = document.getElementById("appointmentsChart").getContext("2d");
  const appointmentsChart = new Chart(apptCtx, {
    type: "bar",
    data: appointmentData,
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: "Number of Appointments",
          },
        },
        x: {
          title: {
            display: true,
            text: "Month",
          },
        },
      },
      plugins: {
        title: {
          display: true,
          text: "Monthly Appointments",
        },
      },
    },
  });
});
