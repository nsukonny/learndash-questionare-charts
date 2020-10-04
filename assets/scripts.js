jQuery(document).ready(function ($) {

    var roundedCanvases = document.getElementsByClassName('lqcharts-rounded');

    if (roundedCanvases.length) {
        let config = {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [
                        30,
                        70,
                    ],
                    backgroundColor: [
                        '#e6e6e6',
                        '#64d724',
                    ],
                    label: 'Chart 1',
                }],
                labels: [
                    '',
                    'Points',
                ]
            },
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                    display: false,
                },
                title: {
                    display: false,
                    text: 'Chart.js Doughnut Chart'
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        };

        for (let i = 0; i < roundedCanvases.length; i++) {
            let percent = roundedCanvases[i].dataset.percent;

            if (undefined !== percent) {
                let color = 50 > percent ? '#FF0000' : '#64D724';

                config = {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [
                                percent,
                                100 - percent,
                            ],
                            backgroundColor: [
                                color,
                                '#e6e6e6',
                            ],
                            label: 'Chart 1',
                        }],
                        labels: [
                            'Points',
                            '',
                        ]
                    },
                    options: {
                        responsive: true,
                        legend: {
                            position: 'top',
                            display: false,
                        },
                        title: {
                            display: false,
                            text: 'Chart.js Doughnut Chart'
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        },
                        tooltips: {
                            enabled: false
                        }
                    }
                };
            }

            let ctx = roundedCanvases[i].getContext('2d'),
                oneChart = new Chart(ctx, config);
        }

        let lqchartsGraph = $('.lqcharts-graph'),
            lineChartData = {
                labels: ['Woche 1', 'Woche 2', 'Woche 3', 'Woche 4', 'Woche 5', 'Woche 6', 'Woche 7', 'Woche 8'],
                datasets: [{
                    label: 'Allg. Empﬁnden',
                    borderColor: '#FF0000',
                    backgroundColor: '#FF0000',
                    fill: false,
                    data: lqchartsGraph.data('emb').split(','),
                    yAxisID: 'y-axis-1',
                }, {
                    label: 'Beweglichkeit',
                    borderColor: '#9900CC',
                    backgroundColor: '#9900CC',
                    fill: false,
                    data: lqchartsGraph.data('bef').split(','),
                    yAxisID: 'y-axis-1'
                }, {
                    label: 'Stabilität',
                    borderColor: '#fff109',
                    backgroundColor: '#fff109',
                    fill: false,
                    data: lqchartsGraph.data('stab').split(','),
                    yAxisID: 'y-axis-1'
                }]
            },
            stepSize = 10;

        if (425 >= $(window).width()) {
            stepSize = 20;
        }

        window.onload = function () {
            var ctx = document.getElementById('lqcharts-graph').getContext('2d');
            window.myLine = Chart.Line(ctx, {
                data: lineChartData,
                options: {
                    responsive: true,
                    hoverMode: 'index',
                    stacked: true,
                    title: {
                        display: false,
                    },
                    scales: {
                        yAxes: [{
                            type: 'linear',
                            display: true,
                            position: 'left',
                            id: 'y-axis-1',
                            gridLines: {
                                display: true,
                                drawBorder: true,
                                drawOnChartArea: true,
                                drawTicks: true,
                            },
                            ticks: {
                                min: 0,
                                max: 100,
                                stepSize: stepSize
                            }
                        }],
                    }
                }
            });
        };
    }

});


function createConfig(gridlines, title) {
    return {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [{
                label: 'My First dataset',
                backgroundColor: window.chartColors.red,
                borderColor: window.chartColors.red,
                data: [10, 30, 39, 20, 25, 34, 0],
                fill: false,
            }, {
                label: 'My Second dataset',
                fill: false,
                backgroundColor: window.chartColors.blue,
                borderColor: window.chartColors.blue,
                data: [18, 33, 22, 19, 11, 39, 30],
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: title
            },
            scales: {
                xAxes: [{
                    gridLines: gridlines
                }],
                yAxes: [{
                    gridLines: gridlines,
                    ticks: {
                        min: 0,
                        max: 100,
                        stepSize: 10
                    }
                }]
            }
        }
    };
}

window.onload = function () {
    var container = document.querySelector('.container');

    [{
        title: 'Display: true',
        gridLines: {
            display: true
        }
    }, {
        title: 'Display: false',
        gridLines: {
            display: false
        }
    }, {
        title: 'Display: false, no border',
        gridLines: {
            display: false,
            drawBorder: false
        }
    }, {
        title: 'DrawOnChartArea: false',
        gridLines: {
            display: true,
            drawBorder: true,
            drawOnChartArea: false,
        }
    }, {
        title: 'DrawTicks: false',
        gridLines: {
            display: true,
            drawBorder: true,
            drawOnChartArea: true,
            drawTicks: false,
        }
    }].forEach(function (details) {
        var div = document.createElement('div');
        div.classList.add('chart-container');

        var canvas = document.createElement('canvas');
        div.appendChild(canvas);
        container.appendChild(div);

        var ctx = canvas.getContext('2d');
        var config = createConfig(details.gridLines, details.title);
        new Chart(ctx, config);
    });
};