<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __("Gestion des moyennes - Année : ") }} <span class="font-semibold text-blue-500 text-xl leading-tight">{{ \App\Models\SchoolYear::where('active', '1')->first()?->school_year }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-8xl mx-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                @livewire('average-management')
            </div>
        </div>
    </div>
</x-app-layout> 