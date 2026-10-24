<?php

namespace App\Service;

use App\Entity\Blog;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Computes the real last modification date of a page, shared by the HTTP cache
 * validators and the sitemap so that both always agree.
 *
 * Template dates rely on file mtimes, which git pull updates on deploy.
 */
class PageLastModified
{
    // Files rendered on every page
    private const LAYOUT_PATTERNS = [
        'templates/base.html.twig',
        'templates/_partials/*.twig',
        'templates/components/*.twig',
        'templates/components/*/*.twig',
        'translations/*.yaml',
    ];

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
    }

    /**
     * @param string[]   $templatePatterns globs relative to templates/
     * @param iterable<object> $entities   entities with a getUpdatedAt() method
     */
    public function compute(array $templatePatterns, iterable $entities = []): \DateTimeImmutable
    {
        $patterns = array_merge(
            self::LAYOUT_PATTERNS,
            array_map(fn (string $pattern) => 'templates/' . $pattern, $templatePatterns)
        );

        $files = [];
        foreach ($patterns as $pattern) {
            $files = array_merge($files, glob($this->projectDir . '/' . $pattern) ?: []);
        }

        $timestamp = max(array_map('filemtime', $files) ?: [0]);

        foreach ($entities as $entity) {
            $updatedAt = $entity->getUpdatedAt();
            if ($updatedAt !== null && $updatedAt->getTimestamp() > $timestamp) {
                $timestamp = $updatedAt->getTimestamp();
            }
        }

        return (new \DateTimeImmutable())->setTimestamp($timestamp);
    }

    /**
     * @param Blog[] $pinnedBlogs
     */
    public function home(array $pinnedBlogs): \DateTimeImmutable
    {
        return $this->compute(['home/index.html.twig', 'home/_*.twig'], $pinnedBlogs);
    }
}
