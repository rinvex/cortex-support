<?php

declare(strict_types=1);

namespace Cortex\Support\Commands;

use Illuminate\Console\Command;
use Cortex\Support\Events\CortexUpgraded;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'cortex:upgrade')]
class UpgradeCommand extends Command
{
    protected $description = 'Upgrade Cortex to the latest version';

    protected $name = 'cortex:upgrade';

    public function handle(): int
    {
        foreach ([
            // @TODO: we're not using `AssetsCommand::class` logic as we're compiling all assets using app-level npm/vite
            AssetsCommand::class,

            // @TODO: why not use `php artisan optimize` directly?!
            'config:clear',
            'route:clear',
            'view:clear',
        ] as $command) {
            $this->call($command);
        }

        CortexUpgraded::dispatch();

        $this->components->info('Successfully upgraded!');

        return static::SUCCESS;
    }
}
