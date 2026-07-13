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
            <label for="email" style="color: #cbd5e1; font-weight: 600; font-size: 0.875rem; margin-bottom: 8px; display: block; padding-left: 4px;">Email</label>
            <input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username" 
                   placeholder="Enter your email"
                   style="background-color: rgba(2, 6, 23, 0.6); border: 1px solid #1e293b; color: #f8fafc; padding: 14px 20px; border-radius: 9999px; width: 100%; transition: all 0.2s; outline: none; font-size: 0.95rem;"
                   onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 2px rgba(99, 102, 241, 0.25)';"
                   onblur="this.style.borderColor='#1e293b'; this.style.boxShadow='none';" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-rose-400" style="padding-left: 12px;" />
        </div>

        <!-- Password -->
        <div style="margin-bottom: 20px;">
            <label for="password" style="color: #cbd5e1; font-weight: 600; font-size: 0.875rem; margin-bottom: 8px; display: block; padding-left: 4px;">Password</label>
            <input wire:model="form.password" id="password" type="password" name="password" required autocomplete="current-password" 
                   placeholder="Enter your password"
                   style="background-color: rgba(2, 6, 23, 0.6); border: 1px solid #1e293b; color: #f8fafc; padding: 14px 20px; border-radius: 9999px; width: 100%; transition: all 0.2s; outline: none; font-size: 0.95rem;"
                   onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 2px rgba(99, 102, 241, 0.25)';"
                   onblur="this.style.borderColor='#1e293b'; this.style.boxShadow='none';" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2 text-rose-400" style="padding-left: 12px;" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; padding: 0 4px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <input wire:model="form.remember" id="remember" type="checkbox" name="remember" 
                       style="width: 16px; height: 16px; accent-color: #6366f1; cursor: pointer; border-radius: 4px;" />
                <label for="remember" style="color: #94a3b8; font-size: 0.875rem; font-weight: 500; cursor: pointer; user-select: none;"
                       onmouseover="this.style.color='#cbd5e1'"
                       onmouseout="this.style.color='#94a3b8'">
                    {{ __('Keep me logged in') }}
                </label>
            </div>
            
            <a href="{{ route('register') }}" wire:navigate 
               style="color: #94a3b8; text-decoration: underline; font-size: 0.875rem; font-weight: 500; transition: color 0.2s;"
               onmouseover="this.style.color='#f8fafc'"
               onmouseout="this.style.color='#94a3b8'">
                {{ __('Need an account?') }}
            </a>
        </div>

        <!-- Action Button (Sign In) -->
        <div style="margin-top: 10px;">
            <button type="submit" 
                    style="background: linear-gradient(135deg, #6366f1 0%, #06b6d4 50%, #10b981 100%); border: 0; padding: 14px 28px; border-radius: 9999px; color: #ffffff; font-size: 1rem; font-weight: 700; cursor: pointer; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4); transition: all 0.2s; width: 100%; text-align: center;"
                    onmouseover="this.style.opacity='0.95'; this.style.transform='translateY(-1px)';"
                    onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)';">
                {{ __('Sign in') }}
            </button>
        </div>
    </form>
</div>
