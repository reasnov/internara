<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        @include('ui::components.layouts.base.head')

        <script>
            // Prevents FOUC (Flash of Unstyled Content)
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                    '(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        </script>
    </head>

    <body class="max-w-screen size-full min-h-screen overflow-x-hidden antialiased">
        @yield('content')
        @stack('scripts')
    </body>

</html>
