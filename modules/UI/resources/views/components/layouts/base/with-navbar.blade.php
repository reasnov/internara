@props(['title' => null])

<x-ui::layouts.base :$title>
    <div class="flex flex-1 flex-col">
        <x-ui::navbar sticky full-width />

        <x-ui::main with-nav full-width>
            {{ $slot }}
        </x-ui::main>

        <x-ui::footer />
    </div>
</x-ui::layouts.base>