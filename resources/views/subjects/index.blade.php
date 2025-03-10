<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Matières') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-4 sm:px-6 lg:px-3">
            @livewire('subject-management')
        </div>
    </div>
</x-app-layout> 