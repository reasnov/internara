@extends('components.layouts.base')
@section('content')
    <div class="flex min-h-screen flex-col">
        @isset($header)
            <header class="bg-white shadow">
                <div class="container mx-auto px-4 md:px-6 lg:px-8">
                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ $header }}
                    </h1>
                </div>
            </header>
        @endisset

        <main class="flex-1">
            <div class="container mx-auto px-4 md:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

        @isset($footer)
            <footer class="mt-auto">
                <div class="container mx-auto px-4 text-center md:px-6 lg:px-8">
                    {{ $footer }}
                </div>
            </footer>
        @endisset
    </div>
@endsection
