define([
    'jquery',
    'M2ECore/External/ChartJs/Chart.min',
    'mage/translate',
    'M2ECore/External/ChartJs/es6-shim.min',
], function ($, Chart, $t) {

    return {
        renderChart: function (selector, dataset, label) {
            const charJsOptions= {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        suggestedMin: 5,
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {if (value % 1 === 0) {return value;}}
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        titleFont: {
                            size: 15
                        },
                        callbacks: {
                            title: function (context) {
                                return context[0].raw.tooltipTitle || context[0].label;
                            }
                        },
                        bodyFont: {
                            size: 15
                        },
                        footerFont: {
                            size: 15
                        }
                    }
                }
            }

            const charJsData = {
                datasets: [{
                    data: dataset,
                    label: label,
                    backgroundColor: '#f1d4b3',
                    borderColor: '#eb5202',
                    borderWidth: 1
                }]
            }

            const charJsPlugins = [];
            if (dataset && dataset.length > 0 && Math.max(...dataset.map(function (item) { return item.y })) === 0) {
                charJsPlugins[0] = {
                    beforeDraw: function (chart) {
                        const width = chart.width;
                        const height = chart.height;
                        const ctx = chart.ctx;

                        ctx.restore();
                        const fontSize = (height / 130).toFixed(2);
                        ctx.font = fontSize + "em sans-serif";
                        ctx.fillStyle = '#bbb7b7';
                        ctx.textBaseline = "middle";

                        const text = $t('No Data');
                        const textX = Math.round((width - ctx.measureText(text).width) / 2);
                        const textY = height / 2;

                        ctx.fillText(text, textX, textY);
                        ctx.save();
                    }
                }
            }

            new Chart($(selector), {
                type: 'bar',
                data: charJsData,
                options: charJsOptions,
                plugins: charJsPlugins
            });
        }
    };
});
