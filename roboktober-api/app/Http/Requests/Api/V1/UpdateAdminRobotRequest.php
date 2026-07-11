<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\Gewichtsklasse;
use App\Enums\RobotStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminRobotRequest extends FormRequest
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
            'team_id' => ['sometimes', 'integer', 'exists:teams,id'],
            'naam' => ['sometimes', 'string', 'max:255'],
            'gewichtsklasse' => ['sometimes', Rule::in(array_map(static fn (Gewichtsklasse $klasse): string => $klasse->value, Gewichtsklasse::cases()))],
            'status' => ['sometimes', Rule::in(array_map(static fn (RobotStatus $status): string => $status->value, RobotStatus::cases()))],
            'beschrijving' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ];
    }
}
