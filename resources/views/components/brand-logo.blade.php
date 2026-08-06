{{--
  Component: Brand Logo AIRS Fitness
  Usage examples:
  1. Hanya Logo: <x-brand-logo type="logo" size="40" />
  2. Logo + Tulisan (Default): <x-brand-logo type="full" size="40" theme="dark" />
  3. Hanya Tulisan: <x-brand-logo type="text" />
--}}
@props([
    'type' => 'logo', // 'logo', 'full', 'text'
    'theme' => 'light', // 'light' (untuk bg terang) atau 'dark' (untuk bg gelap)
    'size' => '40', // tinggi logo dalam pixel
    'url' => '/'
])

@php
    $logoImg = asset('images/logo.png');
    $textColor = $theme === 'light' ? '#ffffff' : '#111827';
    $accentColor = '#c83660';
@endphp

<a href="{{ url($url) }}" class="brand-logo-container d-inline-flex align-items-center text-decoration-none" style="gap: 10px;">
    {{-- 1. HANYA LOGO ATAU LOGO + TULISAN --}}
    @if($type === 'logo' || $type === 'full')
        <img src="{{ $logoImg }}"
             alt="AIRS Fitness Logo"
             class="brand-logo-img"
             style="height: {{ $size }}px; width: auto; borde   r-radius: 8px; object-fit: contain; flex-shrink: 0;">
    @endif

    {{-- 2. HANYA TULISAN ATAU LOGO + TULISAN --}}
    @if($type === 'text' || $type === 'full')
        <span class="brand-logo-text font-weight-bold" style="color: {{ $textColor }}; font-size: {{ (int)$size * 0.55 }}px; letter-spacing: -0.5px; line-height: 1;">
            AIRS<span style="color: {{ $accentColor }};">Fitness</span>
        </span>
    @endif
</a>
