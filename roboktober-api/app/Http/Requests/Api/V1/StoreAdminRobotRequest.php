<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\Gewichtsklasse;
use App\Enums\RobotStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminRobotRequest extends FormRequest
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
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'naam' => ['required', 'string', 'max:255'],
            'gewichtsklasse' => ['required', Rule::in(array_map(static fn (Gewichtsklasse $klasse): string => $klasse->value, Gewichtsklasse::cases()))],
            'status' => ['required', Rule::in(array_map(static fn (RobotStatus $status): string => $status->value, RobotStatus::cases()))],
            'beschrijving' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
