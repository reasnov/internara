@extends('ui::components.layouts.base.with-navbar')

@section('main')
    <div class="flex size-full flex-1 flex-col items-center">
        {{ $slot }}
    </div>
@endsection
