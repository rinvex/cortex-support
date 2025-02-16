<?php

declare(strict_types=1);

namespace Cortex\Support\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Cortex\Support\Facades\CortexAsset;
use Symfony\Component\Console\Attribute\AsCommand;
use Cortex\Support\Commands\Concerns\CanManipulateFiles;

#[AsCommand(name: 'cortex:assets')]
class AssetsCommand extends Command
{
    use CanManipulateFiles;

    protected $description = 'Set up Cortex assets';

    protected $name = 'cortex:assets';

    /** @var array<string> */
    protected array $publishedAssets = [];

    public function handle(Filesystem $filesystem): int
    {
        foreach (CortexAsset::getAlpineComponents() as $asset) {
            if ($asset->isRemote()) {
                continue;
            }

            $this->copyAsset($asset->getPath(), $asset->getPublicPath());
        }

        foreach (CortexAsset::getFonts() as $asset) {
            $assetPublicPath = $asset->getPublicPath();

            foreach ($filesystem->allFiles($asset->getPath()) as $file) {
                if ($file->getExtension() === 'js') {
                    continue;
                }

                $this->copyAsset($file->getPathname(), $assetPublicPath . DIRECTORY_SEPARATOR . $file->getFilename());
            }
        }

        foreach (CortexAsset::getScripts() as $asset) {
            if ($asset->isRemote()) {
                continue;
            }

            $this->copyAsset($asset->getPath(), $asset->getPublicPath());
        }

        foreach (CortexAsset::getStyles() as $asset) {
            if ($asset->isRemote()) {
                continue;
            }

            $this->copyAsset($asset->getPath(), $asset->getPublicPath());
        }

        foreach (CortexAsset::getThemes() as $asset) {
            if ($asset->isRemote()) {
                continue;
            }

            $this->copyAsset($asset->getPath(), $asset->getPublicPath());
        }

        $this->components->bulletList($this->publishedAssets);

        $this->components->info('Successfully published assets!');

        return static::SUCCESS;
    }

    protected function copyAsset(string $from, string $to): void
    {
        $filesystem = app(Filesystem::class);

        [$from, $to] = str_replace('/', DIRECTORY_SEPARATOR, [$from, $to]);

        $filesystem->ensureDirectoryExists(
            (string) str($to)
                ->beforeLast(DIRECTORY_SEPARATOR),
        );

        $filesystem->copy($from, $to);

        $this->publishedAssets[] = $to;
    }
}
