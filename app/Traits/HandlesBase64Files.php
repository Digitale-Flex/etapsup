<?php

namespace App\Traits;

trait HandlesBase64Files
{
    protected function isValidBase64($data): bool
    {
        return base64_encode(base64_decode($data, true)) === $data;
    }

    protected function getFileInfo(string $base64Data): array
    {
        $fileData = base64_decode($base64Data);
        $f = finfo_open();
        $mimeType = finfo_buffer($f, $fileData, FILEINFO_MIME_TYPE);
        finfo_close($f);
        $extension = $this->getExtensionFromMimeType($mimeType);
        return [
            'mime' => $mimeType,
            'extension' => $extension,
        ];
    }

    protected function getExtensionFromMimeType(string $mimeType): string
    {
        $mimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
        ];
        return $mimeTypes[$mimeType] ?? 'bin';
    }

    protected function generateFileName(string $prefix, string $extension): string
    {
        $timestamp = now()->format('YmdHis');
        return "{$prefix}_{$timestamp}.{$extension}";
    }
}
