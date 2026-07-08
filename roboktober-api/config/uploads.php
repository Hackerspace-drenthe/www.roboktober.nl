<?php

declare(strict_types=1);

return [
    'default_disk' => env('UPLOADS_DEFAULT_DISK', 'public'),

    'admin' => [
        'image_max_kb' => (int) env('UPLOADS_ADMIN_IMAGE_MAX_KB', 102400),
        'post_max_kb' => (int) env('UPLOADS_ADMIN_POST_MAX_KB', 106496),
    ],

    'team_photo' => [
        'disk' => env('UPLOADS_TEAM_PHOTO_DISK', 'public'),
        'directory' => 'team-fotos',
        'collection' => 'foto',
    ],
];
