{{-- Partial Pesan Error Validasi Standar PetGym --}}
@if($errors->any())
<div class="alert alert-danger py-2 mb-3" style="font-size: 13px; border-radius: 8px;">
  <strong><i class="icon-warning mr-1"></i> Data tidak dapat disimpan:</strong>
  <ul class="mb-0 pl-3 mt-1">
    @foreach($errors->all() as $err)
      <li>{{ $err }}</li>
    @endforeach
  </ul>
</div>
@endif
