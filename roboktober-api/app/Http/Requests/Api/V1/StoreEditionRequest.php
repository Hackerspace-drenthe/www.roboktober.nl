<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreEditionRequest extends FormRequest
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
            'naam' => ['required', 'string', 'max:255'],
            'omschrijving' => ['nullable', 'string', 'max:5000'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'is_done' => ['nullable', 'boolean'],
            'afbeelding' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.$maxImageKb],
            'location' => ['required', 'array'],
            'location.name' => ['required', 'string', 'max:255'],
            'location.address' => ['required', 'string', 'max:255'],
            'location.place' => ['required', 'string', 'max:255'],
            'location.zipcode' => ['required', 'string', 'max:32'],
            'location.osm_url' => ['nullable', 'url', 'max:2048'],
            'location.instructions' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
