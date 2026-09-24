<?php

namespace Tests\Unit;

use App\Support\RichText;
use PHPUnit\Framework\TestCase;

class RichTextTest extends TestCase
{
    public function test_it_keeps_editor_formatting(): void
    {
        $html = '<h2>Title</h2><p><strong>Bold</strong> <em>italic</em> <u>under</u></p><ul><li><p>One</p></li></ul><blockquote><p>Quote</p></blockquote><hr>';

        $clean = RichText::sanitize($html);

        foreach (['<h2>Title</h2>', '<strong>Bold</strong>', '<em>italic</em>', '<u>under</u>', '<ul>', '<blockquote>', '<hr'] as $fragment) {
            $this->assertStringContainsString($fragment, $clean);
        }
    }

    public function test_it_removes_dangerous_markup(): void
    {
        $clean = RichText::sanitize('<p onclick="alert(1)">Hi<script>alert(1)</script></p><a href="javascript:alert(1)">x</a><img src=x onerror=alert(1)><iframe src="https://evil.test"></iframe><p style="color:red">styled</p>');

        $this->assertStringNotContainsString('script', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('iframe', $clean);
        $this->assertStringNotContainsString('style=', $clean);
        $this->assertStringContainsString('<p>Hi</p>', $clean);
    }

    public function test_empty_editor_content_is_blank(): void
    {
        $this->assertSame('', RichText::sanitize('<p></p>'));
        $this->assertSame('', RichText::sanitize('<p>&nbsp; </p><p><br></p>'));
    }

    public function test_links_get_safe_rel(): void
    {
        $this->assertStringContainsString('rel="noopener noreferrer"', RichText::sanitize('<p><a href="https://gekrafs.nl">site</a></p>'));
    }

    public function test_plain_text_is_converted_to_paragraphs_and_lists(): void
    {
        $html = RichText::sanitize("First paragraph.\n\nSpeakers:\n- Alice\n- Bob\n\nLine one\nline two");

        $this->assertStringContainsString('<p>First paragraph.</p>', $html);
        $this->assertStringContainsString('<p>Speakers:</p><ul><li><p>Alice</p></li><li><p>Bob</p></li></ul>', $html);
        $this->assertStringContainsString('<p>Line one<br />line two</p>', $html);
    }
}
