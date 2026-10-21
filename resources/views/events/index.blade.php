<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Katalog Konser & Event Musik') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- FILTER KATEGORI -->
            <div class="flex flex-wrap gap-2 mb-8 justify-center">
                <a href="{{ route('events.index') }}"
                   class="px-5 py-2 rounded-full text-sm font-semibold transition shadow-sm {{ !request('category') ? 'bg-indigo-600 text-white shadow-indigo-200' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
                    Semua
                </a>
                <a href="{{ route('events.index', ['category' => 'Musik']) }}"
                   class="px-5 py-2 rounded-full text-sm font-semibold transition shadow-sm {{ request('category') == 'Musik' ? 'bg-indigo-600 text-white shadow-indigo-200' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
                    🎵 Musik
                </a>
                <a href="{{ route('events.index', ['category' => 'Festival']) }}"
                   class="px-5 py-2 rounded-full text-sm font-semibold transition shadow-sm {{ request('category') == 'Festival' ? 'bg-indigo-600 text-white shadow-indigo-200' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
                    🎪 Festival
                </a>
                <a href="{{ route('events.index', ['category' => 'Seminar']) }}"
                   class="px-5 py-2 rounded-full text-sm font-semibold transition shadow-sm {{ request('category') == 'Seminar' ? 'bg-indigo-600 text-white shadow-indigo-200' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
                    🎓 Seminar
                </a>
            </div>

            <!-- GRID LIST EVENT -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($events as $event)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            @if($event->image)
                                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">
                                    <span>Tidak Ada Poster</span>
                                </div>
                            @endif

                            <div class="p-6">
                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-indigo-600 bg-indigo-100">
                                    {{ $event->category }}
                                </span>
                                <h3 class="font-bold text-lg text-gray-900 mt-2">{{ $event->title }}</h3>
                                <p class="text-gray-600 text-sm mt-1 font-medium">Rp {{ number_format($event->ticket_price, 0, ',', '.') }}</p>
                                <p class="text-gray-500 text-sm mt-3 line-clamp-2">{{ $event->description }}</p>
                            </div>
                        </div>

                        <div class="p-6 pt-0">
                            <a href="{{ route('events.show', $event->id) }}" class="inline-block w-full text-center bg-gray-900 text-white font-semibold py-2 px-4 rounded hover:bg-gray-800 transition">
                                Detail & Pesan Tiket
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 text-center text-gray-500">
                        <p class="text-base font-medium">Belum ada event atau konser untuk kategori ini.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
