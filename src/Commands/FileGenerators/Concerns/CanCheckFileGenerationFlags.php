<?php

declare(strict_types=1);

namespace Cortex\Support\Commands\FileGenerators\Concerns;

trait CanCheckFileGenerationFlags
{
    protected function hasFileGenerationFlag(string $flag): bool
    {
        return in_array($flag, config('cortex.support.file_generation.flags') ?? []);
    }
}
