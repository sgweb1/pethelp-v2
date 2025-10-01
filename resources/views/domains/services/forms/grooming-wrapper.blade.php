@extends('layouts.dashboard')

@section('content')
    @if(isset($service))
        {{-- Edit mode --}}
        <livewire:services.home-care-service-form :service="$service" />
    @else
        {{-- Create mode --}}
        <livewire:services.home-care-service-form :category-id="$categoryId" />
    @endif
@endsection