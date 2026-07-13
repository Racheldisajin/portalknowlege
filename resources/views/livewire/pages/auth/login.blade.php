<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-slate-300 font-semibold" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1.5 w-full bg-slate-950 border-slate-800 focus:border-indigo-500 focus:ring-indigo-500 text-slate-100 rounded-xl px-4 py-2.5 shadow-sm transition" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-rose-400" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-slate-300 font-semibold" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1.5 w-full bg-slate-950 border-slate-800 focus:border-indigo-500 focus:ring-indigo-500 text-slate-100 rounded-xl px-4 py-2.5 shadow-sm transition"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2 text-rose-400" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember" class="inline-flex items-center cursor-pointer select-none">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-slate-800 bg-slate-950 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-offset-slate-900" name="remember">
                <span class="ms-2.5 text-sm text-slate-400 font-medium hover:text-slate-300 transition">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between pt-2">
            <a class="text-sm text-slate-400 hover:text-white transition duration-150 font-medium" href="{{ route('register') }}" wire:navigate>
                {{ __('Need an account?') }}
            </a>

            <x-primary-button class="bg-gradient-to-r from-indigo-500 to-cyan-500 hover:from-indigo-600 hover:to-cyan-600 transition duration-200 rounded-xl px-5 py-2.5 text-sm font-semibold shadow-lg text-white border-0">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</div>
