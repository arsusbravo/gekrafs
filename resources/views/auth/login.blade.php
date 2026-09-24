<x-layouts.site :title="__('Log in')">
    <x-site.auth-card :title="__('Log in')" :subtitle="__('Welcome back! Please enter your details.')">
        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-input">
                @error('email') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="form-label">{{ __('Password') }}</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="form-input">
                @error('password') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-600">
                {{ __('Remember me') }}
            </label>
            <button type="submit" class="btn btn-primary w-full py-3">{{ __('Log in') }}</button>
        </form>
    </x-site.auth-card>
</x-layouts.site>
