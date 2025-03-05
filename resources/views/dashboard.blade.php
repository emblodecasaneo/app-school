<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tableau de bord') }}
            </h2>
            <div class="text-sm text-gray-600">
                @php
                    $activeYear = \App\Models\SchoolYear::where('active', '1')->first();
                    $totalStudents = \App\Models\Student::count();
                    $totalClasses = \App\Models\Classe::count();
                @endphp
                <span class="mr-4">
                    <x-icons name="calendar" class="inline" size="xs" /> 
                    <span class="font-medium">Année scolaire:</span> 
                    {{ $activeYear ? $activeYear->school_year : 'Non définie' }}
                </span>
                <span class="mr-4">
                    <x-icons name="student" class="inline" size="xs" /> 
                    <span class="font-medium">Élèves:</span> {{ $totalStudents }}
                </span>
                <span>
                    <x-icons name="class" class="inline" size="xs" /> 
                    <span class="font-medium">Classes:</span> {{ $totalClasses }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-5 sm:px-6 lg:px-3">
            @livewire('dashboard-stats')
        </div>
    </div>
</x-app-layout>
