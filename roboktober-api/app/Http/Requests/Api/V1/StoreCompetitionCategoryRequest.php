<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompetitionCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'naam' => ['required', 'string', 'max:120'],
            'omschrijving' => ['nullable', 'string', 'max:2000'],
            'volgorde' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }
}
