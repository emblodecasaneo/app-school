<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __("Liste de niveaux année : ") }}<span class="font-semibold text-blue-500 text-xl leading-tight">{{$currentYear?->school_year}}</span>
            </h2>
            <div class="text-sm text-gray-600">
                @php
                    $totalLevels = \App\Models\Level::count();
                    $totalClasses = \App\Models\Classe::count();
                @endphp
                <span class="mr-4">
                    <x-icons name="level" class="inline" size="xs" /> 
                    <span class="font-medium">Niveaux:</span> {{ $totalLevels }}
                </span>
                <span>
                    <x-icons name="class" class="inline" size="xs" /> 
                    <span class="font-medium">Classes:</span> {{ $totalClasses }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-8xl mx-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                 @livewire('list-niveaux')
            </div>
        </div>
    </div>
</x-app-layout>
