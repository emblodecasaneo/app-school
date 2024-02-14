    <div class="mt-4">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
            {{-- Titre et Bouton créer --}}
            <div class="flex items-center justify-between">
                <input type="text" name="search" placeholder="Rechercher une année scolaire" class="rounded-md w-1/3 border-gray-300"
                wire:model.live="libelle"/>
                <a href="{{ route('settings.create_schoolyear') }}"
                    class="bg-blue-500 rounded-md p-2 text-sm text-white">Nouvelle Année Scolaire</a>
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
                                    <tr>
                                        <th class="text-sm font-medium text-gray-900 px-6 py-6">ID</th>
                                        <th class="text-sm font-medium text-gray-900 px-6 py-6">ANNEE SCOLAIRE</th>
                                        <th class="text-sm font-medium text-gray-900 px-6 py-6">STATUT</th>
                                        <th class="text-sm font-medium text-gray-900 px-6 py-6">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($schoolYearList as $item)
                                        <tr class="border-b-2">
                                            <td class="text-sm font-medium text-gray-900 px-6 py-6">{{ $item->id }}
                                            </td>
                                            <td class="text-sm font-medium text-gray-900 px-6 py-6">
                                                {{ $item->school_year }}</td>
                                            <td class="text-sm font-medium text-gray-900 px-6 py-6">
                                                @if ($item->active > 0)
                                                    <span class="text text-md text-green-500"> * Active</span>
                                                @else
                                                    <span class="text text-sm text-red-500">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-sm font-medium text-gray-900 px-6 py-6">
                                                    <button
                                                        wire:click="toggleStatus({{$item}})"
                                                        class="{{$item->active == 0 ? 'bg-green-400' :'bg-red-400'}} bg-red-300 w-1/3 p-1 text-md rounded-sm">
                                                        {{$item->active == 0 ? 'Activer' :'Désactiver'}}
                                                    </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">
                                                <div style="display: flex; flex-direction:column"
                                                class=" p-10 justify-center items-center">
                                                <img style="height: 80px; width:80px" alt="empty"
                                                    src="{{ asset('assets/ensemble-vide.png') }}" />
                                                <p class="mt-2">Aucune année scolaire trouvé !</p>
                                            <div>                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            {{-- Pagination --}}
                            <div class="mt-2"> {{ $schoolYearList->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
