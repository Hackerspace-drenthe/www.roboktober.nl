<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttachRichMediaRequest extends FormRequest
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
            'target_type' => ['required', 'string', Rule::in(['post', 'page', 'team', 'team_update', 'robot', 'user', 'programma_item'])],
            'target_id' => ['required', 'integer', 'min:1'],
            'collectie' => ['nullable', 'string', Rule::in(['featured', 'gallery', 'bijlagen', 'hero', 'foto', 'default'])],
            'alt_tekst' => ['nullable', 'string', 'max:255'],
            'onderschrift' => ['nullable', 'string', 'max:500'],
            'volgorde' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->has('target_id') && is_numeric($this->input('target_id'))) {
            $merge['target_id'] = (int) $this->input('target_id');
        }

        if ($this->has('volgorde') && is_numeric($this->input('volgorde'))) {
            $merge['volgorde'] = (int) $this->input('volgorde');
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }
}
