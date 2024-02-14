<div class="mt-4">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
        {{-- Titre et Bouton créer --}}
        <div class="flex items-center justify-between">
            <input type="text" name="search" placeholder="Rechercher un niveau" class="rounded-md w-1/3 border-gray-300"
                wire:model.live="search" />
            <a href="{{ route('niveaux.create_level') }}" class="bg-blue-500 rounded-md p-2 text-sm text-white">Ajouter un
                niveau</a>
        </div>
        <div class="flex flex-col  mt-5 boder-gray-400">
            {{-- Message qui appaitra après opération --}}
            @if (Session::get('success'))
                <div class="block p-2 bg-green-500 text-white rounded-sm shadow-sm mt-2">
                    {{ Session::get('success') }}
                </div>
            @endif
            {{-- Style du tableau --}}
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full">
                    <div class="overflow-hidden">
                        <table class="min-w-full text-center">
                            <thead class="border-b bg-gray-50">
                                <tr class="text-blue-500">
                                    <th class="text-md font-semibold px-4 py-4">ID</th>
                                    <th class="text-md font-semibold px-4 py-4">Code</th>
                                    <th class="text-md font-semibold px-4 py-4">Libellé</th>
                                    <th class="text-md font-semibold px-4 py-4">Scolarité</th>
                                    <th class="text-md font-semibold px-4 py-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($levelList as $item)
                                    <tr class="border-b-2">
                                        <td class="text-sm font-medium text-gray-900 px-4 py-4">{{ $item->id }}
                                        </td>
                                        <td class="text-sm font-medium text-gray-900 px-4 py-4">
                                            {{ $item->code }}</td>
                                        <td class="text-sm font-medium text-gray-900 px-4 py-4">
                                            {{ $item->libelle }}
                                        </td>
                                        <td class="text-sm font-medium text-gray-900 px-4 py-4">
                                            {{ $item->scolarite }}
                                        </td>

                                        <td class="text-sm  font-medium text-gray-900 px-4 py-4">
                                            <div style="justify-content: center;" class="flex items-center">
                                                <a href="{{ route('niveaux.update_level', $item) }}"
                                                    class="mr-2 text-md text-white rounded-sm p-2">
                                                    <img alt="modifier" style="height: 25px ; width:25px"
                                                        src="{{ asset('assets/pen.png') }}" />
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
                                        <td colspan="5">
                                            <div style="display: flex; flex-direction:column"
                                            class=" p-10 justify-center items-center">
                                            <img style="height: 80px; width:80px" alt="empty"
                                                src="{{ asset('assets/ensemble-vide.png') }}" />
                                            <p class="mt-2">Aucun niveau trouvé !</p>
                                        <div>                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="mt-2"> {{ $levelList->links() }}</div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
