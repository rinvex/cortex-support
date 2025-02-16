<?php

declare(strict_types=1);

namespace Cortex\Support\Commands\FileGenerators\Contracts;

interface FileGenerator
{
    public function generate(): string;
}
