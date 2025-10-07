@props([
    'height' => '50px',
    'url' => null,
    'theme' => 'black' // black or white
])

@php
    $logoUrl = config('app.url') . '/imgs/workfit_logo_' . $theme . '.png';
    $linkUrl = $url ?? config('app.url');
@endphp

<a href="{{ $linkUrl }}" style="display: inline-block; text-decoration: none;">
    <img src="{{ $logoUrl }}"
         alt="{{ config('app.name') }} Logo"
         style="max-height: {{ $height }}; height: auto; display: block;">
</a>

