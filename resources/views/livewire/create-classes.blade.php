<div class="p-5 bg-white shadow-sm">
    <form method="POST" wire:submit.prevent="store">
        @csrf
        @method('POST')
        @if (Session::get('error'))
            <div class="border-red-500 p-1 rounded-md bg-red-100 animate-bounce">{{ Session::get('error') }}</div>
        @endif
        <div class="flex-1 mb-5">
            <p class="text-gray-500 text">Choix du niveau<span class="text text-red-500">*</span></p>
            <select
                class="block  rounded-md border-gray-300 w-full
         @error('level_id') border-red-500 bg-red-100 @enderror"
                type="text" wire:model="level_id" name="level_id" id="level_id">
                <option></option>
                @foreach ($getAllLevels as $item)
                    <option  value="{{$item->id}}">{{$item->libelle}}</option>
                @endforeach
            </select>
            @error('level_id')
                <div class="text text-red-500 mt-1">Le champ niveau est requis</div>
            @enderror
        </div>

        <div class="flex-1">
            <div class="felx-1">
                <p class="text-gray-500 text">Lebellé de la classe<span class="text text-red-500">*</span></p>
                <input
                    class="block  rounded-md border-gray-300 w-full
             @error('libelle') border-red-500 bg-red-100  @enderror"
                    placeholder="Entrer le libellé du nveau" type="text" wire:model="libelle" />
                @error('libelle')
                    <div class="text text-red-500 mt-1">Le champ libelle est requis</div>
                @enderror
            </div>
        </div>
        <div class="mt-5 flex justify-between items-center">
            <a href="{{ route('classes') }}" class="bg-red-600 p-2 rounded-md text-white text-md">Annuler</a>
            <button type="submit" style="background-color: rgb(12, 86, 170)" class=" p-2 rounded-md text-white text-md">Ajouter une classe</button>
        </div>
</div>
</form>
</div>
