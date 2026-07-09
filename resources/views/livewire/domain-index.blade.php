<?php

use App\Models\Domain;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    public ?int $editId = null;

    #[Rule('required|string|max:255')]
    public string $name = '';

    public $domainList;

    public function mount(): void
    {
        $this->domainList = Domain::orderBy('name')->get();
    }

    public function create(): void
    {
        $this->validate();

        Domain::create(['name' => $this->name]);

        $this->reset('name');
        $this->domainList = Domain::orderBy('name')->get();
    }

    public function edit(int $id): void
    {
        $domain = Domain::findOrFail($id);
        $this->editId = $domain->id;
        $this->name = $domain->name;
    }

    public function update(): void
    {
        $this->validate();

        $domain = Domain::findOrFail($this->editId);
        $domain->update(['name' => $this->name]);

        $this->reset('name', 'editId');
        $this->domainList = Domain::orderBy('name')->get();
    }

    public function cancel(): void
    {
        $this->reset('name', 'editId');
    }

    public function delete(int $id): void
    {
        $domain = Domain::findOrFail($id);
        $domain->knowledge()->detach();
        $domain->delete();

        $this->domainList = Domain::orderBy('name')->get();
    }
}; ?>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-full">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">Domain</h2>
                        <p class="mt-1 text-sm text-gray-600">Kelola daftar domain.</p>
                    </header>

                    <div class="mt-6 space-y-6">
                        <form wire:submit="{{ $editId ? 'update' : 'create' }}" class="flex items-end gap-4">
                            <div class="flex-1">
                                <x-input-label for="name" value="Nama Domain" />
                                <x-text-input wire:model="name" id="name" class="mt-1 block w-full" type="text" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            <div class="flex gap-2">
                                <x-primary-button>{{ $editId ? 'Update' : 'Tambah' }}</x-primary-button>
                                @if ($editId)
                                    <button type="button" wire:click="cancel" class="text-sm text-gray-600 hover:text-gray-900">Batal</button>
                                @endif
                            </div>
                        </form>

                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="py-3 px-2">Nama</th>
                                    <th class="py-3 px-2">Slug</th>
                                    <th class="py-3 px-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($domainList as $d)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-2">{{ $d->name }}</td>
                                        <td class="py-3 px-2 text-gray-500 text-xs">{{ $d->slug }}</td>
                                        <td class="py-3 px-2 text-right">
                                            <button wire:click="edit({{ $d->id }})" class="text-indigo-600 hover:text-indigo-900 text-xs mr-3">Edit</button>
                                            <button wire:confirm="Hapus domain '{{ $d->name }}'?" wire:click="delete({{ $d->id }})" class="text-red-600 hover:text-red-900 text-xs">Hapus</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-6 text-center text-gray-500">Belum ada domain.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
