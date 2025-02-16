<?php

declare(strict_types=1);

namespace Cortex\Support\Contracts;

use Cortex\Support\Enums\IconSize;

interface ScalableIcon
{
    public function getIconForSize(IconSize $size): string;
}
