<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of the resource. (Katalog Publik & Admin Index)
     */
    public function index(Request $request)
    {
        $query = Event::query();

        // Filter berdasarkan kategori jika ada request 'category'
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        $events = $query->latest()->get();

        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource. (Form Tambah Event - Admin)
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created resource in storage. (Simpan Event Baru - Admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Musik,Festival,Seminar',
            'ticket_price' => 'required|integer|min:0',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
        }

        Event::create([
            'title' => $request->title,
            'category' => $request->category,
            'ticket_price' => $request->ticket_price,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Event berhasil ditambahkan!');
    }

    /**
     * Display the specified resource. (Detail Event)
     */
    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource. (Form Edit - Admin)
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage. (Update Event - Admin)
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Musik,Festival,Seminar',
            'ticket_price' => 'required|integer|min:0',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = $event->image;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('events', 'public');
        }

        $event->update([
            'title' => $request->title,
            'category' => $request->category,
            'ticket_price' => $request->ticket_price,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Event berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage. (Hapus Event - Admin)
     */
    public function destroy(Event $event)
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Event berhasil dihapus!');
    }
}
