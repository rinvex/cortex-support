<?php

declare(strict_types=1);

namespace Cortex\Support\View\Components;

use Cortex\Support\Colors\Color;
use Cortex\Support\Facades\CortexColor;
use Cortex\Support\View\Components\Contracts\HasColor;
use Cortex\Support\View\Components\Contracts\HasDefaultGrayColor;

class ToggleComponent implements HasColor, HasDefaultGrayColor
{
    /**
     * @param  array<int, string>  $color
     * @return array<string>
     */
    public function getColorClasses(array $color): array
    {
        $gray = CortexColor::getColor('secondary');

        ksort($color);

        /**
         * Since the toggle doesn't contain text, the icon may be imperative for the user to understand the
         * button's state. Therefore, the color should contrast at least 3:1 with the background to
         * remain compliant with WCAG AA standards.
         *
         * @ref https://www.w3.org/WAI/WCAG21/Understanding/non-bg-contrast.html
         */
        foreach (array_keys($color) as $shade) {
            if (Color::isNonTextContrastRatioAccessible('oklch(1 0 0)', $color[$shade])) {
                $text = $shade;

                break;
            }
        }

        $text ??= 900;

        /**
         * Since the toggle doesn't contain text, the color is imperative for the user to understand the
         * button's state. Therefore, the color should contrast at least 3:1 with the background to
         * remain compliant with WCAG AA standards.
         *
         * @ref https://www.w3.org/WAI/WCAG21/Understanding/non-bg-contrast.html
         */
        $darkestLightGrayBg = $gray[50];

        foreach (array_keys($color) as $shade) {
            if (Color::isNonTextContrastRatioAccessible($darkestLightGrayBg, $color[$shade])) {
                $bg = $shade;

                break;
            }
        }

        $bg ??= 900;

        krsort($color);

        $lightestDarkGrayBg = $gray[700];

        foreach (array_keys($color) as $shade) {
            if (Color::isNonTextContrastRatioAccessible($lightestDarkGrayBg, $color[$shade])) {
                $darkBg = $shade;

                break;
            }
        }

        $darkBg ??= 200;

        return [
            "fi-bg-color-{$bg}",
            "fi-text-color-{$text}",
            "dark:fi-bg-color-{$darkBg}",
        ];
    }
}
