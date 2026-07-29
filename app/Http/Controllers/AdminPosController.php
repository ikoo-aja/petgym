<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use App\Models\Product;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\StaffLog;
use App\Models\PromoCode;
use Carbon\Carbon;

class AdminPosController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $members = Member::where('tenant_id', $tenant->id)->where('status', 'active')->orderBy('name')->get();
        $products = Product::where('tenant_id', $tenant->id)->orderBy('name')->get();
        $recentTransactions = PosTransaction::where('tenant_id', $tenant->id)
            ->with(['member', 'user', 'items'])
            ->latest()
            ->take(15)
            ->get();

        return view('admin.pos.index', compact('members', 'products', 'recentTransactions', 'tenant'));
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'payment_method' => 'required|in:cash,qris,transfer',
            'type' => 'required|in:membership,inventory',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.item_name' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
            'duration_months' => 'nullable|integer|min:1',
            'promo_code' => 'nullable|string',
        ]);

        $invNumber = '#POS-' . strtoupper($tenant->subdomain ? substr($tenant->subdomain, 0, 3) : 'GYM') . '-' . date('YmdHis') . '-' . rand(100, 999);

        $totalAmount = 0;
        foreach ($request->items as $item) {
            $totalAmount += ($item['qty'] * $item['price']);
        }

        // Apply Promo Code if valid
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

            // Deduct stock if product inventory item
            if (!empty($item['product_id'])) {
                $prod = Product::find($item['product_id']);
                if ($prod && $prod->category !== 'membership') {
                    $prod->decrement('stock', $item['qty']);
                }
            }
        }

        // Auto extend member expired date if membership transaction
        if ($request->type === 'membership' && $request->member_id) {
            $member = Member::find($request->member_id);
            if ($member) {
                $months = $request->duration_months ?? 1;
                $currentExpiry = ($member->expired_at && $member->expired_at->isFuture()) ? $member->expired_at : Carbon::today();
                $newExpiry = $currentExpiry->addMonths($months);

                $member->update([
                    'status' => 'active',
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

        // Redirect based on user role
        $redirectRoute = $user->isReceptionist() ? 'receptionist.dashboard' : 'admin.pos.index';

        return redirect()->route($redirectRoute)->with([
            'success' => "Transaksi {$invNumber} berhasil diproses!",
            'print_transaction_id' => $transaction->id
        ]);
    }

    public function invoiceData($id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $transaction = PosTransaction::where('tenant_id', $tenant->id)
            ->with(['member', 'user', 'items', 'tenant'])
            ->findOrFail($id);

        return response()->json($transaction);
    }

    /**
     * Request Void (Pembatalan Transaksi dari Kasir Resepsionis)
     */
    public function requestVoid(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $request->validate([
            'void_reason' => 'required|string|max:500',
        ]);

        $transaction = PosTransaction::where('tenant_id', $tenant->id)->findOrFail($id);

        $transaction->update([
            'void_status' => 'pending',
            'void_reason' => $request->void_reason,
        ]);

        StaffLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'Pengajuan Void Transaksi',
            'description' => "Kasir {$user->name} mengajukan pembatalan (void) invoice {$transaction->invoice_number}. Alasan: {$request->void_reason}",
            'ip_address' => $request->ip(),
        ]);

        $redirectRoute = $user->isReceptionist() ? 'receptionist.dashboard' : 'admin.pos.index';
        return redirect()->route($redirectRoute)->with('success', 'Pengajuan void transaksi telah dikirim ke Manager untuk persetujuan.');
    }

    public function storeProduct(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

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

        return redirect()->route('admin.pos.index')->with('success', "Produk {$product->name} berhasil ditambahkan ke katalog inventaris.");
    }

    public function updateProduct(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        $product = Product::where('tenant_id', $tenant->id)->findOrFail($id);

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

        return redirect()->route('admin.pos.index')->with('success', "Produk {$product->name} berhasil diperbarui.");
    }

    public function destroyProduct(Request $request, $id)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

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

        return redirect()->route('admin.pos.index')->with('success', "Produk {$prodName} berhasil dihapus dari katalog.");
    }
}
