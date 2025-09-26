@extends("layouts.dashboard")

@section("content")
    @if(isset($service))
        <livewire:services.walking-service-form :service="$service" />
    @else
        <livewire:services.walking-service-form :category-id="$categoryId" />
    @endif
@endsection
