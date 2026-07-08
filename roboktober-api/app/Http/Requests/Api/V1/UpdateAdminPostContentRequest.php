<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\ContentFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminPostContentRequest extends FormRequest
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
            'titel' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:320'],
            'content' => ['required', 'string', 'max:65000'],
            'content_format' => [
                'required',
                Rule::in(array_map(static fn (ContentFormat $format): string => $format->value, ContentFormat::cases())),
            ],
            'categorie' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'array', 'max:20'],
            'tags.*' => ['string', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'titel.required' => 'Geef een titel op voor dit bericht.',
            'content.required' => 'Voeg inhoud toe aan dit bericht.',
            'content_format.required' => 'Kies een opmaaktype (HTML of Markdown).',
            'tags.array' => 'Tags moeten als lijst worden meegestuurd.',
        ];
    }
}
