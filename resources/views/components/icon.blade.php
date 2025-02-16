@props([
    'alias' => null,
    'icon' => null,
])

{{ \Cortex\Support\generate_icon_html($icon, $alias, $attributes) }}
