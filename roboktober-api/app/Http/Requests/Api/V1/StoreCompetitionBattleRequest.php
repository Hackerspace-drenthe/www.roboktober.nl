<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompetitionBattleRequest extends FormRequest
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
            'naam' => ['required', 'string', 'max:120'],
            'battle_mode' => ['required', 'string', Rule::in(['solo', 'multi'])],
            'omschrijving' => ['nullable', 'string', 'max:2000'],
            'scheduled_at' => ['nullable', 'date'],
            'volgorde' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }
}
