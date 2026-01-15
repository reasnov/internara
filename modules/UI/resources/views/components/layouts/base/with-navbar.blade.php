@props(['title' => null, 'footer' => null])

<x-ui::layouts.base :$title>
    <div class="flex flex-1 flex-col">
        <x-ui::navbar sticky full-width />

        <x-mary-main with-nav full-width>
            <x-slot:content>
                {{ $slot }}
            </x-slot:content>

            <x-slot:footer class="mt-auto">
                {{ $footer }}

                <x-ui::footer />
            </x-slot:footer>
        </x-mary-main>
    </div>
</x-ui::layouts.base>
