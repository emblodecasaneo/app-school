<div class="mt-4">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
        {{-- Titre et Bouton créer --}}
        <div class="flex items-center justify-between">
            <div class="w-1/2">
                <input type="text" name="search" placeholder="Rechercher une inscription"
                    class="rounded-md w-1/2 mr-2 border-gray-300" wire:model.live="search" />

                <Select type="text" name="selected_class_id" id="selected_class_id" class="rounded-md w-1/4  border-gray-300"
                    wire:model.live="selected_class_id">
                    <option>filter par classe</option>
                    @foreach ($allClass as $item)
                        <option id="{{$item->id}}">{{$item->libelle}}</option>
                    @endforeach
                </Select>
            </div>

            <a href="{{ route('paiements.create_paiement') }}"
                class="bg-blue-500 rounded-md p-2 text-white">Faire un paiement</a>
        </div>
        <div class="flex flex-col  mt-5 boder-gray-400">
            {{-- Message qui appaitra après opération --}}
            @if (Session::get('success'))
                <div style="background-color: rgba(24, 98, 235, 0.753)" class="block p-2 text-white text-md rounded-sm shadow-sm mt-2 mb-2">
                    {{ Session::get('success') }}
                </div>
            @endif
            @if (Session::get('error'))
                <div class="block p-2 bg-red-300 text-gray-900 rounded-sm mb-2 shadow-sm mt-2">
                    {{ Session::get('error')}}
                </div>
            @endif

            {{-- Style du tableau --}}
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full">
                    <div class="overflow-hidden">
                        <table class="min-w-full text-center">
                            <thead class=" bg-gray-50">
                                <tr class="text-blue-500">
                                    <th class="text-md font-semibold  px-4 py-4">ID</th>
                                    <th class="text-md font-semibold  px-4 py-4">Matricule</th>
                                    <th class="text-md font-semibold  px-4 py-4">Nom</th>
                                    <th class="text-md font-semibold  px-4 py-4">Prénom</th>
                                    <th class="text-md font-semibold  px-4 py-4">Classe</th>
                                    <th class="text-md font-semibold  px-4 py-4">montant</th>
                                    <th class="text-md font-semibold  px-4 py-4">Solvable</th>
                                    <th class="text-md font-semibold  px-4 py-4">Reste</th>
                                    <th class="text-md font-semibold  px-4 py-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($paiementList as $item)
                                    <tr class="border-b-2">
                                        <!-- colone pour les ID -->
                                        <td class="text-sm font-medium text-gray-900 px-4 py-4">{{ $item->id }}</td>

                                        <!-- colone pour les bouttons d'actions -->
                                        <td class="text-sm  font-medium text-gray-900 px-4 py-4">{{ $item->student->matricule}} </td>

                                        <!-- colone pour les nom -->
                                        <td class="text-sm  font-medium text-gray-900 px-4 py-4">{{ $item->student->nom}} </td>

                                        <!-- colone pour les nom -->
                                        <td class="text-sm  font-medium text-gray-900 px-4 py-4">{{ $item->student->prenom}} </td>

                                        <!-- colone pour les bouttons d'actions -->
                                        <td class="text-sm font-medium text-gray-900 px-4 py-4">{{ $item->classe->libelle }}</td>

                                        <!-- colone pour les bouttons d'actions -->
                                        <td class="text-sm font-medium text-gray-900 px-4 py-4">{{ $item->montant }}</td>

                                         <!-- colone pour les bouttons d'actions -->
                                         <td class="text-sm font-medium text-gray-900 px-4 py-4">{{ $item->solvable }}</td>

                                          <!-- colone pour les bouttons d'actions -->
                                        <td class="text-sm font-medium text-gray-900 px-4 py-4">{{ $item->reste }}</td>

                                        <!-- colone pour les bouttons d'actions -->
                                        <td class="text-sm  font-medium text-gray-900 px-4 py-4">
                                            <div style="justify-content: center;" class="flex items-center">
                                                <a href="{{ route('inscriptions.update_inscription', $item) }}"
                                                    class="mr-2 text-md text-white rounded-sm p-2">
                                                    <img alt="modifier" style="height: 25px ; width:25px"
                                                        src="{{ asset('assets/pen.png') }}" />
                                                </a>
                                                <button wire:click="confirmingAttributtionDeletion({{$item}})" wire:loading.attr="disabled">
                                                    <img alt="modifier" style="height: 25px ; width:25px"
                                                        src="{{ asset('assets/supprimer.png') }}" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9">
                                            <div style="display: flex; flex-direction:column"
                                                class=" p-10 justify-center items-center">
                                                <img style="height: 80px; width:80px" alt="empty"
                                                    src="{{ asset('assets/ensemble-vide.png') }}" />
                                                <p class="mt-2">Aucune inscription trouvée pour cette année scolaire!</p>
                                                <div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <x-dialog-modal wire:model.live="dialogAttDeletion">
                            <x-slot name="title">
                                {{ __('Supprimer une inscription') }}
                            </x-slot>

                            <x-slot name="content">
                                <div class="flex">
                                    <p wire:igone id="selectName" class="text-md font-medium border-gray-900">{{$selectName}}</p>

                                </div>
                                {{ __('En supprimant cette inscription , cette élève sera exclut de votre établisement mais ses information seront achivé dans le fichier élève pour 05 ans') }}
                            </x-slot>

                            <x-slot name="footer">
                                <x-secondary-button wire:click="$toggle('dialogAttDeletion')" wire:loading.attr="disabled">
                                    {{ __('Annuler') }}
                                </x-secondary-button>

                                <x-danger-button class="ms-3" wire:click="delete({{$item}})" wire:loading.attr="loading">
                                    {{ __('Supprimer') }}
                                </x-danger-button>
                            </x-slot>
                        </x-dialog-modal>

                        {{-- Pagination --}}
                        <div class="mt-2"> {{ $paiementList?->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
