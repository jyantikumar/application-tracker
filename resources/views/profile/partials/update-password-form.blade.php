<section>
    <header>
        <h2 class="text-lg font-bold text-white">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-xs text-gray-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-xs uppercase tracking-wider text-gray-400 font-bold mb-2">{{ __('Current Password *') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-xs text-rose-400" />
        </div>

        <div>
            <label for="update_password_password" class="block text-xs uppercase tracking-wider text-gray-400 font-bold mb-2">{{ __('New Password *') }}</label>
            <input id="update_password_password" name="password" type="password" class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-xs text-rose-400" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs uppercase tracking-wider text-gray-400 font-bold mb-2">{{ __('Confirm Password *') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-xs text-rose-400" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="px-6 py-2.5 text-xs font-semibold text-gray-900 bg-[#92BA56] hover:bg-[#82a74a] rounded-lg transition-colors shadow-md shadow-[#92BA56]/20">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-xs text-emerald-400">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>