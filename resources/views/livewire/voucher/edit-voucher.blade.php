<div class="w-full">
    <div class="mb-8 flex items-center gap-4">
        <flux:button
            href="{{ route('vouchers.index') }}"
            variant="ghost"
            size="sm"
            icon="arrow-left"
            wire:navigate
        >
            {{ __('Back') }}
        </flux:button>
        <div>
            <flux:heading level="1">{{ __('Edit Voucher') }}</flux:heading>
            <flux:text class="mt-2 text-sm">{{ __('Update voucher details') }}</flux:text>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <!-- Form -->
        <div class="md:col-span-2 space-y-6 rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-950">
            <form wire:submit="update" class="space-y-6">
                <!-- Insurance Selection -->
                <div>
                    <flux:select
                        wire:model.live="insuranceId"
                        :label="__('Insurance')"
                        required
                    >
                        <option value="">{{ __('Select Insurance') }}</option>
                        @foreach ($insuranceOptions as $option)
                            <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                        @endforeach
                    </flux:select>
                    @error('insuranceId')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>

                <!-- Discount Type -->
                <div>
                    <flux:radio.group wire:model.live="type" variant="segmented" :label="__('Discount Type')" required>
                        <flux:radio value="percentage">{{ __('Percentage (%)') }}</flux:radio>
                        <flux:radio value="fixed">{{ __('Fixed Amount (Rp)') }}</flux:radio>
                    </flux:radio.group>
                    @error('type')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>

                <!-- Discount Value -->
                <div>
                    <flux:input
                        wire:model="value"
                        :label="__('Discount Value')"
                        type="number"
                        step="0.01"
                        min="0"
                        :placeholder="$type === 'percentage' ? __('e.g., 10') : __('e.g., 50000')"
                        required
                    />
                    @error('value')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>

                <!-- Max Discount -->
                <div>
                    <flux:input
                        wire:model="maxDiscount"
                        :label="__('Maximum Discount (Optional)')"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="e.g., 500000"
                    />
                    <flux:text class="mt-1 text-xs text-zinc-500">
                        {{ __('Applies to percentage discounts to limit maximum deduction') }}
                    </flux:text>
                    @error('maxDiscount')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>

                <!-- Start Date -->
                <div>
                    <flux:input
                        wire:model="startDate"
                        :label="__('Start Date')"
                        type="date"
                        required
                    />
                    @error('startDate')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>

                <!-- End Date -->
                <div>
                    <flux:input
                        wire:model="endDate"
                        :label="__('End Date')"
                        type="date"
                        required
                    />
                    @error('endDate')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>

                <!-- Active Status -->
                <div>
                    <flux:checkbox
                        wire:model="isActive"
                        :label="__('Voucher is active')"
                    />
                    @error('isActive')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="flex gap-3 pt-4">
                    <flux:button type="submit" variant="primary">
                        {{ __('Save Changes') }}
                    </flux:button>
                    <flux:button
                        href="{{ route('vouchers.index') }}"
                        variant="ghost"
                        wire:navigate
                    >
                        {{ __('Cancel') }}
                    </flux:button>
                </div>
            </form>
        </div>

        <!-- Info & Preview -->
        <div class="space-y-4">
            <!-- Metadata -->
            <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading level="3" class="mb-4">{{ __('Information') }}</flux:heading>

                <div class="space-y-3 text-xs">
                    <div>
                        <flux:text class="uppercase tracking-wide text-zinc-500">
                            {{ __('Created') }}
                        </flux:text>
                        <flux:text>{{ $voucher->created_at->timezone(auth()->user()?->timezone ?? config('app.timezone'))->format('d M Y H:i') }}</flux:text>
                    </div>

                    <div class="border-t border-zinc-200 pt-3 dark:border-zinc-700">
                        <flux:text class="uppercase tracking-wide text-zinc-500">
                            {{ __('Last Updated') }}
                        </flux:text>
                        <flux:text>{{ $voucher->updated_at->timezone(auth()->user()?->timezone ?? config('app.timezone'))->format('d M Y H:i') }}</flux:text>
                    </div>

                    @if ($voucher->creator)
                        <div class="border-t border-zinc-200 pt-3 dark:border-zinc-700">
                            <flux:text class="uppercase tracking-wide text-zinc-500">
                                {{ __('Created By') }}
                            </flux:text>
                            <flux:text>{{ $voucher->creator->name }}</flux:text>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Preview -->
            <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading level="3" class="mb-4">{{ __('Preview') }}</flux:heading>

                <div class="space-y-3">
                    <div>
                        <flux:text class="text-xs uppercase tracking-wide text-zinc-500">
                            {{ __('Insurance') }}
                        </flux:text>
                        <flux:text class="font-medium">
                            {{ $insuranceName ?: __('Select insurance') }}
                        </flux:text>
                    </div>

                    <div class="border-t border-zinc-200 pt-3 dark:border-zinc-700">
                        <flux:text class="text-xs uppercase tracking-wide text-zinc-500">
                            {{ __('Discount') }}
                        </flux:text>
                        <flux:text class="text-lg font-bold text-primary-600 dark:text-primary-400">
                            @if ($value)
                                @if ($type === 'percentage')
                                    {{ $value }}%
                                @else
                                    Rp {{ number_format($value, 0, ',', '.') }}
                                @endif
                            @else
                                -
                            @endif
                        </flux:text>
                    </div>

                    @if ($maxDiscount && $type === 'percentage')
                        <div class="border-t border-zinc-200 pt-3 dark:border-zinc-700">
                            <flux:text class="text-xs uppercase tracking-wide text-zinc-500">
                                {{ __('Max Cap') }}
                            </flux:text>
                            <flux:text class="font-medium">
                                Rp {{ number_format($maxDiscount, 0, ',', '.') }}
                            </flux:text>
                        </div>
                    @endif

                    <div class="border-t border-zinc-200 pt-3 dark:border-zinc-700">
                        <flux:text class="text-xs uppercase tracking-wide text-zinc-500">
                            {{ __('Period') }}
                        </flux:text>
                        <flux:text class="text-sm">
                            @if ($startDate && $endDate)
                                {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                            @else
                                -
                            @endif
                        </flux:text>
                    </div>

                    <div class="border-t border-zinc-200 pt-3 dark:border-zinc-700">
                        <flux:text class="text-xs uppercase tracking-wide text-zinc-500">
                            {{ __('Status') }}
                        </flux:text>
                        @if ($isActive)
                            <flux:badge variant="success">{{ __('Active') }}</flux:badge>
                        @else
                            <flux:badge variant="neutral">{{ __('Inactive') }}</flux:badge>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
