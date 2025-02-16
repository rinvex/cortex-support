<?php

declare(strict_types=1);

namespace Cortex\Support\Commands;

use Closure;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Cortex\Support\Commands\Concerns\CanCachePanelComponents;

#[AsCommand(name: 'cortex:optimize-clear')]
class OptimizeClearCommand extends Command
{
    use CanCachePanelComponents;

    protected $description = 'Remove the cached components and Blade icons';

    protected $name = 'cortex:optimize-clear';

    public function handle(): int
    {
        $this->components->info('Clearing cached components and Blade icons.');

        $tasks = collect();

        if ($this->canCachePanelComponents()) {
            $tasks->put(
                'Clearing cached components',
                fn (): bool => $this->callSilent('cortex:clear-cached-components') === static::SUCCESS,
            );
        }

        $tasks->put(
            'Clearing cached Blade icons',
            fn (): bool => $this->callSilent('icons:clear') === static::SUCCESS,
        );

        $tasks->each(fn (Closure $task, string $description) => $this->components->task($description, $task));

        $this->newLine();

        return static::SUCCESS;
    }
}
