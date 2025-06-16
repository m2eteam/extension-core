define([
    'jquery',
    'M2ECore/External/ChartJs/Chart.min',
    'mage/translate',
    'M2ECore/External/ChartJs/es6-shim.min'
], function ($, Chart, $t) {

    return {
        renderWithData: function (selector, dataset) {
            const total = dataset.data.reduce(function (acc, val) {
                return acc + val;
            }, 0);

            new Chart($(selector), {
                type: 'doughnut',
                data: {
                    labels: dataset.labels,
                    datasets: [{
                        data: dataset.data,
                        backgroundColor: dataset.backgroundColor
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            titleFont: {size: 15},
                            bodyFont: {size: 15},
                            footerFont: {size: 15},
                            callbacks: {
                                afterLabel: function (tooltipItem) {
                                    const value = tooltipItem.dataset.data[tooltipItem.dataIndex];
                                    const percent = Math.round((value / total) * 100);
                                    return '(' + percent + '%)';
                                }
                            }
                        }
                    }
                },
                plugins: [this._getChartPlugin(total)]
            });
        },

        renderWithoutData: function (selector) {
            const text = $t('No Data');

            new Chart($(selector), {
                type: 'doughnut',
                data: {
                    labels: [text],
                    datasets: [{
                        data: [1],
                        backgroundColor: '#a9b4b8'
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: false
                    }
                },
                plugins: [this._getChartPlugin(text)]
            });
        },

        _getChartPlugin: function (text) {
            return {
                beforeDraw: function (chart) {
                    const width = chart.width;
                    const height = chart.height;
                    const ctx = chart.ctx;

                    ctx.restore();
                    const fontSize = (height / 130).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.fillStyle = '#bbb7b7';
                    ctx.textBaseline = 'top';

                    const textX = Math.round((width - ctx.measureText(text).width) / 2);
                    const textY = height / 2;

                    ctx.fillText(text, textX, textY);
                    ctx.save();
                }
            };
        }
    };
});
