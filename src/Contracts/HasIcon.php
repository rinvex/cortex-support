<?php

declare(strict_types=1);

namespace Cortex\Support\Contracts;

use BackedEnum;

interface HasIcon
{
    public function getIcon(): string | BackedEnum | null;
}
