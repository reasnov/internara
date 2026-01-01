<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        @include('ui::components.layouts.base.head')
    </head>

    <body class="max-w-screen size-full overflow-x-hidden font-sans antialiased">
        <div class="flex size-full min-h-screen flex-col">
            @yield('content')

            <x-mary-toast />
        </div>

        @stack('scripts')
    </body>

</html>
