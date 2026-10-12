<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Member;
use App\Models\Product;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\StaffLog;
use App\Models\PromoCode;
use App\Models\StockMutation;
use Carbon\Carbon;

class ReceptionistPosController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('warning', 'Fungsi POS Kasir hanya diakses oleh Resepsionis/Kasir. Silakan gunakan menu Langganan Website untuk Pengaturan Sewa Web.');
        }

        $members = Member::where('tenant_id', $tenant->id)
            ->where(function($q) {
                $q->whereNull('user_id')
                  ->orWhereHas('user', function($qu) {
                      $qu->whereNotNull('email_verified_at');
                  });
            })
            ->orderBy('name')
            ->get();
        $products = Product::where('tenant_id', $tenant->id)->orderBy('name')->get();
        $recentTransactions = PosTransaction::where('tenant_id', $tenant->id)
            ->with(['member', 'user', 'items'])
            ->latest()
            ->take(15)
            ->get();

        $openShift = null;
        if ($user->isReceptionist()) {
            $openShift = \App\Models\ReceptionistShift::where('tenant_id', $tenant->id)
                ->where('user_id', $user->id)
                ->where('status', 'open')
                ->first();
        }

        return view('receptionist.pos.index', compact('members', 'products', 'recentTransactions', 'tenant', 'openShift'));
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if ($user->isReceptionist()) {
            $openShift = \App\Models\ReceptionistShift::where('tenant_id', $tenant->id)
                ->where('user_id', $user->id)
                ->where('status', 'open')
                ->first();

            if (!$openShift) {
                return redirect()->back()->with('error', 'Transaksi POS diblokir: Shift kasir Anda belum dibuka (BELUM OPEN KASIR). Silakan buka shift kasir terlebih dahulu di menu Shift & Kasir.');
            }
        }

        $request->validate([
            'member_id' => ['nullable', Rule::exists('members', 'id')->where('tenant_id', $tenant->id)],
            'payment_method' => 'required|in:cash,qris,transfer',
            'type' => 'required|in:membership,inventory,mixed',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.item_name' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
            'items.*.type' => 'nullable|string',
            'duration_months' => 'nullable|integer|min:0',
            'promo_code' => 'nullable|string',
        ]);

        // Cek apakah transaksi memiliki paket membership
        $hasMembership = false;
        $membershipCount = 0;
        foreach ($request->items as $item) {
            if (isset($item['type']) && $item['type'] === 'membership') {
                $hasMembership = true;
                $membershipCount++;
            }
        }

        // Jika duration_months > 0 juga menandakan ada membership
        if ($request->filled('duration_months') && (int)$request->duration_months > 0) {
            $hasMembership = true;
        }

        if ($membershipCount > 1) {
            return redirect()->back()->with('error', 'Transaksi dibatalkan: Hanya diperbolehkan memilih 1 paket membership dalam satu transaksi.');
        }

        if ($hasMembership && empty($request->member_id)) {
            return redirect()->back()->with('error', 'Untuk pembelian paket membership gym, Anda wajib memilih data Member terlebih dahulu.');
        }

        $invNumber = '#POS-' . strtoupper($tenant->subdomain ? substr($tenant->subdomain, 0, 3) : 'GYM') . '-' . date('YmdHis') . '-' . rand(100, 999);

        $totalAmount = 0;
        foreach ($request->items as $item) {
            $totalAmount += ($item['qty'] * $item['price']);
        }

        $discountAmount = 0;
        if ($request->filled('promo_code')) {
            $promo = PromoCode::where('tenant_id', $tenant->id)
                ->where('code', strtoupper($request->promo_code))
                ->first();

            if ($promo && $promo->isValid() && $totalAmount >= $promo->min_purchase) {
                if ($promo->discount_type === 'percentage') {
                    $discountAmount = $totalAmount * ($promo->discount_value / 100);
                } else {
                    $discountAmount = $promo->discount_value;
                }
                $totalAmount = max(0, $totalAmount - $discountAmount);
                $promo->increment('used_count');
            }
        }

        $transaction = PosTransaction::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'member_id' => $request->member_id,
            'invoice_number' => $invNumber,
            'total_amount' => $totalAmount,
            'payment_method' => $request->payment_method,
            'type' => $request->type,
            'void_status' => 'none',
        ]);

        foreach ($request->items as $item) {
            $subtotal = $item['qty'] * $item['price'];
            PosTransactionItem::create([
                'pos_transaction_id' => $transaction->id,
                'product_id' => $item['product_id'] ?? null,
                'item_name' => $item['item_name'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $subtotal,
            ]);

            if (!empty($item['product_id'])) {
                $prod = Product::find($item['product_id']);
                if ($prod && $prod->category !== 'membership') {
                    $prod->decrement('stock', $item['qty']);

                    StockMutation::create([
                        'tenant_id' => $tenant->id,
                        'product_id' => $prod->id,
                        'user_id' => $user->id,
                        'type' => 'sale_pos',
                        'qty' => $item['qty'],
                        'notes' => "Penjualan POS Kasir ({$invNumber})",
                    ]);
                }
            }
        }

        if ($hasMembership && $request->member_id) {
            $member = Member::find($request->member_id);
            if ($member) {
                $months = (int) ($request->duration_months ?? 1);
                $currentExpiry = ($member->expired_at && Carbon::parse($member->expired_at)->isFuture()) 
                    ? Carbon::parse($member->expired_at) 
                    : Carbon::today();

                if ($months > 0) {
                    $newExpiry = (clone $currentExpiry)->addMonths($months);
                } else {
                    $newExpiry = (clone $currentExpiry)->addDay();
                }

                // Deteksi tingkatan tier jika nama paket mengandung standard/premium
                $tier = $member->membership_tier ?: 'basic';
                foreach ($request->items as $item) {
                    if (isset($item['type']) && $item['type'] === 'membership') {
                        $itemNameLower = strtolower($item['item_name'] ?? '');
                        if (str_contains($itemNameLower, 'premium')) {
                            $tier = 'premium';
                        } elseif (str_contains($itemNameLower, 'standard')) {
                            $tier = 'standard';
                        } elseif (str_contains($itemNameLower, 'basic')) {
                            $tier = 'basic';
                        }
                    }
                }

                $member->update([
                    'status' => 'active',
                    'membership_tier' => $tier,
                    'expired_at' => $newExpiry,
                ]);
            }
        }

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Transaksi POS Kasir',
            'description' => "Kasir {$user->name} memproses transaksi {$invNumber} total Rp " . number_format($totalAmount, 0, ',', '.') . ($discountAmount > 0 ? " (Diskon Promo)" : ""),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('receptionist.pos.index')->with([
            'success' => "Transaksi {$invNumber} berhasil diproses!",
            'print_transaction_id' => $transaction->id
        ]);
    }

    public function invoiceData($id)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id ?? ($user->tenant ? $user->tenant->id : 1);

        $query = PosTransaction::where('tenant_id', $tenantId)
            ->with(['member', 'user', 'items', 'tenant']);

        if ($user->role === 'member') {
            $member = Member::where('user_id', $user->id)->first();
            $query->where('member_id', $member ? $member->id : 0);
        }

        $transaction = $query->findOrFail($id);
        return response()->json($transaction);
    }

    public function requestVoid(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        $request->validate(['void_reason' => 'required|string|max:500']);
        $transaction = PosTransaction::where('tenant_id', $tenant->id)->findOrFail($id);
        $transaction->update([
            'void_status' => 'pending',
            'void_reason' => $request->void_reason,
        ]);
        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Pengajuan Pembatalan Transaksi',
            'description' => "Kasir {$user->name} mengajukan pembatalan invoice {$transaction->invoice_number}. Alasan: {$request->void_reason}",
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('receptionist.pos.index')->with('success', 'Pengajuan pembatalan transaksi telah dikirim untuk persetujuan.');
    }

    public function storeProduct(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if ($tenant && !$tenant->hasFeature('inventory')) {
            return redirect()->back()->with('error', "Fitur Manajemen Inventaris Ritel (Suplemen, Minuman & Merchandise) hanya tersedia pada Paket Pro dan Enterprise. Paket Anda saat ini ({$tenant->plan_name}) hanya mendukung layanan dasar POS & Kasir Member.");
        }

        if ($request->filled('price')) {
            $request->merge(['price' => preg_replace('/[^0-9]/', '', (string)$request->price)]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:membership,supplement,drink,merchandise',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
        ]);
        $product = Product::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);
        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Tambah Produk Inventaris',
            'description' => "Menambahkan produk baru: {$product->name} (Rp " . number_format($product->price, 0, ',', '.') . ")",
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('receptionist.pos.index')->with('success', "Produk {$product->name} berhasil ditambahkan ke katalog inventaris.");
    }

    public function updateProduct(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if ($tenant && !$tenant->hasFeature('inventory')) {
            return redirect()->back()->with('error', "Fitur Manajemen Inventaris Ritel hanya tersedia pada Paket Pro dan Enterprise.");
        }

        $product = Product::where('tenant_id', $tenant->id)->findOrFail($id);

        if ($request->filled('price')) {
            $request->merge(['price' => preg_replace('/[^0-9]/', '', (string)$request->price)]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:membership,supplement,drink,merchandise',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
        ]);
        $product->update([
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);
        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Update Produk Inventaris',
            'description' => "Memperbarui produk: {$product->name}",
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('receptionist.pos.index')->with('success', "Produk {$product->name} berhasil diperbarui.");
    }

    public function destroyProduct(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if ($tenant && !$tenant->hasFeature('inventory')) {
            return redirect()->back()->with('error', "Fitur Manajemen Inventaris Ritel hanya tersedia pada Paket Pro dan Enterprise.");
        }

        $product = Product::where('tenant_id', $tenant->id)->findOrFail($id);
        $prodName = $product->name;
        $product->delete();
        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Hapus Produk Inventaris',
            'description' => "Menghapus produk: {$prodName}",
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('receptionist.pos.index')->with('success', "Produk {$prodName} berhasil dihapus dari katalog.");
    }

    public function restockProduct(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if ($tenant && !$tenant->hasFeature('inventory')) {
            return redirect()->back()->with('error', "Fitur Manajemen Inventaris Ritel hanya tersedia pada Paket Pro dan Enterprise.");
        }

        $request->validate([
            'qty_add' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ], [
            'qty_add.required' => 'Jumlah stok masuk wajib diisi.',
            'qty_add.integer' => 'Jumlah stok masuk harus berupa bilangan bulat.',
            'qty_add.min' => 'Jumlah stok masuk minimal 1 pcs.',
        ]);

        $product = Product::where('tenant_id', $tenant->id)->findOrFail($id);
        $oldStock = $product->stock;
        $product->increment('stock', $request->qty_add);
        $newStock = $product->stock;

        $noteText = $request->filled('notes') ? " (Catatan: {$request->notes})" : "";

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Restock Stok Barang',
            'description' => "Kasir/Staf {$user->name} menambahkan stok +{$request->qty_add} pcs untuk '{$product->name}' (Stok: {$oldStock} -> {$newStock}){$noteText}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('receptionist.pos.index')->with('success', "Stok {$product->name} berhasil ditambah +{$request->qty_add} pcs. Total stok sekarang: {$newStock} pcs.");
    }

    public function transferToDisplay(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if ($tenant && !$tenant->hasFeature('inventory')) {
            return redirect()->back()->with('error', "Fitur Manajemen Inventaris Ritel hanya tersedia pada Paket Pro dan Enterprise.");
        }

        $request->validate([
            'qty_transfer' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ], [
            'qty_transfer.required' => 'Jumlah barang yang dipajang wajib diisi.',
            'qty_transfer.integer' => 'Jumlah barang harus berupa bilangan bulat.',
            'qty_transfer.min' => 'Jumlah barang minimal 1 pcs.',
        ]);

        $product = Product::where('tenant_id', $tenant->id)->findOrFail($id);

        if ($product->warehouse_stock < $request->qty_transfer) {
            return redirect()->back()->with('error', "Stok gudang tidak mencukupi! Sisa stok gudang saat ini: {$product->warehouse_stock} pcs (kurang dari permintaan transfer {$request->qty_transfer} pcs).");
        }

        $product->decrement('warehouse_stock', $request->qty_transfer);
        $product->increment('stock', $request->qty_transfer);

        $noteText = $request->filled('notes') ? ": {$request->notes}" : "";

        StockMutation::create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'transfer_to_display',
            'qty' => $request->qty_transfer,
            'notes' => "Pajang dari Gudang ke Etalase Kasir{$noteText}",
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Pajang Produk ke Etalase',
            'description' => "Kasir {$user->name} memindahkan {$request->qty_transfer} pcs '{$product->name}' dari Gudang ke Etalase Kasir (Sisa Gudang: {$product->warehouse_stock} pcs, Etalase: {$product->stock} pcs)",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('receptionist.pos.index')->with('success', "Berhasil memajang {$request->qty_transfer} pcs '{$product->name}' ke Etalase. (Sisa Gudang: {$product->warehouse_stock} pcs, Etalase: {$product->stock} pcs)");
    }
}
