@props([
    'type' => 'button',
    'color' => 'indigo',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'href' => null,
    'wire' => null,
    'outlined' => false,
    'rounded' => false
])

@php
    $baseClasses = "inline-flex items-center justify-center font-poppins-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-150";
    
    $colorClasses = match($color) {
        'indigo' => $outlined 
            ? "border border-indigo-600 text-indigo-600 hover:bg-indigo-50 focus:ring-indigo-500" 
            : "bg-indigo-600 border border-transparent text-white hover:bg-indigo-700 focus:ring-indigo-500",
        'red' => $outlined 
            ? "border border-red-600 text-red-600 hover:bg-red-50 focus:ring-red-500" 
            : "bg-red-600 border border-transparent text-white hover:bg-red-700 focus:ring-red-500",
        'green' => $outlined 
            ? "border border-green-600 text-green-600 hover:bg-green-50 focus:ring-green-500" 
            : "bg-green-600 border border-transparent text-white hover:bg-green-700 focus:ring-green-500",
        'blue' => $outlined 
            ? "border border-blue-600 text-blue-600 hover:bg-blue-50 focus:ring-blue-500" 
            : "bg-blue-600 border border-transparent text-white hover:bg-blue-700 focus:ring-blue-500",
        'gray' => $outlined 
            ? "border border-gray-600 text-gray-600 hover:bg-gray-50 focus:ring-gray-500" 
            : "bg-gray-600 border border-transparent text-white hover:bg-gray-700 focus:ring-gray-500",
        default => $outlined 
            ? "border border-indigo-600 text-indigo-600 hover:bg-indigo-50 focus:ring-indigo-500" 
            : "bg-indigo-600 border border-transparent text-white hover:bg-indigo-700 focus:ring-indigo-500",
    };
    
    $sizeClasses = match($size) {
        'xs' => "px-2 py-1 text-xs",
        'sm' => "px-3 py-1.5 text-sm",
        'md' => "px-4 py-2 text-sm",
        'lg' => "px-5 py-2.5 text-base",
        'xl' => "px-6 py-3 text-lg",
        default => "px-4 py-2 text-sm",
    };
    
    $roundedClasses = $rounded ? "rounded-full" : "rounded-md";
    
    $classes = "{$baseClasses} {$colorClasses} {$sizeClasses} {$roundedClasses}";
    
    $iconSize = match($size) {
        'xs' => 'xs',
        'sm' => 'xs',
        'md' => 'sm',
        'lg' => 'md',
        'xl' => 'md',
        default => 'sm',
    };
    
    $attrs = [];
    
    if ($href) {
        $tag = 'a';
        $attrs['href'] = $href;
    } elseif ($wire) {
        $tag = 'button';
        $attrs['wire:click'] = $wire;
        $attrs['type'] = $type;
    } else {
        $tag = 'button';
        $attrs['type'] = $type;
    }
@endphp

<{{ $tag }} {{ $attributes->merge($attrs)->merge(['class' => $classes]) }}>
    @if($icon && $iconPosition === 'left')
        <x-icons name="{{ $icon }}" class="mr-2" size="{{ $iconSize }}" />
    @endif
    
    {{ $slot }}
    
    @if($icon && $iconPosition === 'right')
        <x-icons name="{{ $icon }}" class="ml-2" size="{{ $iconSize }}" />
    @endif
</{{ $tag }}>
