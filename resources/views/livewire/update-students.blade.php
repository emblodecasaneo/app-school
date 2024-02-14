<div class="p-5 bg-white shadow-sm">
    <form method="POST" wire:submit.prevent="store">
        @csrf
        @method('POST')
        @if (Session::get('error'))
            <div class="border-red-500 p-1 rounded-md bg-red-100 animate-bounce">{{ Session::get('error') }}</div>
        @endif
        <div class="flex-1">

            <!-- Champs pour le nom -->
            <div class="felx-1 mb-4">
                <p class="text-gray-500 text">Nom<span class="text text-red-500">*</span></p>
                <input
                    class="block  rounded-md border-gray-300 w-full
             @error('nom') border-red-500 bg-red-100  @enderror"
                    placeholder="Entrer le libellé du nveau" type="text" wire:model="nom" />
                @error('nom')
                    <div class="text text-red-500 mt-1">Le nom est requis</div>
                @enderror
            </div>

            <!-- Champs pour le prénom -->
            <div class="flex-1 mb-4">
                <p class="text-gray-500 text">Prénom<span class="text text-red-500">*</span></p>
                <input class="block  rounded-md border-gray-300 w-full" placeholder="Entrer le prénom" type="text"
                    wire:model="prenom" />
            </div>

            <!-- Champs pour la date de naissance -->
            <div class="felx-1 mb-4">
                <p class="text-gray-500 text">Date de naissance<span class="text text-red-500">*</span></p>
                <input
                    class="block  rounded-md border-gray-300 w-full
             @error('naissance') border-red-500 bg-red-100  @enderror"
                    placeholder="Entrez la date de nasissance" type="date" wire:model="naissance" />
                @error('naissance')
                    <div class="text text-red-500 mt-1">La date de naissance est requis</div>
                @enderror
            </div>

            <!-- champs pour le matricule -->
            <div class="felx-1 mb-4">
                <p class="text-gray-500 text">Matricule<span class="text text-red-500">*</span></p>
                <input
                    class="block  rounded-md border-gray-300 w-full
             @error('matricule') border-red-500 bg-red-100  @enderror"
                    placeholder="Ce champs n'est pas à remplir , un matricule sera automatiquement générer pour cet éléève" type="text" wire:model="matricule" disabled />
                @error('matricule')
                    <div class="text text-red-500 mt-1">Le matricule est requis et doit être unique</div>
                @enderror
            </div>

            <div class="flex-1 mb-4">
                <p class="text-gray-500 text">Sexe<span class="text text-red-500">*</span></p>
                <select
                    class="block  rounded-md border-gray-300 w-full
             @error('sexe') border-red-500 bg-red-100 @enderror"
                    type="text" wire:model="sexe" name="sexe" id="sexe">
                    <option></option>
                    <option value="M">Masculin</option>
                    <option value="F">Féminin</option>
                </select>
                @error('sexe')
                    <div class="text text-red-500 mt-1">Le champ niveau est requis</div>
                @enderror
            </div>

            <!-- champs pour le contact du parent -->
            <div class="felx-1">
                <p class="text-gray-500 text">Contact du parent<span class="text text-red-500">*</span></p>
                <input
                    class="block  rounded-md border-gray-300 w-full
             @error('naissance') border-red-500 bg-red-100  @enderror"
                    placeholder="Entrez le contact du parent" type="text" wire:model="contact_parent" />
                @error('contact_parent')
                    <div class="text text-red-500 mt-1">Le contact du parent est requis</div>
                @enderror
            </div>
        </div>

        <div class="mt-5 flex justify-between items-center">
            <a href="{{route('students')}}" class="bg-red-600 p-2 rounded-md text-white text-md">Annuler</a>
            <button type="submit"  style="background-color: rgb(29, 83, 201)" class=" p-2 rounded-md text-white text-md">Mettre à jour</button>
        </div>
</div>
</form>
</div>
