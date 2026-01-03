@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <x-ui::layouts.base.head :$title />
    </head>

    <body class="max-w-screen size-full overflow-x-hidden font-sans antialiased">
        <div class="flex size-full min-h-screen flex-col">
            {{ $slot }}

            <x-mary-toast />
        </div>

        @stack('scripts')
    </body>

</html>