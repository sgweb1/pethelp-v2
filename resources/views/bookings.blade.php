@extends('layouts.dashboard')

@section('title', 'Moje rezerwacje - PetHelp')

@section('header-title')
    <div class="flex items-center">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Moje rezerwacje</h1>
        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
            Zarządzanie rezerwacjami
        </span>
    </div>
@endsection

@section('content')
    @livewire('booking-management', ['view' => $view])
@endsection