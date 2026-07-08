<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRichMediaUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        $targetTypes = ['post', 'page', 'team', 'team_update'];
        $collections = ['featured', 'gallery', 'bijlagen', 'hero', 'foto', 'default'];

        return [
            'bestand' => [
                'required',
                'file',
                // 100MB
                'max:102400',
                'mimes:jpg,jpeg,png,webp,gif,mp4,webm,mov,stl,obj,3mf,pdf,zip,txt,md',
            ],
            'naam' => ['sometimes', 'string', 'max:255'],
            'target_type' => ['nullable', 'string', Rule::in($targetTypes)],
            'target_id' => ['required_with:target_type', 'integer', 'min:1'],
            'collectie' => ['nullable', 'string', Rule::in($collections)],
            'alt_tekst' => ['nullable', 'string', 'max:255'],
            'onderschrift' => ['nullable', 'string', 'max:500'],
            'volgorde' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
