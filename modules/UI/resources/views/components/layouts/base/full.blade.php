@extends('ui::components.layouts.base')

@section('content')
    <div class="flex min-h-screen flex-col">
        {{-- Navbar --}}
        <x-ui::navbar />

        {{-- Header --}}
        @isset($header)
            <header class="bg-white shadow dark:bg-gray-800">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- Main Content --}}
        <main class="mx-auto w-full flex-1 px-4 py-6 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        @isset($footer)
            <footer class="mt-auto bg-white shadow dark:bg-gray-800">
                <div class="mx-auto max-w-7xl px-4 py-4 text-center text-gray-500 sm:px-6 lg:px-8 dark:text-gray-400">
                    {{ $footer }}
                </div>
            </footer>
        @endisset
    </div>
@endsection
