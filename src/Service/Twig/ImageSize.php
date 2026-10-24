<?php

namespace App\Service\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Attribute\AsTwigFunction;

/**
 * Reads the intrinsic size of a public image, so that <img> tags can declare
 * width and height and the browser reserves the space before loading (no CLS).
 */
class ImageSize
{
    /** @var array<string, array{width: int, height: int}|null> */
    private array $sizes = [];

    public function __construct(
        #[Autowire('%kernel.project_dir%/public')]
        private readonly string $publicDir,
    ) {
    }

    /**
     * @param string $path relative to public/, as given to asset()
     *
     * @return array{width: int, height: int}|null null when the file is missing or not an image
     */
    #[AsTwigFunction('image_size')]
    public function get(string $path): ?array
    {
        if (!array_key_exists($path, $this->sizes)) {
            // getimagesize only reads the file header
            $size = @getimagesize($this->publicDir . '/' . ltrim($path, '/'));
            $this->sizes[$path] = $size ? ['width' => $size[0], 'height' => $size[1]] : null;
        }

        return $this->sizes[$path];
    }
}
