<section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full border border-pink-200 bg-pink-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-pink-700 dark:border-pink-500/20 dark:bg-pink-500/10 dark:text-pink-400">
                Insurance Insights
            </div>

            <h2 class="mt-3 text-xl font-bold tracking-tight text-zinc-900 dark:text-white">Top Insurances</h2>
            <p class="mt-1 text-sm leading-6 text-zinc-500 dark:text-zinc-400">Insurers with the most visits and highest payments.</p>
        </div>

        <div class="rounded-2xl bg-pink-50 p-3 text-pink-600 dark:bg-pink-500/10 dark:text-pink-400">
            <flux:icon.building-office class="size-5" />
        </div>
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-2">
        <div>
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">By Visits</h3>
            <div class="mt-3 space-y-3">
                @forelse ($top_insurances_visits as $ins)
                    <div class="rounded-2xl border border-zinc-200 bg-zinc-50/70 px-4 py-4 transition hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800/40 dark:hover:bg-zinc-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-zinc-900 dark:text-white">{{ $ins->insurance_name ?? $ins->insurance_id }}</p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ number_format((int) $ins->total_visits) }} visits</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-zinc-900 dark:text-white">Rp {{ number_format((float) $ins->total_revenue, 0, ',', '.') }}</p>
                                <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Revenue</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl bg-zinc-50 px-4 py-6 text-sm text-zinc-500 dark:bg-zinc-800/50 dark:text-zinc-400">No data available.</div>
                @endforelse
            </div>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">By Payments</h3>
            <div class="mt-3 space-y-3">
                @forelse ($top_insurances_revenue as $ins)
                    <div class="rounded-2xl border border-zinc-200 bg-zinc-50/70 px-4 py-4 transition hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800/40 dark:hover:bg-zinc-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-zinc-900 dark:text-white">{{ $ins->insurance_name ?? $ins->insurance_id }}</p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ number_format((int) $ins->total_visits) }} visits</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-zinc-900 dark:text-white">Rp {{ number_format((float) $ins->total_revenue, 0, ',', '.') }}</p>
                                <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Revenue</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl bg-zinc-50 px-4 py-6 text-sm text-zinc-500 dark:bg-zinc-800/50 dark:text-zinc-400">No data available.</div>
                @endforelse
            </div>
        </div>
    </div>
</section>
