@extends('layouts.app')

@section('title', 'Test Livewire')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Test Livewire Component</h1>

    @livewire('test-simple')
</div>
@endsection