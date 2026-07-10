<?php

declare(strict_types=1);

namespace App\Services\Security;

use DOMDocument;
use DOMElement;
use DOMXPath;

class HtmlSanitizer
{
    /**
     * Remove executable and high-risk HTML while keeping basic content markup.
     */
    public static function sanitize(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        $previous = libxml_use_internal_errors(true);

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadHTML(
            '<?xml encoding="utf-8" ?><div id="root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING,
        );

        $xpath = new DOMXPath($document);

        foreach (['script', 'style', 'iframe', 'object', 'embed', 'link', 'meta'] as $tagName) {
            $nodes = $xpath->query('//'.$tagName);

            if ($nodes === false) {
                continue;
            }

            foreach ($nodes as $node) {
                $node->parentNode?->removeChild($node);
            }
        }

        $elements = $xpath->query('//*');

        if ($elements !== false) {
            foreach ($elements as $element) {
                if (! $element instanceof DOMElement || ! $element->hasAttributes()) {
                    continue;
                }

                $attributes = [];
                foreach ($element->attributes as $attribute) {
                    $attributes[] = $attribute->name;
                }

                foreach ($attributes as $attributeName) {
                    $value = (string) $element->getAttribute($attributeName);
                    $normalized = strtolower(trim($value));

                    if (str_starts_with(strtolower($attributeName), 'on')) {
                        $element->removeAttribute($attributeName);
                        continue;
                    }

                    if ($attributeName === 'style') {
                        $element->removeAttribute($attributeName);
                        continue;
                    }

                    if (in_array(strtolower($attributeName), ['href', 'src', 'xlink:href'], true)) {
                        if (
                            str_starts_with($normalized, 'javascript:')
                            || str_starts_with($normalized, 'vbscript:')
                            || str_starts_with($normalized, 'data:text/html')
                        ) {
                            $element->removeAttribute($attributeName);
                        }
                    }
                }
            }
        }

        $root = $document->getElementById('root');
        if (! $root instanceof DOMElement) {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);

            return '';
        }

        $sanitizedHtml = '';
        foreach ($root->childNodes as $childNode) {
            $sanitizedHtml .= $document->saveHTML($childNode) ?: '';
        }

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $sanitizedHtml;
    }
}
