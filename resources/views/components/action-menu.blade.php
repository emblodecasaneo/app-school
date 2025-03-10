@props(['id'])

<div x-data="{ open: false }" class="relative inline-block text-left">
    <div>
        <button @click="open = !open" type="button" class="inline-flex justify-center w-full rounded-full border border-gray-300 shadow-sm px-2 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-0 focus:ring-offset-2 focus:ring-indigo-500/20" id="menu-button-{{ $id }}" aria-expanded="true" aria-haspopup="true">
            <x-icons name="more" class="text-gray-500" />
        </button>
    </div>

    <div x-show="open" 
         @click.outside="open = false"
         style="display: none;"
         class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" 
         role="menu" 
         aria-orientation="vertical" 
         aria-labelledby="menu-button-{{ $id }}" 
         tabindex="-1">
        <div class="py-1" role="none">
            {{ $slot }}
        </div>
    </div>
</div> 