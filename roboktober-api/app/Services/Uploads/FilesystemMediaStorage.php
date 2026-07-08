<?php

declare(strict_types=1);

namespace App\Services\Uploads;

use App\Contracts\Uploads\MediaStorage;
use App\Data\Uploads\StoredUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class FilesystemMediaStorage implements MediaStorage
{
    public function storeUploadedFile(UploadedFile $file, string $directory, ?string $disk = null): StoredUpload
    {
        $resolvedDisk = $this->resolveDisk($disk);
        $originalExtension = strtolower($file->getClientOriginalExtension());
        $detectedExtension = strtolower((string) $file->extension());
        $extension = $originalExtension !== '' ? $originalExtension : ($detectedExtension !== '' ? $detectedExtension : 'bin');

        $fileName = Str::uuid()->toString().'.'.$extension;
        $path = $file->storeAs($directory, $fileName, $resolvedDisk);

        $realPath = $file->getRealPath();
        $sha256 = is_string($realPath) ? hash_file('sha256', $realPath) : null;

        return new StoredUpload(
            path: $path,
            disk: $resolvedDisk,
            originalName: $file->getClientOriginalName(),
            mimeType: $file->getMimeType() ?? 'application/octet-stream',
            extension: $extension,
            size: $file->getSize() ?? 0,
            sha256: $sha256,
        );
    }

    public function delete(string $path, ?string $disk = null): void
    {
        if ($path === '') {
            return;
        }

        Storage::disk($this->resolveDisk($disk))->delete($path);
    }

    public function publicUrl(string $path, ?string $disk = null): string
    {
        $resolvedDisk = $this->resolveDisk($disk);

        if ($resolvedDisk === 'public') {
            return '/storage/'.ltrim($path, '/');
        }

        return Storage::disk($resolvedDisk)->url($path);
    }

    private function resolveDisk(?string $disk): string
    {
        return ($disk !== null && $disk !== '')
            ? $disk
            : (string) config('uploads.default_disk', 'public');
    }
}
