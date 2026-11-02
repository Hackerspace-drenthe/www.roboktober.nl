<?php

declare(strict_types=1);

use App\Security\TotpService;

it('verifies the same TOTP code regardless of summer or winter timezone settings', function (): void {
    $service = new TotpService;
    $secret = 'JBSWY3DPEHPK3PXP';
    $originalTimezone = date_default_timezone_get();

    try {
        date_default_timezone_set('UTC');
        $code = $service->currentCode($secret);

        foreach (['Europe/Amsterdam', 'Etc/GMT-2', 'Etc/GMT-1'] as $timezone) {
            date_default_timezone_set($timezone);
            expect($service->verifyCode($secret, $code))->toBeTrue();
        }
    } finally {
        date_default_timezone_set($originalTimezone);
    }
});
