<x-setup::layouts.setup>
    {{-- The navbar with `sticky` and `full-width` --}}
    <x-nav sticky full-width>

        <x-slot:brand>
            {{-- Drawer toggle for "main-drawer" --}}
            <label class="mr-3 lg:hidden" for="main-drawer">
                <x-icon class="cursor-pointer" name="o-bars-3" />
            </label>

            {{-- Brand --}}
            <div>App</div>
        </x-slot:brand>

        {{-- Right side actions --}}
        <x-slot:actions>
            <x-button class="btn-ghost btn-sm" label="Messages" icon="o-envelope" link="###" responsive />
            <x-button class="btn-ghost btn-sm" label="Notifications" icon="o-bell" link="###" responsive />
        </x-slot:actions>
    </x-nav>

    {{-- The main content with `full-width` --}}
    <x-main with-nav full-width>

        {{-- This is a sidebar that works also as a drawer on small screens --}}
        {{-- Notice the `main-drawer` reference here --}}
        <x-slot:sidebar class="bg-base-200" drawer="main-drawer" collapsible>

            {{-- User --}}
            @if ($user = auth()->user())
                <x-list-item class="pt-2" :item="$user" value="name" sub-value="email" no-separator no-hover>
                    <x-slot:actions>
                        <x-button class="btn-circle btn-ghost btn-xs" icon="o-power" tooltip-left="logoff"
                            no-wire-navigate link="/logout" />
                    </x-slot:actions>
                </x-list-item>

                <x-menu-separator />
            @endif

            {{-- Activates the menu item when a route matches the `link` property --}}
            <x-menu activate-by-route>
                <x-menu-item title="Home" icon="o-home" link="###" />
                <x-menu-item title="Messages" icon="o-envelope" link="###" />
                <x-menu-sub title="Settings" icon="o-cog-6-tooth">
                    <x-menu-item title="Wifi" icon="o-wifi" link="####" />
                    <x-menu-item title="Archives" icon="o-archive-box" link="####" />
                </x-menu-sub>
            </x-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>
</x-setup::layouts.setup>
