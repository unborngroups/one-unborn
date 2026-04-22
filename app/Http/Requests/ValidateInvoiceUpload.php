<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ValidateInvoiceUpload extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $allowedTypes = config('invoice.allowed_files.types');
        $allowedExtensions = config('invoice.allowed_files.extensions');
        $maxSize = config('invoice.allowed_files.max_size');

        return [
            'file' => [
                'required',
                'file',
                'mimes:' . implode(',', $allowedExtensions),
                'max:' . ($maxSize / 1024),
                'regex:/\.(pdf|jpg|jpeg|png|tiff|tif|webp)$/i',
            ],
            'email_id' => 'nullable|exists:email_logs,id',
            'source' => 'nullable|in:mailgun,ses,manual',
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'Invoice file is required.',
            'file.file' => 'Uploaded item must be a file.',
            'file.mimes' => 'Invoice must be a file of type: ' . implode(', ', config('invoice.allowed_files.extensions')),
            'file.max' => 'Invoice file size must not exceed ' . (config('invoice.allowed_files.max_size') / 1024 / 1024) . 'MB.',
            'file.regex' => 'Invalid file extension.',
            'email_id.exists' => 'Email log record not found.',
            'source.in' => 'Invalid source. Must be mailgun, ses, or manual.',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if ($this->file('file')) {
                $file = $this->file('file');

                // Validate MIME type
                $mimeType = $file->getMimeType();
                if (!in_array($mimeType, config('invoice.allowed_files.types'))) {
                    $validator->errors()->add('file', 'Invalid file MIME type: ' . $mimeType);
                }

                // Validate PDF page count if PDF
                if ($mimeType === 'application/pdf') {
                    $this->validatePdfPageCount($file, $validator);
                }

                // Check for duplicate upload (same file hash)
                $this->validateNoDuplicateUpload($file, $validator);

                // Validate file is not corrupted
                $this->validateFileIntegrity($file, $validator);
            }
        });
    }

    private function validatePdfPageCount($file, Validator $validator)
    {
        try {
            $maxPages = config('invoice.allowed_files.max_pages', 50);
            $path = $file->getPathname();

            $content = @file_get_contents($path);
            if (!$content) {
                $validator->errors()->add('file', 'Cannot read PDF file.');
                return;
            }

            // Count /Page occurrences as rough page estimate
            preg_match_all('/\/Type\s*\/Page(?!s)/', $content, $matches);
            $pageCount = count($matches[0]);

            if ($pageCount > $maxPages) {
                $validator->errors()->add('file', "PDF exceeds maximum page limit of {$maxPages}. Found {$pageCount} pages.");
            }
        } catch (\Exception $e) {
            // Log but don't fail on page count validation
            Log::warning('PDF page count validation failed: ' . $e->getMessage());
        }
    }

    private function validateNoDuplicateUpload($file, Validator $validator)
    {
        try {
            $fileHash = hash_file('sha256', $file->getPathname());

            // Check if file with same hash already processed
            $duplicate = \App\Models\EmailLog::where('file_hash', $fileHash)->exists();

            if ($duplicate) {
                $validator->errors()->add('file', 'This invoice file has already been uploaded and processed.');
            }
        } catch (\Exception $e) {
            Log::warning('Duplicate check failed: ' . $e->getMessage());
        }
    }

    private function validateFileIntegrity($file, Validator $validator)
    {
        try {
            $path = $file->getPathname();

            // Try to read first and last bytes
            if (!is_readable($path)) {
                $validator->errors()->add('file', 'File is not readable.');
                return;
            }

            $size = filesize($path);
            if ($size === false || $size === 0) {
                $validator->errors()->add('file', 'File is empty or cannot be accessed.');
                return;
            }

            // For PDFs, check magic bytes
            if ($file->getMimeType() === 'application/pdf') {
                $handle = fopen($path, 'r');
                $header = fread($handle, 4);
                fclose($handle);

                if ($header !== '%PDF') {
                    $validator->errors()->add('file', 'File is not a valid PDF. Magic bytes check failed.');
                }
            }
        } catch (\Exception $e) {
            Log::warning('File integrity check failed: ' . $e->getMessage());
        }
    }
}
