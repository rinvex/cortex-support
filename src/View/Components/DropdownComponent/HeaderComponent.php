<?php

declare(strict_types=1);

namespace Cortex\Support\View\Components\DropdownComponent;

use Cortex\Support\Colors\Color;
use Cortex\Support\Facades\CortexColor;
use Cortex\Support\View\Components\Contracts\HasColor;
use Cortex\Support\View\Components\Contracts\HasDefaultGrayColor;

class HeaderComponent implements HasColor, HasDefaultGrayColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string>
     */
    public function getColorClasses(array $color): array
    {
        $gray = CortexColor::getColor('secondary');

        ksort($color);

        foreach (array_keys($color) as $shade) {
            if ($shade < 600) {
                continue;
            }

            if (Color::isTextContrastRatioAccessible('oklch(1 0 0)', $color[$shade])) {
                $text = $shade;

                break;
            }
        }

        $text ??= 950;

        krsort($color);

        foreach (array_keys($color) as $shade) {
            if ($shade > 400) {
                continue;
            }

            if (Color::isTextContrastRatioAccessible($gray[900], $color[$shade])) {
                $darkText = $shade;

                break;
            }
        }

        $darkText ??= 200;

        return [
            "fi-text-color-{$text}",
            "dark:fi-text-color-{$darkText}",
        ];
    }
}
