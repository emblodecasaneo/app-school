<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("Nouvelle classe") }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-8xl mx-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                @livewire('create-classes')
            </div>
        </div>
    </div>
</x-app-layout>
