<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LocationManagementController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $zoekterm = trim($request->string('q')->toString());

        $locations = Location::query()
            ->when($zoekterm !== '', static fn ($query) => $query
                ->where('name', 'like', '%'.$zoekterm.'%')
                ->orWhere('address', 'like', '%'.$zoekterm.'%')
                ->orWhere('place', 'like', '%'.$zoekterm.'%')
                ->orWhere('zipcode', 'like', '%'.$zoekterm.'%'))
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return LocationResource::collection($locations);
    }
}
