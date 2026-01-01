@auth
    <div>
        //auth
    </div>
@else
    <a class="btn btn-ghost" href="{{ route('login') }}" wire:navigate>Log in</a>
    <a class="btn btn-primary" href="{{ route('register') }}" wire:navigate>Register</a>
@endauth
