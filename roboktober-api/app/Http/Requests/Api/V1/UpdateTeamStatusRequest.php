<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\TeamStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamStatusRequest extends FormRequest
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
            'status' => [
                'required',
                Rule::in(array_map(static fn (TeamStatus $status): string => $status->value, TeamStatus::cases())),
            ],
            'opmerkingen' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
