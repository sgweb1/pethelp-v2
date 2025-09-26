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

@php
    // Przekaż breadcrumbs do dashboard layoutu
    if (isset($breadcrumbs)) {
        View::share('breadcrumbs', $breadcrumbs);
    }
@endphp