@extends('layouts.admin')

@section('title', 'POS Kasir & Struk &mdash; PetGym')
@section('page_title', 'Point of Sales (POS) Kasir')
@section('page_subtitle', 'Modul kasir pendaftaran membership dan penjualan barang ritel inventaris')

@section('content')
<div class="row">
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
              <div class="card border p-3 text-center cursor-pointer item-card" onclick="addToCart('Paket Daily Pass (Harian)', 50000, null, 'membership', 0)" style="border-radius: 10px; cursor: pointer;">
                <h6 class="font-weight-bold text-info">Pass Harian (Daily Pass)</h6>
                <h4 class="font-weight-bold text-dark">Rp 50.000</h4>
                <small class="text-muted">Akses 1 Hari Saja</small>
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
            <small class="text-muted font-weight-bold">Klik card untuk masuk keranjang, atau gunakan tombol opsi di samping.</small>
            <button type="button" class="btn btn-sm btn-success font-weight-bold" data-toggle="modal" data-target="#addProductModal" style="border-radius: 6px;">
              + Tambah Produk
            </button>
          </div>
          <div class="row">
            @forelse($products->where('category', '!=', 'membership') as $prod)
              <div class="col-md-6 mb-3">
                <div class="card border p-3 item-card position-relative" style="border-radius: 10px;">
                  <div onclick="addToCart('{{ $prod->name }}', {{ $prod->price }}, {{ $prod->id }}, 'inventory', 0)" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <span class="font-weight-bold text-dark">{{ $prod->name }}</span>
                      <span class="badge badge-secondary">{{ ucfirst($prod->category) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                      <span class="font-weight-bold text-success">Rp {{ number_format($prod->price, 0, ',', '.') }}</span>
                      <small class="text-muted">Stok: {{ $prod->stock }}</small>
                    </div>
                  </div>
                  <div class="mt-2 pt-2 border-top d-flex justify-content-end align-items-center">
                    <button type="button" class="btn btn-xs btn-outline-primary mr-1 btn-edit-prod py-0 px-2"
                      data-id="{{ $prod->id }}"
                      data-name="{{ $prod->name }}"
                      data-category="{{ $prod->category }}"
                      data-price="{{ $prod->price }}"
                      data-stock="{{ $prod->stock }}"
                      style="font-size:11px;">Edit</button>
                    <form action="{{ route('admin.products.destroy', $prod->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size:11px;">Hapus</button>
                    </form>
                  </div>
                </div>
              </div>
            @empty
              <div class="col-12 py-3 text-center text-muted">Belum ada barang inventaris ritel terdaftar.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: Order Summary & Checkout -->
  <div class="col-md-5">
    <form action="{{ route('admin.pos.checkout') }}" method="POST" class="card-custom">
      @csrf
      <h6 class="font-weight-bold text-dark mb-3">Keranjang Transaksi</h6>

      <div class="form-group mb-3">
        <label class="font-weight-bold text-dark" style="font-size: 13px;">Pilih Member (Opsional jika retail)</label>
        <select name="member_id" class="form-control" style="border-radius: 8px;">
          <option value="">-- Non-Member / Pembeli Umum --</option>
          @foreach($members as $m)
            <option value="{{ $m->id }}">{{ $m->name }} (PIN: {{ $m->access_code }})</option>
          @endforeach
        </select>
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
</div>

<!-- Modal Printable Struk Invoice -->
<div class="modal fade" id="invoicePrintModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="border-radius: 12px;" id="printableInvoiceArea">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title font-weight-bold">Struk Bukti Pembayaran</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-4" id="invoiceModalContent">
        <!-- Filled dynamically -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary font-weight-bold" onclick="window.print()">Cetak Struk (Print)</button>
      </div>
    </div>
  </div>
</div>
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

  @if(session('print_transaction_id'))
    $(document).ready(function() {
      var txId = {{ session('print_transaction_id') }};
      $.get('/admin/pos/invoice/' + txId, function(data) {
        var html = '<div class="text-center mb-3">';
        html += '<h4 class="font-weight-bold mb-0">' + (data.tenant ? data.tenant.name : 'PETGYM') + '</h4>';
        html += '<small class="text-muted">No. Invoice: ' + data.invoice_number + '</small><br>';
        html += '<small class="text-muted">Tanggal: ' + new Date(data.created_at).toLocaleString('id-ID') + '</small>';
        html += '</div><hr>';

        html += '<div class="mb-2" style="font-size:13px;">';
        html += '<strong>Kasir Staf:</strong> ' + (data.user ? data.user.name : 'System') + '<br>';
        html += '<strong>Member:</strong> ' + (data.member ? data.member.name : 'Pelanggan Umum') + '<br>';
        html += '<strong>Metode Bayar:</strong> ' + data.payment_method.toUpperCase() + '';
        html += '</div>';

        html += '<table class="table table-sm border-top border-bottom mb-3" style="font-size:12.5px;">';
        html += '<thead><tr><th>Item</th><th class="text-center">Qty</th><th class="text-right">Total</th></tr></thead><tbody>';

        $.each(data.items, function(i, item) {
          html += '<tr><td>' + item.item_name + '</td><td class="text-center">' + item.qty + '</td><td class="text-right">Rp ' + parseInt(item.subtotal).toLocaleString('id-ID') + '</td></tr>';
        });

        html += '</tbody></table>';

        html += '<div class="d-flex justify-content-between font-weight-bold" style="font-size:15px;">';
        html += '<span>TOTAL:</span><span>Rp ' + parseInt(data.total_amount).toLocaleString('id-ID') + '</span>';
        html += '</div>';

        html += '<div class="text-center text-muted mt-4" style="font-size:11px;">';
        html += 'Terima Kasih atas kunjungan Anda di ' + (data.tenant ? data.tenant.name : 'PetGym') + '!';
        html += '</div>';

        $('#invoiceModalContent').html(html);
        $('#invoicePrintModal').modal('show');
      });
    });
  @endif

  $(document).ready(function() {
    $('.btn-edit-prod').on('click', function(e) {
      e.stopPropagation();
      var id = $(this).data('id');
      $('#editProdName').val($(this).data('name'));
      $('#editProdCategory').val($(this).data('category'));
      $('#editProdPrice').val($(this).data('price'));
      $('#editProdStock').val($(this).data('stock'));
      $('#editProdForm').attr('action', '/admin/products/' + id);
      $('#editProductModal').modal('show');
    });
  });
</script>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('admin.products.store') }}" method="POST" class="modal-content" style="border-radius: 12px;">
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
        <h5 class="modal-title font-weight-bold">Edit Produk Inventaris</h5>
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
