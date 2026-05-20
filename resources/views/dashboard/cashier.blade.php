<x-layouts::app :title="__('Cashier Dashboard')">
    <div class="min-h-screen bg-zinc-100/60 dark:bg-zinc-950">
        
        {{-- Hero Section --}}
        <div class="border-b border-zinc-200 bg-white/80 backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/80">
            <div class="flex flex-col gap-5 px-4 py-6 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400">
                        <div class="size-2 rounded-full bg-blue-500"></div>
                        Cashier Operations
                    </div>
                    <h1 class="mt-3 text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                        Transaction Dashboard
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-500 dark:text-zinc-400">
                        View transaction history and manage patient billing records.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('transactions.create') }}" class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        + Create Transaction
                    </a>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="px-4 py-6 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 rounded-full bg-emerald-100 p-2 text-emerald-600">
                            <flux:icon.check-circle class="size-4" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-emerald-900">Transaction saved</p>
                            <p class="mt-1 text-sm text-emerald-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 rounded-full bg-red-100 p-2 text-red-600">
                            <flux:icon.x-circle class="size-4" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-red-900">Error saving transaction</p>
                            <p class="mt-1 text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            {{-- Transaction History --}}
            <section class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-5 dark:border-zinc-700">
                    <div>
                        <h2 class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">
                            Transaction History
                        </h2>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            Recent patient billing transactions. Click to view details.
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400">
                        <div class="size-2 rounded-full bg-blue-500"></div>
                        {{ $transactions->total() }} records
                    </span>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                        {{-- Table Head --}}
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                            <tr class="text-left">
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Invoice</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Patient</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Cashier</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Total</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Date</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Action</th>
                            </tr>
                        </thead>

                        {{-- Table Body --}}
                        <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-800 dark:bg-zinc-900">
                            @forelse ($transactions as $transaction)
                                <tr class="transition duration-200 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
                                            {{ $transaction->invoice_number }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ $transaction->patient_name }}</p>
                                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $transaction->patient_email }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-zinc-600 dark:text-zinc-300">{{ $transaction->cashier?->name ?? 'Unassigned' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-bold text-zinc-900 dark:text-white">
                                        Rp {{ number_format((float) $transaction->grand_total, 0, ',', '.') }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $transaction->created_at?->timezone(auth()->user()?->timezone ?? config('app.timezone'))->format('d M Y, H:i') ?? '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if($transaction->paid_at)
                                            <a href="{{ route('transactions.invoice', $transaction) }}" class="inline-flex items-center rounded-lg bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:hover:bg-emerald-500/20">
                                                View Invoice
                                            </a>
                                        @else
                                            <div class="flex flex-wrap gap-2">
                                                <a href="{{ route('transactions.edit', $transaction) }}" class="inline-flex items-center rounded-lg bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 transition hover:bg-sky-100 dark:bg-sky-500/10 dark:text-sky-400 dark:hover:bg-sky-500/20">
                                                    Edit
                                                </a>

                                                <form method="POST" action="{{ route('transactions.pay', $transaction) }}" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:hover:bg-amber-500/20">
                                                        Mark as Paid
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="mb-4 rounded-2xl bg-zinc-100 p-4 dark:bg-zinc-800">
                                                <flux:icon.clipboard-document class="size-8 text-zinc-400" />
                                            </div>
                                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">No transactions yet</h3>
                                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Start by creating your first transaction</p>
                                            <a href="{{ route('transactions.create') }}" class="mt-4 inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                                                Create Transaction
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($transactions->hasPages())
                    <div class="border-t border-zinc-200 bg-zinc-50 px-6 py-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-layouts::app>
