<div class="border-base-200 mt-auto w-full border-t p-4">
    <div class="container mx-auto">
        <p class="text-center text-sm font-medium">
            {{ now()->format('Y') }} &copy <b>{{ setting('brand_name', 'Internara') }}</b>.
            @slotRender('footer.app-credit')
        </p>
    </div>
</div>
