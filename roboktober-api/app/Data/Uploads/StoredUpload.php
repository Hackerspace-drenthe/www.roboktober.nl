<?php

declare(strict_types=1);

namespace App\Data\Uploads;

final readonly class StoredUpload
{
    public function __construct(
        public string $path,
        public string $disk,
        public string $originalName,
        public string $mimeType,
        public string $extension,
        public int $size,
        public ?string $sha256,
    ) {
    }
}
