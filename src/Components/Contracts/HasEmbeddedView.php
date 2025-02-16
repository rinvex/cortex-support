<?php

declare(strict_types=1);

namespace Cortex\Support\Components\Contracts;

interface HasEmbeddedView
{
    public function toEmbeddedHtml(): string;
}
