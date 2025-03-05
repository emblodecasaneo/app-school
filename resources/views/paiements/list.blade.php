<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Liste de paiements :') }} <span class="font-semibold text-blue-500 text-xl leading-tight">{{$currentYear?->school_year}}</span>
            </h2>
            <div class="text-sm text-gray-600">
                @php
                    $totalPayments = \App\Models\Payment::count();
                    $totalAmount = \App\Models\Payment::sum('montant');
                    $pendingAmount = \App\Models\Payment::where('solvable', '0')->sum('reste');
                @endphp
                <span class="mr-4">
                    <x-icons name="payment" class="inline" size="xs" /> 
                    <span class="font-medium">Paiements:</span> {{ $totalPayments }}
                </span>
                <span class="mr-4">
                    <x-icons name="add" class="inline" size="xs" /> 
                    <span class="font-medium">Total:</span> {{ number_format($totalAmount, 0, ',', ' ') }} FCFA
                </span>
                <span>
                    <x-icons name="error" class="inline" size="xs" /> 
                    <span class="font-medium">Reste:</span> {{ number_format($pendingAmount, 0, ',', ' ') }} FCFA
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-8xl mx-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                 @livewire('list-paiement')
            </div>
        </div>
    </div>
</x-app-layout>
