<!-- Modal Printable Struk Invoice Thermal Printer -->
<style>
  /* Styling Struk saat tampil di layar modal Web (Preview) */
  #printableReceiptArea {
    background: #ffffff;
    color: #111827;
    font-family: 'Courier New', Courier, monospace, sans-serif;
    padding: 15px;
    border-radius: 8px;
    border: 1px dashed #cbd5e1;
    max-width: 100%;
    margin: 0 auto;
  }
  .receipt-line-dashed {
    border-top: 1px dashed #475569;
    margin: 8px 0;
  }

  /* CSS KHUSUS PRINT: Hanya cetak struk nota fisik, sembunyikan seluruh tampilan layar web */
  @media print {
    body * {
      visibility: hidden !important;
    }
    #printableReceiptArea, #printableReceiptArea * {
      visibility: visible !important;
    }
    #printableReceiptArea {
      position: absolute !important;
      left: 0 !important;
      top: 0 !important;
      width: 80mm !important; /* Standar lebar kertas thermal printer kasir */
      max-width: 100% !important;
      margin: 0 !important;
      padding: 5px !important;
      background: #ffffff !important;
      color: #000000 !important;
      font-family: 'Courier New', Courier, monospace !important;
      font-size: 11px !important;
      box-shadow: none !important;
      border: none !important;
    }
    .no-print, .modal-header, .modal-footer, .modal-backdrop, .btn, .admin-sidebar, .top-navbar, .sidebar, header, nav {
      display: none !important;
    }
    .modal-dialog {
      margin: 0 !important;
      max-width: 100% !important;
    }
    .modal-content {
      border: none !important;
      box-shadow: none !important;
      background: transparent !important;
    }
    .modal {
      position: absolute !important;
      left: 0 !important;
      top: 0 !important;
      overflow: visible !important;
    }
  }
</style>

<div class="modal fade" id="invoicePrintModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width: 440px;">
    <div class="modal-content" style="border-radius: 12px;">
      <div class="modal-header bg-white border-bottom text-dark no-print">
        <h5 class="modal-title font-weight-bold" style="font-size: 15px;"><i class="icon-file-text-o mr-1 text-primary"></i> Struk Bukti Pembayaran</h5>
        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p-3">
        <!-- Thermal Receipt Container -->
        <div id="printableReceiptArea">
          <div class="text-center py-4 text-muted">Memuat data struk...</div>
        </div>
      </div>
      <div class="modal-footer bg-light no-print">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary btn-sm font-weight-bold" onclick="window.print()">
          <i class="icon-print mr-1"></i> Cetak Struk
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  function openInvoiceReceiptModal(txId) {
    $('#printableReceiptArea').html('<div class="text-center py-4 text-muted"><i class="icon-spinner pg-icon-spin mr-1"></i> Memuat data struk...</div>');
    $('#invoicePrintModal').modal('show');

    $.get('/invoice/' + txId + '/data', function(data) {
      var tenantName = data.tenant ? data.tenant.name : 'PETGYM SAAS';
      var tenantSub = data.tenant ? (data.tenant.subdomain ? data.tenant.subdomain + '.petgym.com' : '') : '';
      var tenantPhone = data.tenant ? (data.tenant.phone || '-') : '-';
      var kasirName = data.user ? data.user.name : 'Kasir Staf';
      var memberName = data.member ? data.member.name : 'Pelanggan Umum';
      var createdAt = new Date(data.created_at).toLocaleString('id-ID', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
      });

      var itemsHtml = '';
      var subtotalCalc = 0;
      $.each(data.items, function(i, item) {
        var price = parseInt(item.price).toLocaleString('id-ID');
        var subtotal = parseInt(item.subtotal).toLocaleString('id-ID');
        subtotalCalc += parseInt(item.subtotal);
        itemsHtml += '<tr>';
        itemsHtml += '<td style="padding: 3px 0;">' + item.item_name + '<br><small style="font-size:9px; color:#555;">@ Rp ' + price + '</small></td>';
        itemsHtml += '<td class="text-center" style="padding: 3px 0; vertical-align: top;">' + item.qty + '</td>';
        itemsHtml += '<td class="text-right" style="padding: 3px 0; vertical-align: top;">Rp ' + subtotal + '</td>';
        itemsHtml += '</tr>';
      });

      var totalStr = parseInt(data.total_amount).toLocaleString('id-ID');
      var discountAmount = subtotalCalc - parseInt(data.total_amount);
      var discountHtml = '';
      if (discountAmount > 0) {
        discountHtml = `
          <tr>
            <td style="color:#64748b;">Subtotal</td>
            <td class="text-right" style="color:#64748b;">Rp ${subtotalCalc.toLocaleString('id-ID')}</td>
          </tr>
          <tr>
            <td style="color:#16a34a;">Diskon Promo</td>
            <td class="text-right text-success">- Rp ${discountAmount.toLocaleString('id-ID')}</td>
          </tr>
        `;
      }

      var statusBadge = '<span class="text-success" style="font-size:10px; font-weight:bold;">[ LUNAS ]</span>';
      if (data.void_status === 'approved') {
        statusBadge = '<span class="text-danger" style="font-size:10px; font-weight:bold;">[ VOID / DIBATALKAN ]</span>';
      } else if (data.void_status === 'pending') {
        statusBadge = '<span class="text-warning" style="font-size:10px; font-weight:bold;">[ MENUNGGU VOID ]</span>';
      }

      var paymentMethodText = (data.payment_method || 'CASH').toUpperCase();
      if (data.payment_method === 'transfer' && data.tenant && data.tenant.bank_name) {
        paymentMethodText = 'TRANSFER (' + data.tenant.bank_name + ')';
      } else if (data.payment_method === 'qris') {
        paymentMethodText = 'QRIS' + (data.tenant && data.tenant.qris_merchant_name ? ' (' + data.tenant.qris_merchant_name + ')' : '');
      }

      var html = `
        <div class="text-center mb-2">
          <h4 class="font-weight-bold mb-0 text-dark" style="font-size: 16px; text-transform: uppercase; font-family: 'Courier New', monospace;">${tenantName}</h4>
          ${tenantSub ? `<div style="font-size: 10px; color: #444;">${tenantSub}</div>` : ''}
          <div style="font-size: 10px; color: #444;">Telp: ${tenantPhone}</div>
        </div>

        <div class="receipt-line-dashed">----------------------------------------</div>

        <table style="width: 100%; font-size: 11px; line-height: 1.4;">
          <tr>
            <td>No. Invoice</td>
            <td class="text-right font-weight-bold">${data.invoice_number}</td>
          </tr>
          <tr>
            <td>Tanggal</td>
            <td class="text-right">${createdAt}</td>
          </tr>
          <tr>
            <td>Kasir</td>
            <td class="text-right">${kasirName}</td>
          </tr>
          <tr>
            <td>Pelanggan</td>
            <td class="text-right">${memberName}</td>
          </tr>
          <tr>
            <td>Metode Bayar</td>
            <td class="text-right font-weight-bold">${paymentMethodText}</td>
          </tr>
        </table>

        <div class="receipt-line-dashed">----------------------------------------</div>

        <table style="width: 100%; font-size: 11px; line-height: 1.4;">
          <thead>
            <tr style="border-bottom: 1px dashed #475569;">
              <th class="text-left" style="padding-bottom: 4px;">Item Produk / Layanan</th>
              <th class="text-center" style="padding-bottom: 4px; width: 40px;">Qty</th>
              <th class="text-right" style="padding-bottom: 4px;">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            ${itemsHtml}
          </tbody>
        </table>

        <div class="receipt-line-dashed">----------------------------------------</div>

        <table style="width: 100%; font-size: 12px; line-height: 1.4; margin-top: 4px;">
          ${discountHtml}
          <tr style="font-size: 13px; font-weight: bold;">
            <td>TOTAL BAYAR</td>
            <td class="text-right">Rp ${totalStr}</td>
          </tr>
          <tr>
            <td style="font-size:10px; font-weight:normal; color:#444;">Status Pembayaran</td>
            <td class="text-right">${statusBadge}</td>
          </tr>
        </table>

        <div class="receipt-line-dashed">----------------------------------------</div>

        <div class="text-center mt-2" style="font-size: 10px; color: #444; line-height: 1.3;">
          *** TERIMA KASIH ***<br>
          Simpan struk ini sebagai bukti transaksi sah.<br>
          Layanan Pelanggan: ${tenantName}
        </div>
      `;

      $('#printableReceiptArea').html(html);
    }).fail(function() {
      $('#printableReceiptArea').html('<div class="text-center py-4 text-danger font-weight-bold"><i class="icon-warning mr-1"></i> Gagal memuat data struk transaksi. Pastikan transaksi terdaftar pada tenant Anda.</div>');
    });
  }
</script>
