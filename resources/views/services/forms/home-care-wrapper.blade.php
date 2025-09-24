@extends('layouts.app')

@section('content')
    @if(isset($service))
        <livewire:services.home-care-service-form :service="$service" />
    @else
        <livewire:services.home-care-service-form :category-id="$categoryId" />
    @endif
@endsection