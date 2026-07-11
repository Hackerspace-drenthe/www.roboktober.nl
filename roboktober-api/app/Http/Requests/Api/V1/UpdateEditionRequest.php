<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEditionRequest extends FormRequest
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
        $configuredMaxImageKb = config('uploads.admin.image_max_kb', 102400);
        $maxImageKb = is_int($configuredMaxImageKb) ? $configuredMaxImageKb : 102400;

        return [
            'naam' => ['sometimes', 'string', 'max:255'],
            'omschrijving' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'start_at' => ['sometimes', 'date'],
            'end_at' => ['sometimes', 'nullable', 'date'],
            'is_done' => ['sometimes', 'boolean'],
            'afbeelding' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.$maxImageKb],
            'afbeelding_verwijderen' => ['sometimes', 'boolean'],
            'location' => ['sometimes', 'array'],
            'location.name' => ['required_with:location', 'string', 'max:255'],
            'location.address' => ['required_with:location', 'string', 'max:255'],
            'location.place' => ['required_with:location', 'string', 'max:255'],
            'location.zipcode' => ['required_with:location', 'string', 'max:32'],
            'location.osm_url' => ['nullable', 'url', 'max:2048'],
            'location.instructions' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
