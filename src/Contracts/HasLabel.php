<?php

declare(strict_types=1);

namespace Cortex\Support\Contracts;

use Illuminate\Contracts\Support\Htmlable;

interface HasLabel
{
    public function getLabel(): string | Htmlable | null;
}
