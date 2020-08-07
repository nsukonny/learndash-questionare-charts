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
                    yAxisID: 'y-axis-2'
                }, {
                    label: 'Stabilität',
                    borderColor: '#fff109',
                    backgroundColor: '#fff109',
                    fill: false,
                    data: lqchartsGraph.data('stab').split(','),
                    yAxisID: 'y-axis-2'
                }]
            };

        window.onload = function () {
            var ctx = document.getElementById('lqcharts-graph').getContext('2d');
            window.myLine = Chart.Line(ctx, {
                data: lineChartData,
                options: {
                    responsive: true,
                    hoverMode: 'index',
                    stacked: false,
                    title: {
                        display: false,
                    },
                    scales: {
                        yAxes: [{
                            type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                            display: true,
                            position: 'left',
                            id: 'y-axis-1',
                        }, {
                            type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                            display: true,
                            position: 'right',
                            id: 'y-axis-2',

                            // grid line settings
                            gridLines: {
                                drawOnChartArea: false, // only want the grid lines for one axis to show up
                            },
                        }],
                    }
                }
            });
        };
    }

});