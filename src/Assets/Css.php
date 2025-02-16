<?php

declare(strict_types=1);

namespace Cortex\Support\Assets;

use Closure;
use Illuminate\Foundation\Vite;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\Support\Htmlable;

class Css extends Asset
{
    protected string | Htmlable | Closure | null $html = null;

    protected ?string $relativePublicPath = null;

    public function html(string | Htmlable | Closure | null $html): static
    {
        $this->html = $html;

        return $this;
    }

    public function relativePublicPath(?string $relativePublicPath): static
    {
        $this->relativePublicPath = $relativePublicPath;

        return $this;
    }

    public function getHref(): string
    {
        if ($this->isRemote()) {
            return $this->getPath();
        }

        return asset($this->getRelativePublicPath()) . '?v=' . $this->getVersion();
    }

    public function getHtml(): Htmlable
    {
        $html = value($this->html);

        if (str($html)->contains('<link')) {
            return $html instanceof Htmlable ? $html : new HtmlString($html);
        }

        $html ??= $this->getHref();

        // @Fix: Support Vite compiled assets!
        return $this->isRemote() ? new HtmlString("<link
            href=\"{$html}\"
            rel=\"stylesheet\"
            data-navigate-track
        />") : app(Vite::class)($this->getPath());
    }

    public function getRelativePublicPath(): string
    {
        if (filled($this->relativePublicPath)) {
            return $this->relativePublicPath;
        }

        $path = config('cortex.support.assets_path', '');

        return ltrim("{$path}/styles/{$this->getPackage()}/{$this->getId()}.css", '/');
    }

    public function getPublicPath(): string
    {
        return public_path($this->getRelativePublicPath());
    }
}
