<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\LinkCategorie;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string|ValidationRule>>
     */
    public function rules(): array
    {
        return [
            'titel' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
            'beschrijving' => ['nullable', 'string', 'max:2000'],
            'categorie' => ['required', Rule::in(array_map(static fn (LinkCategorie $categorie): string => $categorie->value, LinkCategorie::cases()))],
            'eigenaar' => ['nullable', 'string', 'max:255'],
            'verified_at' => ['nullable', 'date'],
        ];
    }
}
