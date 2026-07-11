<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\LinkCategorie;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'titel' => ['sometimes', 'string', 'max:255'],
            'url' => ['sometimes', 'url', 'max:2048'],
            'beschrijving' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'categorie' => ['sometimes', Rule::in(array_map(static fn (LinkCategorie $categorie): string => $categorie->value, LinkCategorie::cases()))],
            'eigenaar' => ['sometimes', 'nullable', 'string', 'max:255'],
            'verified_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
