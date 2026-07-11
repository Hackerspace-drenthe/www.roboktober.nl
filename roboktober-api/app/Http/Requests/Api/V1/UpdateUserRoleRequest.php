<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRoleRequest extends FormRequest
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
            'role' => [
                'required',
                Rule::in(array_map(static fn (UserRole $role): string => $role->value, UserRole::cases())),
            ],
        ];
    }
}
