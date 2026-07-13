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
            <label for="name" style="color: #cbd5e1; font-weight: 600; font-size: 0.875rem; margin-bottom: 8px; display: block; padding-left: 4px;">Name</label>
            <input wire:model="name" id="name" type="text" name="name" required autofocus autocomplete="name" 
                   placeholder="Enter your full name"
                   style="background-color: rgba(2, 6, 23, 0.6); border: 1px solid #1e293b; color: #f8fafc; padding: 14px 20px; border-radius: 9999px; width: 100%; transition: all 0.2s; outline: none; font-size: 0.95rem;"
                   onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 2px rgba(99, 102, 241, 0.25)';"
                   onblur="this.style.borderColor='#1e293b'; this.style.boxShadow='none';" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-rose-400" style="padding-left: 12px;" />
        </div>

        <!-- Email Address -->
        <div style="margin-bottom: 20px;">
            <label for="email" style="color: #cbd5e1; font-weight: 600; font-size: 0.875rem; margin-bottom: 8px; display: block; padding-left: 4px;">Email</label>
            <input wire:model="email" id="email" type="email" name="email" required autocomplete="username" 
                   placeholder="Enter your email address"
                   style="background-color: rgba(2, 6, 23, 0.6); border: 1px solid #1e293b; color: #f8fafc; padding: 14px 20px; border-radius: 9999px; width: 100%; transition: all 0.2s; outline: none; font-size: 0.95rem;"
                   onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 2px rgba(99, 102, 241, 0.25)';"
                   onblur="this.style.borderColor='#1e293b'; this.style.boxShadow='none';" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-400" style="padding-left: 12px;" />
        </div>

        <!-- Password -->
        <div style="margin-bottom: 20px;">
            <label for="password" style="color: #cbd5e1; font-weight: 600; font-size: 0.875rem; margin-bottom: 8px; display: block; padding-left: 4px;">Password</label>
            <input wire:model="password" id="password" type="password" name="password" required autocomplete="new-password" 
                   placeholder="Create password"
                   style="background-color: rgba(2, 6, 23, 0.6); border: 1px solid #1e293b; color: #f8fafc; padding: 14px 20px; border-radius: 9999px; width: 100%; transition: all 0.2s; outline: none; font-size: 0.95rem;"
                   onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 2px rgba(99, 102, 241, 0.25)';"
                   onblur="this.style.borderColor='#1e293b'; this.style.boxShadow='none';" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-400" style="padding-left: 12px;" />
        </div>

        <!-- Confirm Password -->
        <div style="margin-bottom: 24px;">
            <label for="password_confirmation" style="color: #cbd5e1; font-weight: 600; font-size: 0.875rem; margin-bottom: 8px; display: block; padding-left: 4px;">Confirm Password</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                   placeholder="Confirm password"
                   style="background-color: rgba(2, 6, 23, 0.6); border: 1px solid #1e293b; color: #f8fafc; padding: 14px 20px; border-radius: 9999px; width: 100%; transition: all 0.2s; outline: none; font-size: 0.95rem;"
                   onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 2px rgba(99, 102, 241, 0.25)';"
                   onblur="this.style.borderColor='#1e293b'; this.style.boxShadow='none';" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-rose-400" style="padding-left: 12px;" />
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; padding: 0 4px;">
            <a href="{{ route('login') }}" wire:navigate 
               style="color: #94a3b8; text-decoration: underline; font-size: 0.875rem; font-weight: 500; transition: color 0.2s;"
               onmouseover="this.style.color='#f8fafc'"
               onmouseout="this.style.color='#94a3b8'">
                {{ __('Already registered?') }}
            </a>
        </div>

        <!-- Action Button (Register) -->
        <div style="margin-top: 10px;">
            <button type="submit" 
                    style="background: linear-gradient(135deg, #6366f1 0%, #06b6d4 50%, #10b981 100%); border: 0; padding: 14px 28px; border-radius: 9999px; color: #ffffff; font-size: 1rem; font-weight: 700; cursor: pointer; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4); transition: all 0.2s; width: 100%; text-align: center;"
                    onmouseover="this.style.opacity='0.95'; this.style.transform='translateY(-1px)';"
                    onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)';">
                {{ __('Register') }}
            </button>
        </div>
    </form>
</div>
