<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

/**
 * Rector configuration for Roboktober API.
 *
 * Automated refactoring to:
 * - PHP 8.2+ features (enums, readonly, fibers, etc.)
 * - Dead code removal
 * - Type-safe patterns
 *
 * @see https://getrector.org/documentation
 * @see PLAN.md §8.1 — code quality standards
 */
return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/database',
        __DIR__.'/tests',
    ])
    ->withPhpSets(php82: true)
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
        SetList::PRIVATIZATION,
    ])
    ->withSkip([
        // Skip Filament-generated files — they follow their own conventions
        __DIR__.'/app/Providers/Filament',
    ])
    ->withImportNames(removeUnusedImports: true);
