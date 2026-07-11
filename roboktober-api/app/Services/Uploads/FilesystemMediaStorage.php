<?php

declare(strict_types=1);

namespace App\Services\Uploads;

use App\Contracts\Uploads\MediaStorage;
use App\Data\Uploads\StoredUpload;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

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

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('Upload kon niet worden opgeslagen op de geselecteerde disk.');
        }

        $realPath = $file->getRealPath();
        $sha256 = null;
        if (is_string($realPath) && $realPath !== '') {
            $hash = hash_file('sha256', $realPath);
            $sha256 = is_string($hash) ? $hash : null;
        }

        $fileSize = $file->getSize();

        return new StoredUpload(
            path: $path,
            disk: $resolvedDisk,
            originalName: $file->getClientOriginalName(),
            mimeType: is_string($file->getMimeType()) ? $file->getMimeType() : 'application/octet-stream',
            extension: $extension,
            size: is_int($fileSize) ? $fileSize : 0,
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

        /** @var FilesystemAdapter $filesystem */
        $filesystem = Storage::disk($resolvedDisk);

        return $filesystem->url($path);
    }

    private function resolveDisk(?string $disk): string
    {
        if ($disk !== null && $disk !== '') {
            return $disk;
        }

        $configured = config('uploads.default_disk', 'public');

        return is_string($configured) && $configured !== '' ? $configured : 'public';
    }
}
