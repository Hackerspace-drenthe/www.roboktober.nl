<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Content format for pages and posts.
 *
 * html     → Written/edited via Filament RichEditor, stored as sanitized HTML
 * markdown → Plain Markdown, rendered on the frontend
 *
 * @see PLAN.md §5.2 — pages.content_format, posts.content_format
 */
enum ContentFormat: string
{
    case Html = 'html';
    case Markdown = 'markdown';
}
