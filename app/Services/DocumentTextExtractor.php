<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser as PdfParser;
use Spatie\PdfToText\Pdf as SpatiePdf;

class DocumentTextExtractor
{
    /**
     * Supported MIME types and their extensions.
     */
    public static array $supportedMimes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'pdf' => 'application/pdf',
        'txt' => 'text/plain',
        'md' => 'text/markdown',
        'markdown' => 'text/markdown',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ];

    /**
     * Validate and extract text from an uploaded file.
     *
     * @param \Illuminate\Http\UploadedFile|\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file
     * @return string
     * @throws \Exception
     */
    public function extract($file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $realPath = $file->getRealPath();

        // 1. Perform validation checks
        $this->validateFile($file);

        // 2. Route to the appropriate parser
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'webp':
                return $this->extractImageOcr($realPath, $file->getClientOriginalName());

            case 'pdf':
                return $this->extractPdf($realPath);

            case 'docx':
                return $this->extractDocx($realPath);

            case 'xlsx':
                return $this->extractXlsx($realPath);

            case 'pptx':
                return $this->extractPptx($realPath);

            case 'txt':
            case 'md':
            case 'markdown':
                return file_get_contents($realPath);

            default:
                throw new \Exception("Format file (.{$extension}) tidak didukung untuk ekstraksi teks.");
        }
    }

    /**
     * Validate the file properties.
     */
    protected function validateFile($file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        // Validate supported extensions
        if (!array_key_exists($extension, self::$supportedMimes)) {
            throw new \Exception("Format file (.{$extension}) tidak didukung.");
        }

        // Validate size (max 10MB)
        $maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if ($file->getSize() > $maxSize) {
            throw new \Exception("Ukuran file melebihi batas maksimum 10MB.");
        }
    }

    /**
     * Extract text from Image using n8n OCR.
     */
    protected function extractImageOcr(string $filePath, string $originalName): string
    {
        try {
            // Upload to catbox.moe to get a clean CDN direct download URL
            $uploadResponse = Http::withoutVerifying()->asMultipart()->post('https://catbox.moe/user/api.php', [
                'reqtype' => 'fileupload',
                'fileToUpload' => fopen($filePath, 'r')
            ]);

            if (!$uploadResponse->successful()) {
                throw new \Exception("Gagal mengunggah gambar ke server CDN sementara.");
            }

            $directUrl = trim($uploadResponse->body());

            if (empty($directUrl) || !str_starts_with($directUrl, 'https://files.catbox.moe/')) {
                throw new \Exception("Gagal mendapatkan URL publik gambar dari server sementara: " . $directUrl);
            }

            // Request OCR from n8n Raycorp
            $ocrResponse = Http::withoutVerifying()->timeout(30)->post('https://n8n.raycorpgroup.com/webhook/ocr-extract', [
                'text' => 'Tolong salin teks dari gambar ini ' . $directUrl
            ]);

            if (!$ocrResponse->successful()) {
                throw new \Exception("Gagal menghubungi server n8n OCR.");
            }

            $ocrData = $ocrResponse->json();
            $contentString = $ocrData['choices'][0]['message']['content'] ?? '';
            
            if (empty($contentString)) {
                return '';
            }

            $contentString = trim($contentString);
            
            // Strip markdown block formatting if present (e.g. ```json ... ```)
            if (str_starts_with($contentString, '```')) {
                $contentString = preg_replace('/^```(?:json)?/i', '', $contentString);
                $contentString = preg_replace('/```$/', '', $contentString);
                $contentString = trim($contentString);
            }

            // Try to decode the nested JSON string
            $nestedData = json_decode($contentString, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($nestedData['results'][0]['text'])) {
                return $nestedData['results'][0]['text'];
            }
            
            // Fallback: return the raw string content if it is not valid nested JSON
            return $contentString;
        } catch (\Exception $e) {
            Log::error('OCR Error: ' . $e->getMessage());
            throw new \Exception("Gagal mengekstrak teks menggunakan OCR: " . $e->getMessage());
        }
    }

    /**
     * Extract text from PDF file.
     */
    protected function extractPdf(string $filePath): string
    {
        try {
            // 1. Try extracting using Spatie/pdf-to-text
            return SpatiePdf::getText($filePath);
        } catch (\Exception $e) {
            // Log that spatie failed or pdftotext binary was not found
            Log::info('Spatie PDF extraction failed, falling back to Smalot PDF Parser. Reason: ' . $e->getMessage());
            
            // 2. Fallback to smalot/pdfparser (Pure PHP, works everywhere without binaries)
            try {
                $parser = new PdfParser();
                $pdf = $parser->parseFile($filePath);
                return $pdf->getText();
            } catch (\Exception $subException) {
                Log::error('PDF Extraction Error: ' . $subException->getMessage());
                throw new \Exception("Gagal mengekstrak file PDF: " . $subException->getMessage());
            }
        }
    }

    /**
     * Extract text from DOCX (Word).
     */
    protected function extractDocx(string $filePath): string
    {
        $zip = new \ZipArchive();
        if ($zip->open($filePath) === true) {
            if (($index = $zip->locateName('word/document.xml')) !== false) {
                $data = $zip->getFromIndex($index);
                $zip->close();

                // Format document structure slightly: insert newlines on paragraph tags
                $data = str_replace(['</w:p>', '</w:r>', '<w:tab/>'], ["\n", " ", "\t"], $data);
                return trim(strip_tags($data));
            }
            $zip->close();
        }
        throw new \Exception("Struktur Word (.docx) tidak valid atau kosong.");
    }

    /**
     * Extract text from XLSX (Excel).
     */
    protected function extractXlsx(string $filePath): string
    {
        $zip = new \ZipArchive();
        if ($zip->open($filePath) === true) {
            if (($index = $zip->locateName('xl/sharedStrings.xml')) !== false) {
                $data = $zip->getFromIndex($index);
                $zip->close();

                $data = str_replace('</si>', "\n", $data);
                return trim(strip_tags($data));
            }
            $zip->close();
            return '';
        }
        throw new \Exception("Struktur Excel (.xlsx) tidak valid.");
    }

    /**
     * Extract text from PPTX (PowerPoint).
     */
    protected function extractPptx(string $filePath): string
    {
        $zip = new \ZipArchive();
        $text = '';
        if ($zip->open($filePath) === true) {
            $slideNum = 1;
            while (($index = $zip->locateName("ppt/slides/slide{$slideNum}.xml")) !== false) {
                $data = $zip->getFromIndex($index);
                $data = str_replace(['</a:p>', '</a:r>'], ["\n", " "], $data);
                $text .= trim(strip_tags($data)) . "\n\n";
                $slideNum++;
            }
            $zip->close();
            return trim($text);
        }
        throw new \Exception("Struktur PowerPoint (.pptx) tidak valid.");
    }
}
