@extends('layouts.superadmin')

@section('title', 'Paket Sewa &mdash; Superadmin Panel')

@section('page_title', 'Paket Sewa')
@section('page_subtitle', 'Manajemen paket langganan dan batasan fitur')

@section('content')
<!-- 3. PAKET SEWA (SUBSCRIPTION & PLAN MANAGEMENT) -->
<section id="plans" class="mb-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="font-weight-bold text-black mb-0">Manajemen Paket Sewa</h4>
    <button class="btn btn-primary btn-sm px-3" data-toggle="modal" data-target="#createPlanModal">+ Buat Paket Baru</button>
  </div>

  <div class="row">
    <!-- Paket Basic -->
    <div class="col-md-4 mb-4">
      <div class="bg-white p-4 rounded shadow-sm border-top border-primary" style="border-top-width: 4px !important; position: relative;">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <h5 class="font-weight-bold text-black mb-0">Paket Basic</h5>
          <span class="badge badge-success" id="badgeBasic">Active</span>
        </div>
        <h3 class="text-primary font-weight-bold">Rp 500.000 <small style="font-size: 14px;" class="text-muted">/ bulan</small></h3>
        <ul class="list-unstyled my-3 text-muted" style="line-height: 2; font-size: 14px;">
          <li>✓ Maksimal 150 Member</li>
          <li>✓ Akses Manajemen Kelas</li>
          <li>✓ Kasir / POS Sederhana</li>
          <li class="text-muted" style="opacity: 0.6;">✗ Analytics Lanjutan</li>
        </ul>
        <hr>
        <div class="custom-control custom-switch mb-3">
          <input type="checkbox" class="custom-control-input plan-toggle" id="toggleBasic" data-badge="badgeBasic" checked>
          <label class="custom-control-label text-muted" for="toggleBasic" style="font-size: 13px; cursor: pointer;">Status Paket</label>
        </div>
        <button class="btn btn-outline-primary btn-block btn-sm btn-edit-plan">Edit Batasan Paket</button>
      </div>
    </div>

    <!-- Paket Pro -->
    <div class="col-md-4 mb-4">
      <div class="bg-white p-4 rounded shadow-sm border-top border-success" style="border-top-width: 4px !important;">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <h5 class="font-weight-bold text-black mb-0">Paket Pro</h5>
          <span class="badge badge-success" id="badgePro">Active</span>
        </div>
        <h3 class="text-success font-weight-bold">Rp 1.200.000 <small style="font-size: 14px;" class="text-muted">/ bulan</small></h3>
        <ul class="list-unstyled my-3 text-muted" style="line-height: 2; font-size: 14px;">
          <li>✓ Maksimal 500 Member</li>
          <li>✓ Akses Manajemen Trainer</li>
          <li>✓ Manajemen Inventaris</li>
          <li>✓ Mobile App Member Access</li>
        </ul>
        <hr>
        <div class="custom-control custom-switch mb-3">
          <input type="checkbox" class="custom-control-input plan-toggle" id="togglePro" data-badge="badgePro" checked>
          <label class="custom-control-label text-muted" for="togglePro" style="font-size: 13px; cursor: pointer;">Status Paket</label>
        </div>
        <button class="btn btn-outline-success btn-block btn-sm btn-edit-plan">Edit Batasan Paket</button>
      </div>
    </div>

    <!-- Paket Enterprise -->
    <div class="col-md-4 mb-4">
      <div class="bg-white p-4 rounded shadow-sm border-top border-warning" style="border-top-width: 4px !important;">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <h5 class="font-weight-bold text-black mb-0">Paket Enterprise</h5>
          <span class="badge badge-success" id="badgeEnterprise">Active</span>
        </div>
        <h3 class="text-warning font-weight-bold">Rp 2.500.000 <small style="font-size: 14px;" class="text-muted">/ bulan</small></h3>
        <ul class="list-unstyled my-3 text-muted" style="line-height: 2; font-size: 14px;">
          <li>✓ Unlimited Member</li>
          <li>✓ Kustom Domain Sendiri</li>
          <li>✓ Dedicated Database</li>
          <li>✓ Support Prioritas 24/7</li>
        </ul>
        <hr>
        <div class="custom-control custom-switch mb-3">
          <input type="checkbox" class="custom-control-input plan-toggle" id="toggleEnterprise" data-badge="badgeEnterprise" checked>
          <label class="custom-control-label text-muted" for="toggleEnterprise" style="font-size: 13px; cursor: pointer;">Status Paket</label>
        </div>
        <button class="btn btn-outline-warning btn-block btn-sm text-warning btn-edit-plan">Edit Batasan Paket</button>
      </div>
    </div>
  </div>
</section>
@endsection

@section('modals')
<!-- MODAL: TAMBAH PAKET BARU -->
<div class="modal fade" id="createPlanModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-header-title font-weight-bold text-black" id="exampleModalLabel">Buat Paket Sewa Baru</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="#" method="POST" id="createPlanForm">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <label class="text-black font-weight-bold">Nama Paket</label>
            <input type="text" name="name" class="form-control" placeholder="Contoh: Paket Ultimate" required>
          </div>
          <div class="form-group">
            <label class="text-black font-weight-bold">Harga Bulanan (Rp)</label>
            <input type="number" name="price" class="form-control" placeholder="3000000" required>
          </div>
          <div class="form-group">
            <label class="text-black font-weight-bold">Maksimal Member</label>
            <input type="number" name="max_members" class="form-control" placeholder="1000">
            <small class="text-muted">Kosongkan jika unlimited</small>
          </div>
          <div class="form-group">
            <label class="text-black font-weight-bold">Fitur Tambahan</label>
            <div class="custom-control custom-checkbox mb-1">
              <input type="checkbox" class="custom-control-input" id="featTrainer">
              <label class="custom-control-label" for="featTrainer">Akses Manajemen Trainer</label>
            </div>
            <div class="custom-control custom-checkbox mb-1">
              <input type="checkbox" class="custom-control-input" id="featInventory">
              <label class="custom-control-label" for="featInventory">Manajemen Inventaris</label>
            </div>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="featDomain">
              <label class="custom-control-label" for="featDomain">Kustom Subdomain/Domain</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm">Buat Paket</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL: EDIT BATASAN & FITUR PAKET -->
<div class="modal fade" id="editPlanModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-header-title font-weight-bold text-black">Edit Batasan & Fitur: <span id="editModalPlanTitle" class="text-primary"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="#" method="POST" id="editPlanForm">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <label class="text-black font-weight-bold">Nama Paket</label>
            <input type="text" name="edit_name" id="edit_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="text-black font-weight-bold">Harga Bulanan (Rp)</label>
            <input type="number" name="edit_price" id="edit_price" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="text-black font-weight-bold">Maksimal Member</label>
            <input type="number" name="edit_max_members" id="edit_max_members" class="form-control" placeholder="Kosongkan jika unlimited">
            <small class="text-muted">Isi angka (misal 150, 500) atau kosongkan untuk Unlimited.</small>
          </div>
          
          <div class="form-group mb-0">
            <label class="text-black font-weight-bold mb-2">Fitur & Modul Paket</label>
            
            <div class="custom-control custom-checkbox mb-2">
              <input type="checkbox" class="custom-control-input edit-feat-cb" id="editFeatClass" data-label="Akses Manajemen Kelas">
              <label class="custom-control-label font-weight-bold text-dark" for="editFeatClass">Akses Manajemen Kelas</label>
            </div>
            <div class="custom-control custom-checkbox mb-2">
              <input type="checkbox" class="custom-control-input edit-feat-cb" id="editFeatPOS" data-label="Kasir / POS Sederhana">
              <label class="custom-control-label font-weight-bold text-dark" for="editFeatPOS">Kasir / POS Sederhana</label>
            </div>
            <div class="custom-control custom-checkbox mb-2">
              <input type="checkbox" class="custom-control-input edit-feat-cb" id="editFeatTrainer" data-label="Akses Manajemen Trainer">
              <label class="custom-control-label font-weight-bold text-dark" for="editFeatTrainer">Akses Manajemen Trainer</label>
            </div>
            <div class="custom-control custom-checkbox mb-2">
              <input type="checkbox" class="custom-control-input edit-feat-cb" id="editFeatInventory" data-label="Manajemen Inventaris">
              <label class="custom-control-label font-weight-bold text-dark" for="editFeatInventory">Manajemen Inventaris</label>
            </div>
            <div class="custom-control custom-checkbox mb-2">
              <input type="checkbox" class="custom-control-input edit-feat-cb" id="editFeatMobile" data-label="Mobile App Member Access">
              <label class="custom-control-label font-weight-bold text-dark" for="editFeatMobile">Mobile App Member Access</label>
            </div>
            <div class="custom-control custom-checkbox mb-2">
              <input type="checkbox" class="custom-control-input edit-feat-cb" id="editFeatAnalytics" data-label="Analytics Lanjutan">
              <label class="custom-control-label font-weight-bold text-dark" for="editFeatAnalytics">Analytics Lanjutan</label>
            </div>
            <div class="custom-control custom-checkbox mb-2">
              <input type="checkbox" class="custom-control-input edit-feat-cb" id="editFeatDomain" data-label="Kustom Domain Sendiri">
              <label class="custom-control-label font-weight-bold text-dark" for="editFeatDomain">Kustom Domain Sendiri</label>
            </div>
            <div class="custom-control custom-checkbox mb-2">
              <input type="checkbox" class="custom-control-input edit-feat-cb" id="editFeatDatabase" data-label="Dedicated Database">
              <label class="custom-control-label font-weight-bold text-dark" for="editFeatDatabase">Dedicated Database</label>
            </div>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input edit-feat-cb" id="editFeatSupport" data-label="Support Prioritas 24/7">
              <label class="custom-control-label font-weight-bold text-dark" for="editFeatSupport">Support Prioritas 24/7</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan Paket</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    let currentEditingCard = null;

    // 1. Submit form tambah paket secara dinamis
    $('#createPlanForm').on('submit', function(e) {
      e.preventDefault();
      const name = $('input[name="name"]').val();
      const rawPrice = $('input[name="price"]').val();
      const price = parseInt(rawPrice).toLocaleString('id-ID');
      const maxMem = $('input[name="max_members"]').val();
      const maxMembers = maxMem ? maxMem + ' Member' : 'Unlimited Member';
      
      const idName = 'toggle' + name.replace(/\s+/g, '');
      const badgeId = 'badge' + name.replace(/\s+/g, '');

      const newCard = `
        <div class="col-md-4 mb-4 new-plan-card" style="display:none;">
          <div class="bg-white p-4 rounded shadow-sm border-top border-info" style="border-top-width: 4px !important; position: relative;">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="font-weight-bold text-black mb-0">${name}</h5>
              <span class="badge badge-success" id="${badgeId}">Active</span>
            </div>
            <h3 class="text-info font-weight-bold">Rp ${price} <small style="font-size: 14px;" class="text-muted">/ bulan</small></h3>
            <ul class="list-unstyled my-3 text-muted" style="line-height: 2; font-size: 14px;">
              <li>✓ Maksimal ${maxMembers}</li>
              <li>✓ Akses Manajemen Kelas</li>
              <li>✓ Kasir / POS Sederhana</li>
            </ul>
            <hr>
            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" class="custom-control-input plan-toggle" id="${idName}" data-badge="${badgeId}" checked>
              <label class="custom-control-label text-muted" for="${idName}" style="font-size: 13px; cursor: pointer;">Status Paket</label>
            </div>
            <button class="btn btn-outline-info btn-block btn-sm btn-edit-plan">Edit Batasan Paket</button>
          </div>
        </div>
      `;
      
      $('.row').first().append(newCard);
      $('.new-plan-card').first().fadeIn(600);
      $('#createPlanModal').modal('hide');
      $('#createPlanForm')[0].reset();
      
      showToast('Paket Baru Dibuat', `Paket "${name}" berhasil ditambahkan ke daftar tier harga.`, 'success');
    });

    // 2. Toggle Switch Status Paket
    $(document).on('change', '.plan-toggle', function() {
      const badgeId = $(this).data('badge');
      const badge = $('#' + badgeId);
      const planName = $(this).closest('.bg-white').find('h5').text();
      
      if ($(this).is(':checked')) {
        badge.removeClass('badge-secondary').addClass('badge-success').text('Active');
        showToast('Paket Diaktifkan', `Paket "${planName}" kini tersedia untuk pendaftar baru.`, 'success');
      } else {
        badge.removeClass('badge-success').addClass('badge-secondary').text('Archived');
        showToast('Paket Diarsipkan', `Paket "${planName}" diarsipkan. Pendaftar baru tidak dapat memilih paket ini.`, 'warning');
      }
    });

    // 3. Membuka Modal Edit Batasan Paket
    $(document).on('click', '.btn-edit-plan', function(e) {
      e.preventDefault();
      currentEditingCard = $(this).closest('.col-md-4');
      
      const planName = currentEditingCard.find('h5').first().text().trim();
      const rawPriceText = currentEditingCard.find('h3').first().text().replace(/[^\d]/g, '');
      const priceVal = parseInt(rawPriceText) || 0;

      // Cari nilai maksimal member dari list
      let maxMembersVal = '';
      currentEditingCard.find('ul li').each(function() {
        const text = $(this).text();
        if (text.includes('Maksimal') || text.includes('Member')) {
          const match = text.match(/\d+/);
          if (match) {
            maxMembersVal = match[0];
          }
        }
      });

      // Isi input modal dengan data kartu terkini
      $('#editModalPlanTitle').text(planName);
      $('#edit_name').val(planName);
      $('#edit_price').val(priceVal);
      $('#edit_max_members').val(maxMembersVal);

      // Centang ulang checkbox fitur berdasarkan list di kartu
      $('.edit-feat-cb').prop('checked', false);
      currentEditingCard.find('ul li').each(function() {
        const liText = $(this).text().trim();
        if (liText.startsWith('✓')) {
          const featureCleanText = liText.replace('✓', '').trim();
          $('.edit-feat-cb').each(function() {
            const labelText = $(this).data('label');
            if (featureCleanText.toLowerCase().includes(labelText.toLowerCase()) || labelText.toLowerCase().includes(featureCleanText.toLowerCase())) {
              $(this).prop('checked', true);
            }
          });
        }
      });

      $('#editPlanModal').modal('show');
    });

    // 4. Submit Form Edit Batasan Paket
    $('#editPlanForm').on('submit', function(e) {
      e.preventDefault();
      if (!currentEditingCard) return;

      const newName = $('#edit_name').val().trim();
      const rawPrice = $('#edit_price').val();
      const newPriceFormatted = parseInt(rawPrice).toLocaleString('id-ID');
      const maxMemVal = $('#edit_max_members').val().trim();
      const newMaxMemText = maxMemVal ? `Maksimal ${maxMemVal} Member` : 'Unlimited Member';

      // Update judul & harga kartu
      currentEditingCard.find('h5').first().text(newName);
      currentEditingCard.find('h3').first().html(`Rp ${newPriceFormatted} <small style="font-size: 14px;" class="text-muted">/ bulan</small>`);

      // Rebuild daftar fitur
      const ul = currentEditingCard.find('ul').first();
      ul.empty();
      ul.append(`<li>✓ ${newMaxMemText}</li>`);

      $('.edit-feat-cb').each(function() {
        const labelText = $(this).data('label');
        if ($(this).is(':checked')) {
          ul.append(`<li>✓ ${labelText}</li>`);
        } else {
          ul.append(`<li class="text-muted" style="opacity: 0.6;">✗ ${labelText}</li>`);
        }
      });

      $('#editPlanModal').modal('hide');
      showToast('Paket Diperbarui', `Batasan & fitur untuk "${newName}" berhasil disimpan!`, 'success');
    });
  });
</script>
@endsection
