<div class="min-h-screen bg-zinc-100/60 dark:bg-zinc-950">
    <div class="sticky top-0 z-20 border-b border-zinc-200/80 bg-white/80 backdrop-blur-xl dark:border-zinc-800 dark:bg-zinc-900/80">
        <div class="px-4 py-5 sm:px-6 lg:px-8">
            <div class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400">
                <div class="size-2 rounded-full bg-blue-500"></div>
                Healthcare Billing
            </div>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight text-zinc-900 dark:text-white">
                {{ $transaction ? __('Edit Transaction') : __('Create Transaction') }}
            </h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                {{ $transaction ? __('Update patient billing before payment.') : __('Record patient billing with fast procedure search and live totals.') }}
            </p>
        </div>
    </div>

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

        <div
            wire:loading.flex
            wire:target="insuranceId,items.*,save,addItem,removeItem"
            class="mb-6 hidden items-center gap-3 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700 shadow-sm"
        >
            <div class="size-4 animate-spin rounded-full border-2 border-blue-200 border-t-blue-600"></div>
            <span>Updating transaction details...</span>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-6">
                <section class="rounded-3xl border border-zinc-200/70 bg-white/90 p-6 shadow-sm backdrop-blur transition-all duration-200 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900/90">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Patient Information</h2>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Capture core demographic details before billing.</p>
                        </div>
                        <span
                            class="
                                rounded-full px-3 py-1 text-xs font-semibold
                                {{ $this->isReady
                                    ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'
                                    : 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400'
                                }}
                            "
                        >
                            {{ $this->isReady
                                ? 'Ready to submit'
                                : 'Required fields incomplete'
                            }}
                        </span>
                    </div>

                    <div class="mt-6 grid gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="patient_name" class="mb-2 block text-sm font-medium text-gray-700">Patient Name</label>
                            <input
                                id="patient_name"
                                type="text"
                                wire:model.live="patientName"
                                class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none transition-all duration-200 placeholder:text-zinc-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder:text-zinc-500"
                                placeholder="Enter patient full name"
                            >
                            @error('patientName')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="patient_email" class="mb-2 block text-sm font-medium text-gray-700">Email</label>
                            <input
                                id="patient_email"
                                type="email"
                                wire:model.live="patientEmail"
                                class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none transition-all duration-200 placeholder:text-zinc-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder:text-zinc-500"
                                placeholder="patient@email.com"
                            >
                            @error('patientEmail')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="patient_phone" class="mb-2 block text-sm font-medium text-gray-700">Phone</label>
                            <input
                                id="patient_phone"
                                type="text"
                                wire:model.live="patientPhone"
                                class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none transition-all duration-200 placeholder:text-zinc-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder:text-zinc-500"
                                placeholder="08xxxxxxxxxx"
                            >
                            @error('patientPhone')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="patient_gender" class="mb-2 block text-sm font-medium text-gray-700">Gender</label>
                            <select
                                id="patient_gender"
                                wire:model.live="patientGender"
                                class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                            >
                                <option value="">Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            @error('patientGender')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="patient_dob" class="mb-2 block text-sm font-medium text-gray-700">Date of Birth</label>
                            <input
                                id="patient_dob"
                                type="date"
                                wire:model.live="patientDob"
                                class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none transition-all duration-200 placeholder:text-zinc-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder:text-zinc-500"
                            >
                            @error('patientDob')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="insurance_id" class="mb-2 block text-sm font-medium text-gray-700">Insurance</label>
                            <select
                                id="insurance_id"
                                wire:model.live="insuranceId"
                                wire:change="recalculateTotals"
                                class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                            >
                                <option value="">Non Insurance</option>
                                @foreach ($insurance as $insuranceOption)
                                    <option value="{{ data_get($insuranceOption, 'id') }}">
                                        {{ data_get($insuranceOption, 'name', __('Unnamed insurer')) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('insuranceId')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm transition dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Procedure Items</h2>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Use searchable procedure selection for fast billing input.</p>
                        </div>

                        <button
                            type="button"
                            wire:click="addItem"
                            wire:loading.attr="disabled"
                            wire:target="addItem,removeItem,save"
                            class="inline-flex items-center justify-center rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-100 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400"
                        >
                            <span wire:loading.remove wire:target="addItem">Add Procedure</span>
                            <span wire:loading wire:target="addItem">Adding...</span>
                        </button>
                    </div>

                    @if (empty($procedures))
                        <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                            Procedure data is not available right now. Please verify the API connection before submitting a transaction.
                        </div>
                    @endif

                    <div class="mt-6 space-y-4">
                        <div wire:loading.block wire:target="insuranceId,items.*" class="space-y-4">
                            @for ($i = 0; $i < max(count($items), 1); $i++)
                                <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm transition dark:border-zinc-700 dark:bg-zinc-900">
                                    <div class="h-5 w-32 animate-pulse rounded bg-gray-200"></div>
                                    <div class="mt-4 h-12 animate-pulse rounded-xl bg-zinc-100 dark:bg-zinc-800"></div>
                                    <div class="mt-5 grid gap-4 md:grid-cols-3">
                                        <div class="h-20 animate-pulse rounded-xl bg-zinc-100 dark:bg-zinc-800"></div>
                                        <div class="h-20 animate-pulse rounded-xl bg-zinc-100 dark:bg-zinc-800"></div>
                                        <div class="h-20 animate-pulse rounded-xl bg-zinc-100 dark:bg-zinc-800"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <div wire:loading.remove wire:target="insuranceId,items.*" class="space-y-4">
                        @foreach ($items as $index => $item)
                            <div wire:key="transaction-item-{{ $index }}"
                            class="relative overflow-hidden rounded-3xl border border-zinc-200/70 bg-white/90 p-6 shadow-sm transition-all duration-200 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900/90"
                            >
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between gap-3">
                                            <label class="block text-sm font-medium text-gray-700">
                                                Procedure {{ $index + 1 }}
                                            </label>

                                            @if (count($items) > 1)
                                                <button
                                                    type="button"
                                                    wire:click="removeItem({{ $index }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="removeItem,save"
                                                    class="inline-flex items-center justify-center rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-100 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400"
                                                >
                                                    Remove
                                                </button>
                                            @endif
                                        </div>

                                        <div class="mt-2">
                                            <select
                                                wire:model.live="items.{{ $index }}.procedure_id"
                                                wire:change="recalculateTotals"
                                                data-procedure-select
                                                data-model="items.{{ $index }}.procedure_id"
                                                data-selected="{{ $item['procedure_id'] }}"
                                                class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none transition-all duration-200 placeholder:text-zinc-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                                                placeholder="Search procedure..."
                                            >
                                                <option value="">Select procedure</option>
                                                @foreach ($procedures as $procedure)
                                                    <option
                                                        value="{{ data_get($procedure, 'id') }}"
                                                        @selected($item['procedure_id'] === data_get($procedure, 'id'))
                                                    >
                                                        {{ data_get($procedure, 'name', __('Unnamed procedure')) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        @error('items.'.$index.'.procedure_id')
                                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-5 grid gap-4 md:grid-cols-3">
                                    <div class="rounded-2xl border border-zinc-200 bg-zinc-50/80 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Subtotal</p>
                                        <p class="mt-2 text-lg font-semibold text-zinc-900 dark:text-white">Rp {{ number_format((float) data_get($item, 'price', 0), 0, ',', '.') }}</p>
                                    </div>

                                    <div class="rounded-2xl border border-red-200 bg-red-50 p-4 dark:border-red-500/20 dark:bg-red-500/10">
                                        <p class="text-xs font-medium uppercase tracking-wide text-red-500">Discount</p>
                                        <p class="mt-2 text-lg font-semibold text-red-600">Rp {{ number_format((float) data_get($item, 'discount', 0), 0, ',', '.') }}</p>
                                    </div>

                                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/10">
                                        <p class="text-xs font-medium uppercase tracking-wide text-emerald-600">Final</p>
                                        <p class="mt-2 text-lg font-semibold text-emerald-700">Rp {{ number_format((float) data_get($item, 'final_price', 0), 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    </div>
                </section>
            </div>

            <aside class="xl:sticky xl:top-6 xl:self-start">
                <section class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
                    <div>
                        <p class="text-sm font-medium text-blue-600">Sticky Summary</p>
                        <h2 class="mt-1 text-lg font-semibold text-zinc-900 dark:text-white">Transaction Summary</h2>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Totals update automatically when procedures or insurance change.</p>
                    </div>

                    <div class="h-2 bg-linear-to-r from-blue-500 via-cyan-400 to-emerald-400"></div>

                    <div class="p-6">
                        <div class="mt-6 space-y-4">
                            <div wire:loading.remove wire:target="insuranceId,items.*" class="space-y-4">
                            <div class="rounded-2xl border border-zinc-200 bg-zinc-50/80 p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm text-zinc-500 dark:text-zinc-400">Subtotal</span>
                                    <span class="text-base font-semibold text-zinc-900 dark:text-white">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-red-200 bg-red-50 dark:border-red-500/20 dark:bg-red-500/10 p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm text-red-500">Discount</span>
                                    <span class="text-base font-semibold text-red-600">Rp {{ number_format($discountTotal, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 dark:border-emerald-500/20 dark:bg-emerald-500/10 p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm text-emerald-600">Grand Total</span>
                                    <span class="text-xl font-semibold text-emerald-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            </div>

                            <div wire:loading.block wire:target="insuranceId,items.*" class="space-y-4">
                                <div class="h-16 animate-pulse rounded-xl bg-zinc-100 dark:bg-zinc-800"></div>
                                <div class="h-16 animate-pulse rounded-xl bg-zinc-100 dark:bg-zinc-800"></div>
                                <div class="h-16 animate-pulse rounded-xl bg-zinc-100 dark:bg-zinc-800"></div>
                            </div>
                        </div>

                        <div class="mt-6 rounded-2xl border border-zinc-200 bg-white text-sm text-zinc-900 shadow-sm outline-none transition-all duration-200 placeholder:text-zinc-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:placeholder:text-zinc-500 p-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-zinc-500 dark:text-zinc-400">Selected Procedures</span>
                                <span class="font-semibold text-zinc-900 dark:text-white">{{ collect($items)->filter(fn ($item) => filled($item['procedure_id']))->count() }}</span>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-sm">
                                <span class="text-zinc-500 dark:text-zinc-400">Coverage</span>
                                <span class="font-semibold text-zinc-900 dark:text-white">{{ $insuranceId ? 'Insurance Applied' : 'Self Pay' }}</span>
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="save"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-linear-to-r from-blue-600 to-cyan-500 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/20 transition-all duration-200 hover:-translate-y-px hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-60"
                            @disabled(!$this->isReady)
                        >
                            <span wire:loading.remove wire:target="save">{{ $transaction ? __('Update Transaction') : __('Save Transaction') }}</span>
                            <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                                <span class="size-4 animate-spin rounded-full border-2 border-blue-200 border-t-white"></span>
                                {{ $transaction ? __('Updating...') : __('Saving...') }}
                            </span>
                        </button>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    @script
            <script>
            const initProcedureSelects = () => {
                document.querySelectorAll('[data-procedure-select]').forEach((select) => {
                    select.classList.add('w-full', 'rounded-2xl', 'border', 'border-zinc-200', 'bg-white', 'px-4', 'py-3', 'text-sm', 'text-zinc-900', 'shadow-sm', 'outline-none', 'transition-all', 'duration-200', 'focus:border-blue-500', 'focus:ring-4', 'focus:ring-blue-500/10', 'dark:border-zinc-700', 'dark:bg-zinc-800', 'dark:text-white');

                    const selectedValue = select.dataset.selected ?? '';
                    if (selectedValue && select.value !== selectedValue) {
                        select.value = selectedValue;
                    }
                });
            };

            queueMicrotask(initProcedureSelects);

            document.addEventListener('livewire:navigated', initProcedureSelects);

            Livewire.hook('morph.updated', () => {
                queueMicrotask(initProcedureSelects);
            });
        </script>
    @endscript
</div>
