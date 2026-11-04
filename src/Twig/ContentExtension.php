<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Cleans HTML produced by the Trix editor at display time.
 */
final class ContentExtension extends AbstractExtension
{
    /**
     * A heading (h1-h6) or paragraph containing only whitespace, &nbsp; (entity or
     * raw U+00A0) or <br> tags.
     */
    private const EMPTY_TAG = '#<(h[1-6]|p)(?:\s[^>]*)?>(?:\s|&nbsp;|&\#160;|&\#xa0;|\x{00A0}|<br\s*/?>)*</\1>#iu';

    public function getFilters(): array
    {
        return [
            new TwigFilter('strip_empty_tags', [$this, 'stripEmptyTags']),
        ];
    }

    public function stripEmptyTags(?string $html): string
    {
        if (null === $html || '' === $html) {
            return '';
        }

        return preg_replace(self::EMPTY_TAG, '', $html) ?? $html;
    }
}
