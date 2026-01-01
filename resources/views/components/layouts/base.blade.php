<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        @include('components.layouts.base.head')
    </head>

    <body class="max-w-screen size-full min-h-screen overflow-x-hidden antialiased">
        <!-- Page Content -->
        @yield('content')

        <!-- Scripts -->
        @stack('scripts')
    </body>

</html>
