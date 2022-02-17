window.onload = function () {
    var chBar = document.getElementById("chBar").getContext("2d");
    if (chBar) {
        renderChatComparative();
    }
};

window.getTeamPerformanceData = async function () {
    const urlApp = document.getElementById('urlApp').value + '/team/get-performance';

    let performanceData = $.get(urlApp, function (result) {
        performanceData = result;
    });

    return performanceData;
}

window.renderChatComparative = async function () {
    getTeamPerformanceData()
        .then((data) => {
            let sellers = data.map(function (member) {
                return member.seller;
            });

            let sales = data.map(function (member) {
                return member.vendas;
            });

            let metas = data.map(function (member) {
                return member.meta;
            });

            renderGrafico(sellers, sales, metas);

        });
}

window.renderGrafico = function (sellers, sales, metas) {
    // chart colors
    var colors = ['#007bff', '#28a745', '#333333', '#c3e6cb', '#dc3545', '#6c757d'];

    var chartData = {
        labels: sellers,
        datasets: [{
            type: 'bar',
            data: sales,
            fill: false,
            backgroundColor: colors[3],
            label: 'Vendedor',
            yAxisID: 'y-axis-1'
        },
        {
            type: 'line',
            data: metas,
            fill: false,
            label: 'Meta Semana',
            borderColor: colors[0],
            backgroundColor: colors[0],
            pointBorderColor: colors[0],
            pointBackgroundColor: colors[0],
            pointHoverBackgroundColor: colors[0],
            pointHoverBorderColor: colors[0],
            yAxisID: 'y-axis-2'
        }]
    };

    window.myBar = new Chart(chBar, {
        type: 'bar',
        data: chartData,
        options: {
            responsive: true,
            tooltips: {
                mode: 'label'
            },
            elements: {
                line: {
                    fill: false
                }
            },
            scales: {
                xAxes: [{
                    display: true,
                    gridLines: {
                        display: false
                    },
                    labels: {
                        show: true,
                    }
                }],
                yAxes: [{
                    type: "linear",
                    display: true,
                    position: "left",
                    id: "y-axis-1",
                    gridLines: {
                        display: false
                    },
                    labels: {
                        show: true,

                    }
                }, {
                    type: "linear",
                    display: true,
                    position: "right",
                    id: "y-axis-2",
                    gridLines: {
                        display: false
                    },
                    labels: {
                        show: true,

                    }
                }]
            }
        }
    });
}

window.sellerDelete = function (seller) {
    if (confirm('Confirma exclusão desse membro da sua equipe?')) {
        window.location.href = "/teams/" + seller + '/remove';
    }
}