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

    <form wire:submit="login">
        <!-- Email Address -->
        <div style="margin-bottom: 20px;">
            <label for="email" style="color: #cbd5e1; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; display: block;">{{ __('Email') }}</label>
            <input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username" 
                   style="background-color: #020617; border: 1px solid #1e293b; color: #f8fafc; padding: 12px 16px; border-radius: 12px; width: 100%; transition: all 0.2s; outline: none;"
                   onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 2px rgba(99, 102, 241, 0.2)';"
                   onblur="this.style.borderColor='#1e293b'; this.style.boxShadow='none';" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-rose-400" />
        </div>

        <!-- Password -->
        <div style="margin-bottom: 20px;">
            <label for="password" style="color: #cbd5e1; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; display: block;">{{ __('Password') }}</label>
            <input wire:model="form.password" id="password" type="password" name="password" required autocomplete="current-password" 
                   style="background-color: #020617; border: 1px solid #1e293b; color: #f8fafc; padding: 12px 16px; border-radius: 12px; width: 100%; transition: all 0.2s; outline: none;"
                   onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 2px rgba(99, 102, 241, 0.2)';"
                   onblur="this.style.borderColor='#1e293b'; this.style.boxShadow='none';" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2 text-rose-400" />
        </div>

        <!-- Remember Me -->
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 24px;">
            <input wire:model="form.remember" id="remember" type="checkbox" name="remember" 
                   style="width: 16px; height: 16px; accent-color: #6366f1; cursor: pointer;" />
            <label for="remember" style="color: #94a3b8; font-size: 0.875rem; font-weight: 500; cursor: pointer; user-select: none;"
                   onmouseover="this.style.color='#cbd5e1'"
                   onmouseout="this.style.color='#94a3b8'">
                {{ __('Remember me') }}
            </label>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 8px;">
            <a href="{{ route('register') }}" wire:navigate 
               style="color: #94a3b8; text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s;"
               onmouseover="this.style.color='#f8fafc'"
               onmouseout="this.style.color='#94a3b8'">
                {{ __('Need an account?') }}
            </a>

            <button type="submit" 
                    style="background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%); border: 0; padding: 10px 20px; border-radius: 12px; color: #ffffff; font-size: 0.875rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35); transition: all 0.2s;"
                    onmouseover="this.style.opacity='0.95'; this.style.transform='translateY(-1px)';"
                    onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)';">
                {{ __('Log in') }}
            </button>
        </div>
    </form>
</div>
