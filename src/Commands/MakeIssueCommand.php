<?php

declare(strict_types=1);

namespace Cortex\Support\Commands;

use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Cortex\Support\Commands\Concerns\CanOpenUrlInBrowser;

#[AsCommand(name: 'make:cortex-issue', aliases: [
    'cortex:issue',
    'cortex:make-issue',
])]
class MakeIssueCommand extends Command
{
    use CanOpenUrlInBrowser;

    protected $description = 'Generates a link to the Cortex issue page and pre-fills the version numbers.';

    protected $name = 'make:cortex-issue';

    /**
     * @var array<string>
     */
    protected $aliases = [
        'cortex:issue',
        'cortex:make-issue',
    ];

    public function handle(): void
    {
        $url = 'https://github.com/rinvex/cortex/issues/new?' . http_build_query([
            'template' => 'bug_report.yml',
            'package-version' => InstalledVersions::getPrettyVersion('cortex/support'),
            'laravel-version' => InstalledVersions::getPrettyVersion('laravel/framework'),
            'livewire-version' => InstalledVersions::getPrettyVersion('livewire/livewire'),
            'php-version' => PHP_VERSION,
        ]);

        $this->openUrlInBrowser($url);
    }
}
