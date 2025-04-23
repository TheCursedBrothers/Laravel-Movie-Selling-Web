<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="text-2xl font-bold text-center text-white mb-6">Đăng nhập</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-300" />
            <x-text-input id="email" class="block mt-1 w-full text-white bg-gray-700 border border-gray-600 focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-30 rounded-md shadow-sm"
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mật khẩu')" class="text-gray-300" />

            <x-text-input id="password" class="block mt-1 w-full text-white bg-gray-700 border border-gray-600 focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-30 rounded-md shadow-sm"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded bg-gray-700 border-gray-600 text-orange-500 shadow-sm focus:ring-orange-500" name="remember">
                <span class="ml-2 text-sm text-gray-300">{{ __('Ghi nhớ đăng nhập') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-400 hover:text-orange-500 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500" href="{{ route('password.request') }}">
                    {{ __('Quên mật khẩu?') }}
                </a>
            @endif

            <x-primary-button class="ml-3 bg-orange-500 hover:bg-orange-600 text-gray-900 font-semibold py-2 px-4 rounded shadow-md hover:shadow-lg transition duration-200">
                {{ __('Đăng nhập') }}
            </x-primary-button>
        </div>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-400">
                {{ __('Chưa có tài khoản?') }}
                <a href="{{ route('register') }}" class="text-orange-500 hover:text-orange-400 font-medium">
                    {{ __('Đăng ký ngay') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
