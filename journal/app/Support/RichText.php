<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Journal entry text: the editor produces a small subset of HTML (bold, italic, paragraphs).
 * Older entries and quick notes may be plain text, so every helper accepts both.
 */
class RichText
{
    /**
     * Keep only the formatting the editor offers; strip everything else (scripts, styles, links...).
     */
    public static function sanitize(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $config = (new HtmlSanitizerConfig)
            ->allowElement('b')
            ->allowElement('strong')
            ->allowElement('i')
            ->allowElement('em')
            ->allowElement('p')
            ->allowElement('div')
            ->allowElement('br');

        // Unwrap other common tags but keep their text (script, style etc. are still dropped entirely)
        foreach (['a', 'span', 'font', 'u', 's', 'strike', 'sub', 'sup', 'small', 'mark', 'code', 'pre', 'blockquote',
            'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'section', 'article', 'header', 'footer'] as $tag) {
            $config = $config->blockElement($tag);
        }

        $clean = trim((new HtmlSanitizer($config))->sanitize($html));

        return self::toPlainText($clean) === '' ? null : $clean;
    }

    /**
     * HTML that is safe to show in the editor; plain-text content is escaped and keeps its line breaks.
     */
    public static function toEditorHtml(?string $content): string
    {
        if ($content === null || $content === '') {
            return '';
        }

        return self::isHtml($content) ? self::sanitize($content) ?? '' : nl2br(e($content), false);
    }

    /**
     * Text without markup, with block elements turned into line breaks.
     */
    public static function toPlainText(?string $content): string
    {
        if (! self::isHtml((string) $content)) {
            return trim((string) $content);
        }

        $text = preg_replace(['#<br\s*/?>#i', '#</?(p|div)\b[^>]*>#i'], "\n", (string) $content);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace("/\n{3,}/", "\n\n", $text));
    }

    /**
     * Markdown for exports: **bold**, *italic*, one paragraph per line.
     */
    public static function toMarkdown(?string $content): string
    {
        if (! self::isHtml((string) $content)) {
            return trim((string) $content);
        }

        $markdown = preg_replace(
            ['#<(b|strong)>(.*?)</\1>#is', '#<(i|em)>(.*?)</\1>#is'],
            ['**$2**', '*$2*'],
            (string) $content,
        );

        return self::toPlainText($markdown);
    }

    /**
     * Append a plain-text paragraph (e.g. a dashboard quick note) to existing content.
     */
    public static function appendParagraph(?string $content, string $text): string
    {
        $existing = match (true) {
            $content === null || trim($content) === '' => '',
            self::isHtml($content) => self::sanitize($content) ?? '',
            default => '<p>'.nl2br(e(trim($content)), false).'</p>',
        };

        return $existing.'<p>'.e($text).'</p>';
    }

    /**
     * Content written in the editor contains its tags; older plain text may contain a stray "<" but not these.
     */
    private static function isHtml(string $content): bool
    {
        return (bool) preg_match('#</?(b|strong|i|em|p|div|br)\b[^>]*>#i', $content);
    }
}
