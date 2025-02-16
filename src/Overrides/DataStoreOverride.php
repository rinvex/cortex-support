<?php

declare(strict_types=1);

namespace Cortex\Support\Overrides;

use Livewire\Livewire;
use Livewire\Component;
use Livewire\Mechanisms\DataStore;
use Cortex\Support\Partials\SupportPartials;

class DataStoreOverride extends DataStore
{
    /**
     * @param  Component  $instance
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($instance, $key, $default = null)
    {
        if ($key !== 'skipRender') {
            return parent::get($instance, $key, $default);
        }

        if (! Livewire::isLivewireRequest()) {
            return parent::get($instance, $key, $default);
        }

        if ($trueOrPlaceholderHtml = parent::get($instance, $key, $default)) {
            return $trueOrPlaceholderHtml;
        }

        $supportPartials = app(SupportPartials::class);
        $supportPartials->setComponent($instance);

        return $supportPartials->shouldSkipRender();
    }
}
