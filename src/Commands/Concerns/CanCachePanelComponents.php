<?php

declare(strict_types=1);

namespace Cortex\Support\Commands\Concerns;

use Cortex\Panels\Commands\CacheComponentsCommand;

trait CanCachePanelComponents
{
    protected function canCachePanelComponents(): bool
    {
        return class_exists(CacheComponentsCommand::class);
    }
}
