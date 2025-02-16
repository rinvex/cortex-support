<?php

declare(strict_types=1);

namespace Cortex\Support\Facades;

use BackedEnum;
use Cortex\Support\Icons\IconManager;
use Illuminate\Support\Facades\Facade;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @method static string | null resolve(string $name)
 *
 * @see IconManager
 */
class CortexIcon extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return IconManager::class;
    }

    /**
     * @param  array<string, string | BackedEnum | Htmlable>  $icons
     */
    public static function register(array $icons): void
    {
        static::resolved(function (IconManager $iconManager) use ($icons): void {
            $iconManager->register($icons);
        });
    }
}
