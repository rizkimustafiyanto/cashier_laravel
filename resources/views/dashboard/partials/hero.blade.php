<div class="border-b border-zinc-200 bg-white/80 backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/80">

    <div class="flex flex-col gap-5 px-4 py-6 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">

        <div>
            <div class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400">
                <div class="size-2 rounded-full bg-blue-500"></div>
                Healthcare Operations
            </div>

            <h1 class="mt-3 text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                Dashboard Overview
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500 dark:text-zinc-400">
                Monitor revenue, procedures, voucher usage, and healthcare transaction performance in one centralized dashboard.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">

            <div class="rounded-2xl border border-zinc-200 bg-white px-5 py-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                    Report Date
                </p>

                <p class="mt-2 text-sm font-semibold text-zinc-900 dark:text-white">
                    {{ now()->timezone(auth()->user()?->timezone ?? config('app.timezone'))->format('d M Y') }}
                </p>
            </div>

            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 shadow-sm dark:border-emerald-500/20 dark:bg-emerald-500/10">
                <p class="text-xs font-medium uppercase tracking-wide text-emerald-700 dark:text-emerald-400">
                    System Status
                </p>

                <div class="mt-2 flex items-center gap-2">
                    <div class="size-2 rounded-full bg-emerald-500"></div>

                    <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-300">
                        Operational
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>