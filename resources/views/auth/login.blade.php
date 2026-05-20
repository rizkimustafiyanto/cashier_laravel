<x-layouts::auth :title="__('Healthcare Staff Login')">

    <div class="flex flex-col gap-8">

        {{-- Header --}}
        <div class="space-y-5 text-center">

            <div class="mx-auto flex size-16 items-center justify-center rounded-3xl bg-blue-600 text-white shadow-lg shadow-blue-500/20">
                <flux:icon.heart class="size-8" />
            </div>

            <div>

                <div class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700 dark:border-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400">
                    <div class="size-2 rounded-full bg-blue-500"></div>
                    Healthcare Operations
                </div>

                <h1 class="mt-5 text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    Healthcare Staff Login
                </h1>

                <p class="mt-3 text-sm leading-6 text-zinc-500 dark:text-zinc-400">
                    Access patient billing, healthcare transactions, voucher management, and clinic operational dashboards securely.
                </p>

            </div>

        </div>

        {{-- Session Status --}}
        <x-auth-session-status
            class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400"
            :status="session('status')"
        />

        {{-- Login Form --}}
        <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">

            <form
                method="POST"
                action="{{ route('login.store') }}"
                class="flex flex-col gap-6"
            >
                @csrf

                {{-- Email --}}
                <flux:input
                    name="email"
                    :label="__('Email address')"
                    :value="old('email')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="staff@healthcare.com"
                />

                {{-- Password --}}
                <div class="space-y-2">

                    <div class="flex items-center justify-between">

                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            {{ __('Password') }}
                        </label>

                        @if (Route::has('password.request'))

                            <flux:link
                                class="text-xs font-medium text-blue-600 transition hover:text-blue-700 dark:text-blue-400"
                                :href="route('password.request')"
                                wire:navigate
                            >
                                {{ __('Forgot password?') }}
                            </flux:link>

                        @endif

                    </div>

                    <flux:input
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        :placeholder="__('Enter your password')"
                        viewable
                    />

                </div>

                {{-- Remember --}}
                <div class="flex items-center justify-between">

                    <flux:checkbox
                        name="remember"
                        :label="__('Remember me')"
                        :checked="old('remember')"
                    />

                    <div class="hidden items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400 sm:flex">
                        <div class="size-2 rounded-full bg-emerald-500"></div>
                        Secure Connection
                    </div>

                </div>

                {{-- Button --}}
                <flux:button
                    variant="primary"
                    type="submit"
                    class="h-12 w-full rounded-2xl text-sm font-semibold shadow-sm transition hover:shadow-md"
                    data-test="login-button"
                >
                    {{ __('Access Dashboard') }}
                </flux:button>

            </form>

        </div>

        {{-- Footer --}}
        <div class="text-center text-xs leading-6 text-zinc-500 dark:text-zinc-400">

            <p>
                Protected healthcare operational system.
            </p>

            <p class="mt-1">
                Authorized medical staff only.
            </p>

        </div>

    </div>

</x-layouts::auth>