@props([
    'type' => 'default', // default, dark, light
    'class' => '',
    'width' => null,
    'height' => null,
    'email' => false, // Use absolute URL for emails
])

@php
    // Use absolute URL for emails, relative for web
    $assetFn = $email ? 'url' : 'asset';

    $logoSrc = match($type) {
        'dark' => $assetFn('imgs/dark-logo.png'),
        'light' => $assetFn('imgs/logo.png'),
        default => $assetFn('imgs/logo.png'),
    };

    $defaultClass = 'h-auto';
    $finalClass = $class ?: $defaultClass;
@endphp

<img
    src="{{ $logoSrc }}"
    alt="WorkFit"
    class="{{ $finalClass }}"
    @if($width) width="{{ $width }}" @endif
    @if($height) height="{{ $height }}" @endif
    {{ $attributes }}
>
