<div class="mt-4">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
        {{-- Titre et Bouton créer --}}
        <div class="flex items-center justify-between">
            <div class="w-1/2">
                <input type="text" name="search" placeholder="Rechercher une inscription"
                    class="rounded-md w-1/2 mr-2 border-gray-300" wire:model.live="search" />

                <Select type="text" name="selected_class_id" id="selected_class_id" class="rounded-md w-1/4 border-gray-300"
                    wire:model.live="selected_class_id">
                    <option value="">Toutes les classes</option>
                    @foreach ($allClass as $item)
                        <option value="{{$item->libelle}}">{{$item->libelle}}</option>
                    @endforeach
                </Select>
            </div>

            <a href="{{ route('inscriptions.create_inscription') }}"
                class="bg-blue-500 rounded-md p-2 text-sm text-white">Faire une inscription</a>
        </div>

        {{-- Messages Livewire --}}
        @if (isset($message))
            <div class="block p-2 {{ $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-md mb-4 mt-4">
                {{ $message }}
            </div>
        @endif

        {{-- Messages de session --}}
        @if (Session::get('success'))
            <div class="block p-2 bg-green-100 text-green-800 rounded-md mb-4 mt-4">
                {{ Session::get('success') }}
            </div>
        @endif
        @if (Session::get('error'))
            <div class="block p-2 bg-red-100 text-red-800 rounded-md mb-4 mt-4">
                {{ Session::get('error')}}
            </div>
        @endif

        {{-- Affichage de l'année scolaire active --}}
        @if($activeYear)
            <div class="bg-blue-50 p-2 my-2 rounded-md">
                <p class="text-blue-800">Inscriptions pour l'année scolaire active : <strong>{{ $activeYear->school_year }}</strong></p>
            </div>
        @else
            <div class="bg-yellow-50 p-2 my-2 rounded-md">
                <p class="text-yellow-800">Aucune année scolaire active. Veuillez activer une année scolaire.</p>
            </div>
        @endif

        {{-- Style du tableau --}}
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full">
                <div class="overflow-hidden">
                    <table class="min-w-full text-center">
                        <thead class="bg-gray-50">
                            <tr class="text-blue-500">
                                <th class="text-md font-semibold px-4 py-4">ID</th>
                                <th class="text-md font-semibold px-4 py-4">Matricule</th>
                                <th class="text-md font-semibold px-4 py-4">Nom</th>
                                <th class="text-md font-semibold px-4 py-4">Prénom</th>
                                <th class="text-md font-semibold px-4 py-4">Classe</th>
                                <th class="text-md font-semibold px-4 py-4">Code niveau</th>
                                <th class="text-md font-semibold px-4 py-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inscriptionList as $item)
                                <tr class="border-b-2">
                                    <td class="text-sm font-medium text-gray-900 px-4 py-4">{{ $item->id }}</td>
                                    <td class="text-sm font-medium text-gray-900 px-4 py-4">{{ $item->student->matricule }}</td>
                                    <td class="text-sm font-medium text-gray-900 px-4 py-4">{{ $item->student->nom }}</td>
                                    <td class="text-sm font-medium text-gray-900 px-4 py-4">{{ $item->student->prenom }}</td>
                                    <td class="text-sm font-medium text-gray-900 px-4 py-4">{{ $item->classe->libelle }}</td>
                                    <td class="text-sm font-medium text-gray-900 px-4 py-4">{{ $item->classe->level->code }}</td>
                                    <td class="text-sm font-medium text-gray-900 px-4 py-4">
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
                                    <td colspan="7">
                                        <div style="display: flex; flex-direction:column"
                                            class="p-10 justify-center items-center">
                                            <img style="height: 80px; width:80px" alt="empty"
                                                src="{{ asset('assets/ensemble-vide.png') }}" />
                                            <p class="mt-2">Aucune inscription trouvée pour cette année scolaire!</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="mt-3">{{ $inscriptionList->links() }}</div>
                </div>
            </div>
        </div>

        {{-- Modal de confirmation de suppression --}}
        <x-dialog-modal wire:model.live="dialogAttDeletion">
            <x-slot name="title">
                {{ __('Supprimer une inscription') }}
            </x-slot>

            <x-slot name="content">
                <div class="flex">
                    <p wire:igone id="selectName" class="text-md font-medium border-gray-900">{{$selectName}}</p>
                </div>
                {{ __('En supprimant cette inscription, cet élève sera exclu de votre établissement mais ses informations seront archivées dans le fichier élève pour 5 ans.') }}
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('dialogAttDeletion')" wire:loading.attr="disabled">
                    {{ __('Annuler') }}
                </x-secondary-button>

                <x-danger-button class="ms-3" wire:click="delete({{$item ?? 0}})" wire:loading.attr="disabled">
                    {{ __('Supprimer') }}
                </x-danger-button>
            </x-slot>
        </x-dialog-modal>
    </div>
</div>
