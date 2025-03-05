<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold  text-xl text-gray-800 leading-tight">
                {{ __("Liste d'élèves année : ") }} <span  class="font-semibold text-blue-500  text-xl leading-tight">{{$currentYear?->school_year}}</span>
            </h2>
            <div class="text-sm text-gray-600">
                @php
                    $totalStudents = \App\Models\Student::count();
                    $maleStudents = \App\Models\Student::where('sexe', 'M')->count();
                    $femaleStudents = \App\Models\Student::where('sexe', 'F')->count();
                    $inscribedStudents = 0;
                    if ($currentYear) {
                        $inscribedStudents = \App\Models\Attributtion::where('school_year_id', $currentYear->id)->count();
                    }
                @endphp
                <span class="mr-4">
                    <x-icons name="student" class="inline" size="xs" /> 
                    <span class="font-medium">Total:</span> {{ $totalStudents }}
                </span>
                <span class="mr-4">
                    <x-icons name="add" class="inline" size="xs" /> 
                    <span class="font-medium">Inscrits:</span> {{ $inscribedStudents }}
                </span>
                <span>
                    <span class="font-medium">G/F:</span> {{ $maleStudents }}/{{ $femaleStudents }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-8xl mx-4 sm:px-6 lg:px-3">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                 @livewire('list-students')
            </div>
        </div>
    </div>
</x-app-layout>
