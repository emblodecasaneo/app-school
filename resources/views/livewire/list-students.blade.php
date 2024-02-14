<div class="mt-4">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
        {{-- Titre et Bouton créer --}}
        <div class="flex items-center justify-between">
            <div class="w-1/2">
                <input type="text" name="search" placeholder="Rechercher un(e) élève par nom"
                    class="rounded-md w-1/2 mr-2 border-gray-300" wire:model.live="search" />

                <Select type="text" name="genre" id="genre" class="rounded-md w-1/4  border-gray-300"
                    wire:model.live="genre">
                    <option value="FM">Tous les sexes</option>
                    <option value="M">Masculin</option>
                    <option value="F">Féminin</option>
                </Select>
            </div>

            <a href="{{ route('students.create_student') }}"
                class="bg-blue-500 rounded-md p-2 text-sm text-white">Ajouter
                un(e)
                élève</a>
        </div>
        <div class="flex flex-col  mt-5 boder-gray-400">
            {{-- Message qui appaitra après opération --}}
            @if (Session::get('success'))
                <div class="block p-2 bg-green-400 text-white text-md rounded-sm shadow-sm mt-2 mb-2">
                    {{ Session::get('success') }}
                </div>
            @endif
            @if (Session::get('error'))
                <div class="block p-2 bg-red-300 text-gray-900 rounded-sm mb-2 shadow-sm mt-2">
                    {{ Session::get('error') }}
                </div>
            @endif
            {{-- Style du tableau --}}
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full">
                    <div class="overflow-hidden">
                        <table class="min-w-full text-center">
                            <thead class="border-b bg-gray-50">
                                <tr class="text-blue-500">
                                    <th class="text-md font-semibold  px-4 py-4">ID</th>
                                    <th class="text-md font-semibold  px-4 py-4">Matricule</th>
                                    <th class="text-md font-semibold  px-4 py-4">Nom</th>
                                    <th class="text-md font-semibold  px-4 py-4">Prénom</th>
                                    <th class="text-md font-semibold  px-4 py-4">Sexe</th>
                                    <th class="text-md font-semibold  px-4 py-4">Date de naissance</th>
                                    <th class="text-md font-semibold  px-4 py-4">Contact du parent</th>
                                    <th class="text-md font-semibold  px-4 py-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($studentList as $item)
                                    <tr class="border-b-2">
                                        <td class="text-sm font-medium text-gray-900 px-4 py-4">{{ $item->id }}
                                        </td>
                                        <td class="text-sm font-medium text-gray-900 px-4 py-4">
                                            {{ $item->matricule }}</td>

                                        <td class="text-sm font-medium text-gray-900 px-4 py-4">
                                            {{ $item->nom }}
                                        </td>
                                        <td class="text-sm font-medium text-gray-900 px-4 py-4">
                                            {{ $item->prenom }}
                                        </td>
                                        <td class="text-sm font-medium text-gray-900 px-4 py-4">
                                            @if ($item->sexe === 'F')
                                            Féminin
                                            @else
                                              Masculin
                                            @endif
                                        </td>
                                        <td class="text-sm font-medium text-gray-900 px-4 py-4">
                                            {{ $item->naissance }}
                                        </td>
                                        <td class="text-sm font-medium text-gray-900 px-4 py-4">
                                            {{ $item->contact_parent }}
                                        </td>
                                        <td  class="text-sm  font-medium text-gray-900 px-4 py-4">
                                            <div style="justify-content: center;" class="flex items-center">
                                                <a href="{{ route('students.update_student', $item) }}"
                                                    class="mr-2 text-md text-white rounded-sm p-2">
                                                    <img alt="modifier" style="height: 25px ; width:25px"
                                                        src="{{ asset('assets/editer-le-profil.png') }}" />
                                                </a>
                                                <button wire:click="delete({{ $item->id }})">
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
                                                class=" p-10 justify-center items-center">
                                                <img style="height: 80px; width:80px" alt="empty"
                                                    src="{{ asset('assets/ensemble-vide.png') }}" />
                                                <p class="mt-2">Aucun élève trouvé !</p>
                                            <div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="mt-3"> {{ $studentList->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
