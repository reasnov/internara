@extends('ui::components.layouts.base.with-navbar')

@section('main')
    <div class="flex size-full flex-1 flex-col items-center justify-center pt-20">
        {{ $slot }}
    </div>
@endsection
