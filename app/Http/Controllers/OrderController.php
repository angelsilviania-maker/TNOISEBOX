<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * README: Menampilkan daftar seluruh pesanan masuk di Dashboard Admin.
     */
    public function index()
    {
        $orders = Order::with('orderDetails.event')->latest()->get();
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * README: Khusus untuk halaman Dashboard Admin yang menampilkan event & rekap pesanan.
     */
    public function adminDashboard()
    {
        $events = Event::latest()->get();
        $orders = Order::with('orderDetails.event')->latest()->get();
        return view('dashboard', compact('events', 'orders'));
    }

    /**
     * README: Fungsi untuk memproses penyimpanan pesanan tiket baru.
     * Menggunakan proteksi agar user wajib login/punya akun terlebih dahulu.
     */
    public function store(Request $request)
    {
        // README: Cek apakah user sudah login atau belum (mencegah tamu memesan)
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login atau buat akun terlebih dahulu untuk memesan tiket.');
        }

        // README: Validasi input data dari form (hanya event_id dan jumlah tiket)
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // README: Cari data event berdasarkan ID dan hitung total harga otomatis
        $event = Event::findOrFail($request->event_id);
        $totalPrice = $event->ticket_price * $request->quantity;

        // README: Ambil data user yang sedang aktif login
        $user = auth()->user();

        // README: Menggunakan Database Transaction agar data pesanan & detail tersimpan aman secara bersamaan
        DB::beginTransaction();
        try {
            // README: Simpan data pesanan utama (Nama & Email diambil otomatis dari akun user)
            $order = Order::create([
                // 'user_id' => $user->id, // Aktifkan baris ini jika tabel orders lo punya kolom user_id
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'total_price' => $totalPrice,
                'status' => 'Pending', // Status awal pesanan adalah Pending (menunggu konfirmasi admin)
            ]);

            // README: Simpan detail pesanan (Relasi ke event dan jumlah tiket yang dibeli)
            OrderDetail::create([
                'order_id' => $order->id,
                'event_id' => $event->id,
                'quantity' => $request->quantity,
                'subtotal' => $totalPrice,
            ]);

            DB::commit();

            return redirect()->route('events.index')->with('success', 'Pesanan tiket berhasil dibuat!');
        } catch (\Exception $e) {
            // README: Batalkan penyimpanan jika terjadi kendala pada database
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memesan tiket.');
        }
    }

    /**
     * README: Fungsi untuk memperbarui status pesanan oleh Admin (Pending -> Confirmed / Cancelled).
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:Pending,Confirmed,Cancelled',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    /**
     * README: Fungsi untuk menghapus data pesanan dari database.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return back()->with('success', 'Pesanan berhasil dihapus!');
    }
}
