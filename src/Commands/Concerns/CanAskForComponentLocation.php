<?php

declare(strict_types=1);

namespace Cortex\Support\Commands\Concerns;

use Cortex\Support\Facades\CortexCli;

use function Laravel\Prompts\select;

trait CanAskForComponentLocation
{
    /**
     * @return array{
     *     0: string,
     *     1: string,
     *     2: ?string,
     * }
     */
    protected function askForComponentLocation(string $path, string $question = 'Where would you like to create the component?'): array
    {
        $pathNamespace = (string) str($path)->replace('/', '\\');

        $locations = CortexCli::getComponentLocations();

        if (blank($locations)) {
            return [
                "Cortex\\Custom\\{$pathNamespace}",
                app_path($path),
                '',
            ];
        }

        $options = [
            null => "Cortex\\Custom\\{$pathNamespace}",
            ...array_map(
                fn (string $namespace): string => "{$namespace}\\{$pathNamespace}",
                array_combine(
                    array_keys($locations),
                    array_keys($locations),
                ),
            ),
        ];

        $namespace = select(
            label: $question,
            options: $options,
        );

        if (blank($namespace)) {
            return [
                "Cortex\\Custom\\{$pathNamespace}",
                app_path($path),
                '',
            ];
        }

        return [
            "{$namespace}\\{$pathNamespace}",
            $locations[$namespace]['path'] . '/' . $path,
            $locations[$namespace]['viewNamespace'] ?? null,
        ];
    }
}
