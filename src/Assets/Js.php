<?php

declare(strict_types=1);

namespace Cortex\Support\Assets;

use Illuminate\Foundation\Vite;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\Support\Htmlable;

class Js extends Asset
{
    protected bool $isAsync = false;

    protected bool $isDeferred = false;

    protected bool $isCore = false;

    protected bool $isNavigateOnce = true;

    protected bool $isModule = false;

    /**
     * @var array<string, string>
     */
    protected array $extraAttributes = [];

    protected string | Htmlable | null $html = null;

    public function async(bool $condition = true): static
    {
        $this->isAsync = $condition;

        return $this;
    }

    public function defer(bool $condition = true): static
    {
        $this->isDeferred = $condition;

        return $this;
    }

    public function core(bool $condition = true): static
    {
        $this->isCore = $condition;

        return $this;
    }

    public function navigateOnce(bool $condition = true): static
    {
        $this->isNavigateOnce = $condition;

        return $this;
    }

    public function module(bool $condition = true): static
    {
        $this->isModule = $condition;

        return $this;
    }

    public function html(string | Htmlable | null $html): static
    {
        $this->html = $html;

        return $this;
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    public function isDeferred(): bool
    {
        return $this->isDeferred;
    }

    public function isCore(): bool
    {
        return $this->isCore;
    }

    public function isNavigateOnce(): bool
    {
        return $this->isNavigateOnce;
    }

    public function isModule(): bool
    {
        return $this->isModule;
    }

    /**
     * @param  array<string, string>  $attributes
     */
    public function extraAttributes(array $attributes): static
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    public function getHtml(): Htmlable
    {
        $html = $this->html;

        if (str($html)->contains('<script')) {
            return $html instanceof Htmlable ? $html : new HtmlString($html);
        }

        $html ??= $this->getSrc();

        $async = $this->isAsync() ? 'async' : '';
        $defer = $this->isDeferred() ? 'defer' : '';
        $module = $this->isModule() ? 'type="module"' : '';
        $extraAttributesHtml = $this->getExtraAttributesHtml();

        $hasSpaMode = cortex()->getCurrentOrDefaultPanel()->hasSpaMode();

        $navigateOnce = ($hasSpaMode && $this->isNavigateOnce()) ? 'data-navigate-once' : '';
        $navigateTrack = $hasSpaMode ? 'data-navigate-track' : '';

        // @Fix: Support Vite compiled assets!
        return $this->isRemote() ? new HtmlString("
            <script
                src=\"{$html}\"
                {$async}
                {$defer}
                {$module}
                {$extraAttributesHtml}
                {$navigateOnce}
                {$navigateTrack}
            ></script>
        ") : app(Vite::class)->useScriptTagAttributes([$async, $defer, $module, $extraAttributesHtml, $navigateOnce, $navigateTrack])($this->getPath());
    }

    /**
     * @return array<string, string>
     */
    public function getExtraAttributes(): array
    {
        return $this->extraAttributes;
    }

    public function getExtraAttributesHtml(): string
    {
        $attributes = '';

        foreach ($this->getExtraAttributes() as $key => $value) {
            $attributes .= " {$key}=\"{$value}\"";
        }

        return $attributes;
    }

    public function getSrc(): string
    {
        if ($this->isRemote()) {
            return $this->getPath();
        }

        return asset($this->getRelativePublicPath()) . '?v=' . $this->getVersion();
    }

    public function getRelativePublicPath(): string
    {
        $path = config('cortex.support.assets_path', '');

        return ltrim("{$path}/scripts/{$this->getPackage()}/{$this->getId()}.js", '/');
    }

    public function getPublicPath(): string
    {
        return public_path($this->getRelativePublicPath());
    }
}
