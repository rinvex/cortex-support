<?php

declare(strict_types=1);

namespace Cortex\Support\Commands\Concerns;

use ReflectionClass;
use Illuminate\Support\Arr;
use Illuminate\Filesystem\Filesystem;
use Cortex\Support\Commands\FileGenerators\Contracts\FileGenerator;

use function Laravel\Prompts\confirm;

trait CanManipulateFiles
{
    /**
     * @param  string | array<string>  $paths
     */
    protected function checkForCollision(string | array $paths): bool
    {
        foreach (Arr::wrap($paths) as $path) {
            if (! $this->fileExists($path)) {
                continue;
            }

            if (
                (! app()->runningUnitTests()) &&
                (! confirm(basename($path) . ' already exists, do you want to overwrite it?'))
            ) {
                $this->components->error("{$path} already exists, aborting.");

                return true;
            }

            unlink($path);
        }

        return false;
    }

    /**
     * @param  array<string, string>  $replacements
     */
    protected function copyStubToApp(string $stub, string $targetPath, array $replacements = []): void
    {
        $filesystem = app(Filesystem::class);

        if (! $this->fileExists($stubPath = base_path("stubs/cortex/support/{$stub}.stub"))) {
            $stubPath = $this->getDefaultStubPath() . "/{$stub}.stub";
        }

        $stub = str($filesystem->get($stubPath));

        foreach ($replacements as $key => $replacement) {
            $stub = $stub->replace("{{ {$key} }}", $replacement);
        }

        $stub = (string) $stub;

        $this->writeFile($targetPath, $stub);
    }

    protected function fileExists(string $path): bool
    {
        $filesystem = app(Filesystem::class);

        return $filesystem->exists($path);
    }

    protected function writeFile(string $path, string | FileGenerator $contents): void
    {
        $filesystem = app(Filesystem::class);

        $filesystem->ensureDirectoryExists(
            pathinfo($path, PATHINFO_DIRNAME),
        );

        $filesystem->put($path, (($contents instanceof FileGenerator) ? $contents->generate() : $contents));
    }

    protected function getDefaultStubPath(): string
    {
        $reflectionClass = new ReflectionClass($this);

        return (string) str($reflectionClass->getFileName())
            ->beforeLast('Commands')
            ->append('../stubs');
    }
}
