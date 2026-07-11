<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\ContentFormat;
use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamUpdateRequest extends FormRequest
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
            'excerpt' => ['nullable', 'string', 'max:320'],
            'content' => ['required', 'string', 'max:65000'],
            'content_format' => [
                'required',
                Rule::in(array_map(static fn (ContentFormat $format): string => $format->value, ContentFormat::cases())),
            ],
            'afbeeldingen' => ['nullable', 'array', 'max:12'],
            'afbeeldingen.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:51200'],
            'verwijder_afbeelding_ids' => ['nullable', 'array', 'max:50'],
            'verwijder_afbeelding_ids.*' => ['integer', Rule::exists(Media::class, 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'titel.required' => 'Geef een titel op voor je voortgangsbericht.',
            'content.required' => 'Voeg inhoud toe aan je voortgangsbericht.',
            'content_format.required' => 'Kies een opmaaktype (HTML of Markdown).',
            'afbeeldingen.array' => 'Afbeeldingen moeten als lijst worden meegestuurd.',
            'afbeeldingen.*.image' => 'Elke bijlage moet een afbeelding zijn.',
            'afbeeldingen.*.mimes' => 'Gebruik JPG, PNG of WEBP voor afbeeldingen.',
            'afbeeldingen.*.max' => 'Elke afbeelding mag maximaal 50 MB groot zijn.',
            'verwijder_afbeelding_ids.array' => 'Verwijderlijst moet als array worden meegestuurd.',
            'verwijder_afbeelding_ids.*.integer' => 'Elke verwijderde afbeelding moet een geldig ID zijn.',
            'verwijder_afbeelding_ids.*.exists' => 'Een of meer geselecteerde afbeeldingen bestaan niet.',
        ];
    }
}
