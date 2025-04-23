<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Mật khẩu hiện tại -->
        <div>
            <x-input-label for="current_password" :value="__('Mật khẩu hiện tại')" class="text-gray-300" />
            <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full text-white bg-gray-700 border border-gray-600 focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-30 rounded-md shadow-sm" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <!-- Mật khẩu mới -->
        <div>
            <x-input-label for="password" :value="__('Mật khẩu mới')" class="text-gray-300" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full text-white bg-gray-700 border border-gray-600 focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-30 rounded-md shadow-sm" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <!-- Xác nhận mật khẩu mới -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Xác nhận mật khẩu mới')" class="text-gray-300" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full text-white bg-gray-700 border border-gray-600 focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-30 rounded-md shadow-sm" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end gap-4">
            <x-primary-button class="bg-orange-500 hover:bg-orange-600 text-gray-900 font-semibold py-2 px-4 rounded shadow-md hover:shadow-lg transition duration-200">
                {{ __('Đổi mật khẩu') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-400"
                >{{ __('Đã cập nhật mật khẩu.') }}</p>
            @endif
        </div>
    </form>
</section>
