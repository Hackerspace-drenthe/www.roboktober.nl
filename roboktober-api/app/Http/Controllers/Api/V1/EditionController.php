<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\EditionResource;
use App\Models\Edition;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EditionController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $editions = Edition::query()
            ->where('is_done', false)
            ->orderBy('start_at')
            ->get();

        return EditionResource::collection($editions);
    }
}
