<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\Gewichtsklasse;
use App\Models\Edition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation rules for authenticated team registration submissions.
 *
 * OWASP A03: Input Validation — all user-submitted data is strictly validated.
 * Email is stored as-is (not hashed) for organizer contact use.
 *
 * @see PLAN.md §5.1 — team registration model
 */
class StoreTeamRequest extends FormRequest
{
    /**
     * Registration endpoint enforces auth via route middleware.
     */
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
            'edition_id' => [
                'required',
                'integer',
                Rule::exists(Edition::class, 'id')->where(
                    static fn ($query) => $query->where('is_done', 0),
                ),
            ],
            'naam' => ['required', 'string', 'max:255'],
            'contactpersoon' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'volwassenen' => ['required', 'integer', 'min:1', 'max:20'],
            'kinderen' => ['nullable', 'integer', 'min:0', 'max:50'],
            'opmerkingen' => ['nullable', 'string', 'max:2000'],
            'teamfoto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:51200'],
            'robots' => ['required', 'array', 'min:1', 'max:10'],
            'robots.*.naam' => ['required', 'string', 'max:255'],
            'robots.*.gewichtsklasse' => [
                'required',
                Rule::in(array_map(static fn (Gewichtsklasse $klasse): string => $klasse->value, Gewichtsklasse::cases())),
            ],
            'robots.*.beschrijving' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'naam.required' => 'Geef je teamnaam op.',
            'edition_id.required' => 'Kies de editie waarvoor je wilt aanmelden.',
            'edition_id.exists' => 'De gekozen editie is niet beschikbaar voor aanmelding.',
            'contactpersoon.required' => 'Geef de naam van de contactpersoon op.',
            'email.required' => 'Geef een e-mailadres op.',
            'email.email' => 'Geef een geldig e-mailadres op.',
            'volwassenen.required' => 'Geef het aantal volwassen deelnemers op.',
            'volwassenen.min' => 'Er moet minimaal één volwassen deelnemer zijn.',
            'robots.required' => 'Voeg minimaal één robot toe.',
            'robots.min' => 'Voeg minimaal één robot toe.',
            'robots.*.naam.required' => 'Geef voor elke robot een naam op.',
            'robots.*.gewichtsklasse.required' => 'Kies voor elke robot een gewichtsklasse.',
            'teamfoto.image' => 'De teamfoto moet een afbeelding zijn.',
            'teamfoto.mimes' => 'De teamfoto moet een JPG, PNG of WEBP bestand zijn.',
            'teamfoto.max' => 'De teamfoto mag maximaal 50 MB groot zijn.',
        ];
    }
}
