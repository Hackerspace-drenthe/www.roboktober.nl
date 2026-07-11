<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\ContentFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminPageContentRequest extends FormRequest
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
            'titel' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:65000'],
            'content_format' => [
                'required',
                Rule::in(array_map(static fn (ContentFormat $format): string => $format->value, ContentFormat::cases())),
            ],
            'seo' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'titel.required' => 'Geef een titel op voor deze pagina.',
            'content.required' => 'Voeg inhoud toe aan deze pagina.',
            'content_format.required' => 'Kies een opmaaktype (HTML of Markdown).',
        ];
    }
}
