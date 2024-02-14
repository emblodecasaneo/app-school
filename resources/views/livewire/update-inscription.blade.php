<div class="p-5 bg-white shadow-sm">
    <form method="POST" wire:submit.prevent="store">
        @csrf
        @method('POST')
        @if (Session::get('error'))
            <div class="border-red-500 p-1 rounded-md bg-red-100 animate-bounce">{{ Session::get('error') }}</div>
        @endif

         <!-- Champ pour le matricule -->
         <div class="flex-1">
            <div class="felx-1 mb-4">
                <p class="text-gray-500 text">matricule<span class="text text-red-500">*</span></p>
                <input
                    class="block  rounded-md border-gray-300 w-full
             @error('matricule') border-red-500 bg-red-100  @enderror"
                  readonly  placeholder="Entrer le matricule" type="text" wire:model.live="matricule" />
            </div>


         <!-- Champ pour le nom de l'élève -->
         <div class="flex-1">
            <div class="felx-1 mb-4">
                <p class="text-gray-500 text">Nom de l'élève<span class="text text-red-500">*</span></p>
                <input
                    class="block  rounded-md border-gray-300 w-full
             @error('nom') border-red-500 bg-red-100  @enderror"
                    readonly type="text" wire:model="nom" />
            </div>

        <!-- Champ pour le choix du niveau -->
        <div class="flex-1 mb-4">
            <p class="text-gray-500 text">Niveau<span class="text text-red-500">*</span></p>
            <select
                class="block  rounded-md border-gray-300 w-full
         @error('level_id') border-red-500 bg-red-100 @enderror"
                type="text" wire:model.live="level_id" name="level_id" id="level_id">
                <option></option>
                @foreach ($getAllLevels as $item)
                    <option  value="{{$item->id}}">{{$item->libelle}}</option>
                @endforeach
            </select>
            @error('level_id')
                <div class="text text-red-500 mt-1">Le champ niveau est requis</div>
            @enderror
        </div>

        <!-- Champ pour le choix de la classe  -->
        <div class="flex-1 mb-4">
            <p class="text-gray-500 text">Classe<span class="text text-red-500">*</span></p>
            <select
                class="block  rounded-md border-gray-300 w-full
         @error('classe_id') border-red-500 bg-red-100 @enderror"
                type="text" wire:model="classe_id" name="classe_id" id="classe_id">
                <option></option>
                @foreach ($classList as $item)
                    <option  value="{{$item->id}}">{{$item->libelle}}</option>
                @endforeach
            </select>
            @error('classe_id')
                <div class="text text-red-500 mt-1">Le champ niveau est requis</div>
            @enderror
        </div>

        <!-- Champ pour le commentaire -->
        <div class="flex-1">
            <div class="felx-1 mb-4">
                <p class="text-gray-500 text">Commentaire<span class="text text-red-500">*</span></p>
                <input
                    class="block  rounded-md border-gray-300 w-full
             @error('comments') border-red-500 bg-red-100  @enderror"
                    placeholder="Entrer le libellé du nveau" type="text" wire:model="comments" />
            </div>
        </div>

        <!-- Section pour les buttons d'action -->
        <div class=" flex justify-between items-center">
            <a href="{{ route('inscriptions') }}" class="bg-red-500 p-2 rounded-md text-white text-md">Annuler</a>
            <button type="submit" style="background-color: rgb(12, 86, 170)" class=" p-2 rounded-md text-white text-md">Mettre à jour</button>
        </div>
</div>
</form>
</div>
