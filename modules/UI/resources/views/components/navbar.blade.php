{{--
    This is the main navigation bar component for the application.
    It is built with DaisyUI and is designed to be extensible through slots.
--}}
<div class="navbar bg-base-100 border-base-200 sticky top-0 z-30 border-b">
    <div class="navbar-start">
        {{-- Slot for brand/logo --}}
        @slotRender('navbar.brand')
    </div>

    <div class="navbar-center hidden lg:flex">
        {{-- Slot for main desktop menu items --}}
        @slotRender('navbar.menu')
    </div>

    <div class="navbar-end">
        {{-- Slot for user actions, theme toggles, etc. --}}
        @slotRender('navbar.actions')

        {{-- Mobile dropdown --}}
        <div class="dropdown dropdown-end lg:hidden">
            <label class="btn btn-ghost lg:hidden" tabindex="0">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
                </svg>
            </label>
            <ul class="menu menu-sm dropdown-content z-1 bg-base-100 rounded-box mt-3 w-52 p-2 shadow" tabindex="0">
                {{-- Render the same menu and actions slots for mobile --}}
                @slotRender('navbar.menu')
                {{-- A separate slot for mobile-specific actions if needed --}}
                @slotRender('navbar.actions.mobile')
            </ul>
        </div>
    </div>
</div>
