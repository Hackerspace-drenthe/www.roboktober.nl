<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\AnalyticsEvent;
use App\Models\User;
use Illuminate\Support\Carbon;

describe('Analytics events API', function (): void {
    it('stores events and updates page aggregates for page views', function (): void {
        $user = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        $token = $user->createToken('visitor')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/analytics/events', [
                'session_id' => str_repeat('a', 24),
                'event_type' => 'page_view',
                'page_path' => '/teams',
                'route_name' => 'teams',
            ])
            ->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertDatabaseHas('analytics_events', [
            'event_type' => 'page_view',
            'page_path' => '/teams',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('page_visit_aggregates', [
            'page_path' => '/teams',
            'bucket_start' => Carbon::now()->startOfHour()->toDateTimeString(),
            'visits' => 1,
        ]);
    });

    it('returns enriched admin analytics with journeys and actor splits', function (): void {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $member = User::factory()->create([
            'role' => UserRole::Visitor,
        ]);

        AnalyticsEvent::query()->create([
            'user_id' => $member->id,
            'session_id' => 'session-loggedin-123456',
            'visitor_hash' => hash('sha256', 'member'),
            'event_type' => 'page_view',
            'page_path' => '/',
            'occurred_at' => now()->subMinutes(20),
        ]);

        AnalyticsEvent::query()->create([
            'user_id' => $member->id,
            'session_id' => 'session-loggedin-123456',
            'visitor_hash' => hash('sha256', 'member'),
            'event_type' => 'tab_switch',
            'page_path' => '/teams/competitie',
            'occurred_at' => now()->subMinutes(19),
        ]);

        AnalyticsEvent::query()->create([
            'user_id' => null,
            'session_id' => 'session-anon-123456789',
            'visitor_hash' => hash('sha256', 'anon'),
            'event_type' => 'page_view',
            'page_path' => '/aanmelden',
            'occurred_at' => now()->subMinutes(10),
        ]);

        $token = $admin->createToken('admin')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/analytics/page-visits?granularity=daily')
            ->assertOk()
            ->assertJsonPath('data.totals.logged_in_users', 1)
            ->assertJsonPath('data.totals.anonymous_visitors', 1)
            ->assertJsonPath('data.totals.sessions_tracked', 2)
            ->assertJsonPath('data.events_by_type.page_view', 2)
            ->assertJsonPath('data.journeys.top_transitions.0.from', '/')
            ->assertJsonPath('data.journeys.top_transitions.0.to', '/teams/competitie');
    });
});
