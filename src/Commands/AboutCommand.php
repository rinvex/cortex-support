<?php

declare(strict_types=1);

namespace Cortex\Support\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'cortex:about')]
class AboutCommand extends Command
{
    protected $description = 'Display basic information about Cortex packages that are installed';

    protected $name = 'cortex:about';

    public function handle(): void
    {
        $this->call('about', [
            '--only' => 'cortex',
        ]);
    }
}
