@props(['title' => null])

<x-ui::layouts.base :$title>
    {{ $slot }}
</x-ui::layouts.base>
