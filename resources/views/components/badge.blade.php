@php
    use Cortex\Support\Enums\ActionSize;
    use Cortex\Support\Enums\IconPosition;
    use Cortex\Support\Enums\IconSize;
    use Cortex\Support\View\Components\BadgeComponent;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'color' => 'primary',
    'deleteButton' => null,
    'disabled' => false,
    'form' => null,
    'formId' => null,
    'href' => null,
    'icon' => null,
    'iconAlias' => null,
    'iconPosition' => IconPosition::Before,
    'iconSize' => null,
    'keyBindings' => null,
    'loadingIndicator' => true,
    'size' => ActionSize::Medium,
    'spaMode' => null,
    'tag' => 'span',
    'target' => null,
    'tooltip' => null,
    'type' => 'button',
])

@php
    if (! $iconPosition instanceof IconPosition) {
        $iconPosition = filled($iconPosition) ? (IconPosition::tryFrom($iconPosition) ?? $iconPosition) : null;
    }

    if (! $size instanceof ActionSize) {
        $size = filled($size) ? (ActionSize::tryFrom($size) ?? $size) : null;
    }

    if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
        $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
    }

    $isDeletable = count($deleteButton?->attributes->getAttributes() ?? []) > 0;

    $wireTarget = $loadingIndicator ? $attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first() : null;

    $hasLoadingIndicator = filled($wireTarget) || ($type === 'submit' && filled($form));

    if ($hasLoadingIndicator) {
        $loadingIndicatorTarget = html_entity_decode($wireTarget ?: $form, ENT_QUOTES);
    }

    $hasTooltip = filled($tooltip);
@endphp

<{{ $tag }}
    @if (($tag === 'a') && (! ($disabled && $hasTooltip)))
        {{ \Cortex\Support\generate_href_html($href, $target === '_blank', $spaMode) }}
    @endif
    @if ($keyBindings)
        x-bind:id="$id('key-bindings')"
        x-mousetrap.global.{{ collect($keyBindings)->map(fn (string $keyBinding): string => str_replace('+', '-', $keyBinding))->implode('.') }}="document.getElementById($el.id).click()"
    @endif
    @if ($hasTooltip)
        x-tooltip="{
            content: @js($tooltip),
            theme: $store.theme,
        }"
    @endif
    {{
        $attributes
            ->merge([
                'aria-disabled' => $disabled ? 'true' : null,
                'disabled' => $disabled && blank($tooltip),
                'form' => $tag === 'button' ? $formId : null,
                'type' => $tag === 'button' ? $type : null,
                'wire:loading.attr' => $tag === 'button' ? 'disabled' : null,
                'wire:target' => ($hasLoadingIndicator && $loadingIndicatorTarget) ? $loadingIndicatorTarget : null,
            ], escape: false)
            ->when(
                $disabled && $hasTooltip,
                fn (ComponentAttributeBag $attributes) => $attributes->filter(
                    fn (mixed $value, string $key): bool => ! str($key)->startsWith(['href', 'x-on:', 'wire:click']),
                ),
            )
            ->class([
                'fi-badge',
                'fi-disabled' => $disabled,
                ($size instanceof ActionSize) ? "fi-size-{$size->value}" : (is_string($size) ? $size : ''),
            ])
            ->color(BadgeComponent::class, $color)
    }}
>
    @if ($iconPosition === IconPosition::Before)
        @if ($icon)
            {{
                \Cortex\Support\generate_icon_html($icon, $iconAlias, (new \Illuminate\View\ComponentAttributeBag([
                    'wire:loading.remove.delay.' . config('cortex.support.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                    'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
                ]))->class([$iconClasses]), size: $iconSize ?? \Cortex\Support\Enums\IconSize::Small)
            }}
        @endif

        @if ($hasLoadingIndicator)
            {{
                \Cortex\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                    'wire:loading.delay.' . config('cortex.support.livewire_loading_delay', 'default') => '',
                    'wire:target' => $loadingIndicatorTarget,
                ]))->class([$iconClasses]), size: $iconSize ?? \Cortex\Support\Enums\IconSize::Small)
            }}
        @endif
    @endif

    <span class="fi-badge-label-ctn">
        <span class="fi-badge-label">
            {{ $slot }}
        </span>
    </span>

    @if ($isDeletable)
        @php
            $deleteButtonWireTarget = $deleteButton->attributes->whereStartsWith(['wire:target', 'wire:click'])->filter(fn ($value): bool => filled($value))->first();

            $deleteButtonHasLoadingIndicator = filled($deleteButtonWireTarget);

            if ($deleteButtonHasLoadingIndicator) {
                $deleteButtonLoadingIndicatorTarget = html_entity_decode($deleteButtonWireTarget, ENT_QUOTES);
            }
        @endphp

        <button
            type="button"
            {{
                $deleteButton->attributes
                    ->except(['label'])
                    ->class([
                        'fi-badge-delete-btn',
                    ])
            }}
        >
            {{
                \Cortex\Support\generate_icon_html(\Cortex\Support\Icons\Heroicon::XMark, alias: 'badge.delete-button', attributes: (new \Illuminate\View\ComponentAttributeBag([
                    'wire:loading.remove.delay.' . config('cortex.support.livewire_loading_delay', 'default') => $deleteButtonHasLoadingIndicator,
                    'wire:target' => $deleteButtonHasLoadingIndicator ? $deleteButtonLoadingIndicatorTarget : false,
                ])), size: \Cortex\Support\Enums\IconSize::ExtraSmall)
            }}

            @if ($deleteButtonHasLoadingIndicator)
                {{
                    \Cortex\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                        'wire:loading.delay.' . config('cortex.support.livewire_loading_delay', 'default') => '',
                        'wire:target' => $deleteButtonLoadingIndicatorTarget,
                    ])), size: \Cortex\Support\Enums\IconSize::ExtraSmall)
                }}
            @endif

            @if (filled($label = $deleteButton->attributes->get('label')))
                <span class="sr-only">
                    {{ $label }}
                </span>
            @endif
        </button>
    @elseif ($iconPosition === IconPosition::After)
        @if ($icon)
            {{
                \Cortex\Support\generate_icon_html($icon, $iconAlias, (new \Illuminate\View\ComponentAttributeBag([
                    'wire:loading.remove.delay.' . config('cortex.support.livewire_loading_delay', 'default') => $hasLoadingIndicator,
                    'wire:target' => $hasLoadingIndicator ? $loadingIndicatorTarget : false,
                ]))->class([$iconClasses]), size: $iconSize ?? \Cortex\Support\Enums\IconSize::Small)
            }}
        @endif

        @if ($hasLoadingIndicator)
            {{
                \Cortex\Support\generate_loading_indicator_html((new \Illuminate\View\ComponentAttributeBag([
                    'wire:loading.delay.' . config('cortex.support.livewire_loading_delay', 'default') => '',
                    'wire:target' => $loadingIndicatorTarget,
                ]))->class([$iconClasses]), size: $iconSize ?? \Cortex\Support\Enums\IconSize::Small)
            }}
        @endif
    @endif
</{{ $tag }}>
