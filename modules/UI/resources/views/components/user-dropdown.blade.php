@auth
    <div class="dropdown dropdown-end">
        <label tabindex="0" class="btn btn-ghost flex items-center gap-2">
            <div class="avatar">
                <div class="w-8 rounded-full">
                    {{-- Placeholder for user avatar --}}
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random" />
                </div>
            </div>
            <span class="hidden md:inline">{{ auth()->user()->name }}</span>
        </label>
        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
            <li>
                {{-- Profile Link --}}
                <a href="#" wire:navigate>Profile</a>
            </li>
            <li>
                {{-- Logout Form --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); this.closest('form').submit();">
                        Logout
                    </a>
                </form>
            </li>
        </ul>
    </div>
@else
    <a href="{{ route('login') }}" wire:navigate class="btn btn-ghost">Log in</a>
    <a href="{{ route('register') }}" wire:navigate class="btn btn-primary">Register</a>
@endauth
