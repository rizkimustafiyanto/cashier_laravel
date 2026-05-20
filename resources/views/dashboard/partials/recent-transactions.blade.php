<section
    class="overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
>

    {{-- Header --}}
    <div
        class="flex items-center justify-between border-b border-zinc-200 px-6 py-5 dark:border-zinc-700"
    >
        <div>
            <h2 class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">
                Recent Transactions
            </h2>

            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Latest patient billing activity across the clinic.
            </p>
        </div>

        <span
            class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400"
        >
            <div class="size-2 rounded-full bg-blue-500"></div>

            {{ count($recent_transactions) }} records
        </span>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">

        <table class="min-w-full table-auto divide-y divide-zinc-200 text-sm dark:divide-zinc-700">

            {{-- Table Head --}}
            <thead class="bg-zinc-50 dark:bg-zinc-800/50">

                <tr class="text-left">

                    <th
                        class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        Invoice
                    </th>

                    <th
                        class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        Patient Info
                    </th>

                    <th
                        class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        Cashier
                    </th>

                    <th
                        class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        Status
                    </th>

                    <th
                        class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        Grand Total
                    </th>

                    <th
                        class="px-6 py-4 text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400"
                    >
                        Created At
                    </th>

                </tr>

            </thead>

            {{-- Table Body --}}
            <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-800 dark:bg-zinc-900">

                @forelse ($recent_transactions as $transaction)

                    <tr
                        class="transition duration-200 hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                    >

                        {{-- Invoice --}}
                        <td class="whitespace-nowrap px-6 py-4">

                            <span
                                class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-400"
                            >
                                {{ $transaction->invoice_number }}
                            </span>

                        </td>

                        {{-- Patient --}}
                        <td class="px-6 py-4 whitespace-nowrap">

                            <div class="space-y-1">

                                <p class="font-medium text-zinc-900 dark:text-white">
                                    {{ $transaction->patient_name }}
                                </p>

                                <div
                                    class="flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400"
                                >

                                    <span>
                                        {{ \Carbon\Carbon::parse($transaction->patient_dob)->age }}
                                        years
                                    </span>

                                    <span>•</span>

                                    <span class="capitalize">
                                        {{ $transaction->patient_gender }}
                                    </span>

                                </div>

                            </div>

                        </td>

                        {{-- Cashier --}}
                        <td class="px-6 py-4 whitespace-nowrap">

                            <p class="text-zinc-600 dark:text-zinc-300">
                                {{ $transaction->cashier?->name ?? 'Unassigned' }}
                            </p>

                        </td>

                        {{-- Status --}}
                        <td class="whitespace-nowrap px-6 py-4">

                            @php

                                $statusClasses = match($transaction->status?->value) {

                                    'paid' => '
                                        border-emerald-200
                                        bg-emerald-50
                                        text-emerald-700
                                        dark:border-emerald-500/20
                                        dark:bg-emerald-500/10
                                        dark:text-emerald-400
                                    ',

                                    'cancelled' => '
                                        border-red-200
                                        bg-red-50
                                        text-red-700
                                        dark:border-red-500/20
                                        dark:bg-red-500/10
                                        dark:text-red-400
                                    ',

                                    default => '
                                        border-amber-200
                                        bg-amber-50
                                        text-amber-700
                                        dark:border-amber-500/20
                                        dark:bg-amber-500/10
                                        dark:text-amber-400
                                    ',
                                };

                            @endphp

                            <span
                                class="
                                    inline-flex items-center gap-2 rounded-full border
                                    px-3 py-1 text-xs font-semibold
                                    {{ $statusClasses }}
                                "
                            >

                                <div class="size-2 rounded-full bg-current"></div>

                                {{ ucfirst($transaction->status->value ?? 'draft') }}

                            </span>

                        </td>

                        {{-- Total --}}
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm font-bold text-zinc-900 dark:text-white"
                        >
                            Rp {{ number_format((float) $transaction->grand_total, 0, ',', '.') }}
                        </td>

                        {{-- Created At --}}
                        <td
                            class="whitespace-nowrap px-6 py-4 text-sm text-zinc-500 dark:text-zinc-400"
                        >
                            {{ $transaction->created_at?->timezone(auth()->user()?->timezone ?? config('app.timezone'))->format('d M Y, H:i') ?? '-' }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="px-6 py-16 text-center"
                        >

                            <div class="flex flex-col items-center justify-center">

                                <div
                                    class="mb-4 rounded-2xl bg-zinc-100 p-4 dark:bg-zinc-800"
                                >
                                    <flux:icon.clipboard-document
                                        class="size-8 text-zinc-400"
                                    />
                                </div>

                                <h3
                                    class="text-sm font-semibold text-zinc-900 dark:text-white"
                                >
                                    No recent transactions
                                </h3>

                                <p
                                    class="mt-1 text-sm text-zinc-500 dark:text-zinc-400"
                                >
                                    Transaction activity will appear here.
                                </p>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</section>