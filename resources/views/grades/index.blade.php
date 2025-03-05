<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Notes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-4 sm:px-6 lg:px-3">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                @livewire('grade-management')
            </div>
        </div>
    </div>
</x-app-layout> 