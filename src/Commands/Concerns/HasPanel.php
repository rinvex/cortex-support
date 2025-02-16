<?php

declare(strict_types=1);

namespace Cortex\Support\Commands\Concerns;

use Cortex\Panels\Panel;
use Illuminate\Support\Arr;

use function Laravel\Prompts\select;
use function Laravel\Prompts\confirm;

trait HasPanel
{
    protected ?Panel $panel;

    protected function configurePanel(string $question, ?string $initialQuestion = null): void
    {
        if (! class_exists(Panel::class)) {
            $this->panel = null;

            return;
        }

        $panelName = $this->option('panel');

        $this->panel = filled($panelName) ? cortex()->getPanel($panelName, isStrict: false) : null;

        if ($this->panel) {
            return;
        }

        if (filled($initialQuestion) && (! confirm(label: $initialQuestion))) {
            $this->panel = null;

            return;
        }

        $panels = cortex()->getPanels();

        /** @var Panel $panel */
        $panel = (count($panels) > 1) ? $panels[select(
            label: $question,
            options: array_map(
                fn (Panel $panel): string => $panel->getId(),
                $panels,
            ),
            default: cortex()->getDefaultPanel()->getId(),
        )] : Arr::first($panels);

        $this->panel = $panel;
    }
}
