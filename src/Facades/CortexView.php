<?php

declare(strict_types=1);

namespace Cortex\Support\Facades;

use Closure;
use Cortex\Support\View\ViewManager;
use Illuminate\Support\Facades\Facade;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @method static bool hasSpaMode(?string $url = null)
 * @method static Htmlable renderHook(string $name, string | array<string> | null $scopes = null)
 *
 * @see ViewManager
 */
class CortexView extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ViewManager::class;
    }

    /**
     * @param  string | array<string> | null  $scopes
     */
    public static function registerRenderHook(string $name, Closure $hook, string | array | null $scopes = null): void
    {
        static::resolved(function (ViewManager $viewManager) use ($name, $hook, $scopes): void {
            $viewManager->registerRenderHook($name, $hook, $scopes);
        });
    }
}
