<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\ContentFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgrammaItemRequest extends FormRequest
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
            'beschrijving' => ['sometimes', 'string', 'max:65000'],
            'content_format' => [
                'sometimes',
                Rule::in(array_map(static fn (ContentFormat $format): string => $format->value, ContentFormat::cases())),
            ],
            'start_at' => ['sometimes', 'date'],
            'end_at' => ['nullable', 'date'],
            'volgorde' => ['sometimes', 'integer', 'min:0', 'max:10000'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
