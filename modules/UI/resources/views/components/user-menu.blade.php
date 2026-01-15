<div>
    @auth
        // auth
    @else
        <div class="flex flex-nowrap items-center gap-4">
            @if (\Illuminate\Support\Facades\Route::has('login'))
                <a class="btn btn-ghost" href="{{ route('login') }}" wire:navigate>Log in</a>
            @endif

            @if (\Illuminate\Support\Facades\Route::has('register'))
                <a class="btn btn-primary" href="{{ route('register') }}" wire:navigate>
                    Register
                </a>
            @endif
        </div>
    @endauth
</div>
