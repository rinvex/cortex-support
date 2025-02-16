@php
    use Cortex\Support\View\Components\ToggleComponent;
    use Illuminate\Support\Arr;
@endphp

@props([
    'state',
    'offColor' => 'secondary',
    'offIcon' => null,
    'onColor' => 'primary',
    'onIcon' => null,
])

<button
    x-data="{ state: {{ $state }} }"
    x-bind:aria-checked="state?.toString()"
    x-on:click="state = ! state"
    x-bind:class="
        state ? @js(Arr::toCssClasses([
                    'fi-toggle-on',
                    ...\Cortex\Support\get_component_color_classes(ToggleComponent::class, $onColor),
                ])) : @js(Arr::toCssClasses([
                            'fi-toggle-off',
                            ...\Cortex\Support\get_component_color_classes(ToggleComponent::class, $offColor),
                        ]))
    "
    {{
        $attributes
            ->merge([
                'role' => 'switch',
                'type' => 'button',
            ], escape: false)
            ->class(['fi-toggle'])
    }}
>
    <div>
        <div aria-hidden="true">
            {{ \Cortex\Support\generate_icon_html($offIcon, size: \Cortex\Support\Enums\IconSize::ExtraSmall) }}
        </div>

        <div aria-hidden="true">
            {{
                \Cortex\Support\generate_icon_html(
                    $onIcon,
                    attributes: (new \Illuminate\View\ComponentattributeBag)->merge(['x-cloak' => 'x-cloak'], escape: false),
                    size: \Cortex\Support\Enums\IconSize::ExtraSmall,
                )
            }}
        </div>
    </div>
</button>
