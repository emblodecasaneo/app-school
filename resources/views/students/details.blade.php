<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails de l\'élève') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-5 sm:px-6 lg:px-3">
            @livewire('student-details', ['studentId' => $studentId])
        </div>
    </div>
</x-app-layout> 