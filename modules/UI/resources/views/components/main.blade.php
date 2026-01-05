@props([
    'sidebar' => null,
    'footer' => null,
])

<x-mary-main {{ $attributes }}>
    @isset($sidebar)
        <slot:sidebar {{ $sidebar->attributes }}>
            {{ $sidebar }}
        </slot:sidebar>
    @endisset

    <x-slot:content>
        {{ $slot }}
    </x-slot:content>

    @isset($footer)
        <x-slot:footer {{ $footer->attributes }}>
            {{ $footer }}
        </x-slot:footer>
    @endisset
</x-mary-main>
