<x-mary-form {{ $attributes }}>
    {{ $slot }}

    @isset($actions)
        <x-slot:actions>
            {{ $actions }}
        </x-slot>
    @endisset
</x-mary-form>
