@props([
    'label',
    'route' => null,
    'active' => null,
    'href' => null,
    'disabled' => false,
])

@php
    $isActive = $active ? request()->routeIs($active) : ($route ? request()->routeIs($route) : false);
    $baseClasses = 'flex items-center gap-2 rounded px-2 py-1.5 transition';
    $activeClasses = 'bg-white/10 text-white ring-1 ring-white/20';
    $inactiveClasses = 'text-white/80 hover:bg-white/10 hover:text-white';
    $disabledClasses = 'text-white/40 cursor-not-allowed';
    $url = null;

    if (! $disabled) {
        if ($href) {
            $url = $href;
        } elseif ($route) {
            $url = route($route);
        }
    }
@endphp

@if ($url)
    <a href="{{ $url }}" class="{{ $baseClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
        <span class="flex h-4 w-4 items-center justify-center text-white/70">
            {{ $slot }}
        </span>
        <span class="truncate">{{ $label }}</span>
        @if ($isActive)
            <span class="ml-auto h-1.5 w-1.5 rounded-full bg-white"></span>
        @endif
    </a>
@else
    <div class="{{ $baseClasses }} {{ $disabledClasses }}">
        <span class="flex h-4 w-4 items-center justify-center text-white/40">
            {{ $slot }}
        </span>
        <span class="truncate">{{ $label }}</span>
    </div>
@endif
