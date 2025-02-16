<?php

declare(strict_types=1);

namespace Cortex\Support\View\Components\DropdownComponent\ItemComponent;

use Cortex\Support\View\Components\Contracts\HasColor;
use Cortex\Support\View\Components\Contracts\HasDefaultGrayColor;

class IconComponent implements HasColor, HasDefaultGrayColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string>
     */
    public function getColorClasses(array $color): array
    {
        return [];
    }
}
