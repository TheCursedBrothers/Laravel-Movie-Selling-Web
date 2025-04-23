<x-guest-layout>
    <h1 class="text-2xl font-bold text-center text-white mb-6">Đăng ký tài khoản</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Họ tên')" class="text-gray-300" />
            <x-text-input id="name" class="block mt-1 w-full text-white bg-gray-700 border border-gray-600 focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-30 rounded-md shadow-sm" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" class="text-gray-300" />
            <x-text-input id="email" class="block mt-1 w-full text-white bg-gray-700 border border-gray-600 focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-30 rounded-md shadow-sm" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mật khẩu')" class="text-gray-300" />

            <x-text-input id="password" class="block mt-1 w-full text-white bg-gray-700 border border-gray-600 focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-30 rounded-md shadow-sm"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Xác nhận mật khẩu')" class="text-gray-300" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full text-white bg-gray-700 border border-gray-600 focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-30 rounded-md shadow-sm"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-400 hover:text-orange-500 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500" href="{{ route('login') }}">
                {{ __('Đã có tài khoản?') }}
            </a>

            <x-primary-button class="ml-4 bg-orange-500 hover:bg-orange-600 text-gray-900 font-semibold py-2 px-4 rounded shadow-md hover:shadow-lg transition duration-200">
                {{ __('Đăng ký') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
