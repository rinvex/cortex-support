<?php

declare(strict_types=1);

namespace Cortex\Support\Commands\Concerns;

use Cortex\Support\Facades\CortexCli;

use function Laravel\Prompts\select;

trait CanAskForLivewireComponentLocation
{
    /**
     * @return array{
     *     0: string,
     *     1: string,
     *     2: ?string,
     * }
     */
    protected function askForLivewireComponentLocation(string $question = 'Where would you like to create the Livewire component?'): array
    {
        $locations = CortexCli::getLivewireComponentLocations();

        if (blank($locations)) {
            return [
                'Cortex\\Custom\\Livewire',
                app_path('Livewire'),
                '',
            ];
        }

        $options = [
            null => 'Cortex\\Custom\\Livewire',
            ...array_combine(
                array_keys($locations),
                array_keys($locations),
            ),
        ];

        $namespace = select(
            label: $question,
            options: $options,
        );

        if (blank($namespace)) {
            return [
                'Cortex\\Custom\\Livewire',
                app_path('Livewire'),
                '',
            ];
        }

        return [
            $namespace,
            $locations[$namespace]['path'],
            $locations[$namespace]['viewNamespace'] ?? null,
        ];
    }
}
