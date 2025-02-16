<?php

declare(strict_types=1);

namespace Cortex\Support\Assets;

class AlpineComponent extends Asset
{
    public function getPublicPath(): string
    {
        return public_path($this->getRelativePublicPath());
    }

    public function getRelativePublicPath(): string
    {
        $path = config('cortex.support.assets_path', '');

        return ltrim("{$path}/scripts/{$this->getPackage()}/components/{$this->getId()}.js", '/');
    }

    public function getSrc(): string
    {
        return asset($this->getRelativePublicPath()) . '?v=' . $this->getVersion();
    }
}
