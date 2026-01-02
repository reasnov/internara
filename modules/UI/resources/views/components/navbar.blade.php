<x-mary-nav {{ $attributes }}>
    <x-slot:brand>
        {{-- Brand --}}
        @slotRender('navbar.brand')
    </x-slot:brand>

    {{-- Right side actions --}}
    <x-slot:actions class="space-x-2">
        @slotRender('navbar.actions')
    </x-slot:actions>
</x-mary-nav>
