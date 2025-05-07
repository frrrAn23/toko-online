<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Produk;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{
    public function addToCart($id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $produk = Produk::findOrFail($id);

        $order = Order::firstOrCreate(
            ['customer_id' => $customer->id, 'status' => 'pending'],
            ['total_harga' => 0]
        );

        $orderItem = OrderItem::firstOrCreate(
            ['order_id' => $order->id, 'produk_id' => $produk->id],
            ['quantity' => 1, 'harga' => $produk->harga]
        );

        if (!$orderItem->wasRecentlyCreated) {
            $orderItem->quantity++;
            $orderItem->save();
        }

        $order->total_harga = $order->orderItems()->sum(DB::raw('quantity * harga'));
        $order->save();

        return redirect()->route('order.cart')->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    public function viewCart()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->first();

        if ($order) {
            $order->load('orderItems.produk');
        }

        return view('v_order.cart', compact('order'));
    }

    public function removeCartItem($itemId)
    {
        $orderItem = OrderItem::findOrFail($itemId);
        $order = $orderItem->order;

        $order->total_harga -= ($orderItem->harga * $orderItem->quantity);
        $order->save();

        $orderItem->delete();

        return redirect()->route('order.cart')->with('success', 'Produk berhasil dihapus dari keranjang');
    }

    public function updateCartItem(Request $request, $id)
{
    // Validasi input untuk quantity
    $request->validate([
        'quantity' => 'required|integer|min:1',
    ]);

    // Cari OrderItem berdasarkan ID
    $orderItem = OrderItem::findOrFail($id);

    // Update quantity
    $orderItem->quantity = $request->quantity;
    $orderItem->save();

    // Recalculate total harga order
    $order = $orderItem->order;
    $order->total_harga = $order->orderItems()->sum(DB::raw('quantity * harga'));
    $order->save();

    // Kembali ke halaman keranjang dengan pesan sukses
    return redirect()->route('order.cart')->with('success', 'Jumlah produk berhasil diperbarui.');
}

}
