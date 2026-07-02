<?php

declare(strict_types=1);

use App\Enums\Gewichtsklasse;
use App\Enums\RobotStatus;
use App\Enums\TeamStatus;

describe('Gewichtsklasse enum', function (): void {
    it('has correct values', function (): void {
        expect(Gewichtsklasse::Antweight->value)->toBe('antweight');
        expect(Gewichtsklasse::Beetleweight->value)->toBe('beetleweight');
        expect(Gewichtsklasse::Featherweight->value)->toBe('featherweight');
    });

    it('has correct max weights', function (): void {
        expect(Gewichtsklasse::Antweight->maxGewichtGram())->toBe(150);
        expect(Gewichtsklasse::Beetleweight->maxGewichtGram())->toBe(1360);
        expect(Gewichtsklasse::Featherweight->maxGewichtGram())->toBe(13600);
    });

    it('has Dutch labels', function (): void {
        expect(Gewichtsklasse::Antweight->label())->toContain('150');
        expect(Gewichtsklasse::Beetleweight->label())->toContain('1,36');
    });
});

describe('TeamStatus enum', function (): void {
    it('has correct values', function (): void {
        expect(TeamStatus::Pending->value)->toBe('pending');
        expect(TeamStatus::Approved->value)->toBe('approved');
        expect(TeamStatus::Rejected->value)->toBe('rejected');
    });

    it('has Dutch labels', function (): void {
        expect(TeamStatus::Approved->label())->toBe('Goedgekeurd');
        expect(TeamStatus::Pending->label())->toBe('In behandeling');
        expect(TeamStatus::Rejected->label())->toBe('Afgewezen');
    });
});

describe('RobotStatus enum', function (): void {
    it('has correct values', function (): void {
        expect(RobotStatus::InOntwikkeling->value)->toBe('in_ontwikkeling');
        expect(RobotStatus::Gereed->value)->toBe('gereed');
        expect(RobotStatus::BattleReady->value)->toBe('battle_ready');
    });
});
