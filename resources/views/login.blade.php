@extends('layouts.gov')

@section('title', 'LOGIN | GOVERNMENT RECRUITMENT PORTAL')
@section('meta_description', 'Secure candidate login for the recruitment portal.')

@section('content')
    <section class="mx-auto max-w-lg">
        <x-official.form title="Candidate Login" description="Enter registered credentials to continue">
            @if ($errors->any())
                <div class="border border-[var(--gov-danger)] bg-red-50 px-3 py-2 text-xs text-[var(--gov-danger)]" role="alert">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4" novalidate>
                @csrf

                <x-official.form-field
                    name="email"
                    label="Email Address"
                    type="email"
                    :required="true"
                    autocomplete="username"
                />

                <x-official.form-field
                    name="password"
                    label="Password"
                    type="password"
                    :required="true"
                    autocomplete="current-password"
                />

                <div>
                    <label for="captcha" class="mb-1 block text-sm font-semibold text-slate-800">
                        Captcha
                        <span class="text-[var(--gov-danger)]" aria-hidden="true">*</span>
                    </label>
                    <div class="mb-2 border border-[var(--gov-border)] bg-slate-100 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-slate-600">
                        Captcha Placeholder
                    </div>
                    <input
                        id="captcha"
                        name="captcha"
                        type="text"
                        required
                        class="w-full border border-[var(--gov-border)] px-3 py-2 text-sm outline-none focus:border-[var(--gov-navy)] focus:ring-1 focus:ring-[var(--gov-navy)]"
                        placeholder="Enter Captcha"
                        aria-label="Captcha placeholder input"
                    >
                </div>

                <label for="remember" class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input
                        id="remember"
                        name="remember"
                        type="checkbox"
                        value="1"
                        @checked(old('remember'))
                        class="border-[var(--gov-border)] text-[var(--gov-navy)] focus:ring-[var(--gov-navy)]"
                    >
                    Remember me
                </label>

                <button type="submit" class="w-full border border-[var(--gov-navy)] bg-[var(--gov-navy)] px-4 py-2 text-sm font-semibold uppercase tracking-wide text-white hover:bg-[#0d243f]">
                    Login
                </button>
            </form>

            <div class="flex items-center justify-between border-t border-[var(--gov-border)] pt-3 text-xs">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="font-semibold text-[var(--gov-navy)] hover:underline">
                        Forgot Password
                    </a>
                @endif
                <a href="{{ route('register') }}" class="font-semibold text-[var(--gov-maroon)] hover:underline">
                    Register
                </a>
            </div>
        </x-official.form>
    </section>
@endsection
