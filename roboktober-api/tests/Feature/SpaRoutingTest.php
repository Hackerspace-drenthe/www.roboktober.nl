<?php

declare(strict_types=1);

it('redirects direct frontend paths to the spa mount', function (): void {
    $this->get('/aanmelden')->assertRedirect('/app/aanmelden');
});

it('preserves query parameters when redirecting to spa mount', function (): void {
    $this->get('/aanmelden?ref=test')->assertRedirect('/app/aanmelden?ref=test');
});

it('serves app mounted spa paths without redirect loops', function (): void {
    $this->get('/app/programma')->assertOk();
    $this->get('/app/pageid')->assertOk();
    $this->get('/app/teams')->assertOk();
    $this->get('/app/teams/competitie')->assertOk();
});

it('keeps legacy competitie path compatible through spa mount', function (): void {
    $this->get('/competitie')->assertRedirect('/app/competitie');
    $this->get('/competitie?tab=overall')->assertRedirect('/app/competitie?tab=overall');
});
