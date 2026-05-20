<section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Procedure Snapshot</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Most frequently used healthcare services.</p>
        </div>
        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600 text-center">
            {{ number_format($summary->totalProcedures) }} procedures
        </span>
    </div>

    <div class="mt-4 space-y-3">
        @forelse ($top_procedures as $procedure)
            <div class="rounded-2xl border border-zinc-200 bg-zinc-50/70 px-4 py-4 transition hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800/50 dark:hover:bg-zinc-800">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="font-medium text-zinc-900 dark:text-white">{{ $procedure->procedure_name }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Revenue Rp {{ number_format((float) $procedure->total_revenue, 0, ',', '.') }}</p>
                    </div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs text-center font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                        {{ number_format((int) $procedure->total_used) }} uses
                    </span>
                </div>
            </div>
        @empty
            <div class="rounded-xl bg-zinc-50 px-4 py-6 text-sm text-zinc-500 dark:bg-zinc-800/50 dark:text-zinc-400">
                No procedure performance data available.
            </div>
        @endforelse
    </div>
</section>
