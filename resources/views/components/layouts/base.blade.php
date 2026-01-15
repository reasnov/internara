@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <x-layouts.base.head :$title />
    </head>

    <body class="max-w-screen size-full min-h-screen overflow-x-hidden antialiased">
        <!-- Page Content -->
        {{ $slot }}

        <!-- Scripts -->
        @stack('scripts')
    </body>
</html>
