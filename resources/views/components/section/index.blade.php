@php
    use Cortex\Support\Enums\Alignment;
    use Cortex\Support\Enums\IconSize;
    use Cortex\Support\View\Components\SectionComponent\IconComponent;

    use function Cortex\Support\is_slot_empty;
@endphp

@props([
    'afterHeader' => null,
    'aside' => false,
    'collapsed' => false,
    'collapsible' => false,
    'compact' => false,
    'contained' => true,
    'contentBefore' => false,
    'description' => null,
    'divided' => false,
    'footer' => null,
    'hasContentEl' => true,
    'heading' => null,
    'headingTag' => 'h2',
    'icon' => null,
    'iconColor' => 'secondary',
    'iconSize' => null,
    'persistCollapsed' => false,
    'secondary' => false,
])

@php
    if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
        $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
    }

    $hasDescription = filled((string) $description);
    $hasHeading = filled($heading);
    $hasIcon = filled($icon);
    $hasHeader = $hasIcon || $hasHeading || $hasDescription || $collapsible || (! is_slot_empty($afterHeader));
@endphp

<section
    {{-- TODO: Investigate Livewire bug - https://github.com/filamentphp/filament/pull/8511 --}}
    x-data="{
        isCollapsed: @if ($persistCollapsed) $persist(@js($collapsed)).as(`section-${$el.id}-isCollapsed`) @else @js($collapsed) @endif,
    }"
    @if ($collapsible)
        x-on:collapse-section.window="if ($event.detail.id == $el.id) isCollapsed = true"
        x-on:expand="isCollapsed = false"
        x-on:open-section.window="if ($event.detail.id == $el.id) isCollapsed = false"
        x-on:toggle-section.window="if ($event.detail.id == $el.id) isCollapsed = ! isCollapsed"
        x-bind:class="isCollapsed && 'fi-collapsed'"
    @endif
    {{
        $attributes->class([
            'fi-section',
            'fi-section-not-contained' => ! $contained,
            'fi-section-has-content-before' => $contentBefore,
            'fi-section-has-header' => $hasHeader,
            'fi-aside' => $aside,
            'fi-compact' => $compact,
            'fi-collapsible' => $collapsible,
            'fi-divided' => $divided,
            'fi-secondary' => $secondary,
        ])
    }}
>
    @if ($hasHeader)
        <header
            @if ($collapsible)
                x-on:click="isCollapsed = ! isCollapsed"
            @endif
            class="fi-section-header"
        >
            {{
                \Cortex\Support\generate_icon_html($icon, attributes: (new \Illuminate\View\ComponentAttributeBag)
                    ->class([
                        ...\Cortex\Support\get_component_color_classes(IconComponent::class, $iconColor),
                    ]), size: $iconSize ?? IconSize::Large)
            }}

            @if ($hasHeading || $hasDescription)
                <div class="fi-section-header-text-ctn">
                    @if ($hasHeading)
                        <{{ $headingTag }} class="fi-section-header-heading">
                            {{ $heading }}
                        </{{ $headingTag }}>
                    @endif

                    @if ($hasDescription)
                        <p class="fi-section-header-description">
                            {{ $description }}
                        </p>
                    @endif
                </div>
            @endif

            {{ $afterHeader }}

            @if ($collapsible)
                <x-cortex.support::icon-button
                    color="secondary"
                    :icon="\Cortex\Support\Icons\Heroicon::ChevronDown"
                    icon-alias="section.collapse-button"
                    x-on:click.stop="isCollapsed = ! isCollapsed"
                    class="fi-section-collapse-btn"
                />
            @endif
        </header>
    @endif

    <div
        @if ($collapsible)
            x-bind:aria-expanded="(! isCollapsed).toString()"
            @if ($collapsed || $persistCollapsed)
                x-cloak
            @endif
        @endif
        class="fi-section-content-ctn"
    >
        @if ($hasContentEl)
            <div class="fi-section-content">
                {{ $slot }}
            </div>
        @else
            {{ $slot }}
        @endif

        @if (! is_slot_empty($footer))
            <footer class="fi-section-footer">
                {{ $footer }}
            </footer>
        @endif
    </div>
</section>
