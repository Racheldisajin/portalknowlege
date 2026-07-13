<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register">
        <!-- Name -->
        <div style="margin-bottom: 20px;">
            <label for="name" style="color: #cbd5e1; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; display: block;">{{ __('Name') }}</label>
            <input wire:model="name" id="name" type="text" name="name" required autofocus autocomplete="name" 
                   style="background-color: #020617; border: 1px solid #1e293b; color: #f8fafc; padding: 12px 16px; border-radius: 12px; width: 100%; transition: all 0.2s; outline: none;"
                   onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 2px rgba(99, 102, 241, 0.2)';"
                   onblur="this.style.borderColor='#1e293b'; this.style.boxShadow='none';" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-rose-400" />
        </div>

        <!-- Email Address -->
        <div style="margin-bottom: 20px;">
            <label for="email" style="color: #cbd5e1; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; display: block;">{{ __('Email') }}</label>
            <input wire:model="email" id="email" type="email" name="email" required autocomplete="username" 
                   style="background-color: #020617; border: 1px solid #1e293b; color: #f8fafc; padding: 12px 16px; border-radius: 12px; width: 100%; transition: all 0.2s; outline: none;"
                   onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 2px rgba(99, 102, 241, 0.2)';"
                   onblur="this.style.borderColor='#1e293b'; this.style.boxShadow='none';" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-400" />
        </div>

        <!-- Password -->
        <div style="margin-bottom: 20px;">
            <label for="password" style="color: #cbd5e1; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; display: block;">{{ __('Password') }}</label>
            <input wire:model="password" id="password" type="password" name="password" required autocomplete="new-password" 
                   style="background-color: #020617; border: 1px solid #1e293b; color: #f8fafc; padding: 12px 16px; border-radius: 12px; width: 100%; transition: all 0.2s; outline: none;"
                   onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 2px rgba(99, 102, 241, 0.2)';"
                   onblur="this.style.borderColor='#1e293b'; this.style.boxShadow='none';" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-400" />
        </div>

        <!-- Confirm Password -->
        <div style="margin-bottom: 24px;">
            <label for="password_confirmation" style="color: #cbd5e1; font-weight: 600; font-size: 0.875rem; margin-bottom: 6px; display: block;">{{ __('Confirm Password') }}</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                   style="background-color: #020617; border: 1px solid #1e293b; color: #f8fafc; padding: 12px 16px; border-radius: 12px; width: 100%; transition: all 0.2s; outline: none;"
                   onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 2px rgba(99, 102, 241, 0.2)';"
                   onblur="this.style.borderColor='#1e293b'; this.style.boxShadow='none';" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-rose-400" />
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 8px;">
            <a href="{{ route('login') }}" wire:navigate 
               style="color: #94a3b8; text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s;"
               onmouseover="this.style.color='#f8fafc'"
               onmouseout="this.style.color='#94a3b8'">
                {{ __('Already registered?') }}
            </a>

            <button type="submit" 
                    style="background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%); border: 0; padding: 10px 20px; border-radius: 12px; color: #ffffff; font-size: 0.875rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35); transition: all 0.2s;"
                    onmouseover="this.style.opacity='0.95'; this.style.transform='translateY(-1px)';"
                    onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)';">
                {{ __('Register') }}
            </button>
        </div>
    </form>
</div>
