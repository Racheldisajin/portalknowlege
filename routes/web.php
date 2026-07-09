<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Volt::route('knowledge', 'knowledge-index')->name('knowledge.index');
    Volt::route('knowledge/create', 'knowledge-create')->name('knowledge.create');
    Volt::route('knowledge/{knowledge}/edit', 'knowledge-edit')->name('knowledge.edit');
    Volt::route('domains', 'domain-index')->name('domains.index');
});

require __DIR__.'/auth.php';
