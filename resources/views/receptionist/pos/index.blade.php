@extends('layouts.admin')

@section('title', 'POS Kasir & Struk &mdash; PetGym')
@section('page_title', 'Point of Sales (POS) Kasir')
@section('page_subtitle', 'Modul kasir pendaftaran membership dan penjualan barang ritel inventaris')

@section('content')
<div class="row">
  @if(Auth::user() && Auth::user()->isReceptionist() && empty($openShift))
  <div class="col-12 mb-3">
    <div class="alert alert-danger border-0 p-3 shadow-sm rounded-lg d-flex justify-content-between align-items-center" style="border-radius: 10px; background-color: #fee2e2; color: #991b1b;">
      <div>
        <h6 class="font-weight-bold mb-1"><i class="icon-alert-triangle mr-1"></i> STATUS KASIR: BELUM OPEN SHIFT KASIR</h6>
        <p class="mb-0 small">Seluruh proses transaksi POS / Kasir diblokir karena Anda belum melakukan <strong>Open Shift Kasir</strong>. Silakan buka shift kasir terlebih dahulu.</p>
      </div>
      <a href="{{ route('receptionist.shifts') }}" class="btn btn-sm btn-danger font-weight-bold px-3 py-2 ml-3" style="border-radius: 8px;">
        <i class="icon-clock-o mr-1"></i> Buka Shift Kasir
      </a>
    </div>
  </div>
  @endif

  @if(!Auth::user() || !Auth::user()->isOwner())
  <!-- Left Side: POS Checkout Form -->
  <div class="col-md-7">
    <div class="card-custom">
      <h6 class="font-weight-bold text-dark mb-3">Pilih Produk / Paket Keanggotaan</h6>

      <!-- Tabs Category -->
      <ul class="nav nav-pills mb-3" id="posTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active font-weight-bold" id="membership-tab" data-toggle="pill" href="#membership-sec" role="tab">Paket Membership</a>
        </li>
        <li class="nav-item">
          <a class="nav-link font-weight-bold" id="inventory-tab" data-toggle="pill" href="#inventory-sec" role="tab">Inventaris Ritel</a>
        </li>
      </ul>

      <div class="tab-content" id="posTabContent">
        <!-- Tab Membership -->
        <div class="tab-pane fade show active" id="membership-sec" role="tabpanel">
          <div class="row">
            <div class="col-md-6 mb-3">
              <div class="card border p-3 text-center cursor-pointer item-card" onclick="addToCart('Perpanjangan / Membership 1 Bulan', 500000, null, 'membership', 1)" style="border-radius: 10px; cursor: pointer;">
                <h6 class="font-weight-bold text-primary">Paket Membership 1 Bulan</h6>
                <h4 class="font-weight-bold text-dark">Rp 500.000</h4>
                <small class="text-muted">Akses Gym & Kelas Basic</small>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="card border p-3 text-center cursor-pointer item-card" onclick="addToCart('Perpanjangan / Membership 3 Bulan', 1350000, null, 'membership', 3)" style="border-radius: 10px; cursor: pointer;">
                <h6 class="font-weight-bold text-success">Paket Membership 3 Bulan</h6>
                <h4 class="font-weight-bold text-dark">Rp 1.350.000</h4>
                <small class="text-muted">Hemat Rp 150.000</small>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="card border p-3 text-center cursor-pointer item-card" onclick="addToCart('Paket Membership 1 Tahun', 4500000, null, 'membership', 12)" style="border-radius: 10px; cursor: pointer;">
                <h6 class="font-weight-bold text-warning">Paket Membership 1 Tahun</h6>
                <h4 class="font-weight-bold text-dark">Rp 4.500.000</h4>
                <small class="text-muted">Akses VVIP 12 Bulan</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab Inventory Ritel -->
        <div class="tab-pane fade" id="inventory-sec" role="tabpanel">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted font-weight-bold" style="font-size: 13px;">Daftar Produk Ritel Aktif</span>
            <button type="button" class="btn btn-sm btn-outline-success font-weight-bold" data-toggle="modal" data-target="#addProductModal" style="border-radius: 6px;">
              + Tambah Produk
            </button>
          </div>

          <div class="row">
            @forelse($products as $p)
              <div class="col-md-6 mb-3">
                <div class="card border p-3 cursor-pointer item-card h-100" onclick="addToCart('{{ $p->name }}', {{ $p->price }}, {{ $p->id }}, 'inventory', 0)" style="border-radius: 10px; cursor: pointer;">
                  <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="font-weight-bold text-dark mb-0" style="font-size: 13.5px;">{{ $p->name }}</h6>
                    <span class="badge badge-light border text-dark">{{ ucfirst($p->category) }}</span>
                  </div>
                  <div class="font-weight-bold text-primary mb-1">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                  <small class="text-muted">Stok: {{ $p->stock }} pcs</small>
                </div>
              </div>
            @empty
              <div class="col-12 text-center py-4 text-muted">
                Belum ada produk ritel. Klik "+ Tambah Produk" untuk mendaftarkan barang.
              </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: Order Summary & Checkout -->
  <div class="col-md-5">
    <form action="{{ route('receptionist.pos.checkout') }}" method="POST" class="card-custom">
      @csrf
      <h6 class="font-weight-bold text-dark mb-3">Keranjang Transaksi</h6>

      <div class="form-group mb-3 position-relative" id="posMemberSearchWrapper">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <label class="font-weight-bold text-dark mb-0" style="font-size: 13px;">Pilih Member (Perpanjangan / Ritel)</label>
          <a href="{{ route('manager.members.index') }}" class="text-primary font-weight-bold" style="font-size: 12px;">+ Member Baru</a>
        </div>
        <input type="hidden" name="member_id" id="posSelectedMemberId" value="">
        <div class="input-group">
          <input type="text" id="posMemberSearchInput" class="form-control" placeholder="Ketik nama atau no. HP member..." autocomplete="off" style="border-radius: 8px;">
          <div class="input-group-append" id="posClearMemberBtnGroup" style="display: none;">
            <button class="btn btn-outline-secondary font-weight-bold" type="button" id="posBtnClearMember" title="Hapus Pilihan">&times;</button>
          </div>
        </div>
        <div id="posMemberSearchResults" class="list-group shadow position-absolute w-100 mt-1" style="display: none; max-height: 220px; overflow-y: auto; z-index: 1050; border-radius: 8px; left: 0;">
        </div>
        <small class="text-muted d-block mt-1" id="posMemberSearchHelp">Ketik nama atau nomor HP member untuk mencari.</small>
      </div>

      <!-- Items List -->
      <div class="table-responsive mb-3 border rounded p-2" style="max-height: 220px; overflow-y: auto;">
        <table class="table table-sm table-borderless mb-0">
          <thead class="text-muted border-bottom" style="font-size: 11px;">
            <tr>
              <th>Produk</th>
              <th class="text-center">Qty</th>
              <th class="text-right">Subtotal</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="cartTableBody">
            <tr id="emptyCartRow">
              <td colspan="4" class="text-center text-muted py-3">Keranjang masih kosong. Klik item di sebelah kiri.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Total & Payment Method -->
      <div class="d-flex justify-content-between align-items-center py-2 mb-3 border-top border-bottom">
        <span class="font-weight-bold text-dark">TOTAL BAYAR</span>
        <h3 class="font-weight-bold text-success mb-0" id="cartTotalText">Rp 0</h3>
      </div>

      <input type="hidden" name="type" id="transactionTypeInput" value="inventory">
      <input type="hidden" name="duration_months" id="durationMonthsInput" value="0">

      <div class="form-group mb-3">
        <label class="font-weight-bold text-dark" style="font-size: 13px;">Metode Pembayaran *</label>
        <select name="payment_method" class="form-control" required style="border-radius: 8px;">
          <option value="cash">Tunai (Cash)</option>
          <option value="qris">QRIS Standar</option>
          <option value="transfer">Bank Transfer</option>
        </select>
      </div>

      <button type="submit" id="btnCheckout" class="btn btn-block btn-success font-weight-bold py-2" disabled style="border-radius: 8px;">
        Proses Bayar & Struk
      </button>
    </form>
  </div>
  @else
  <!-- Owner View: Full Width Transaction History & Revenue Report -->
  <div class="col-md-12">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0">Riwayat Penjualan Kasir POS & Omset Transaksi</h6>
        <span class="badge badge-success font-weight-bold px-3 py-2" style="border-radius: 10px;">{{ count($recentTransactions) }} Transaksi Terakhir</span>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase;">
            <tr>
              <th>Waktu Transaksi</th>
              <th>No. Struk / Invoice</th>
              <th>Member / Pembeli</th>
              <th>Kasir / Staf</th>
              <th>Metode Pembayaran</th>
              <th class="text-right">Total Transaksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentTransactions as $tx)
              <tr>
                <td style="font-size: 12.5px;" class="text-dark font-weight-bold">{{ $tx->created_at ? $tx->created_at->format('d M Y H:i WIB') : '-' }}</td>
                <td style="font-size: 12.5px;"><code class="font-weight-bold text-primary">{{ $tx->invoice_number ?? 'POS-'.$tx->id }}</code></td>
                <td>
                  <div class="font-weight-bold text-dark">{{ $tx->member ? $tx->member->name : 'Pembeli Umum / Non-Member' }}</div>
                </td>
                <td style="font-size: 12.5px;" class="text-dark">{{ $tx->user ? $tx->user->name : 'Kasir System' }}</td>
                <td>
                  <span class="badge badge-light border text-uppercase font-weight-bold px-2 py-1">{{ $tx->payment_method ?? 'Cash' }}</span>
                </td>
                <td class="text-right font-weight-bold text-success" style="font-size: 14px;">
                  Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat transaksi kasir tercatat.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
</div>

<!-- Modal Printable Struk Invoice Thermal Printer -->
@include('partials.receipt-modal')
@endsection

@section('scripts')
<script>
  var cart = [];

  function addToCart(name, price, productId, type, durationMonths) {
    var existing = cart.find(item => item.name === name);
    if (existing) {
      existing.qty += 1;
    } else {
      cart.push({
        name: name,
        price: price,
        productId: productId,
        type: type,
        durationMonths: durationMonths,
        qty: 1
      });
    }
    renderCart();
  }

  function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
  }

  function renderCart() {
    var body = $('#cartTableBody');
    body.empty();

    if (cart.length === 0) {
      body.html('<tr id="emptyCartRow"><td colspan="4" class="text-center text-muted py-3">Keranjang masih kosong. Klik item di sebelah kiri.</td></tr>');
      $('#cartTotalText').text('Rp 0');
      $('#btnCheckout').prop('disabled', true);
      return;
    }

    var total = 0;
    var hasMembership = false;
    var maxMonths = 0;

    $.each(cart, function(i, item) {
      var subtotal = item.price * item.qty;
      total += subtotal;

      if (item.type === 'membership') {
        hasMembership = true;
        if (item.durationMonths > maxMonths) maxMonths = item.durationMonths;
      }

      var row = '<tr>';
      row += '<td style="font-size:12.5px;" class="font-weight-bold">' + item.name + '</td>';
      row += '<td class="text-center" style="font-size:12px;">' + item.qty + '</td>';
      row += '<td class="text-right" style="font-size:12px;">Rp ' + subtotal.toLocaleString('id-ID') + '</td>';
      row += '<td class="text-right"><button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeFromCart(' + i + ')">&times;</button></td>';

      // Hidden inputs for form submit
      row += '<input type="hidden" name="items[' + i + '][product_id]" value="' + (item.productId || '') + '">';
      row += '<input type="hidden" name="items[' + i + '][item_name]" value="' + item.name + '">';
      row += '<input type="hidden" name="items[' + i + '][qty]" value="' + item.qty + '">';
      row += '<input type="hidden" name="items[' + i + '][price]" value="' + item.price + '">';

      row += '</tr>';
      body.append(row);
    });

    $('#cartTotalText').text('Rp ' + total.toLocaleString('id-ID'));
    $('#btnCheckout').prop('disabled', false);

    $('#transactionTypeInput').val(hasMembership ? 'membership' : 'inventory');
    $('#durationMonthsInput').val(maxMonths);
  }

  // List Member untuk Pencarian Autocomplete POS
  var posMemberList = [
    @foreach($members as $m)
      @php
        $expiredAt = $m->expired_at ? \Carbon\Carbon::parse($m->expired_at) : null;
        $memberStatus = !$expiredAt ? 'Belum Aktif' : ($expiredAt->isPast() ? 'Expired' : 'Aktif');
        $statusBadgeClass = !$expiredAt ? 'badge-secondary' : ($expiredAt->isPast() ? 'badge-danger' : 'badge-success');
      @endphp
      {
        id: {{ $m->id }},
        name: "{{ addslashes($m->name) }}",
        phone: "{{ $m->phone ?? '' }}",
        status: "{{ $memberStatus }}",
        statusBadge: "{{ $statusBadgeClass }}"
      },
    @endforeach
  ];

  $(document).ready(function() {
    var $searchInput = $('#posMemberSearchInput');
    var $resultsContainer = $('#posMemberSearchResults');
    var $selectedId = $('#posSelectedMemberId');
    var $clearBtnGroup = $('#posClearMemberBtnGroup');

    function renderPosSearchResults(query) {
      $resultsContainer.empty();
      query = (query || '').trim().toLowerCase();

      var matches = posMemberList.filter(function(item) {
        if (!query) return true;
        var nameMatch = item.name.toLowerCase().indexOf(query) !== -1;
        var phoneMatch = item.phone.toLowerCase().indexOf(query) !== -1;
        return nameMatch || phoneMatch;
      });

      // Opsi Non-Member / Pembeli Umum
      var generalItem = $('<a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2">')
        .html('<div><strong class="text-dark">Pembeli Umum / Non-Member</strong></div><span class="badge badge-light border">Umum</span>')
        .on('click', function(e) {
          e.preventDefault();
          $selectedId.val('');
          $searchInput.val('Pembeli Umum / Non-Member').prop('readonly', true);
          $clearBtnGroup.show();
          $resultsContainer.hide();
        });
      $resultsContainer.append(generalItem);

      if (matches.length > 0) {
        $.each(matches.slice(0, 15), function(i, item) {
          var itemEl = $('<a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2">')
            .html('<div><strong class="text-dark">' + item.name + '</strong></div><span class="badge ' + item.statusBadge + '">' + item.status + '</span>')
            .on('click', function(e) {
              e.preventDefault();
              $selectedId.val(item.id);
              $searchInput.val(item.name + ' — ' + item.status).prop('readonly', true);
              $clearBtnGroup.show();
              $resultsContainer.hide();
            });
          $resultsContainer.append(itemEl);
        });
      } else {
        $resultsContainer.append('<div class="list-group-item text-muted py-2 text-center" style="font-size: 13px;">Member tidak ditemukan</div>');
      }

      $resultsContainer.show();
    }

    $searchInput.on('focus input', function() {
      if (!$searchInput.prop('readonly')) {
        renderPosSearchResults($(this).val());
      }
    });

    $('#posBtnClearMember').on('click', function() {
      $selectedId.val('');
      $searchInput.val('').prop('readonly', false).focus();
      $clearBtnGroup.hide();
      renderPosSearchResults('');
    });

    $(document).on('click', function(e) {
      if (!$(e.target).closest('#posMemberSearchWrapper').length) {
        $resultsContainer.hide();
      }
    });

    @if(session('print_transaction_id'))
      openInvoiceReceiptModal({{ session('print_transaction_id') }});
    @endif

    $('.btn-edit-prod').on('click', function(e) {
      e.stopPropagation();
      var id = $(this).data('id');
      $('#editProdName').val($(this).data('name'));
      $('#editProdCategory').val($(this).data('category'));
      $('#editProdPrice').val($(this).data('price'));
      $('#editProdStock').val($(this).data('stock'));
      $('#editProdForm').attr('action', '/receptionist/products/' + id);
      $('#editProductModal').modal('show');
    });
  });
</script>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('receptionist.products.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Form Tambah Produk Inventaris</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Produk *</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Whey Protein / Air Mineral / Handuk" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Kategori *</label>
          <select name="category" class="form-control" required>
            <option value="supplement">Suplemen / Nutrisi</option>
            <option value="drink">Minuman</option>
            <option value="merchandise">Merchandise / Perlengkapan</option>
          </select>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Harga Jual (Rp) *</label>
          <input type="number" name="price" class="form-control" placeholder="15000" required min="0">
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Stok Awal *</label>
          <input type="number" name="stock" class="form-control" value="50" required min="0">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success font-weight-bold">Simpan Produk</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Produk -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editProdForm" action="" method="POST" class="modal-content" style="border-radius: 12px;">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Ubah Produk Inventaris</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Nama Produk *</label>
          <input type="text" name="name" id="editProdName" class="form-control" required>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Kategori *</label>
          <select name="category" id="editProdCategory" class="form-control" required>
            <option value="supplement">Suplemen / Nutrisi</option>
            <option value="drink">Minuman</option>
            <option value="merchandise">Merchandise / Perlengkapan</option>
          </select>
        </div>
        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Harga Jual (Rp) *</label>
          <input type="number" name="price" id="editProdPrice" class="form-control" required min="0">
        </div>
        <div class="form-group mb-0">
          <label class="font-weight-bold text-dark" style="font-size: 13px;">Jumlah Stok *</label>
          <input type="number" name="stock" id="editProdStock" class="form-control" required min="0">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary font-weight-bold">Update Produk</button>
      </div>
    </form>
  </div>
</div>
@endsection
