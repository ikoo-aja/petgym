@extends('layouts.admin')

@section('title', 'POS Kasir & Struk &mdash; PetGym')
@section('page_title', 'Kasir')
@section('page_subtitle', 'Transaksi penjualan membership, produk, dan layanan gym')

@section('content')
<div class="row">
  @if(Auth::user() && Auth::user()->isReceptionist() && empty($openShift))
  <div class="col-12 mb-3">
    <div class="alert alert-danger border-0 p-3 shadow-sm rounded-lg d-flex justify-content-between align-items-center" style="border-radius: 10px; background-color: #fee2e2; color: #991b1b;">
      <div>
        <h6 class="font-weight-bold mb-1"><i class="icon-warning mr-1"></i> Status Kasir: Shift Belum Dibuka</h6>
        <p class="mb-0 small">Seluruh proses transaksi POS / Kasir diblokir karena Anda belum melakukan <strong>Open Shift Kasir</strong>. Silakan buka shift kasir terlebih dahulu.</p>
      </div>
      <a href="{{ route('receptionist.shifts') }}" class="btn btn-sm btn-danger font-weight-bold px-3 py-2 ml-3" style="border-radius: 8px;">
        <i class="icon-clock-o mr-1"></i> Buka Shift Kasir
      </a>
    </div>
  </div>
  @endif

  @if(!Auth::user() || !Auth::user()->isOwner())
  <!-- Left Side: POS Catalog Selection -->
  <div class="col-md-7">
    <div class="card-custom mb-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0"><i class="icon-shopping-bag mr-1 text-primary"></i> Pilih Produk / Paket Gym</h6>
        <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 11px;">Klik item untuk tambah ke keranjang</span>
      </div>

      <!-- Tabs Category -->
      <ul class="nav nav-pills mb-3" id="posTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active font-weight-bold" id="membership-tab" data-toggle="pill" href="#membership-sec" role="tab" style="border-radius: 8px;">
            <i class="icon-id-card-o mr-1"></i> Paket Membership
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link font-weight-bold" id="inventory-tab" data-toggle="pill" href="#inventory-sec" role="tab" style="border-radius: 8px;">
            <i class="icon-cube mr-1"></i> Inventaris Ritel
          </a>
        </li>
      </ul>

      <div class="tab-content" id="posTabContent">
        <!-- Tab Membership -->
        <div class="tab-pane fade show active" id="membership-sec" role="tabpanel">
          <div class="row">
            <div class="col-md-6 mb-3">
              <div class="card border p-3 text-center cursor-pointer item-card h-100 membership-plan-card" id="plan-card-1" onclick="selectMembershipPlan('Paket Membership 1 Bulan', 500000, 1)" style="border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                <div class="badge badge-primary mb-2 align-self-center px-3 py-1 font-weight-bold">Akses 1 Bulan</div>
                <h5 class="font-weight-bold text-dark mb-1">Paket 1 Bulan</h5>
                <h4 class="font-weight-bold text-primary mb-2">Rp 500.000</h4>
                <p class="text-muted small mb-0">Akses Penuh Gym & Fasilitas Studio Dasar</p>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="card border p-3 text-center cursor-pointer item-card h-100 membership-plan-card" id="plan-card-3" onclick="selectMembershipPlan('Paket Membership 3 Bulan', 1350000, 3)" style="border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                <div class="badge badge-success mb-2 align-self-center px-3 py-1 font-weight-bold">Hemat Rp 150.000</div>
                <h5 class="font-weight-bold text-dark mb-1">Paket 3 Bulan</h5>
                <h4 class="font-weight-bold text-success mb-2">Rp 1.350.000</h4>
                <p class="text-muted small mb-0">Akses Gym 90 Hari + Bonus 1x Sesi PT</p>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="card border p-3 text-center cursor-pointer item-card h-100 membership-plan-card" id="plan-card-12" onclick="selectMembershipPlan('Paket Membership 1 Tahun', 4500000, 12)" style="border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                <div class="badge badge-warning mb-2 align-self-center px-3 py-1 font-weight-bold">VVIP Best Value</div>
                <h5 class="font-weight-bold text-dark mb-1">Paket 1 Tahun</h5>
                <h4 class="font-weight-bold text-dark mb-2">Rp 4.500.000</h4>
                <p class="text-muted small mb-0">Akses VIP 12 Bulan + Free Merchandise</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab Inventory Ritel -->
        <div class="tab-pane fade" id="inventory-sec" role="tabpanel">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <span class="text-muted font-weight-bold" style="font-size: 13px;">Daftar Produk Ritel, Minuman &amp; Suplemen</span>
              <small class="text-muted d-block" style="font-size: 11px;">Stok barang dipasok &amp; dikelola langsung oleh Supervisor Gym.</small>
            </div>
          </div>

          <div class="row">
            @forelse($products as $p)
              <div class="col-md-6 mb-3">
                <div class="card border p-3 h-100 item-card position-relative" style="border-radius: 10px;">
                  <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="font-weight-bold text-dark mb-0" style="font-size: 13.5px;">{{ $p->name }}</h6>
                    <span class="badge badge-light border text-dark">{{ ucfirst($p->category) }}</span>
                  </div>
                  @if($p->vendor_name)
                    <div class="text-muted small mb-1" style="font-size: 11px;"><i class="icon-truck mr-1"></i> Vendor: {{ $p->vendor_name }}</div>
                  @endif
                  <div class="font-weight-bold text-primary mb-2" style="font-size: 15px;">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                  
                  <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                    <div>
                      @if($p->stock > 0)
                        <span class="badge {{ $p->stock <= 10 ? 'badge-warning text-dark' : 'badge-success' }} px-2 py-1" style="font-size: 12px;">
                          <i class="icon-check-circle mr-1"></i> Stok: <strong>{{ $p->stock }}</strong> pcs
                        </span>
                      @else
                        <span class="badge badge-danger px-2 py-1" style="font-size: 12px;">
                          <i class="icon-times-circle mr-1"></i> Stok Habis
                        </span>
                      @endif
                    </div>
                    <div>
                      @if($p->stock > 0)
                        <button type="button" class="btn btn-sm btn-primary font-weight-bold px-3" onclick="addRetailToCart('{{ addslashes($p->name) }}', {{ $p->price }}, {{ $p->id }}, {{ $p->stock }})" style="border-radius: 6px;">
                          <i class="icon-plus mr-1"></i> + Beli
                        </button>
                      @else
                        <button type="button" class="btn btn-sm btn-secondary font-weight-bold px-3" disabled style="border-radius: 6px;">
                          Habis
                        </button>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            @empty
              <div class="col-12 text-center py-4 text-muted">
                Belum ada produk ritel. Hubungi Supervisor untuk menginput pasokan barang dari vendor.
              </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: Order Summary & Checkout -->
  <div class="col-md-5">
    <form action="{{ route('receptionist.pos.checkout') }}" method="POST" id="posCheckoutForm" class="card-custom">
      @csrf
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0"><i class="icon-shopping-cart mr-1 text-primary"></i> Keranjang Transaksi</h6>
        <button type="button" class="btn btn-sm btn-link text-danger font-weight-bold p-0" onclick="clearCart()" id="btnClearAllCart" style="display: none; font-size: 12px;">
          Kosongkan Keranjang
        </button>
      </div>

      <!-- Member Selection Box -->
      <div class="form-group mb-3 position-relative" id="posMemberSearchWrapper">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <label class="font-weight-bold text-dark mb-0" style="font-size: 13px;">
            Pilih Member <span class="text-danger" id="memberRequiredStar" style="display: none;">*</span>
          </label>
          <a href="{{ route('manager.members.index') }}" class="text-primary font-weight-bold" style="font-size: 12px;" target="_blank">
            <i class="icon-user-plus mr-1"></i> + Member Baru
          </a>
        </div>
        <input type="hidden" name="member_id" id="posSelectedMemberId" value="">
        <div class="input-group">
          <input type="text" id="posMemberSearchInput" class="form-control" placeholder="Cari nama member..." autocomplete="off" style="border-radius: 8px;">
          <div class="input-group-append" id="posClearMemberBtnGroup" style="display: none;">
            <button class="btn btn-outline-secondary font-weight-bold" type="button" id="posBtnClearMember" title="Hapus Pilihan">&times;</button>
          </div>
        </div>
        <div id="posMemberSearchResults" class="list-group shadow position-absolute w-100 mt-1" style="display: none; max-height: 220px; overflow-y: auto; z-index: 1050; border-radius: 8px; left: 0;">
        </div>
      </div>

      <!-- BLOK 1: Paket Membership Gym (Single Plan) -->
      <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <span class="font-weight-bold text-dark" style="font-size: 12.5px;">
            <i class="icon-id-card mr-1 text-primary"></i> 1. Paket Membership (Maksimal 1)
          </span>
          <span id="membershipBadgeCount" class="badge badge-light border text-muted" style="font-size: 11px;">0 Paket</span>
        </div>
        <div id="membershipCartBlock" class="p-2 border rounded" style="background-color: #fafafa; border-radius: 8px;">
          <div class="text-center text-muted py-2" id="emptyMembershipText" style="font-size: 12px;">
            Belum ada paket membership dipilih.
          </div>
          <div id="selectedMembershipContent" style="display: none;">
            <!-- Rendered by JS -->
          </div>
        </div>
      </div>

      <!-- BLOK 2: Daftar Belanja Produk Ritel / Inventaris -->
      <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <span class="font-weight-bold text-dark" style="font-size: 12.5px;">
            <i class="icon-cube mr-1 text-success"></i> 2. Produk Ritel & Minuman
          </span>
          <span id="retailBadgeCount" class="badge badge-light border text-muted" style="font-size: 11px;">0 Item</span>
        </div>
        <div class="table-responsive border rounded" style="max-height: 200px; overflow-y: auto; background: #ffffff; border-radius: 8px;">
          <table class="table table-sm table-borderless mb-0">
            <thead class="text-muted border-bottom bg-light" style="font-size: 11px;">
              <tr>
                <th>Produk</th>
                <th class="text-center" style="width: 100px;">Jumlah</th>
                <th class="text-right">Subtotal</th>
                <th style="width: 30px;"></th>
              </tr>
            </thead>
            <tbody id="retailCartTableBody">
              <tr id="emptyRetailRow">
                <td colspan="4" class="text-center text-muted py-2" style="font-size: 12px;">
                  Belum ada produk ritel di keranjang.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Ringkasan Rincian Subtotal -->
      <div class="p-2 mb-3 bg-light rounded border" style="font-size: 12px; border-radius: 8px;">
        <div class="d-flex justify-content-between text-muted mb-1">
          <span>Subtotal Paket Membership:</span>
          <span class="font-weight-bold text-dark" id="subtotalMembershipText">Rp 0</span>
        </div>
        <div class="d-flex justify-content-between text-muted mb-1">
          <span>Subtotal Produk Ritel:</span>
          <span class="font-weight-bold text-dark" id="subtotalRetailText">Rp 0</span>
        </div>
        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
          <strong class="text-dark font-weight-bold" style="font-size: 13.5px;">TOTAL TAGIHAN:</strong>
          <h4 class="font-weight-bold text-success mb-0" id="cartTotalText">Rp 0</h4>
        </div>
      </div>

      <input type="hidden" name="type" id="transactionTypeInput" value="inventory">
      <input type="hidden" name="duration_months" id="durationMonthsInput" value="0">
      
      <!-- Container for dynamic hidden inputs of items -->
      <div id="hiddenItemsContainer"></div>

      <!-- Metode Pembayaran -->
      <div class="form-group mb-3">
        <label class="font-weight-bold text-dark" style="font-size: 13px;">Metode Pembayaran *</label>
        <select name="payment_method" id="posPaymentMethodSelect" class="form-control" required style="border-radius: 8px;">
          <option value="cash">Tunai (Cash)</option>
          <option value="qris">QRIS Standar</option>
          <option value="transfer">Bank Transfer</option>
        </select>
      </div>

      <!-- Kalkulator Kasir Tunai (Cash Given & Change) -->
      <div id="cashCalculatorSection" class="p-3 mb-3 border rounded" style="background-color: #f8fafc; border-radius: 8px;">
        <label class="font-weight-bold text-dark mb-1" style="font-size: 12.5px;">
          <i class="icon-money mr-1 text-success"></i> Uang Diterima dari Pelanggan (Rp) *
        </label>
        <div class="input-group mb-2">
          <div class="input-group-prepend">
            <span class="input-group-text font-weight-bold bg-white" style="font-size: 13px;">Rp</span>
          </div>
          <input type="text" id="posCashGivenInput" class="form-control font-weight-bold rupiah-input" placeholder="0" style="font-size: 15px; border-radius: 0 8px 8px 0;" autocomplete="off">
        </div>

        <!-- Tombol Nominal Cepat -->
        <div class="d-flex flex-wrap mb-2" style="gap: 5px;">
          <button type="button" class="btn btn-xs btn-outline-secondary font-weight-bold py-1 px-2" onclick="setCashGiven('exact')">Uang Pas</button>
          <button type="button" class="btn btn-xs btn-outline-secondary font-weight-bold py-1 px-2" onclick="setCashGiven(50000)">50.000</button>
          <button type="button" class="btn btn-xs btn-outline-secondary font-weight-bold py-1 px-2" onclick="setCashGiven(100000)">100.000</button>
          <button type="button" class="btn btn-xs btn-outline-secondary font-weight-bold py-1 px-2" onclick="setCashGiven(200000)">200.000</button>
          <button type="button" class="btn btn-xs btn-outline-secondary font-weight-bold py-1 px-2" onclick="setCashGiven(500000)">500.000</button>
        </div>

        <!-- Tampilan Kembalian -->
        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
          <span class="font-weight-bold text-muted" style="font-size: 12.5px;">KEMBALIAN:</span>
          <span class="font-weight-bold text-dark" id="posChangeText" style="font-size: 15px;">Rp 0</span>
        </div>
        <div id="cashWarningAlert" class="text-danger small mt-1" style="display: none; font-size: 11.5px;">
          <i class="icon-warning mr-1"></i> Uang diterima kurang dari total tagihan!
        </div>
      </div>

      <!-- Detail Pembayaran: Bank Transfer -->
      <div id="transferInfoSection" class="p-3 mb-3 border rounded shadow-sm" style="display: none; background: #f0f9ff; border-color: #bae6fd !important; border-radius: 8px;">
        <div class="d-flex align-items-center mb-2">
          <div class="p-2 bg-primary text-white rounded mr-2" style="font-size: 13px; line-height: 1;">
            <i class="icon-bank"></i>
          </div>
          <div>
            <strong class="text-dark d-block" style="font-size: 12.5px;">Rekening Bank Tujuan Transfer</strong>
            <small class="text-muted">Diatur oleh Pemilik Gym (Owner)</small>
          </div>
        </div>

        @if($tenant && $tenant->bank_account_number)
          <div class="bg-white p-2.5 rounded border mb-2" style="border-radius: 6px;">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="text-muted small">Nama Bank:</span>
              <strong class="text-dark small">{{ $tenant->bank_name ?? 'Bank Transfer' }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="text-muted small">No. Rekening:</span>
              <div class="d-flex align-items-center">
                <code class="font-weight-bold text-primary mr-2" style="font-size: 14px;" id="posBankAccNumber">{{ $tenant->bank_account_number }}</code>
                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1 font-weight-bold btn-copy-rek" onclick="copyToClipboard('{{ $tenant->bank_account_number }}', this)" title="Salin No. Rekening">
                  <i class="icon-copy mr-1"></i>Salin
                </button>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted small">Atas Nama (A.N.):</span>
              <strong class="text-dark small">{{ $tenant->bank_account_holder ?? '-' }}</strong>
            </div>
          </div>
          <div class="alert alert-info py-1 px-2 small mb-0" style="font-size: 11px;">
            <i class="icon-info-circle mr-1"></i> Pastikan transfer pelanggan berhasil masuk ke rekening sebelum menyelesaikan transaksi.
          </div>
        @else
          <div class="alert alert-warning py-2 px-3 small mb-0" style="border-radius: 6px;">
            <i class="icon-warning mr-1"></i> Rekening bank belum diatur oleh <strong>Owner</strong> di menu Pengaturan Gym.
          </div>
        @endif
      </div>

      <!-- Detail Pembayaran: QRIS -->
      <div id="qrisInfoSection" class="p-3 mb-3 border rounded shadow-sm text-center" style="display: none; background: #fdf4ff; border-color: #f5d0fe !important; border-radius: 8px;">
        <div class="d-flex align-items-center justify-content-center mb-2">
          <div class="p-2 bg-dark text-white rounded mr-2" style="font-size: 13px; line-height: 1;">
            <i class="icon-qrcode"></i>
          </div>
          <div class="text-left">
            <strong class="text-dark d-block" style="font-size: 12.5px;">QRIS Standar Pembayaran</strong>
            <small class="text-muted">{{ $tenant->qris_merchant_name ?: ($tenant->name ?? 'Merchant Gym') }}</small>
          </div>
        </div>

        @if($tenant && $tenant->qris_image_url)
          <div class="p-2 bg-white rounded border d-inline-block shadow-sm my-1" style="border-radius: 8px;">
            <img src="{{ asset('storage/' . $tenant->qris_image_url) }}" alt="QRIS Merchant" class="img-fluid" style="max-height: 160px; object-fit: contain; cursor: pointer;" onclick="openQrisModal()" title="Klik untuk perbesar QRIS">
          </div>
          <div>
            <button type="button" class="btn btn-sm btn-outline-dark font-weight-bold mt-1 py-1 px-3" onclick="openQrisModal()" style="border-radius: 6px; font-size: 11.5px;">
              <i class="icon-search mr-1"></i> Tampilkan QRIS Fullscreen
            </button>
          </div>
          <small class="text-muted d-block mt-2" style="font-size: 11px;">Mendukung semua aplikasi e-wallet &amp; mobile banking.</small>
        @else
          <div class="alert alert-warning py-2 px-3 small mb-0 mt-2 text-left" style="border-radius: 6px;">
            <i class="icon-warning mr-1"></i> Gambar QR Code QRIS belum diunggah oleh <strong>Owner</strong> di menu Pengaturan Gym.
          </div>
        @endif
      </div>

      <!-- Info Notifikasi QRIS / Transfer -->
      <div id="digitalPaymentNotice" class="alert alert-warning border-0 p-2 mb-3" style="display: none; background-color: #fef3c7; color: #92400e; border-radius: 8px; font-size: 12px;">
        <i class="icon-info-circle mr-1"></i> <strong>Verifikasi Pembayaran:</strong> Pastikan saldo pembayaran non-tunai telah diterima kasir sebelum memproses transaksi.
      </div>

      <button type="submit" id="btnCheckout" class="btn btn-block btn-success font-weight-bold py-2 shadow-sm" disabled style="border-radius: 8px;">
        <i class="icon-check-circle mr-1"></i> Proses Bayar & Cetak Struk
      </button>
    </form>
  </div>
  @else
  <!-- Owner View: Full Width Transaction History & Revenue Report -->
  <div class="col-md-12">
    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="font-weight-bold text-dark mb-0"><i class="icon-file-text-o mr-1 text-primary"></i> Riwayat Penjualan Kasir POS & Omset Transaksi</h6>
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

<!-- Modal Fullscreen Scan QRIS Pembayaran -->
<div class="modal fade" id="qrisFullscreenModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow border-0" style="border-radius: 14px;">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title font-weight-bold mb-0">
          <i class="icon-qrcode mr-1 text-warning"></i> Scan QRIS Pembayaran Pelanggan
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-center p-4">
        <span class="badge badge-light border text-dark font-weight-bold px-3 py-1 mb-2" style="font-size: 13px;">
          {{ $tenant->qris_merchant_name ?: ($tenant->name ?? 'Merchant Gym') }}
        </span>
        <div class="mb-3">
          <small class="text-muted d-block font-weight-bold text-uppercase">Total Tagihan Transaksi:</small>
          <h3 class="font-weight-bold text-success mb-0" id="qrisModalTotal">Rp 0</h3>
        </div>

        @if($tenant && $tenant->qris_image_url)
          <div class="p-3 bg-white rounded border d-inline-block shadow-sm mb-3" style="border-radius: 10px;">
            <img src="{{ asset('storage/' . $tenant->qris_image_url) }}" alt="QRIS Merchant" class="img-fluid rounded" style="max-height: 280px; object-fit: contain;">
          </div>
          <p class="text-muted small mb-0">
            Arahkan kamera smartphone atau aplikasi e-wallet (GoPay, OVO, Dana, BCA, Livin, BRImo) pelanggan ke QR Code di atas.
          </p>
        @else
          <div class="alert alert-warning py-3 px-3 mb-0 text-center" style="border-radius: 8px;">
            <i class="icon-warning mb-2 d-block" style="font-size: 28px;"></i>
            <strong>QRIS Belum Diunggah</strong>
            <p class="small mb-0 mt-1">Pemilik gym belum mengunggah gambar QRIS di menu Pengaturan Owner.</p>
          </div>
        @endif
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal" style="border-radius: 8px;">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Printable Struk Thermal Printer -->
@include('partials.receipt-modal')
@endsection

@section('scripts')
<script>
  // State Keranjang
  var selectedMembership = null; // Single plan: { name, price, durationMonths } or null
  var retailCart = [];           // Array of { productId, name, price, qty, maxStock }

  // 1. Pilih Paket Membership (Hanya 1 Paket)
  function selectMembershipPlan(name, price, durationMonths) {
    selectedMembership = {
      name: name,
      price: price,
      durationMonths: durationMonths
    };

    // Highlight card yang aktif
    $('.membership-plan-card').removeClass('border-primary shadow-sm bg-light');
    $('#plan-card-' + durationMonths).addClass('border-primary shadow-sm bg-light');

    renderAllCart();
  }

  // 2. Hapus Paket Membership
  function removeMembershipPlan() {
    selectedMembership = null;
    $('.membership-plan-card').removeClass('border-primary shadow-sm bg-light');
    renderAllCart();
  }

  // 3. Tambah Produk Ritel ke Keranjang
  function addRetailToCart(name, price, productId, maxStock) {
    var existing = retailCart.find(item => item.productId === productId);
    if (existing) {
      if (existing.qty < maxStock) {
        existing.qty += 1;
      } else {
        alert('Stok produk ' + name + ' tidak mencukupi (Maks: ' + maxStock + ' pcs).');
        return;
      }
    } else {
      retailCart.push({
        productId: productId,
        name: name,
        price: price,
        qty: 1,
        maxStock: maxStock
      });
    }
    renderAllCart();
  }

  // 4. Ubah Qty Produk Ritel (+/-)
  function updateRetailQty(index, delta) {
    if (retailCart[index]) {
      var item = retailCart[index];
      var newQty = item.qty + delta;

      if (newQty <= 0) {
        retailCart.splice(index, 1);
      } else if (newQty > item.maxStock) {
        alert('Jumlah melebihi batas stok tersedia (' + item.maxStock + ' pcs).');
      } else {
        item.qty = newQty;
      }
      renderAllCart();
    }
  }

  // 5. Hapus Item Ritel
  function removeRetailItem(index) {
    retailCart.splice(index, 1);
    renderAllCart();
  }

  // 6. Kosongkan Seluruh Keranjang
  function clearCart() {
    selectedMembership = null;
    retailCart = [];
    $('.membership-plan-card').removeClass('border-primary shadow-sm bg-light');
    renderAllCart();
  }

  // 7. Render Seluruh Keranjang & Kalkulasi
  function renderAllCart() {
    var total = 0;
    var subtotalMem = selectedMembership ? selectedMembership.price : 0;
    var subtotalRet = 0;

    // A. Render Blok Membership
    var memContent = $('#selectedMembershipContent');
    var emptyMemText = $('#emptyMembershipText');

    if (selectedMembership) {
      emptyMemText.hide();
      $('#membershipBadgeCount').text('1 Paket').removeClass('badge-light text-muted').addClass('badge-primary');
      var memHtml = `
        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-white border border-primary">
          <div>
            <div class="font-weight-bold text-dark" style="font-size: 13px;">${selectedMembership.name}</div>
            <div class="text-muted small">Durasi: <strong>${selectedMembership.durationMonths} Bulan</strong></div>
            <div class="font-weight-bold text-primary" style="font-size: 13px;">Rp ${selectedMembership.price.toLocaleString('id-ID')}</div>
          </div>
          <button type="button" class="btn btn-sm btn-outline-danger font-weight-bold" onclick="removeMembershipPlan()" title="Hapus Paket">
            &times; Hapus
          </button>
        </div>
      `;
      memContent.html(memHtml).show();
      $('#memberRequiredStar').show();
    } else {
      $('#membershipBadgeCount').text('0 Paket').removeClass('badge-primary').addClass('badge-light text-muted');
      memContent.hide().empty();
      emptyMemText.show();
      $('#memberRequiredStar').hide();
    }

    // B. Render Blok Ritel
    var retailBody = $('#retailCartTableBody');
    retailBody.empty();

    if (retailCart.length === 0) {
      retailBody.html('<tr id="emptyRetailRow"><td colspan="4" class="text-center text-muted py-2" style="font-size: 12px;">Belum ada produk ritel di keranjang.</td></tr>');
      $('#retailBadgeCount').text('0 Item').removeClass('badge-success').addClass('badge-light text-muted');
    } else {
      var totalRetailQty = 0;
      $.each(retailCart, function(i, item) {
        var subtotal = item.price * item.qty;
        subtotalRet += subtotal;
        totalRetailQty += item.qty;

        var row = `
          <tr>
            <td style="font-size: 12px; vertical-align: middle;">
              <strong class="text-dark">${item.name}</strong><br>
              <span class="text-muted small">@ Rp ${item.price.toLocaleString('id-ID')}</span>
            </td>
            <td class="text-center" style="vertical-align: middle;">
              <div class="input-group input-group-sm justify-content-center" style="width: 85px; margin: 0 auto;">
                <div class="input-group-prepend">
                  <button type="button" class="btn btn-outline-secondary btn-sm px-2 py-0" onclick="updateRetailQty(${i}, -1)">-</button>
                </div>
                <input type="text" class="form-control form-control-sm text-center font-weight-bold p-0" value="${item.qty}" readonly style="height: 24px; font-size: 12px;">
                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-secondary btn-sm px-2 py-0" onclick="updateRetailQty(${i}, 1)">+</button>
                </div>
              </div>
            </td>
            <td class="text-right font-weight-bold text-dark" style="font-size: 12px; vertical-align: middle;">
              Rp ${subtotal.toLocaleString('id-ID')}
            </td>
            <td class="text-right" style="vertical-align: middle;">
              <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeRetailItem(${i})">&times;</button>
            </td>
          </tr>
        `;
        retailBody.append(row);
      });
      $('#retailBadgeCount').text(totalRetailQty + ' Item').removeClass('badge-light text-muted').addClass('badge-success');
    }

    // C. Render Hidden Inputs untuk Submit Form
    var hiddenContainer = $('#hiddenItemsContainer');
    hiddenContainer.empty();
    var itemIndex = 0;

    if (selectedMembership) {
      hiddenContainer.append(`
        <input type="hidden" name="items[${itemIndex}][product_id]" value="">
        <input type="hidden" name="items[${itemIndex}][item_name]" value="${selectedMembership.name}">
        <input type="hidden" name="items[${itemIndex}][qty]" value="1">
        <input type="hidden" name="items[${itemIndex}][price]" value="${selectedMembership.price}">
        <input type="hidden" name="items[${itemIndex}][type]" value="membership">
      `);
      itemIndex++;
    }

    $.each(retailCart, function(i, item) {
      hiddenContainer.append(`
        <input type="hidden" name="items[${itemIndex}][product_id]" value="${item.productId}">
        <input type="hidden" name="items[${itemIndex}][item_name]" value="${item.name}">
        <input type="hidden" name="items[${itemIndex}][qty]" value="${item.qty}">
        <input type="hidden" name="items[${itemIndex}][price]" value="${item.price}">
        <input type="hidden" name="items[${itemIndex}][type]" value="inventory">
      `);
      itemIndex++;
    });

    // D. Hitung Total Tagihan
    total = subtotalMem + subtotalRet;
    $('#subtotalMembershipText').text('Rp ' + subtotalMem.toLocaleString('id-ID'));
    $('#subtotalRetailText').text('Rp ' + subtotalRet.toLocaleString('id-ID'));
    $('#cartTotalText').text('Rp ' + total.toLocaleString('id-ID'));

    // E. Update Transaction Type
    var tType = 'inventory';
    if (selectedMembership && retailCart.length > 0) {
      tType = 'mixed';
    } else if (selectedMembership) {
      tType = 'membership';
    }
    $('#transactionTypeInput').val(tType);
    $('#durationMonthsInput').val(selectedMembership ? selectedMembership.durationMonths : 0);

    // F. Kontrol Tombol Kosongkan Keranjang
    if (selectedMembership || retailCart.length > 0) {
      $('#btnClearAllCart').show();
    } else {
      $('#btnClearAllCart').hide();
    }

    // G. Hitung Kembalian Tunai
    calculateCashChange();
  }

  // 8. Kalkulator Kasir Uang Tunai & Kembalian
  function calculateCashChange() {
    var subtotalMem = selectedMembership ? selectedMembership.price : 0;
    var subtotalRet = retailCart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    var grandTotal = subtotalMem + subtotalRet;

    var method = $('#posPaymentMethodSelect').val();
    var cashGiven = unformatRupiahNumber($('#posCashGivenInput').val());
    var hasItems = (selectedMembership !== null || retailCart.length > 0);

    var canSubmit = false;

    if (method === 'cash') {
      var change = cashGiven - grandTotal;
      if (grandTotal > 0 && cashGiven >= grandTotal) {
        $('#posChangeText').text('Rp ' + Math.max(0, change).toLocaleString('id-ID')).removeClass('text-danger').addClass('text-success font-weight-bold');
        $('#cashWarningAlert').hide();
        canSubmit = true;
      } else if (grandTotal > 0 && cashGiven < grandTotal) {
        $('#posChangeText').text('Rp 0').removeClass('text-success font-weight-bold').addClass('text-muted');
        $('#cashWarningAlert').show();
        canSubmit = false;
      } else {
        $('#posChangeText').text('Rp 0');
        $('#cashWarningAlert').hide();
        canSubmit = false;
      }
    } else {
      // Non-cash (QRIS / Transfer)
      canSubmit = hasItems && (grandTotal > 0);
    }

    // Validasi Tambahan: Jika ada paket membership, wajib memilih member
    var selectedMemberId = $('#posSelectedMemberId').val();
    if (selectedMembership && !selectedMemberId) {
      canSubmit = false;
    }

    $('#btnCheckout').prop('disabled', !canSubmit);
  }

  function setCashGiven(val) {
    var subtotalMem = selectedMembership ? selectedMembership.price : 0;
    var subtotalRet = retailCart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    var grandTotal = subtotalMem + subtotalRet;

    if (val === 'exact') {
      $('#posCashGivenInput').val(formatRupiahNumber(grandTotal));
    } else {
      $('#posCashGivenInput').val(formatRupiahNumber(val));
    }
    calculateCashChange();
  }

  // 9. Helper: Salin Rekening ke Clipboard
  function copyToClipboard(text, btn) {
    if (!text) return;
    navigator.clipboard.writeText(text).then(function() {
      if (btn) {
        var originalHtml = $(btn).html();
        $(btn).html('<i class="icon-check mr-1"></i>Tersalin!').addClass('btn-success text-white').removeClass('btn-outline-primary');
        setTimeout(function() {
          $(btn).html(originalHtml).removeClass('btn-success text-white').addClass('btn-outline-primary');
        }, 2000);
      }
    }).catch(function(err) {
      alert('Gagal menyalin: ' + err);
    });
  }

  // 10. Helper: Buka Modal QRIS Fullscreen
  function openQrisModal() {
    var subtotalMem = selectedMembership ? selectedMembership.price : 0;
    var subtotalRet = retailCart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    var grandTotal = subtotalMem + subtotalRet;
    $('#qrisModalTotal').text('Rp ' + grandTotal.toLocaleString('id-ID'));
    $('#qrisFullscreenModal').modal('show');
  }

  // 11. List Member Autocomplete Search (Case-Insensitive & Multi-Match)
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
        access_code: "{{ $m->access_code ?? '' }}",
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
      var cleanQueryDigits = query.replace(/[^0-9]/g, '');

      var matches = posMemberList.filter(function(item) {
        if (!query) return true;
        var nameMatch = (item.name || '').toLowerCase().indexOf(query) !== -1;
        var phoneClean = (item.phone || '').replace(/[^0-9]/g, '');
        var phoneMatch = (item.phone || '').toLowerCase().indexOf(query) !== -1 || (cleanQueryDigits && phoneClean.indexOf(cleanQueryDigits) !== -1);
        var pinMatch = (item.access_code || '').toLowerCase().indexOf(query) !== -1;
        return nameMatch || phoneMatch || pinMatch;
      });

      // Opsi Non-Member / Pembeli Umum (Hanya jika tidak ada paket membership)
      var generalItem = $('<a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2">')
        .html('<div><strong class="text-dark">Pembeli Umum / Non-Member</strong><br><small class="text-muted">Untuk transaksi ritel tanpa perpanjangan paket</small></div><span class="badge badge-light border">Umum</span>')
        .on('click', function(e) {
          e.preventDefault();
          if (selectedMembership) {
            alert('Perhatian: Pembeli umum tidak dapat membeli paket membership. Silakan pilih member terdaftar.');
            return;
          }
          $selectedId.val('');
          $searchInput.val('Pembeli Umum / Non-Member').prop('readonly', true);
          $clearBtnGroup.show();
          $resultsContainer.hide();
          calculateCashChange();
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
              calculateCashChange();
            });
          $resultsContainer.append(itemEl);
        });
      } else {
        $resultsContainer.append('<div class="list-group-item text-muted py-2 text-center" style="font-size: 13px;">Member tidak ditemukan.</div>');
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
      calculateCashChange();
    });

    $(document).on('click', function(e) {
      if (!$(e.target).closest('#posMemberSearchWrapper').length) {
        $resultsContainer.hide();
      }
    });

    // Handle Perubahan Metode Pembayaran (Cash / Transfer / QRIS)
    $('#posPaymentMethodSelect').on('change', function() {
      var val = $(this).val();
      if (val === 'cash') {
        $('#cashCalculatorSection').slideDown();
        $('#transferInfoSection').slideUp();
        $('#qrisInfoSection').slideUp();
        $('#digitalPaymentNotice').slideUp();
      } else if (val === 'transfer') {
        $('#cashCalculatorSection').slideUp();
        $('#transferInfoSection').slideDown();
        $('#qrisInfoSection').slideUp();
        $('#digitalPaymentNotice').slideDown();
      } else if (val === 'qris') {
        $('#cashCalculatorSection').slideUp();
        $('#transferInfoSection').slideUp();
        $('#qrisInfoSection').slideDown();
        $('#digitalPaymentNotice').slideDown();
      }
      calculateCashChange();
    });

    $('#posCashGivenInput').on('input', function() {
      calculateCashChange();
    });

    // Mencegah submit jika form belum valid
    $('#posCheckoutForm').on('submit', function(e) {
      if (selectedMembership && !$('#posSelectedMemberId').val()) {
        e.preventDefault();
        alert('Untuk pembelian paket membership gym, Anda wajib memilih data Member terlebih dahulu.');
        $('#posMemberSearchInput').focus();
        return false;
      }
    });

    @if(session('print_transaction_id'))
      openInvoiceReceiptModal({{ session('print_transaction_id') }});
    @endif
  });
</script>
@endsection
