<?php

use App\Models\Domain;
use App\Models\Knowledge;
use App\Services\DocumentTextExtractor;
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

    #[Rule('nullable|file|mimes:jpg,jpeg,png,webp,pdf,docx,xlsx,pptx,txt,md,markdown|max:10240')]
    public $file;

    #[Rule('required|array')]
    public array $domains = [];

    public $domainList;

    public bool $deleteFile = false;

    public bool $isExtracting = false;
    public string $extractionStatus = '';

    public function mount(): void
    {
        $this->domainList = Domain::all();
        $this->title = $this->knowledge->title;
        $this->text = $this->knowledge->text;
        $this->domains = $this->knowledge->domains->pluck('id')->toArray();
    }

    public function updatedFile(): void
    {
        $this->validateOnly('file');
        
        $this->isExtracting = true;
        $this->extractionStatus = 'Sedang mengekstrak teks dari file...';

        try {
            $extractor = new DocumentTextExtractor();
            $extractedText = $extractor->extract($this->file);
            
            // Prefill title if empty
            if (empty($this->title)) {
                $originalName = $this->file->getClientOriginalName();
                $this->title = pathinfo($originalName, PATHINFO_FILENAME);
            }

            $this->text = $extractedText;
            $this->extractionStatus = 'Ekstraksi teks berhasil!';
            $this->deleteFile = false; // Reset delete flag if new file uploaded
        } catch (\Exception $e) {
            $this->addError('file', $e->getMessage());
            $this->extractionStatus = '';
        } finally {
            $this->isExtracting = false;
        }
    }

    public function update(): void
    {
        $this->validate();

        // Safeguard: Extract text if text is empty but a new file is uploaded
        if ($this->file && empty($this->text)) {
            try {
                $extractor = new DocumentTextExtractor();
                $this->text = $extractor->extract($this->file);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Extraction failed on update safeguard: ' . $e->getMessage());
            }
        }

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
                        <!-- Premium Dropzone Box -->
                        <div>
                            <x-input-label for="file" value="Upload File Pendukung Baru (Gambar, PDF, Word, Excel, PowerPoint, Teks)" />
                            
                            <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-300 px-6 py-8 bg-gray-50 hover:bg-gray-100/70 hover:border-indigo-400 transition duration-150 ease-in-out relative group">
                                <div class="text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 group-hover:scale-110 transition duration-150 ease-in-out">
                                        @if ($file && !$isExtracting)
                                            <svg class="h-6 w-6 text-emerald-600 animate-bounce" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif ($isExtracting)
                                            <svg class="animate-spin h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        @else
                                            <svg class="h-6 w-6 text-gray-500 group-hover:text-indigo-600 transition duration-150 ease-in-out" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="mt-3 flex text-sm leading-6 text-gray-600 justify-center">
                                        <label for="file" class="relative cursor-pointer rounded-md bg-transparent font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                            <span>Pilih file untuk diunggah</span>
                                            <input wire:model="file" id="file" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf,.docx,.xlsx,.pptx,.txt,.md,.markdown" class="sr-only" />
                                        </label>
                                    </div>
                                    <p class="text-xs leading-5 text-gray-500 mt-1">
                                        Gambar, PDF, DOCX, XLSX, PPTX, TXT, MD (Maksimal 10MB)
                                    </p>
                                </div>
                            </div>

                            <!-- Current File / Actions -->
                            @if ($knowledge->file_path && !$file)
                                <div class="mt-3 p-3 bg-gray-50 border rounded-md flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        <span class="font-medium truncate max-w-xs">File saat ini: {{ basename($knowledge->file_path) }}</span>
                                    </div>
                                    <label class="text-xs font-semibold text-red-600 hover:text-red-800 cursor-pointer flex items-center gap-1">
                                        <input type="checkbox" wire:model="deleteFile" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        Hapus file
                                    </label>
                                </div>
                            @endif

                            <!-- Uploading and Extraction State UI -->
                            <div class="mt-2 text-sm">
                                <div wire:loading wire:target="file" class="flex items-center gap-2 text-indigo-600 font-medium">
                                    <svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Sedang mengunggah file...</span>
                                </div>

                                @if ($isExtracting)
                                    <div class="flex items-center gap-2 text-indigo-600 font-medium animate-pulse">
                                        <svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>{{ $extractionStatus }}</span>
                                    </div>
                                @endif

                                @if ($file && !$isExtracting && $extractionStatus)
                                    <div class="text-emerald-600 font-semibold flex items-center gap-1.5">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        <span>{{ $extractionStatus }} ({{ $file->getClientOriginalName() }})</span>
                                    </div>
                                @endif
                            </div>

                            <x-input-error class="mt-2" :messages="$errors->get('file')" />
                        </div>

                        <div>
                            <x-input-label for="title" value="Judul" />
                            <x-text-input wire:model="title" id="title" class="mt-1 block w-full shadow-sm" type="text" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="text" value="Konten (Teks yang diekstraksi)" />
                            <div class="relative mt-1">
                                <textarea wire:model="text" id="text" rows="12" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm leading-relaxed" placeholder="Konten atau teks hasil ekstraksi akan muncul di sini..."></textarea>
                                @if ($isExtracting)
                                    <div class="absolute inset-0 bg-white/70 flex items-center justify-center rounded-md transition duration-150">
                                        <div class="flex flex-col items-center gap-2">
                                            <div class="w-10 h-10 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                                            <span class="text-sm font-semibold text-indigo-700">Mengekstrak dokumen...</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('text')" />
                            <p class="mt-1.5 text-xs text-gray-500">Mendukung format Markdown. Teks dapat diedit secara manual setelah diekstraksi.</p>
                        </div>

                        <div>
                            <x-input-label value="Domain" />
                            <div class="mt-2 grid grid-cols-3 gap-2">
                                @foreach ($domainList as $d)
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-gray-900">
                                        <input wire:model="domains" type="checkbox" value="{{ $d->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        {{ $d->name }}
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('domains')" />
                        </div>

                        <div class="flex items-center gap-4 pt-2">
                            <x-primary-button>Update</x-primary-button>
                            <a href="{{ route('knowledge.index') }}" wire:navigate class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</div>

