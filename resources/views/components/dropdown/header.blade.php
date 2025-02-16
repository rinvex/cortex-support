@php
    use Cortex\Support\Enums\IconSize;
    use Cortex\Support\View\Components\DropdownComponent\HeaderComponent;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'color' => 'secondary',
    'icon' => null,
    'iconSize' => null,
    'tag' => 'div',
])

@php
    if (! ($iconSize instanceof IconSize)) {
        $iconSize = filled($iconSize) ? (IconSize::tryFrom($iconSize) ?? $iconSize) : null;
    }
@endphp

<{{ $tag }}
    {{
        $attributes
            ->class([
                'fi-dropdown-header',
            ])
            ->color(HeaderComponent::class, $color)
    }}
>
    {{ \Cortex\Support\generate_icon_html($icon, size: $iconSize) }}

    <span>
        {{ $slot }}
    </span>
</{{ $tag }}>
