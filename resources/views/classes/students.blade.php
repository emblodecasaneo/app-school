<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-poppins-semibold text-xl text-gray-800 leading-tight">
                {{ __('Élèves de la classe') }}
            </h2>
            <x-button href="{{ route('classes') }}" icon="back" color="gray">
                Retour aux classes
            </x-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-4 sm:px-6 lg:px-3">
            @livewire('classe-students', ['classeId' => $classeId])
        </div>
    </div>
</x-app-layout> 