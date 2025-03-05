<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Progression des élèves') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-4 sm:px-3 lg:px-3">
            @livewire('year-progression')
        </div>
    </div>
</x-app-layout> 