<div class="p-5 bg-white shadow-sm">
    <form method="POST" wire:submit.prevent="store">
        @csrf
        @method('POST')
        @if(Session::get('error'))
            <div class="border-red-500 p-1 rounded-md bg-red-100 animate-bounce">{{Session::get('error')}}</div>
        @endif
        <div>
            <input  class="block  rounded-md border-gray-300 w-full
             @error('libelle') border-red-500 bg-red-100 animate-bounce @enderror" placeholder="Libellé de l'année scolaie" type="text"
            wire:model="libelle"/>
            @error('libelle')
            <div class="text text-red-500 mt-1">Le champ libelle est requis</div>
            @enderror
        </div>
        <div class="mt-5 flex justify-between items-center">
            <button class="bg-red-600 p-2 rounded-md text-white text-md">Annuler</button>
            <button type="submit" class="bg-green-600 p-2 rounded-md text-white text-md">Ajouter</button>
        </div>
</div>
</form>
</div>
