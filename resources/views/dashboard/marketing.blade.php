@php
    use Illuminate\Support\Str;

    $topProcedure = $top_procedures->first();
    $voucherSeries = $voucher_usage->pluck('total_usage')->map(fn ($value) => (int) $value)->values();
    $voucherLabels = $voucher_usage->pluck('voucher_type')->map(fn ($value) => Str::headline((string) $value))->values();
@endphp

<x-layouts::app :title="__('Marketing Dashboard')">

    <div class="min-h-screen bg-zinc-100/60 dark:bg-zinc-950">

        @include('dashboard.partials.hero')

        <div class="px-4 py-6 sm:px-6 lg:px-8 space-y-6">

            @include('dashboard.partials.summary-cards')

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(320px,1fr)]">

                @include('dashboard.partials.recent-transactions')

                <div class="space-y-6">
                    @include('dashboard.partials.top-insurances')
                    @include('dashboard.partials.voucher-usage')
                    @include('dashboard.partials.procedure-snapshot')
                </div>

            </div>

        </div>

    </div>

</x-layouts::app>