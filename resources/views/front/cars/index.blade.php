@extends('layout.app')

@section('content')
    <div class="mt-4 mb-4 flex flex-col items-center justify-center max-w-4xl mx-auto">
        @include('front.cars.filter')
        <div class="list w-full">
            @include('front.cars.list')
        </div>
{{--        <div class="flex justify-between py-10">--}}
{{--            @if($cars->count())--}}
{{--                {{ $cars->links() }}--}}
{{--            @endif--}}
{{--        </div>--}}
    </div>
@endsection
