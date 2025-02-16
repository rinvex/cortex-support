<?php

declare(strict_types=1);

namespace Cortex\Support\View\Components\Contracts;

interface HasColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string>
     */
    public function getColorClasses(array $color): array;
}
