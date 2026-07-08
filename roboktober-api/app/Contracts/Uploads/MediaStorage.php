<?php

declare(strict_types=1);

namespace App\Contracts\Uploads;

use App\Data\Uploads\StoredUpload;
use Illuminate\Http\UploadedFile;

interface MediaStorage
{
    public function storeUploadedFile(UploadedFile $file, string $directory, ?string $disk = null): StoredUpload;

    public function delete(string $path, ?string $disk = null): void;

    public function publicUrl(string $path, ?string $disk = null): string;
}
