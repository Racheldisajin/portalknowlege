<?php

use App\Models\Domain;
use App\Models\Knowledge;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] class extends Component {
    use WithFileUploads;

    public Knowledge $knowledge;

    #[Rule('required|string|max:255')]
    public string $title = '';

    #[Rule('nullable|string')]
    public string $text = '';

    #[Rule('nullable|file|mimes:md,markdown|max:2048')]
    public $file;

    #[Rule('required|array')]
    public array $domains = [];

    public $domainList;

    public bool $deleteFile = false;

    public function mount(): void
    {
        $this->domainList = Domain::all();
        $this->title = $this->knowledge->title;
        $this->text = $this->knowledge->text;
        $this->domains = $this->knowledge->domains->pluck('id')->toArray();
    }

    public function update(): void
    {
        $this->validate();

        $filePath = $this->knowledge->file_path;

        if ($this->deleteFile && $filePath) {
            Storage::disk('public')->delete($filePath);
            $filePath = null;
        }

        if ($this->file) {
            if ($this->knowledge->file_path) {
                Storage::disk('public')->delete($this->knowledge->file_path);
            }
            $filePath = $this->file->store('knowledge', 'public');
        }

        $this->knowledge->update([
            'title' => $this->title,
            'text' => $this->text,
            'file_path' => $filePath,
        ]);

        $this->knowledge->domains()->sync($this->domains);

        $this->redirect(route('knowledge.index'), navigate: true);
    }
}; ?>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">Edit Knowledge</h2>
                        <p class="mt-1 text-sm text-gray-600">Perbarui judul, konten, dan domain knowledge.</p>
                    </header>

                    <form wire:submit="update" class="mt-6 space-y-6">
                        <div>
                            <x-input-label for="title" value="Judul" />
                            <x-text-input wire:model="title" id="title" class="mt-1 block w-full" type="text" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="text" value="Konten" />
                            <textarea wire:model="text" id="text" rows="8" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('text')" />
                            <p class="mt-1 text-sm text-gray-600">Mendukung format Markdown.</p>
                        </div>

                        <div>
                            <x-input-label for="file" value="Upload File .md" />
                            <input wire:model="file" id="file" type="file" accept=".md,.markdown" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <x-input-error class="mt-2" :messages="$errors->get('file')" />

                            @if ($knowledge->file_path && !$file)
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="text-sm text-gray-600">File saat ini: {{ basename($knowledge->file_path) }}</span>
                                    <label class="text-sm text-red-600">
                                        <input type="checkbox" wire:model="deleteFile" class="rounded border-gray-300 text-red-600">
                                        Hapus file
                                    </label>
                                </div>
                            @endif
                        </div>

                        <div>
                            <x-input-label value="Domain" />
                            <div class="mt-2 grid grid-cols-3 gap-2">
                                @foreach ($domainList as $d)
                                    <label class="flex items-center gap-2 text-sm">
                                        <input wire:model="domains" type="checkbox" value="{{ $d->id }}" class="rounded border-gray-300 text-indigo-600">
                                        {{ $d->name }}
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('domains')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>Update</x-primary-button>
                            <a href="{{ route('knowledge.index') }}" wire:navigate class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</div>
