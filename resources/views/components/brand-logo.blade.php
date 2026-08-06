{{--
  Component: Brand Logo AIRS Fitness
  Usage examples:
  1. Hanya Logo: <x-brand-logo type="logo" size="40" />
  2. Logo + Tulisan (Default): <x-brand-logo type="full" size="40" theme="dark" />
  3. Hanya Tulisan: <x-brand-logo type="text" />
--}}
@props([
    'type' => 'full', // 'logo', 'full', 'text'
    'theme' => 'dark', // 'light' (untuk bg terang) atau 'dark' (untuk bg gelap)
    'size' => '40', // tinggi logo dalam pixel
    'url' => '/'
])

@php
    $logoDark = asset('images/logo.png');
    $logoWhite = asset('images/logo-white.png');
    $textColor = $theme === 'dark' ? '#ffffff' : '#111827';
    $accentColor = '#c83660';
@endphp

<a href="{{ url($url) }}" class="brand-logo-container d-inline-flex align-items-center text-decoration-none" style="gap: 10px;">
    {{-- 1. HANYA LOGO ATAU LOGO + TULISAN --}}
    @if($type === 'logo' || $type === 'full')
        <img src="{{ $logoWhite }}"
             alt="AIRS Fitness Logo"
             class="brand-logo-img brand-logo-white"
             style="height: {{ $size }}px; width: auto; border-radius: 8px; object-fit: contain; flex-shrink: 0; display: {{ $theme === 'dark' ? 'inline-block' : 'none' }};">
        <img src="{{ $logoDark }}"
             alt="AIRS Fitness Logo"
             class="brand-logo-img brand-logo-dark"
             style="height: {{ $size }}px; width: auto; border-radius: 8px; object-fit: contain; flex-shrink: 0; display: {{ $theme === 'light' ? 'inline-block' : 'none' }};">
    @endif

    {{-- 2. HANYA TULISAN ATAU LOGO + TULISAN --}}
    @if($type === 'text' || $type === 'full')
        <span class="brand-logo-text font-weight-bold" style="font-size: {{ (int)$size * 0.55 }}px; letter-spacing: -0.5px; line-height: 1;">
            <span class="brand-logo-text-airs" style="color: {{ $textColor }}; transition: color 0.3s ease;">AIRS</span><span style="color: {{ $accentColor }};">Fitness</span>
        </span>
    @endif
</a>
