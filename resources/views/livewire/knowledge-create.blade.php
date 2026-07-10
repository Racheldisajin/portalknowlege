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

    #[Rule('required|string|max:255')]
    public string $title = '';

    #[Rule('nullable|string')]
    public string $text = '';

    public $files = []; // Handles the temporary files uploaded by Livewire

    public array $uploadedFiles = []; // Holds the list of files to save: [['path' => ..., 'name' => ..., 'text' => ..., 'is_image' => ...]]

    #[Rule('required|array')]
    public array $domains = [];

    public $domainList;

    public bool $isExtracting = false;
    public string $extractionStatus = '';

    public function mount(): void
    {
        $this->domainList = Domain::all();
    }

    public function updatedFiles(): void
    {
        $this->validate([
            'files.*' => 'file|mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx,pptx,txt,md,markdown,xml,mp4,mov,avi,mkv,webm|max:10240'
        ]);
        
        $this->isExtracting = true;
        $this->extractionStatus = 'Sedang memproses berkas...';

        try {
            $extractor = new DocumentTextExtractor();
            
            foreach ($this->files as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = strtolower($file->getClientOriginalExtension());
                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);

                // 1. Extract text first (while file is accessible locally)
                $extractedText = '';
                try {
                    $extractedText = $extractor->extract($file);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Gagal mengekstrak teks untuk {$originalName}: " . $e->getMessage());
                }

                // 2. Store file based on type
                $storedPath = '';
                if ($isImage) {
                    // Images are stored locally on the project
                    $storedPath = $file->store('knowledge', 'public');
                } else {
                    // All non-images are stored in RayCloud
                    $rayData = $extractor->uploadToRayCloud($file->getRealPath());
                    $storedPath = $rayData['url'];
                }

                $this->uploadedFiles[] = [
                    'name' => $originalName,
                    'path' => $storedPath,
                    'text' => $extractedText,
                    'is_image' => $isImage,
                ];

                // Auto-fill title if empty
                if (empty($this->title)) {
                    $this->title = pathinfo($originalName, PATHINFO_FILENAME);
                }
            }

            $this->extractionStatus = 'Ekstraksi teks berhasil!';
        } catch (\Exception $e) {
            $this->addError('files', $e->getMessage());
            $this->extractionStatus = '';
        } finally {
            $this->isExtracting = false;
            $this->files = []; // Reset temporary file uploads
        }
    }

    public function removeUploadedFile(int $index): void
    {
        if (isset($this->uploadedFiles[$index])) {
            $fileData = $this->uploadedFiles[$index];

            try {
                if ($fileData['is_image']) {
                    // Delete local image
                    Storage::disk('public')->delete($fileData['path']);
                } else {
                    // Delete from RayCloud
                    $urlPath = parse_url($fileData['path'], PHP_URL_PATH);
                    $path = preg_replace('/^\/files\//', '', $urlPath);
                    DocumentTextExtractor::deleteFromRayCloud($path);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Gagal menghapus berkas saat pembatalan: " . $e->getMessage());
            }

            unset($this->uploadedFiles[$index]);
            $this->uploadedFiles = array_values($this->uploadedFiles);
        }
    }

    public function save(): void
    {
        $this->validate();

        // Concatenate all file texts if main text is empty
        $combinedText = $this->text;
        if (empty($combinedText) && !empty($this->uploadedFiles)) {
            $texts = array_column($this->uploadedFiles, 'text');
            $combinedText = implode("\n\n---\n\n", array_filter($texts));
        }

        $knowledge = Knowledge::create([
            'title' => $this->title,
            'text' => $combinedText,
            'file_path' => $this->uploadedFiles[0]['path'] ?? null, // backward compatibility
        ]);

        $knowledge->domains()->attach($this->domains);

        // Save related files
        foreach ($this->uploadedFiles as $uf) {
            $knowledge->files()->create([
                'file_path' => $uf['path'],
                'text' => $uf['text'],
            ]);
        }

        $this->redirect(route('knowledge.index'), navigate: true);
    }
}; ?>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">Tambah Knowledge</h2>
                        <p class="mt-1 text-sm text-gray-600">Buat knowledge baru dengan mengunggah dokumen atau menulis teks secara manual.</p>
                    </header>

                    <form wire:submit="save" class="mt-6 space-y-6">
                        <!-- Premium Dropzone Box -->
                        <div>
                            <x-input-label for="files" value="Upload File Pendukung (Gambar, PDF, Word, Excel, PowerPoint, Teks)" />
                            
                            <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-300 px-6 py-8 bg-gray-50 hover:bg-gray-100/70 hover:border-indigo-400 transition duration-150 ease-in-out relative group">
                                <div class="text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 group-hover:scale-110 transition duration-150 ease-in-out">
                                        @if (!empty($uploadedFiles) && !$isExtracting)
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
                                        <label for="files" class="relative cursor-pointer rounded-md bg-transparent font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                            <span>Pilih berkas-berkas untuk diunggah</span>
                                            <input wire:model="files" id="files" type="file" multiple accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,.doc,.docx,.xls,.xlsx,.pptx,.txt,.md,.markdown,.xml,.mp4,.mov,.avi,.mkv,.webm" class="sr-only" />
                                        </label>
                                    </div>
                                    <p class="text-xs leading-5 text-gray-500 mt-1">
                                        Gambar, Video, PDF, DOC/DOCX, XLS/XLSX, PPTX, TXT, MD, XML (Maksimal 10MB per file)
                                    </p>
                                </div>
                            </div>
 
                            <!-- Uploading and Extraction State UI -->
                            <div class="mt-2 text-sm">
                                <div wire:loading wire:target="files" class="flex items-center gap-2 text-indigo-600 font-medium">
                                    <svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Sedang mengunggah berkas...</span>
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
                            </div>
 
                            <x-input-error class="mt-2" :messages="$errors->get('files')" />

                            <!-- List of Uploaded Files and Their Texts -->
                            @if (!empty($uploadedFiles))
                                <div class="mt-4 space-y-4">
                                    <h3 class="font-semibold text-sm text-gray-700">Berkas Terunggah:</h3>
                                    @foreach ($uploadedFiles as $index => $uf)
                                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-3 relative">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    @if ($uf['is_image'])
                                                        <span class="px-2 py-0.5 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-md">Lokal (Gambar)</span>
                                                    @else
                                                        <span class="px-2 py-0.5 text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 rounded-md">RayCloud (Dokumen)</span>
                                                    @endif
                                                    <span class="text-sm font-medium text-gray-800 truncate max-w-xs" title="{{ $uf['name'] }}">{{ $uf['name'] }}</span>
                                                </div>
                                                <button type="button" wire:click="removeUploadedFile({{ $index }})" class="text-xs text-red-600 hover:text-red-800 font-semibold flex items-center gap-1">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-500 font-medium">Teks yang diekstraksi untuk berkas ini:</label>
                                                <textarea wire:model="uploadedFiles.{{ $index }}.text" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-xs"></textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
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
                            <x-primary-button>Simpan</x-primary-button>
                            <a href="{{ route('knowledge.index') }}" wire:navigate class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</div>

