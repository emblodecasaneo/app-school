@props(['href' => null, 'wire' => null, 'icon' => null, 'color' => 'indigo', 'onclick' => null])

@php
    $classes = "group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-{$color}-50 hover:text-{$color}-700";
    
    if ($href) {
        $tag = 'a';
    } else {
        $tag = 'button';
    }
@endphp

@if($href)
    <a href="{{ $href }}" class="{{ $classes }}" role="menuitem" tabindex="-1" @if($onclick) onclick="{{ $onclick }}" @endif>
        @if ($icon)
            <x-icons name="{{ $icon }}" class="mr-3 text-gray-500 group-hover:text-{{ $color }}-600" size="sm" />
        @endif
        <span>{{ $slot }}</span>
    </a>
@elseif($wire)
    <button type="button" wire:click="{{ $wire }}" class="{{ $classes }}" role="menuitem" tabindex="-1" @if($onclick) onclick="{{ $onclick }}" @endif wire:loading.attr="disabled">
        @if ($icon)
            <x-icons name="{{ $icon }}" class="mr-3 text-gray-500 group-hover:text-{{ $color }}-600" size="sm" />
        @endif
        <span>{{ $slot }}</span>
    </button>
@else
    <button type="button" class="{{ $classes }}" role="menuitem" tabindex="-1" @if($onclick) onclick="{{ $onclick }}" @endif>
        @if ($icon)
            <x-icons name="{{ $icon }}" class="mr-3 text-gray-500 group-hover:text-{{ $color }}-600" size="sm" />
        @endif
        <span>{{ $slot }}</span>
    </button>
@endif 