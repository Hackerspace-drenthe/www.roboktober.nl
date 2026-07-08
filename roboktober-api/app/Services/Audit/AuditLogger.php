<?php

declare(strict_types=1);

namespace App\Services\Audit;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     * @param  array<string, mixed>|null  $context
     */
    public function log(
        User $actor,
        string $action,
        Model $subject,
        ?array $before = null,
        ?array $after = null,
        ?array $context = null,
    ): void {
        AuditLog::query()->create([
            'actor_user_id' => $actor->id,
            'action' => $action,
            'subject_type' => $subject::class,
            'subject_id' => (int) $subject->getKey(),
            'before' => $before,
            'after' => $after,
            'context' => $context,
        ]);
    }
}
