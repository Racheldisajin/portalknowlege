<?php

use App\Models\Domain;
use App\Models\Knowledge;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public string $search = '';
    public ?int $domainFilter = null;
    public $allDomains;

    public function mount(): void
    {
        $this->allDomains = Domain::orderBy('name')->get();
    }

    public function withKnowledge()
    {
        return Knowledge::with('domains')
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->domainFilter, fn($q) => $q->whereHas('domains', fn($q) => $q->where('domains.id', $this->domainFilter)))
            ->latest()
            ->paginate(10);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDomainFilter(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $knowledge = Knowledge::findOrFail($id);

        if ($knowledge->file_path) {
            Storage::disk('public')->delete($knowledge->file_path);
        }

        $knowledge->domains()->detach();
        $knowledge->delete();
    }
}; ?>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-full">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">Knowledge</h2>
                        <p class="mt-1 text-sm text-gray-600">Daftar semua knowledge.</p>
                    </header>

                    <div class="mt-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <x-text-input wire:model.live="search" type="text" class="w-64" placeholder="Cari judul..." />
                                <select wire:model.live="domainFilter" class="rounded border-gray-300 text-sm">
                                    <option value="">Semua Domain</option>
                                    @foreach ($allDomains as $domain)
                                        <option value="{{ $domain->id }}">{{ $domain->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <a href="{{ route('knowledge.create') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                + Tambah
                            </a>
                        </div>

                        @php $allKnowledge = $this->withKnowledge(); @endphp

                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b text-left">
                                    <th class="py-3 px-2">Judul</th>
                                    <th class="py-3 px-2">Domain</th>
                                    <th class="py-3 px-2">File</th>
                                    <th class="py-3 px-2">Dibuat</th>
                                    <th class="py-3 px-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($allKnowledge as $k)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-2">{{ $k->title }}</td>
                                        <td class="py-3 px-2">
                                            @foreach ($k->domains as $d)
                                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ $d->name }}</span>
                                            @endforeach
                                        </td>
                                        <td class="py-3 px-2">
                                            @if ($k->file_path)
                                                <span class="text-green-600 text-xs">Terlampir</span>
                                            @else
                                                <span class="text-gray-400 text-xs">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-2 text-gray-500 text-xs">{{ $k->created_at->format('d M Y') }}</td>
                                        <td class="py-3 px-2 text-right">
                                            <a href="{{ route('knowledge.edit', $k) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900 text-xs mr-3">Edit</a>
                                            <button wire:confirm="Hapus knowledge ini?" wire:click="delete({{ $k->id }})" class="text-red-600 hover:text-red-900 text-xs">Hapus</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-gray-500">Belum ada knowledge.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $allKnowledge->links() }}
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
