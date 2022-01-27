// var chartData = {
//     labels: ["Andre", "Paulo", "Talita", "Marina", "Marcelo", "Carlos", "Tião Rosa"],
//     datasets: [{
//         data: [2, 6, 8, 5, 3, 4, 6],
//     },
//     {
//         data: [4, 1, 10, 3, 2, 5, 8],
//     }]
// };

/* chart.js chart examples */



window.onload = function () {
    var chBar = document.getElementById("chBar");
    if (chBar) {
        renderChatComparative();
    }
};

function renderChatComparative() {
    // chart colors
    var colors = ['#007bff', '#28a745', '#333333', '#c3e6cb', '#dc3545', '#6c757d'];

    /* large line chart */

    var chartData = {
        labels: ["Andre", "Paulo", "Talita", "Marina", "Marcelo", "Carlos", "Tião Rosa"],
        datasets: [{
            data: [2, 6, 8, 5, 3, 4, 6],
            backgroundColor: colors[3],
            label: 'Semana passada'
        },
        {
            data: [4, 1, 10, 3, 2, 5, 8],
            backgroundColor: colors[1],
            label: 'Semana atual'
        }]
    };

    new Chart(chBar, {
        type: 'bar',
        data: chartData,
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: false
                    }
                }]
            },
            legend: {
                display: false
            }
        }
    });
}