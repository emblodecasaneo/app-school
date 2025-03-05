<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Listes des inscriptions :') }} <span class="font-semibold text-blue-500 text-xl leading-tight">{{$currentYear?->school_year}}</span>
            </h2>
            <div class="text-sm text-gray-600">
                @php
                    $totalStudents = \App\Models\Student::count();
                    $totalInscriptions = 0;
                    if ($currentYear) {
                        $totalInscriptions = \App\Models\Attributtion::where('school_year_id', $currentYear->id)->count();
                    }
                    $inscriptionRate = $totalStudents > 0 ? round(($totalInscriptions / $totalStudents) * 100) : 0;
                @endphp
                <span class="mr-4">
                    <x-icons name="add" class="inline" size="xs" /> 
                    <span class="font-medium">Inscriptions:</span> {{ $totalInscriptions }}
                </span>
                <span class="mr-4">
                    <x-icons name="student" class="inline" size="xs" /> 
                    <span class="font-medium">Total élèves:</span> {{ $totalStudents }}
                </span>
                <span>
                    <x-icons name="level" class="inline" size="xs" /> 
                    <span class="font-medium">Taux:</span> {{ $inscriptionRate }}%
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-8xl mx-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                 @livewire('list-inscription')
            </div>
        </div>
    </div>
</x-app-layout>
