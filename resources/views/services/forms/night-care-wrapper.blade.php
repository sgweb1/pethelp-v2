@extends('layouts.app')

@section('content')
    @if(isset($service))
        {{-- Edit mode --}}
        <livewire:services.night-care-service-form :service="$service" />
    @else
        {{-- Create mode --}}
        <livewire:services.night-care-service-form :category-id="$categoryId" />
    @endif
@endsection