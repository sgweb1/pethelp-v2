@extends('layouts.dashboard')

@section('title', $title ?? 'Dashboard - PetHelp')

@if(isset($meta))
    @section('meta')
        {!! $meta !!}
    @endsection
@endif

@section('content')
    {{ $slot }}
@endsection