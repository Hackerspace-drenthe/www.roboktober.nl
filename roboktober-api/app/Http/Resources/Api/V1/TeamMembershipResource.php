<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\TeamMembership;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TeamMembership
 */
class TeamMembershipResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'request_message' => $this->request_message,
            'team' => $this->team !== null ? [
                'id' => $this->team->id,
                'naam' => $this->team->naam,
            ] : null,
            'user' => $this->user !== null ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] : null,
            'reviewed_at' => $this->reviewed_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
