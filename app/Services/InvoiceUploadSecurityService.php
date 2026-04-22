<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class InvoiceUploadSecurityService
{
    /**
     * Validate and secure invoice upload file.
     * Returns array with validation status and error messages.
     */
    public function validate(UploadedFile $file): array
    {
        $errors = [];

        // 1. Check file exists and is readable
        if (!$file->isValid()) {
            $errors[] = 'File upload failed: ' . $file->getErrorMessage();
            return ['valid' => false, 'errors' => $errors];
        }

        // 2. Validate file extension
        if (!$this->isAllowedExtension($file)) {
            $allowedExt = implode(', ', config('invoice.allowed_files.extensions'));
            $errors[] = "File extension not allowed. Allowed types: {$allowedExt}";
        }

        // 3. Validate MIME type
        if (!$this->isAllowedMimeType($file)) {
            $allowedMimes = implode(', ', config('invoice.allowed_files.types'));
            $errors[] = "File MIME type not allowed. Allowed types: {$allowedMimes}";
        }

        // 4. Validate file size
        if (!$this->isValidFileSize($file)) {
            $maxSize = config('invoice.allowed_files.max_size') / 1024 / 1024;
            $errors[] = "File size exceeds maximum of {$maxSize}MB";
        }

        // 5. Validate file integrity
        if (!$this->validateFileIntegrity($file)) {
            $errors[] = 'File integrity check failed. File may be corrupted.';
        }

        // 6. Check for virus/malware (scanner agnostic)
        if (!$this->checkFileSafety($file)) {
            $errors[] = 'File failed security scan.';
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors
        ];
    }

    /**
     * Check if file extension is allowed.
     */
    private function isAllowedExtension(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $allowed = array_map('strtolower', config('invoice.allowed_files.extensions', []));

        return in_array($extension, $allowed);
    }

    /**
     * Check if MIME type is allowed.
     */
    private function isAllowedMimeType(UploadedFile $file): bool
    {
        $mimeType = $file->getMimeType();
        $allowed = config('invoice.allowed_files.types', []);

        return in_array($mimeType, $allowed);
    }

    /**
     * Validate file size against maximum allowed.
     */
    private function isValidFileSize(UploadedFile $file): bool
    {
        $maxSize = config('invoice.allowed_files.max_size', 25 * 1024 * 1024);
        return $file->getSize() <= $maxSize;
    }

    /**
     * Validate file integrity and format.
     */
    private function validateFileIntegrity(UploadedFile $file): bool
    {
        try {
            $path = $file->getPathname();
            $mimeType = $file->getMimeType();

            // Check file is readable
            if (!is_readable($path)) {
                Log::warning("File not readable: {$path}");
                return false;
            }

            // Check file size > 0
            if (filesize($path) === 0) {
                Log::warning("Empty file uploaded: {$path}");
                return false;
            }

            // Validate PDF format
            if ($mimeType === 'application/pdf') {
                return $this->validatePdfIntegrity($path);
            }

            // Validate image format
            if (str_starts_with($mimeType, 'image/')) {
                return $this->validateImageIntegrity($path, $mimeType);
            }

            return true;
        } catch (\Exception $e) {
            Log::error("File integrity check exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate PDF file integrity.
     */
    private function validatePdfIntegrity(string $path): bool
    {
        try {
            // Check PDF magic bytes
            $handle = fopen($path, 'r');
            $header = fread($handle, 4);
            fclose($handle);

            if ($header !== '%PDF') {
                Log::warning("Invalid PDF header: {$path}");
                return false;
            }

            // Check PDF page count
            $maxPages = config('invoice.allowed_files.max_pages', 50);
            $content = file_get_contents($path);

            preg_match_all('/\/Type\s*\/Page(?!s)/', $content, $matches);
            $pageCount = count($matches[0]);

            if ($pageCount > $maxPages) {
                Log::warning("PDF exceeds max pages ({$pageCount} > {$maxPages}): {$path}");
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::warning("PDF integrity check failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate image file integrity.
     */
    private function validateImageIntegrity(string $path, string $mimeType): bool
    {
        try {
            $imageInfo = @getimagesize($path);

            if ($imageInfo === false) {
                Log::warning("Invalid image file: {$path}");
                return false;
            }

            // Verify MIME type matches actual image
            $actualMime = $imageInfo['mime'];
            if ($actualMime !== $mimeType) {
                Log::warning("Image MIME type mismatch for {$path}: expected {$mimeType}, got {$actualMime}");
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::warning("Image integrity check failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check file safety (ClamAV integration placeholder).
     * Override this method if antivirus scanning is available.
     */
    private function checkFileSafety(UploadedFile $file): bool
    {
        // If ClamAV or other scanner is configured, integrate here
        // For now, return true (assume file is safe after above checks)
        
        if (config('invoice.debug.enabled')) {
            Log::info("Skipping antivirus scan in debug mode for: " . $file->getClientOriginalName());
        }

        return true;
    }

    /**
     * Generate secure filename for storage.
     */
    public function generateSecureFilename(UploadedFile $file): string
    {
        $hash = hash_file('sha256', $file->getPathname());
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->timestamp;

        return "{$timestamp}_{$hash}.{$extension}";
    }

    /**
     * Get file hash for duplicate detection.
     */
    public function getFileHash(UploadedFile $file): string
    {
        return hash_file('sha256', $file->getPathname());
    }

    /**
     * Store file securely.
     */
    public function storeSecurely(UploadedFile $file, string $path = 'invoices/'): ?string
    {
        try {
            $filename = $this->generateSecureFilename($file);
            $storagePath = $file->storeAs($path, $filename, 'local');

            Log::info("Invoice file stored securely: {$storagePath}");
            return $storagePath;
        } catch (\Exception $e) {
            Log::error("Failed to store invoice file: " . $e->getMessage());
            return null;
        }
    }
}
