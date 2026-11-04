<?php

namespace App\Tests\Twig;

use App\Twig\ContentExtension;
use PHPUnit\Framework\TestCase;

class ContentExtensionTest extends TestCase
{
    private ContentExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new ContentExtension();
    }

    /**
     * @dataProvider emptyTags
     */
    public function testRemovesEmptyTags(string $html): void
    {
        self::assertSame('<p>Avant</p><p>Après</p>', $this->extension->stripEmptyTags('<p>Avant</p>'.$html.'<p>Après</p>'));
    }

    public function emptyTags(): iterable
    {
        yield 'empty h2' => ['<h2>&nbsp;</h2>'];
        yield 'empty h3' => ['<h3>&nbsp;</h3>'];
        yield 'empty paragraph' => ['<p>&nbsp;</p>'];
        yield 'paragraph with raw non-breaking space' => ["<p>\u{00A0}</p>"];
        yield 'paragraph with whitespace only' => ["<p> \n </p>"];
        yield 'paragraph with a lone br' => ['<p><br></p>'];
        yield 'paragraph with self-closing br' => ['<p><br /></p>'];
        yield 'heading with attributes' => ['<h2 class="x">&nbsp;</h2>'];
    }

    public function testKeepsFilledParagraph(): void
    {
        $html = '<p>Un paragraphe&nbsp;rempli.</p>';

        self::assertSame($html, $this->extension->stripEmptyTags($html));
    }

    public function testKeepsFilledHeadingAndParagraphWithBr(): void
    {
        $html = '<h2>Titre</h2><p>Ligne 1<br>Ligne 2</p>';

        self::assertSame($html, $this->extension->stripEmptyTags($html));
    }

    public function testDoesNotMatchAcrossDifferentTags(): void
    {
        $html = '<p>&nbsp;</h2>';

        self::assertSame($html, $this->extension->stripEmptyTags($html));
    }

    public function testNullAndEmptyInput(): void
    {
        self::assertSame('', $this->extension->stripEmptyTags(null));
        self::assertSame('', $this->extension->stripEmptyTags(''));
    }
}
