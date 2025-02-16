<?php

declare(strict_types=1);

namespace Cortex\Support\Commands;

use Closure;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Cortex\Support\Commands\Concerns\CanCachePanelComponents;

#[AsCommand(name: 'cortex:optimize')]
class OptimizeCommand extends Command
{
    use CanCachePanelComponents;

    protected $description = 'Cache components and Blade icons to increase performance';

    protected $name = 'cortex:optimize';

    public function handle(): int
    {
        $this->components->info('Caching components and Blade icons.');

        $tasks = collect();

        if ($this->canCachePanelComponents()) {
            $tasks->put(
                'Caching components',
                fn (): bool => $this->callSilent('cortex:cache-components') === static::SUCCESS
            );
        }

        $tasks->put('Caching Blade icons', fn (): bool => $this->callSilent('icons:cache') === static::SUCCESS);

        $tasks->each(fn (Closure $task, string $description) => $this->components->task($description, $task));

        $this->newLine();

        return static::SUCCESS;
    }
}
