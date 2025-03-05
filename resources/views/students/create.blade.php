<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold  text-xl text-gray-800 leading-tight">
                {{ __("Enregistrement d'un(e) élève") }}
            </h2>

            </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-8xl mx-4 sm:px-6 lg:px-3">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                 @livewire('create-students')
            </div>
        </div>
    </div>
</x-app-layout>
