<div {{ $attributes->merge(['class' => 'mt-auto w-full p-4']) }}>
    {{ $slot }}

    <div class="container mx-auto">
        <p class="text-center text-sm font-medium">
            {{ now()->format('Y') }} &copy <b>{{ setting('brand_name', 'Internara') }}</b>.
            @slotRender('footer.app-credit')
        </p>
    </div>
</div>
