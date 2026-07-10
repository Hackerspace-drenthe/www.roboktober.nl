<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProgrammaItemResource;
use App\Models\Edition;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProgrammaController extends Controller
{
    public function index(Edition $edition): AnonymousResourceCollection
    {
        $items = $edition->programmaItems()
            ->where('is_published', true)
            ->with('media')
            ->orderBy('start_at')
            ->orderBy('volgorde')
            ->orderBy('id')
            ->get();

        return ProgrammaItemResource::collection($items);
    }
}
