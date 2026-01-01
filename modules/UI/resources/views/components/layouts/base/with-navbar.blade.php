@extends('ui::components.layouts.base')

@section('content')
    <div class="min-h-screen flex flex-col">
        {{-- Main Navbar Component --}}
        <x-ui::navbar />

        {{-- Page-specific content will be injected here --}}
        <main class="flex-1 w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
            {{ $slot }}
        </main>
    </div>
@endsection
