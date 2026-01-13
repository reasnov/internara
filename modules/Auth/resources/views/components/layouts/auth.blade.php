@props(['title' => null])

<x-ui::layouts.base.with-navbar :$title>
    <div class="flex size-full flex-1 flex-col items-center justify-center pt-16">
        {{ $slot }}
    </div>
</x-ui::layouts.base.with-navbar>
