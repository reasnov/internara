<!DOCTYPE html>
<html data-theme="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        @include('ui::components.layouts.base.head')
    </head>

    <body class="max-w-screen size-full min-h-screen overflow-x-hidden antialiased">
        {{ $slot }}

        @stack('scripts')
    </body>

</html>
