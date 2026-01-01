@extends('ui::components.layouts.base')

@section('content')
    <div class="flex flex-1 flex-col">
        <x-ui::navbar sticky full-width />

        <x-ui::main with-nav full-width>
            @yield('main')
        </x-ui::main>

        <x-ui::footer />
    </div>
@endsection
