{{-- Partial Empty State Standar PetGym --}}
@props([
    'icon' => 'icon-info',
    'title' => 'Belum ada data tersedia',
    'subtitle' => null,
    'actionUrl' => null,
    'actionLabel' => null,
    'colspan' => null,
])

@if($colspan)
  <tr>
    <td colspan="{{ $colspan }}" class="text-center py-4 text-muted">
      <span class="{{ $icon }} d-block mb-2" style="font-size: 28px; opacity: 0.6;"></span>
      <p class="mb-0 font-weight-bold text-dark" style="font-size: 14px;">{{ $title }}</p>
      @if($subtitle)
        <small class="text-muted d-block mt-1">{{ $subtitle }}</small>
      @endif
      @if($actionUrl && $actionLabel)
        <a href="{{ $actionUrl }}" class="btn btn-sm btn-primary mt-2 font-weight-bold" style="border-radius: 6px;">
          {{ $actionLabel }}
        </a>
      @endif
    </td>
  </tr>
@else
  <div class="text-center py-4 text-muted">
    <span class="{{ $icon }} d-block mb-2" style="font-size: 32px; opacity: 0.6;"></span>
    <p class="mb-0 font-weight-bold text-dark" style="font-size: 14px;">{{ $title }}</p>
    @if($subtitle)
      <small class="text-muted d-block mt-1">{{ $subtitle }}</small>
    @endif
    @if($actionUrl && $actionLabel)
      <a href="{{ $actionUrl }}" class="btn btn-sm btn-primary mt-3 font-weight-bold" style="border-radius: 6px;">
        {{ $actionLabel }}
      </a>
    @endif
  </div>
@endif
