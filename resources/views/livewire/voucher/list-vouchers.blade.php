<div class="w-full">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <flux:heading level="1">{{ __('Vouchers') }}</flux:heading>
            <flux:text class="mt-2 text-sm">{{ __('Manage insurance vouchers and discounts') }}</flux:text>
        </div>
        <div class="flex items-center gap-2">
            <flux:button href="{{ route('vouchers.create') }}" variant="primary" icon="plus" wire:navigate>
                {{ __('Create Voucher') }}
            </flux:button>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-col gap-4 rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-950">
        <div class="flex flex-col gap-4 md:flex-row md:items-end">
            <div class="flex-1">
                <flux:input
                    wire:model.live.debounce-500ms="search"
                    :placeholder="__('Search insurance...')"
                    icon="magnifying-glass"
                    clearable
                />
            </div>
            <flux:select wire:model.live="filterStatus" :label="__('Status')">
                <option value="all">{{ __('All') }}</option>
                <option value="active">{{ __('Active') }}</option>
                <option value="inactive">{{ __('Inactive') }}</option>
            </flux:select>
            <flux:button variant="ghost" wire:click="resetFilters" icon="arrow-path">
                {{ __('Reset') }}
            </flux:button>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-950">
        <table class="w-full text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-zinc-900 dark:text-white">
                        <button
                            wire:click="toggleSort('insurance_name')"
                            class="flex items-center gap-2 hover:text-zinc-700 dark:hover:text-zinc-300"
                        >
                            {{ __('Insurance') }}
                            @if ($sortBy === 'insurance_name')
                                <flux:icon
                                    icon="{{ $sortOrder === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                    class="h-4 w-4"
                                />
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left font-semibold text-zinc-900 dark:text-white">
                        {{ __('Type') }}
                    </th>
                    <th class="px-6 py-3 text-left font-semibold text-zinc-900 dark:text-white">
                        {{ __('Value') }}
                    </th>
                    <th class="px-6 py-3 text-left font-semibold text-zinc-900 dark:text-white">
                        {{ __('Max Discount') }}
                    </th>
                    <th class="px-6 py-3 text-left font-semibold text-zinc-900 dark:text-white">
                        {{ __('Period') }}
                    </th>
                    <th class="px-6 py-3 text-left font-semibold text-zinc-900 dark:text-white">
                        <button
                            wire:click="toggleSort('is_active')"
                            class="flex items-center gap-2 hover:text-zinc-700 dark:hover:text-zinc-300"
                        >
                            {{ __('Status') }}
                            @if ($sortBy === 'is_active')
                                <flux:icon
                                    icon="{{ $sortOrder === 'asc' ? 'arrow-up' : 'arrow-down' }}"
                                    class="h-4 w-4"
                                />
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-center font-semibold text-zinc-900 dark:text-white">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->vouchers as $voucher)
                    <tr class="border-b border-zinc-200 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-900">
                        <td class="px-6 py-4">
                            <flux:text class="font-medium">{{ $voucher->insurance_name }}</flux:text>
                            <flux:text class="text-xs text-zinc-500">{{ $voucher->insurance_id }}</flux:text>
                        </td>
                        <td class="px-6 py-4">
                            <flux:badge variant="{{ $voucher->type === 'percentage' ? 'primary' : 'info' }}">
                                {{ $voucher->type === 'percentage' ? __('Percentage') : __('Fixed') }}
                            </flux:badge>
                        </td>
                        <td class="px-6 py-4">
                            <flux:text>
                                @if ($voucher->type === 'percentage')
                                    {{ $voucher->value }}%
                                @else
                                    Rp {{ number_format($voucher->value, 0, ',', '.') }}
                                @endif
                            </flux:text>
                        </td>
                        <td class="px-6 py-4">
                            @if ($voucher->max_discount)
                                <flux:text>Rp {{ number_format($voucher->max_discount, 0, ',', '.') }}</flux:text>
                            @else
                                <flux:text class="text-zinc-400">-</flux:text>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <flux:text class="text-xs">
                                {{ $voucher->start_date->format('d M Y') }} - {{ $voucher->end_date->format('d M Y') }}
                            </flux:text>
                        </td>
                        <td class="px-6 py-4">
                            <button
                                wire:click="toggleActive({{ $voucher->id }})"
                                class="inline-flex items-center gap-2"
                            >
                                @if ($voucher->is_active)
                                    <flux:badge variant="success">{{ __('Active') }}</flux:badge>
                                @else
                                    <flux:badge variant="neutral">{{ __('Inactive') }}</flux:badge>
                                @endif
                            </button>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <flux:button
                                    href="{{ route('vouchers.edit', $voucher) }}"
                                    variant="subtle"
                                    size="sm"
                                    icon="pencil-square"
                                    wire:navigate
                                >
                                    {{ __('Edit') }}
                                </flux:button>
                                <flux:button
                                    wire:click="deleteVoucher('{{ $voucher->id }}')"
                                    wire:confirm="{{ __('Are you sure?') }}"
                                    variant="subtle"
                                    size="sm"
                                    icon="trash"
                                    class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                >
                                    {{ __('Delete') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <flux:icon icon="inbox" class="mx-auto h-12 w-12 text-zinc-400" />
                            <flux:text class="mt-2">{{ __('No vouchers found') }}</flux:text>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $this->vouchers->links() }}
    </div>
</div>
