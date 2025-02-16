<?php

declare(strict_types=1);

namespace Cortex\Support\Concerns;

use Closure;

trait CanGrow
{
    protected bool | Closure | null $canGrow = null;

    public function grow(bool | Closure | null $condition = true): static
    {
        $this->canGrow = $condition;

        return $this;
    }

    public function canGrow(bool $default = true): bool
    {
        return (bool) ($this->evaluate($this->canGrow) ?? $default);
    }
}
