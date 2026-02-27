<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-300">
        Enter the 6-digit OTP sent to your email to complete login.
    </div>

    <form method="POST" action="{{ route('two-factor.verify') }}">
        @csrf

        <div>
            <x-input-label for="otp" :value="__('One Time Password')" />
            <x-text-input
                id="otp"
                class="block mt-1 w-full"
                type="text"
                name="otp"
                :value="old('otp')"
                required
                autofocus
                maxlength="6"
                autocomplete="one-time-code"
            />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="mt-4 flex items-center justify-between">
            <x-primary-button>
                {{ __('Verify OTP') }}
            </x-primary-button>
        </div>
    </form>

    <form method="POST" action="{{ route('two-factor.resend') }}" class="mt-4">
        @csrf
        <button type="submit" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
            Resend OTP
        </button>
    </form>
</x-guest-layout>
