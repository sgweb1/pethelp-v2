@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-6">
    <livewire:review-form :booking="$booking" />
</div>
@endsection