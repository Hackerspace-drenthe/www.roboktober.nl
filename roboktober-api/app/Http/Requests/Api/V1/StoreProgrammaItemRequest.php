<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\ContentFormat;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProgrammaItemRequest extends FormRequest
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
            'beschrijving' => ['required', 'string', 'max:65000'],
            'content_format' => [
                'required',
                Rule::in(array_map(static fn (ContentFormat $format): string => $format->value, ContentFormat::cases())),
            ],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'volgorde' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
