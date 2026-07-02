<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for public team registration submissions.
 *
 * OWASP A03: Input Validation — all user-submitted data is strictly validated.
 * Email is stored as-is (not hashed) for organizer contact use.
 *
 * @see PLAN.md §5.1 — team registration model
 */
class StoreTeamRequest extends FormRequest
{
    /**
     * All registrations are publicly accessible (no auth required).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string|\Illuminate\Contracts\Validation\Rule>>
     */
    public function rules(): array
    {
        return [
            'naam' => ['required', 'string', 'max:255'],
            'contactpersoon' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'volwassenen' => ['required', 'integer', 'min:1', 'max:20'],
            'kinderen' => ['nullable', 'integer', 'min:0', 'max:50'],
            'opmerkingen' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'naam.required' => 'Geef je teamnaam op.',
            'contactpersoon.required' => 'Geef de naam van de contactpersoon op.',
            'email.required' => 'Geef een e-mailadres op.',
            'email.email' => 'Geef een geldig e-mailadres op.',
            'volwassenen.required' => 'Geef het aantal volwassen deelnemers op.',
            'volwassenen.min' => 'Er moet minimaal één volwassen deelnemer zijn.',
        ];
    }
}
