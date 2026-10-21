<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

// Redirect setelah login berdasarkan role (Admin / Customer)
Route::get('/dashboard', function () {
    if (auth()->user() && auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('events.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// =========================================================
// ROUTE KATALOG & DETAIL (Bisa diakses Admin & Customer)
// =========================================================
Route::middleware(['auth'])->group(function () {
    // Katalog Event
    Route::get('/', function () {
        return app(EventController::class)->index(request());
    })->name('events.index');

    // Detail Event
    Route::get('/event/{event}', function (Event $event) {
        return app(EventController::class)->show($event);
    })->name('events.show');

    // Proses Pesanan Tiket (Checkout - Khusus Customer, Admin dilarang)
    Route::post('/orders', function (\Illuminate\Http\Request $request) {
        if (auth()->user()->role === 'admin') {
            abort(403, 'Admin tidak dapat melakukan pemesanan tiket.');
        }
        return app(OrderController::class)->store($request);
    })->name('orders.store');
});

// =========================================================
// ROUTE KHUSUS ADMIN & PROFILE (Wajib Login)
// =========================================================
Route::middleware(['auth'])->group(function () {
    // Admin Dashboard Rekap Pesanan & Event
    Route::get('/admin/dashboard', function () {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('events.index');
        }
        $events = Event::latest()->get();
        $orders = Order::with('orderDetails.event')->latest()->get();
        return view('dashboard', compact('events', 'orders'));
    })->name('admin.dashboard');

    // CRUD Event (Khusus Admin)
    Route::resource('admin/events', EventController::class)->except(['index', 'show'])->names('admin.events');

    // Update Status Pesanan Tiket (Khusus Admin)
    Route::patch('/orders/{order}', function (Order $order, \Illuminate\Http\Request $request) {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses khusus Admin.');
        }
        return app(OrderController::class)->update($request, $order);
    })->name('orders.update');

    // Profile Bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
