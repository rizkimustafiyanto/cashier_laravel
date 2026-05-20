<section class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">

    {{-- HEADER --}}
    <div class="flex items-start justify-between gap-3">
        <div>
            <div class="inline-flex items-center gap-2 rounded-full border border-pink-200 bg-pink-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-pink-700 dark:border-pink-500/20 dark:bg-pink-500/10 dark:text-pink-400">
                Insurance Insights
            </div>

            <h2 class="mt-2 text-base font-bold text-zinc-900 dark:text-white">
                Top Insurances
            </h2>
        </div>

        <div class="rounded-xl bg-pink-50 p-2 text-pink-600 dark:bg-pink-500/10 dark:text-pink-400">
            <flux:icon.building-office class="size-4" />
        </div>
    </div>

    {{-- LIST --}}
    <div class="mt-5 space-y-2">

        @forelse ($top_insurances_visits as $ins)
            <div class="rounded-xl border border-zinc-200 bg-zinc-50/60 p-3 transition hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800/40 dark:hover:bg-zinc-800">

                {{-- NAME (SAFE WRAP) --}}
                <p class="text-sm font-semibold text-zinc-900 dark:text-white break-words leading-snug">
                    {{ $ins->insurance_name ?? $ins->insurance_id }}
                </p>

                {{-- METRICS ROW --}}
                <div class="mt-2 flex items-center justify-between gap-3 text-xs">

                    <div class="text-zinc-500 dark:text-zinc-400">
                        <span class="font-medium text-zinc-700 dark:text-zinc-300">
                            {{ number_format((int) $ins->total_visits) }}
                        </span>
                        visits
                    </div>

                    <div class="text-right">
                        <span class="font-semibold text-zinc-900 dark:text-white">
                            Rp {{ number_format((float) $ins->total_revenue, 0, ',', '.') }}
                        </span>
                    </div>

                </div>

            </div>
        @empty
            <div class="rounded-xl bg-zinc-50 px-3 py-4 text-xs text-zinc-500 dark:bg-zinc-800/50 dark:text-zinc-400">
                No data available.
            </div>
        @endforelse

    </div>
</section>