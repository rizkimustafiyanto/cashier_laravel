<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')

    <title>Healthcare Voucher Management</title>
</head>

<body class="min-h-screen bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-white">

    {{-- Navbar --}}
    <header class="sticky top-0 z-50 border-b border-zinc-200 bg-white/80 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/80">

        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">

            <div class="flex items-center gap-3">

                <div class="flex size-10 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-sm">
                    <flux:icon.heart class="size-5" />
                </div>

                <div>
                    <h1 class="text-sm font-semibold tracking-wide">
                        Healthcare Voucher System
                    </h1>

                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Medical Operations Platform
                    </p>
                </div>

            </div>

            <div class="flex items-center gap-3">

                @auth

                    <a
                        href="{{ route('dashboard') }}"
                        class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Dashboard
                    </a>

                @else

                    <a
                        href="{{ route('login') }}"
                        class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Login
                    </a>

                @endauth

            </div>

        </div>

    </header>

    {{-- Hero --}}
    <section class="relative overflow-hidden">

        <div class="absolute inset-0 bg-linear-to-br from-blue-50 via-transparent to-emerald-50 dark:from-blue-950/20 dark:to-emerald-950/20"></div>

        <div class="relative mx-auto grid max-w-7xl gap-12 px-4 py-24 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-32">

            {{-- Left --}}
            <div class="flex flex-col justify-center">

                <div class="inline-flex w-fit items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400">
                    <div class="size-2 rounded-full bg-blue-500"></div>
                    Healthcare Administration
                </div>

                <h1 class="mt-6 text-5xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    Modern Healthcare Voucher & Transaction Management
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-8 text-zinc-600 dark:text-zinc-400">
                    Streamline medical transactions, monitor healthcare operations, manage vouchers, and improve cashier efficiency through a centralized enterprise-ready platform.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-4">

                    <a
                        href="{{ route('login') }}"
                        class="rounded-2xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        Access Dashboard
                    </a>

                    <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                        <div class="size-2 rounded-full bg-emerald-500"></div>
                        System Operational
                    </div>

                </div>

            </div>

            {{-- Right --}}
            <div class="relative">

                <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-xl dark:border-zinc-800 dark:bg-zinc-900">

                    <div class="flex items-center justify-between border-b border-zinc-200 pb-4 dark:border-zinc-800">

                        <div>
                            <h2 class="text-lg font-semibold">
                                Daily Operations
                            </h2>

                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                Healthcare performance overview
                            </p>
                        </div>

                        <div class="rounded-xl bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                            Live
                        </div>

                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">

                        <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-800 dark:bg-zinc-800/50">
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                Transactions
                            </p>

                            <p class="mt-2 text-3xl font-bold">
                                {{ number_format($summary->totalTransactions) }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-800 dark:bg-zinc-800/50">
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                Revenue
                            </p>

                            <p class="mt-2 text-3xl font-bold">
                                Rp {{ number_format($summary->totalRevenue, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-800 dark:bg-zinc-800/50">
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                Active Vouchers
                            </p>

                            <p class="mt-2 text-3xl font-bold">
                                {{ number_format($activeVouchers) }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-800 dark:bg-zinc-800/50">
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                Procedures
                            </p>

                            <p class="mt-2 text-3xl font-bold">
                                {{ number_format($summary->totalProcedures) }}
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {{-- Features --}}
    <section class="border-t border-zinc-200 bg-white py-20 dark:border-zinc-800 dark:bg-zinc-900">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="max-w-2xl">
                <h2 class="text-3xl font-bold tracking-tight">
                    Designed for Healthcare Operations
                </h2>

                <p class="mt-4 text-zinc-500 dark:text-zinc-400">
                    Built to optimize medical transaction workflows, voucher campaigns, and cashier performance monitoring.
                </p>
            </div>

            <div class="mt-12 grid gap-6 lg:grid-cols-3">

                <div class="rounded-3xl border border-zinc-200 p-6 dark:border-zinc-800">
                    <flux:icon.credit-card class="size-6 text-blue-600" />

                    <h3 class="mt-4 text-lg font-semibold">
                        Transaction Management
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-zinc-500 dark:text-zinc-400">
                        Streamline cashier workflows and healthcare billing processes efficiently.
                    </p>
                </div>

                <div class="rounded-3xl border border-zinc-200 p-6 dark:border-zinc-800">
                    <flux:icon.ticket class="size-6 text-blue-600" />

                    <h3 class="mt-4 text-lg font-semibold">
                        Voucher Management
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-zinc-500 dark:text-zinc-400">
                        Create and monitor healthcare voucher campaigns with realtime reporting.
                    </p>
                </div>

                <div class="rounded-3xl border border-zinc-200 p-6 dark:border-zinc-800">
                    <flux:icon.chart-bar class="size-6 text-blue-600" />

                    <h3 class="mt-4 text-lg font-semibold">
                        Analytics Dashboard
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-zinc-500 dark:text-zinc-400">
                        Monitor healthcare operational performance through centralized analytics.
                    </p>
                </div>

            </div>

        </div>

    </section>

</body>
</html>