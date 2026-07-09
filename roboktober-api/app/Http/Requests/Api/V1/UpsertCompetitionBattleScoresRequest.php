<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Models\Robot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertCompetitionBattleScoresRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string|\Illuminate\Contracts\Validation\ValidationRule>>
     */
    public function rules(): array
    {
        return [
            'entries' => ['required', 'array', 'min:1', 'max:200'],
            'entries.*.robot_id' => ['required', 'integer', Rule::exists(Robot::class, 'id')],
            'entries.*.punten' => ['required', 'integer', 'min:-1000', 'max:1000'],
            'entries.*.opmerkingen' => ['nullable', 'string', 'max:500'],
        ];
    }
}
