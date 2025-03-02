<div class="p-5 bg-white shadow-sm">
    <form method="POST" wire:submit.prevent="store">
        @csrf
        @method('POST')
        @if (Session::get('error'))
            <div class="border-red-500 p-1 rounded-md bg-red-100 animate-bounce">{{ Session::get('error') }}</div>
        @endif
        <div class="flex-1">
            <div class="felx-1 mb-5">
                <p class="text-gray-900 text">Lebellé du niveau<span class="text text-red-500">*</span></p>
                <input
                    class="block  rounded-md border-gray-300 w-full
             @error('libelle') border-red-500 bg-red-100  @enderror"
                    placeholder="Entrer le libellé du nveau" type="text" wire:model="libelle" />
                @error('libelle')
                    <div class="text text-red-500 mt-1">Le champ libelle est requis</div>
                @enderror
            </div>
            <div class="flex-1">
                <p class="text-gray-900 text">Code du niveau<span class="text text-red-500">*</span></p>
                <input
                    class="block  rounded-md border-gray-300 w-full
             @error('code') border-red-500 bg-red-100 @enderror"
                    placeholder="Entrer le code du niveau" type="text" wire:model="code" />
                @error('code')
                    <div class="text text-red-500 mt-1">Le champ code est requis</div>
                @enderror
            </div>
            <div class="flex-1 mt-5">
                <p class="text-gray-900 text">Montant de la scolarité<span class="text text-red-500">*</span></p>
                <input
                    class="block rounded-md border-gray-300 w-full
             @error('scolarite') border-red-500 bg-red-100 @enderror"
                    placeholder="Entrer le montant de la scolarité" type="number" wire:model="scolarite" />
                @error('scolarite')
                    <div class="text text-red-500 mt-1">Le montant de la scolarité est requis et doit être un nombre positif</div>
                @enderror
            </div>
        </div>
        <div class="mt-5 flex justify-between items-center">
            <a href="{{route('niveaux')}}" class="bg-red-600 p-2 rounded-md text-white text-md">Annuler</a>
            <button type="submit" class="bg-green-600 p-2 rounded-md text-white text-md">Ajouter</button>
        </div>
</div>
</form>
</div>
