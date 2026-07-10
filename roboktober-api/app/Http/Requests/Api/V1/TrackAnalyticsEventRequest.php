<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class TrackAnalyticsEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'session_id' => ['required', 'string', 'min:16', 'max:64'],
            'event_type' => ['required', 'string', 'in:page_view,session_start,session_end,tab_switch,click,form_start,form_submit'],
            'event_name' => ['nullable', 'string', 'max:120'],
            'page_path' => ['nullable', 'string', 'max:255', 'starts_with:/'],
            'route_name' => ['nullable', 'string', 'max:120'],
            'referrer_path' => ['nullable', 'string', 'max:255', 'starts_with:/'],
            'payload' => ['nullable', 'array'],
            'occurred_at' => ['nullable', 'date'],
        ];
    }
}
