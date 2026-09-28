@extends('layouts.superadmin')

@section('title', 'Paket Sewa &mdash; Superadmin Panel')

@section('page_title', 'Paket Sewa')
@section('page_subtitle', 'Manajemen paket langganan dan batasan fitur')

@section('content')
<!-- 3. PAKET SEWA (SUBSCRIPTION & PLAN MANAGEMENT) -->
<section id="plans" class="mb-5">
  @if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
    <ul class="mb-0 pl-3">
      @foreach($errors->all() as $err)
        <li class="font-weight-bold small">{{ $err }}</li>
      @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="font-weight-bold text-black mb-0">Manajemen Paket Sewa</h4>
    <button class="btn btn-primary btn-sm px-3" data-toggle="modal" data-target="#createPlanModal">+ Buat Paket Baru</button>
  </div>

  <div class="row">
    @php
      $masterFeatures = [
        'Akses Manajemen Kelas',
        'Kasir / POS Sederhana',
        'Akses Manajemen Trainer',
        'Manajemen Inventaris',
        'Mobile App Member Access',
        'Analytics Lanjutan',
        'Kustom Domain Sendiri',
        'Dedicated Database',
        'Support Prioritas 24/7'
      ];
    @endphp

    @forelse($plans as $plan)
      @php
        $planFeatures = is_array($plan->features) ? $plan->features : [];
        $borderClass = 'border-primary';
        $btnClass = 'btn-outline-primary';
        $textClass = 'text-primary';
        if (strpos(strtolower($plan->name), 'pro') !== false) {
            $borderClass = 'border-success';
            $btnClass = 'btn-outline-success';
            $textClass = 'text-success';
        } elseif (strpos(strtolower($plan->name), 'enterprise') !== false || strpos(strtolower($plan->name), 'ultimate') !== false) {
            $borderClass = 'border-warning';
            $btnClass = 'btn-outline-warning text-warning';
            $textClass = 'text-warning';
        }
      @endphp
      <div class="col-md-4 mb-4">
        <div class="bg-white p-4 rounded shadow-sm border-top {{ $borderClass }}" style="border-top-width: 4px !important; position: relative; min-height: 100%; display: flex; flex-direction: column;">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h5 class="font-weight-bold text-black mb-0">{{ $plan->name }}</h5>
            <span class="badge {{ $plan->status == 'active' ? 'badge-success' : 'badge-secondary' }}" id="badge-{{ $plan->id }}">
              {{ ucfirst($plan->status) }}
            </span>
          </div>
          <h3 class="{{ $textClass }} font-weight-bold">
            Rp {{ number_format($plan->price, 0, ',', '.') }} 
            <small style="font-size: 14px;" class="text-muted">/ bulan</small>
          </h3>

          <ul class="list-unstyled my-3 text-muted" style="line-height: 2; font-size: 14px;">
            <li><i class="icon-check text-success mr-1"></i> {{ $plan->max_members ? 'Maksimal ' . $plan->max_members . ' Member' : 'Unlimited Member' }}</li>
            @foreach($masterFeatures as $fItem)
              @if(in_array($fItem, $planFeatures))
                <li><i class="icon-check text-success mr-1"></i> {{ $fItem }}</li>
              @else
                <li class="text-muted" style="opacity: 0.5;"><i class="icon-close text-muted mr-1"></i> {{ $fItem }}</li>
              @endif
            @endforeach
          </ul>

          <hr class="mt-auto mb-3">

          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="custom-control-input plan-toggle" id="toggle-{{ $plan->id }}" data-id="{{ $plan->id }}" data-name="{{ $plan->name }}" {{ $plan->status == 'active' ? 'checked' : '' }}>
              <label class="custom-control-label text-muted" for="toggle-{{ $plan->id }}" style="font-size: 13px; cursor: pointer;">Status Paket</label>
            </div>

            <!-- Delete Form -->
            <form action="{{ route('superadmin.plans.destroy', $plan->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus paket ini dari database?">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-link text-danger p-0" style="font-size: 12px; font-weight: bold; text-decoration: none;">
                Hapus Paket
              </button>
            </form>
          </div>

          <button class="btn {{ $btnClass }} btn-block btn-sm btn-edit-plan"
                  data-id="{{ $plan->id }}"
                  data-name="{{ $plan->name }}"
                  data-price="{{ (int)$plan->price }}"
                  data-max-members="{{ $plan->max_members }}"
                  data-features='@json($planFeatures)'>
            Edit Batasan Paket
          </button>
        </div>
      </div>
    @empty
      <div class="col-12 py-5 text-center bg-white rounded shadow-sm">
        <p class="text-muted mb-0">Belum ada paket sewa terdaftar di database.</p>
      </div>
    @endforelse
  </div>
</section>
@endsection

@section('modals')
<!-- MODAL: TAMBAH PAKET BARU -->
<div class="modal fade" id="createPlanModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0" style="border-radius: 14px; overflow: hidden;">
      <div class="modal-header bg-white border-bottom py-3 px-4">
        <h5 class="modal-header-title font-weight-bold text-black mb-0">Buat Paket Sewa Baru</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('superadmin.plans.store') }}" method="POST" id="createPlanForm">
        @csrf
        <div class="modal-body p-4">
          <div class="row">
            <div class="col-md-4 form-group mb-3">
              <label class="text-black font-weight-bold small">Nama Paket <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" placeholder="Contoh: Paket Ultimate" required>
            </div>
            <div class="col-md-4 form-group mb-3">
              <label class="text-black font-weight-bold small">Harga Bulanan (Rp) <span class="text-danger">*</span></label>
              <input type="number" name="price" class="form-control" placeholder="3000000" required>
            </div>
            <div class="col-md-4 form-group mb-3">
              <label class="text-black font-weight-bold small">Maksimal Member</label>
              <input type="number" name="max_members" class="form-control" placeholder="1000">
              <small class="text-muted d-block mt-1">Kosongkan jika unlimited</small>
            </div>
          </div>
          
          <div class="form-group mb-0 bg-light p-3 rounded border">
            <label class="text-black font-weight-bold small d-block mb-3">
              <span class="icon-layers mr-1 text-primary"></span> Fitur & Modul Paket (Pilih Fitur Aktif)
            </label>
            <div class="row">
              @foreach($masterFeatures as $idx => $fName)
                <div class="col-md-4 col-sm-6 mb-2">
                  <div class="custom-control custom-checkbox bg-white p-2 rounded border h-100 d-flex align-items-center">
                    <input type="checkbox" name="features[]" value="{{ $fName }}" class="custom-control-input" id="createFeat{{ $idx }}" {{ $idx < 5 ? 'checked' : '' }}>
                    <label class="custom-control-label font-weight-bold text-dark small pl-1 w-100" for="createFeat{{ $idx }}" style="cursor: pointer; line-height: 1.3;">
                      {{ $fName }}
                    </label>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light py-2 px-4 border-top">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm font-weight-bold px-3">Buat & Simpan Paket</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL: EDIT BATASAN & FITUR PAKET -->
<div class="modal fade" id="editPlanModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0" style="border-radius: 14px; overflow: hidden;">
      <div class="modal-header bg-white border-bottom py-3 px-4">
        <h5 class="modal-header-title font-weight-bold text-black mb-0">Edit Batasan & Fitur: <span id="editModalPlanTitle" class="text-primary"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST" id="editPlanForm">
        @csrf
        @method('PUT')
        <div class="modal-body p-4">
          <div class="row">
            <div class="col-md-4 form-group mb-3">
              <label class="text-black font-weight-bold small">Nama Paket <span class="text-danger">*</span></label>
              <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="col-md-4 form-group mb-3">
              <label class="text-black font-weight-bold small">Harga Bulanan (Rp) <span class="text-danger">*</span></label>
              <input type="number" name="price" id="edit_price" class="form-control" required>
            </div>
            <div class="col-md-4 form-group mb-3">
              <label class="text-black font-weight-bold small">Maksimal Member</label>
              <input type="number" name="max_members" id="edit_max_members" class="form-control" placeholder="Kosongkan jika unlimited">
              <small class="text-muted d-block mt-1">Kosongkan untuk Unlimited.</small>
            </div>
          </div>
          
          <div class="form-group mb-0 bg-light p-3 rounded border">
            <label class="text-black font-weight-bold small d-block mb-3">
              <span class="icon-layers mr-1 text-primary"></span> Fitur & Modul Paket
            </label>
            <div class="row">
              @foreach($masterFeatures as $idx => $fName)
                <div class="col-md-4 col-sm-6 mb-2">
                  <div class="custom-control custom-checkbox bg-white p-2 rounded border h-100 d-flex align-items-center">
                    <input type="checkbox" name="features[]" value="{{ $fName }}" class="custom-control-input edit-feat-checkbox" id="editFeat{{ $idx }}">
                    <label class="custom-control-label font-weight-bold text-dark small pl-1 w-100" for="editFeat{{ $idx }}" style="cursor: pointer; line-height: 1.3;">
                      {{ $fName }}
                    </label>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light py-2 px-4 border-top">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm font-weight-bold px-3">Simpan Perubahan ke Database</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    // 1. Membuka Modal Edit & Populasi Form
    $('.btn-edit-plan').on('click', function(e) {
      e.preventDefault();
      const planId = $(this).data('id');
      const planName = $(this).data('name');
      const planPrice = $(this).data('price');
      const maxMembers = $(this).data('max-members');
      const features = $(this).data('features') || [];

      // Update Form Action URL
      const updateUrl = "{{ url('/superadmin/plans') }}/" + planId;
      $('#editPlanForm').attr('action', updateUrl);

      // Set Input Values
      $('#editModalPlanTitle').text(planName);
      $('#edit_name').val(planName);
      $('#edit_price').val(planPrice);
      $('#edit_max_members').val(maxMembers !== null ? maxMembers : '');

      // Check Feature Checkboxes
      $('.edit-feat-checkbox').prop('checked', false);
      $('.edit-feat-checkbox').each(function() {
        const featureValue = $(this).val();
        if (features.includes(featureValue)) {
          $(this).prop('checked', true);
        }
      });

      $('#editPlanModal').modal('show');
    });

    // 2. Toggle Status Switch Via AJAX
    $('.plan-toggle').on('change', function() {
      const planId = $(this).data('id');
      const planName = $(this).data('name');
      const isChecked = $(this).is(':checked');
      const badge = $('#badge-' + planId);

      fetch("{{ url('/superadmin/plans') }}/" + planId + "/toggle-status", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          if (data.new_status === 'active') {
            badge.removeClass('badge-secondary').addClass('badge-success').text('Aktif');
            showToast('Paket Diaktifkan', `Status paket "${planName}" berhasil diaktifkan di database.`, 'success');
          } else {
            badge.removeClass('badge-success').addClass('badge-secondary').text('Diarsipkan');
            showToast('Paket Diarsipkan', `Status paket "${planName}" diarsipkan di database.`, 'warning');
          }
        }
      })
      .catch(error => {
        showToast('Error', 'Gagal memperbarui status paket di database.', 'error');
      });
    });
  });
</script>
@endsection
