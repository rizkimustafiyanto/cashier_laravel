<section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">

        <div>
            <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400">
                Voucher Analytics
            </div>

            <h2 class="mt-3 text-xl font-bold tracking-tight text-zinc-900 dark:text-white">
                Voucher Usage
            </h2>

            <p class="mt-1 text-sm leading-6 text-zinc-500 dark:text-zinc-400">
                Distribution of voucher programs used across healthcare transactions.
            </p>
        </div>

        <div class="rounded-2xl bg-amber-50 p-3 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
            <flux:icon.presentation-chart-bar class="size-5" />
        </div>

    </div>

    {{-- Chart --}}
    <div
        id="voucher-usage-chart"
        class="mt-6 h-72 rounded-2xl border border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-700 dark:bg-zinc-800/30"
        data-series='@json($voucherSeries)'
        data-labels='@json($voucherLabels)'
    ></div>
    {{-- Chart Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chartElement = document.getElementById('voucher-usage-chart');

            if (!chartElement || !window.ApexCharts) {
                return;
            }

            const voucherSeries = JSON.parse(chartElement.dataset.series ?? '[]');
            const voucherLabels = JSON.parse(chartElement.dataset.labels ?? '[]');

            if (!Array.isArray(voucherSeries) || !Array.isArray(voucherLabels) || voucherSeries.length === 0) {
                return;
            }

            const chart = new window.ApexCharts(chartElement, {
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: {
                        show: false,
                    },
                },
                series: [
                    {
                        name: 'Voucher Uses',
                        data: voucherSeries,
                    },
                ],
                plotOptions: {
                    bar: {
                        borderRadius: 14,
                        horizontal: false,
                        columnWidth: '50%',
                    },
                },
                dataLabels: {
                    enabled: false,
                },
                xaxis: {
                    categories: voucherLabels,
                    labels: {
                        style: {
                            colors: '#64748b',
                        },
                    },
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#64748b',
                        },
                    },
                },
                tooltip: {
                    theme: 'light',
                },
                grid: {
                    strokeDashArray: 4,
                    borderColor: '#e2e8f0',
                },
                fill: {
                    opacity: 0.9,
                },
            });

            chart.render();
        });
    </script>
    {{-- Voucher List --}}
    <div class="mt-6 space-y-3">

        @forelse ($voucher_usage as $usage)

            <div class="flex items-center justify-between rounded-2xl border border-zinc-200 bg-zinc-50/70 px-4 py-4 transition hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800/40 dark:hover:bg-zinc-800">

                <div>

                    <div class="flex items-center gap-2">

                        <div class="size-2 rounded-full bg-amber-500"></div>

                        <p class="font-semibold text-zinc-900 dark:text-white">
                            {{ Str::headline((string) $usage->voucher_type) }}
                        </p>

                    </div>

                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Discount Rp {{ number_format((float) $usage->total_discount, 0, ',', '.') }}
                    </p>

                </div>

                <div class="text-right">

                    <p class="text-lg font-bold text-zinc-900 dark:text-white">
                        {{ number_format((int) $usage->total_usage) }}
                    </p>

                    <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                        Uses
                    </p>

                </div>

            </div>

        @empty

            <div class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 px-4 py-8 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/40 dark:text-zinc-400">
                No voucher usage data available.
            </div>

        @endforelse

    </div>

</section>