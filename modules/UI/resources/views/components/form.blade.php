<x-mary-form {{ $attributes }}>
    {{ $slot }}

    @isset($actions)
        <x-slot:actions>
            {{ $actions }}
        </x-slot:actions>
    @endisset
</x-mary-form>
