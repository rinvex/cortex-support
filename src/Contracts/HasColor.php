<?php

declare(strict_types=1);

namespace Cortex\Support\Contracts;

interface HasColor
{
    /**
     * @return string | array<int | string, string | int> | null
     */
    public function getColor(): string | array | null;
}
