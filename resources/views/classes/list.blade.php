<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Liste de classes année :') }} <span class="font-semibold text-blue-500 text-xl leading-tight">{{$currentYear?->school_year}}</span>
            </h2>
            <div class="text-sm text-gray-600">
                @php
                    $totalClasses = \App\Models\Classe::count();
                    $totalLevels = \App\Models\Level::count();
                    $totalStudents = \App\Models\Student::count();
                    $averageStudentsPerClass = $totalClasses > 0 ? round($totalStudents / $totalClasses, 1) : 0;
                @endphp
                <span class="mr-4">
                    <x-icons name="class" class="inline" size="xs" /> 
                    <span class="font-medium">Classes:</span> {{ $totalClasses }}
                </span>
                <span class="mr-4">
                    <x-icons name="level" class="inline" size="xs" /> 
                    <span class="font-medium">Niveaux:</span> {{ $totalLevels }}
                </span>
                <span>
                    <x-icons name="student" class="inline" size="xs" /> 
                    <span class="font-medium">Moy/classe:</span> {{ $averageStudentsPerClass }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-8xl mx-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                 @livewire('list-classes')
            </div>
        </div>
    </div>
</x-app-layout>
