<?php

declare(strict_types=1);

namespace Cortex\Support\Commands;

use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Cortex\Panels\Providers\PanelsServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Cortex\Support\Commands\Concerns\CanGeneratePanels;
use Cortex\Support\Commands\Concerns\CanManipulateFiles;
use Cortex\Support\Commands\Concerns\CanOpenUrlInBrowser;
use Cortex\Support\Commands\Exceptions\FailureCommandOutput;

use function Laravel\Prompts\confirm;

#[AsCommand(name: 'cortex:install')]
class InstallCommand extends Command
{
    use CanGeneratePanels;
    use CanManipulateFiles;
    use CanOpenUrlInBrowser;

    protected $description = 'Install Cortex';

    protected $name = 'cortex:install';

    /**
     * @return array<InputOption>
     */
    protected function getOptions(): array
    {
        return [
            new InputOption(
                name: 'panels',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Install the panel builder and create the first panel',
            ),
            new InputOption(
                name: 'scaffold',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Install the Cortex packages for use outside of panels, in your Blade or Livewire application',
            ),
            new InputOption(
                name: 'notifications',
                shortcut: null,
                mode: InputOption::VALUE_NONE,
                description: 'Install the Cortex flash notifications into the scaffolded layout file',
            ),
            new InputOption(
                name: 'force',
                shortcut: 'F',
                mode: InputOption::VALUE_NONE,
                description: 'Overwrite the contents of the files if they already exist',
            ),
        ];
    }

    public function __invoke(): int
    {
        try {
            $this->installAdminPanel();
            $this->installScaffolding();
            $this->installUpgradeCommand();
        } catch (FailureCommandOutput) {
            return static::FAILURE;
        }

        $this->call(UpgradeCommand::class);

        $this->askToStar();

        return static::SUCCESS;
    }

    protected function installAdminPanel(): void
    {
        if (! $this->option('panels')) {
            return;
        }

        if (! class_exists(PanelsServiceProvider::class)) {
            $this->components->error('Please require [cortex/panels] before attempting to install the Panel Builder.');

            throw new FailureCommandOutput;
        }

        $this->generatePanel(defaultId: 'admin', isForced: $this->option('force'));
    }

    protected function installScaffolding(): void
    {
        if (! $this->option('scaffold')) {
            return;
        }

        static::updateNpmPackages();

        $filesystem = app(Filesystem::class);
        $filesystem->delete(resource_path('js/bootstrap.js'));
        $filesystem->copyDirectory(__DIR__.'/../../stubs/scaffolding', base_path());

        if (
            InstalledVersions::isInstalled('cortex/notifications') &&
            ($this->option('notifications') || confirm(
                label: 'Would you like to be able to send flash notifications using Cortex? If so, we will install the notification Livewire component into the base layout file.',
                default: true,
            ))
        ) {
            $layout = $filesystem->get(resource_path('views/components/layouts/app.blade.php'));
            $layout = (string) str($layout)
                ->replace('{{ $slot }}', '{{ $slot }}' . PHP_EOL . PHP_EOL . '        @livewire(\'notifications\')');
            $filesystem->put(resource_path('views/components/layouts/app.blade.php'), $layout);
        }

        $this->components->info('Scaffolding installed successfully.');

        $this->components->info('Please run `npm install && npm run dev` to compile your new assets.');
    }

    protected static function updateNpmPackages(bool $dev = true): void
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), associative: true);

        $packages[$configurationKey] = static::updateNpmPackageArray(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : []
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }

    /**
     * @param  array<string, string>  $packages
     * @return array<string, string>
     */
    protected static function updateNpmPackageArray(array $packages): array
    {
        return [
            '@tailwindcss/forms' => '^0.5.2',
            '@tailwindcss/typography' => '^0.5.4',
            'tailwindcss' => '^4.0.0',
        ];
    }

    protected function installUpgradeCommand(): void
    {
        $path = base_path('composer.json');

        if (! file_exists($path)) {
            return;
        }

        $configuration = json_decode(file_get_contents($path), associative: true);

        $command = '@php artisan cortex:upgrade';

        if (in_array($command, $configuration['scripts']['post-autoload-dump'] ?? [])) {
            return;
        }

        $configuration['scripts']['post-autoload-dump'] ??= [];
        $configuration['scripts']['post-autoload-dump'][] = $command;

        file_put_contents(
            $path,
            (string) str(json_encode($configuration, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
                ->append(PHP_EOL)
                ->replace(
                    search: "    \"keywords\": [\n        \"laravel\",\n        \"framework\"\n    ],",
                    replace: '    "keywords": ["laravel", "framework"],',
                )
                ->replace(
                    search: "    \"keywords\": [\n        \"framework\",\n        \"laravel\"\n    ],",
                    replace: '    "keywords": ["framework", "laravel"],',
                ),
        );
    }

    protected function askToStar(): void
    {
        if ($this->option('no-interaction')) {
            return;
        }

        if (! confirm(
            label: 'All done! Would you like to show some love by starring the Cortex repo on GitHub?',
            default: true,
        )) {
            return;
        }

        $this->openUrlInBrowser('https://github.com/rinvex/cortex');
    }
}
