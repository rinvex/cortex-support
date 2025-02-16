<?php

declare(strict_types=1);

namespace Cortex\Support;

use Stringable;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Htmlable;

class Markdown implements Htmlable, Stringable
{
    final public function __construct(
        protected string $text,
        protected bool $isInline = false,
    ) {}

    public static function inline(string $text): static
    {
        return new static($text, isInline: true);
    }

    public static function block(string $text): static
    {
        return new static($text);
    }

    public function toHtml(): string
    {
        return $this->isInline
            ? Str::inlineMarkdown($this->text)
            : Str::markdown($this->text);
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }
}
