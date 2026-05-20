<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Revenue</p>
                <p class="mt-3 text-2xl font-semibold text-zinc-900 dark:text-white">Rp {{ number_format($summary->totalRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl bg-blue-50 p-3 text-blue-600">
                <flux:icon.banknotes class="size-5" />
            </div>
        </div>
        <p class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">Accumulated billing value across completed transactions.</p>
    </section>

    <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Transactions</p>
                <p class="mt-3 text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($summary->totalTransactions) }}</p>
            </div>
            <div class="rounded-xl bg-blue-50 p-3 text-blue-600">
                <flux:icon.clipboard-document class="size-5" />
            </div>
        </div>
        <p class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">Patient payments processed by the cashier team.</p>
    </section>

    <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Discount</p>
                <p class="mt-3 text-2xl font-semibold text-zinc-900 dark:text-white">Rp {{ number_format($summary->totalDiscount, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl bg-red-50 p-3 text-red-500">
                <flux:icon.ticket class="size-5" />
            </div>
        </div>
        <p class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">Discount value generated from active voucher programs.</p>
    </section>

    <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Top Procedure</p>
                <p class="mt-3 text-lg font-semibold text-zinc-900 dark:text-white">{{ $topProcedure?->procedure_name ?? 'No procedure data yet' }}</p>
            </div>
            <div class="rounded-xl bg-emerald-50 p-3 text-emerald-500">
                <flux:icon.heart class="size-5" />
            </div>
        </div>
        <div class="mt-4 flex items-center justify-between text-sm">
            <span class="text-zinc-500 dark:text-zinc-400">Times used</span>
            <span class="font-semibold text-zinc-900 dark:text-white">{{ number_format((int) ($topProcedure?->total_used ?? 0)) }}</span>
        </div>
    </section>
</div>
