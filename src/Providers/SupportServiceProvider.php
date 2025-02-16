<?php

declare(strict_types=1);

namespace Cortex\Support\Providers;

use Livewire\Livewire;
use Illuminate\Support\Str;
use Cortex\Support\Assets\Js;
use Cortex\Support\Assets\Css;
use Cortex\Support\CliManager;
use Composer\InstalledVersions;
use Illuminate\Support\Stringable;
use Livewire\Mechanisms\DataStore;
use Cortex\Support\View\ViewManager;
use Illuminate\Support\Facades\File;
use Cortex\Support\Icons\IconManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Cortex\Support\Assets\AssetManager;
use Cortex\Support\Colors\ColorManager;
use Cortex\Support\Enums\GridDirection;
use Cortex\Support\Facades\CortexAsset;
use Cortex\Support\Facades\CortexColor;
use Illuminate\View\ComponentAttributeBag;
use Laravel\Octane\Events\RequestReceived;
use Cortex\Support\Partials\SupportPartials;
use Cortex\Support\Components\ComponentManager;
use Cortex\Support\Overrides\DataStoreOverride;
use Illuminate\Foundation\Console\AboutCommand;
use Cortex\Panels\Commands\CacheComponentsCommand;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Cortex\Support\View\Components\Contracts\HasColor;
use Rinvex\Packages\Providers\PackageServiceProvider;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Cortex\Support\Components\Contracts\ScopedComponentManager;
use Livewire\Mechanisms\PersistentMiddleware\PersistentMiddleware;

class SupportServiceProvider extends PackageServiceProvider
{
    public function packageRegistered(): void
    {
        $this->app->scoped(
            AssetManager::class,
            fn () => new AssetManager,
        );

        $this->app->singleton(
            CliManager::class,
            fn () => new CliManager,
        );

        $this->app->scoped(
            ScopedComponentManager::class,
            fn () => $this->app->make(ComponentManager::class)->clone(),
        );
        $this->app->booted(fn () => ComponentManager::resolveScoped());
        class_exists(RequestReceived::class) && Event::listen(RequestReceived::class, fn () => ComponentManager::resolveScoped());

        $this->app->scoped(
            ColorManager::class,
            fn () => new ColorManager,
        );

        $this->app->scoped(
            IconManager::class,
            fn () => new IconManager,
        );

        $this->app->scoped(
            ViewManager::class,
            fn () => new ViewManager,
        );

        $this->app->scoped(
            HtmlSanitizerInterface::class,
            fn (): HtmlSanitizer => new HtmlSanitizer(
                (new HtmlSanitizerConfig)
                    ->allowSafeElements()
                    ->allowRelativeLinks()
                    ->allowRelativeMedias()
                    ->allowAttribute('class', allowedElements: '*')
                    ->allowAttribute('style', allowedElements: '*')
                    ->withMaxInputLength(500000),
            ),
        );

        $this->app->scoped(
            'originalRequest',
            function () {
                if (! Livewire::isLivewireRequest()) {
                    return request();
                }

                $persistentMiddleware = app(PersistentMiddleware::class);

                /** @phpstan-ignore-next-line */
                $request = invade($persistentMiddleware)->makeFakeRequest();

                /** @phpstan-ignore-next-line */
                invade($persistentMiddleware)->getRouteFromRequest($request);

                return $request;
            },
        );

        $this->app->singleton(DataStore::class, DataStoreOverride::class);
    }

    public function packageBooted(): void
    {
        app('livewire')->componentHook(new SupportPartials);

        CortexAsset::register([
        //    Js::make('support', __DIR__.'/../../dist/index.js'),
        //    Css::make('support', str(realpath(__DIR__.'/../../dist/index.css'))->after(base_path('/'))->toString()),
            Css::make('support', str(realpath(__DIR__.'/../../resources/styles/index.css'))->after(base_path('/'))->toString()),
        ], 'cortex.support');

        Blade::directive('cortexScripts', function (string $expression): string {
            return "<?php echo \Cortex\Support\Facades\CortexAsset::renderScripts({$expression}) ?>";
        });

        Blade::directive('cortexStyles', function (string $expression): string {
            return "<?php echo \Cortex\Support\Facades\CortexAsset::renderStyles({$expression}) ?>";
        });

        Blade::extend(function ($view) {
            return preg_replace('/\s*@trim\s*/m', '', $view);
        });

        ComponentAttributeBag::macro('color', function (string | HasColor $component, ?string $color): ComponentAttributeBag {
            return $this->class(CortexColor::getComponentClasses($component, $color));
        });

        ComponentAttributeBag::macro('grid', function (array | int | null $columns = [], GridDirection $direction = GridDirection::Row): ComponentAttributeBag {
            if (! is_array($columns)) {
                $columns = ['lg' => $columns];
            }

            $columns = array_filter($columns);

            $columns['default'] ??= 1;

            return $this
                ->class([
                    'fi-grid',
                    'fi-grid-direction-col' => $direction === GridDirection::Column,
                    ...array_map(
                        fn (string $breakpoint): string => match ($breakpoint) {
                            'default' => '',
                            default => "{$breakpoint}:fi-grid-cols",
                        },
                        array_keys($columns),
                    ),
                ])
                ->style(array_map(
                    fn (string $breakpoint, int $columns): string => match ($direction) {
                        GridDirection::Row => '--cols-'.str_replace('!', 'n', str_replace('@', 'c', $breakpoint)).": repeat({$columns}, minmax(0, 1fr))",
                        GridDirection::Column => '--cols-'.str_replace('!', 'n', str_replace('@', 'c', $breakpoint)).": {$columns}",
                    },
                    array_keys($columns),
                    array_values($columns),
                ));
        });

        ComponentAttributeBag::macro('gridColumn', function (array | int | string | null $span = [], array | int | null $start = [], bool $isHidden = false): ComponentAttributeBag {
            if (! is_array($span)) {
                $span = ['default' => $span];
            }

            if (! is_array($start)) {
                $start = ['default' => $start];
            }

            $span = array_filter($span);

            $start = array_filter($start);

            return $this
                ->class([
                    'fi-grid-col',
                    'fi-hidden' => $isHidden || (($span['default'] ?? null) === 'hidden'),
                    ...array_map(
                        fn (string $breakpoint): string => match ($breakpoint) {
                            'default' => '',
                            default => "{$breakpoint}:fi-grid-col-span",
                        },
                        array_keys($span),
                    ),
                    ...array_map(
                        fn (string $breakpoint): string => match ($breakpoint) {
                            'default' => 'fi-grid-col-start',
                            default => "{$breakpoint}:fi-grid-col-start",
                        },
                        array_keys($start),
                    ),
                ])
                ->style([
                    ...array_map(
                        fn (string $breakpoint, int | string $span): string => '--col-span-'.str_replace('!', 'n', str_replace('@', 'c', $breakpoint)).': '.match ($span) {
                                'full' => '1 / -1',
                                default => "span {$span} / span {$span}",
                            },
                        array_keys($span),
                        array_values($span),
                    ),
                    ...array_map(
                        fn (string $breakpoint, int $start): string => '--col-start-'.str_replace('!', 'n', str_replace('@', 'c', $breakpoint)).': '.$start,
                        array_keys($start),
                        array_values($start),
                    ),
                ]);
        });

        Str::macro('sanitizeHtml', function (string $html): string {
            return app(HtmlSanitizerInterface::class)->sanitize($html);
        });

        Stringable::macro('sanitizeHtml', function (): Stringable {
            /** @phpstan-ignore-next-line */
            return new Stringable(Str::sanitizeHtml($this->value));
        });

        Str::macro('ucwords', function (string $value): string {
            return implode(' ', array_map(
                [Str::class, 'ucfirst'],
                explode(' ', $value),
            ));
        });

        Stringable::macro('ucwords', function (): Stringable {
            /** @phpstan-ignore-next-line */
            return new Stringable(Str::ucwords($this->value));
        });

        if (class_exists(InstalledVersions::class)) {
            // @TODO: dynamically loop through all installed cortex modules!
            $packages = [
                'cortex/forms',
                'cortex/notifications',
                'cortex/support',
                'cortex/tables',
            ];

            AboutCommand::add('Cortex', static fn () => [
                'Version' => InstalledVersions::getPrettyVersion('cortex/support'),
                'Packages' => collect($packages)
                    ->filter(fn (string $package): bool => InstalledVersions::isInstalled($package))
                    ->join(', '),
                'Views' => function () use ($packages): string {
                    $publishedViewPaths = collect($packages)
                        ->filter(fn (string $package): bool => is_dir(resource_path("views/vendor/{$package}")));

                    if (! $publishedViewPaths->count()) {
                        return '<fg=green;options=bold>NOT PUBLISHED</>';
                    }

                    return "<fg=red;options=bold>PUBLISHED:</> {$publishedViewPaths->join(', ')}";
                },
                'Blade Icons' => function (): string {
                    return File::exists(app()->bootstrapPath('cache/blade-icons.php'))
                        ? '<fg=green;options=bold>CACHED</>'
                        : '<fg=yellow;options=bold>NOT CACHED</>';
                },
                'Panel Components' => function (): string {
                    if (! class_exists(CacheComponentsCommand::class)) {
                        return '<options=bold>NOT AVAILABLE</>';
                    }

                    $path = app()->bootstrapPath('cache/cortex/panels');

                    return File::isDirectory($path) && ! File::isEmptyDirectory($path)
                        ? '<fg=green;options=bold>CACHED</>'
                        : '<fg=yellow;options=bold>NOT CACHED</>';
                },
            ]);
        }

        // @TODO: why do we need the following?!
        if ($this->app->runningInConsole()) {
            $this->optimizes(
                optimize: 'cortex:optimize', /** @phpstan-ignore-line */
                clear: 'cortex:optimize-clear', /** @phpstan-ignore-line */
                key: 'cortex',/** @phpstan-ignore-line */
            );
        }
    }
}
