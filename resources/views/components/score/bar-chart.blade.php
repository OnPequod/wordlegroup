<div>
    @once
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
        @endpush
    @endonce

    <div
        class="h-72 sm:h-80"
        x-data="{
                labels: ['1', '2', '3', '4', '5', '6', 'Missed'],
                values: @json($values),
                chart: null,
                init() {
                this.waitForChart();
            },
            waitForChart() {
                if (typeof Chart === 'undefined' || typeof ChartDataLabels === 'undefined') {
                    setTimeout(() => this.waitForChart(), 50);
                    return;
                }
                this.initChart();
            },
            initChart() {
                Chart.register(ChartDataLabels);

                const colors = this.labels.map((label) => label === 'Missed' ? '#a1a1aa' : '#166534')

                let chart = new Chart(this.$refs.canvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: this.labels,
                        datasets: [{
                            data: this.values,
                            backgroundColor: colors,
                            borderColor: colors,
                        }],
                    },
                    options: {
                        base: 0,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        interaction: { intersect: false },
                        layout: { padding: { right: 25 } },
                        scales: {
                            y: {
                                min: 0,
                                grid: {
                                    display: false,
                                },
                                ticks: {
                                    font: {
                                        size: 12,
                                        weight: 500,
                                    },
                                    color: '#71717a',
                                },
                                title: {
                                    display: false,
                                }
                            },
                            x: {
                                beginAtZero: true,
                                grid: {
                                    display: true,
                                    color: '#f4f4f5',
                                },
                                ticks: {
                                    stepSize: {{ $stepSize }},
                                    font: {
                                        size: 11,
                                        weight: 400,
                                    },
                                    color: '#a1a1aa',
                                },
                                title: {
                                    display: false,
                                }
                            }
                        },
                        plugins: {
                            datalabels: {
                                color: 'black',
                                anchor: 'end',
                                align: 'right',
                                labels: {
                                    title: {
                                        font: {
                                            weight: 400,
                                        }
                                    },
                                }
                            },
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                enabled: false,
                                displayColors: false,
                                callbacks: {
                                    label(point) {
                                        return 'Scores: '+point.raw
                                    }
                                }
                            }
                        }
                    }
                })


                this.$watch('values', () => {
                    chart.data.labels = this.labels
                    chart.data.datasets[0].data = this.values

                    chart.update()
                })
            }
        }"
    >
        <canvas x-ref="canvas"></canvas>
    </div>
</div>
