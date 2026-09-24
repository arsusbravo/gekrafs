<?php

namespace App\Support;

use Illuminate\Support\Str;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * HTML content written with the admin's rich text editor (event descriptions and news bodies).
 */
class RichText
{
    private static ?HtmlSanitizer $sanitizer = null;

    /**
     * Strip everything the editor cannot produce: scripts, styles, event handlers, unsafe links.
     */
    public static function sanitize(?string $html): string
    {
        if (self::isBlank($html)) {
            return '';
        }

        return trim(self::sanitizer()->sanitize(self::isHtml($html) ? $html : self::fromPlainText($html)));
    }

    /**
     * Convert legacy plain text (blank lines between paragraphs, "- " list items) to HTML.
     */
    public static function fromPlainText(string $text): string
    {
        $blocks = preg_split("/\R{2,}/", trim(str_replace("\r\n", "\n", $text)));

        return collect($blocks)->map(function (string $block) {
            $lines = explode("\n", $block);
            $items = array_filter($lines, fn ($line) => Str::startsWith(ltrim($line), ['- ', '* ']));

            // A heading line followed by list items, e.g. "Speakers:\n- One\n- Two".
            if ($items && count($items) >= count($lines) - 1) {
                $intro = count($items) < count($lines) ? '<p>'.e(array_shift($lines)).'</p>' : '';
                $list = collect($lines)->map(fn ($line) => '<li><p>'.e(ltrim(ltrim($line), '-* ')).'</p></li>')->implode('');

                return $intro.'<ul>'.$list.'</ul>';
            }

            return '<p>'.implode('<br>', array_map('e', $lines)).'</p>';
        })->implode('');
    }

    /**
     * True when the HTML has no visible text, e.g. "<p></p>" from an empty editor.
     */
    public static function isBlank(?string $html): bool
    {
        return trim(html_entity_decode(strip_tags($html ?? '')), " \t\n\r\0\x0B\u{A0}") === '';
    }

    public static function isHtml(string $value): bool
    {
        return (bool) preg_match('/<(p|h[1-6]|ul|ol|li|blockquote|br|strong|em|a)[\s>\/]/i', $value);
    }

    private static function sanitizer(): HtmlSanitizer
    {
        return self::$sanitizer ??= new HtmlSanitizer(
            (new HtmlSanitizerConfig)
                ->allowElement('p')
                ->allowElement('br')
                ->allowElement('strong')
                ->allowElement('b')
                ->allowElement('em')
                ->allowElement('i')
                ->allowElement('u')
                ->allowElement('s')
                ->allowElement('h2')
                ->allowElement('h3')
                ->allowElement('h4')
                ->allowElement('ul')
                ->allowElement('ol', ['start'])
                ->allowElement('li')
                ->allowElement('blockquote')
                ->allowElement('hr')
                ->allowElement('code')
                ->allowElement('pre')
                ->allowElement('a', ['href', 'target'])
                ->allowLinkSchemes(['http', 'https', 'mailto', 'tel'])
                ->allowRelativeLinks()
                ->forceAttribute('a', 'rel', 'noopener noreferrer')
        );
    }
}
