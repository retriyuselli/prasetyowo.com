@extends('profile.layout')

@section('profile-page-title', 'Data Crew')
@section('profile-page-subtitle', 'Daftar lengkap data pribadi seluruh crew')

@section('profile-content')
<div class="space-y-5">

    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('profile.crew-data') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                </svg>
                <input type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama, email, atau posisi..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>

            <select name="position"
                class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                <option value="">Semua Posisi</option>
                @foreach($positions as $pos)
                    <option value="{{ $pos }}" @selected($position === $pos)>{{ $pos }}</option>
                @endforeach
            </select>

            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                Filter
            </button>

            @if($search || $position)
                <a href="{{ route('profile.crew-data') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition text-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Summary --}}
    <div class="flex items-center justify-between text-sm text-gray-500">
        <span>Menampilkan <span class="font-semibold text-gray-700">{{ $crewList->firstItem() ?? 0 }}–{{ $crewList->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-700">{{ $crewList->total() }}</span> crew</span>
    </div>

    {{-- Crew Grid --}}
    @if($crewList->isEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-16 text-center text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 11-8 0 4 4 0 018 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <p class="font-medium">Tidak ada data crew ditemukan.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($crewList as $dp)
                @php
                    $emp       = $dp->employee;
                    $fotoPath  = $emp?->photo ?? $dp->foto ?? null;
                    $avatarUrl = 'https://ui-avatars.com/api/?name='.urlencode($dp->nama_lengkap ?? '-').'&size=128&background=e0e7ff&color=4338ca&font-size=0.4';
                    $isActive  = $emp && is_null($emp->date_of_out);
                @endphp
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition">

                    {{-- Card header --}}
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-5 pt-5 pb-3 flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-white shadow-sm flex-shrink-0">
                            @if($fotoPath)
                                <img src="{{ Storage::url($fotoPath) }}"
                                     alt="{{ $dp->nama_lengkap }}"
                                     class="w-14 h-14 object-cover"
                                     onerror="this.src='{{ $avatarUrl }}'">
                            @else
                                <img src="{{ $avatarUrl }}" alt="{{ $dp->nama_lengkap }}" class="w-14 h-14 object-cover">
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="font-semibold text-gray-900 truncate">{{ $dp->nama_lengkap ?? '-' }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ $dp->pekerjaan ?? $emp?->position ?? 'Tidak diketahui' }}</div>
                            <div class="mt-1">
                                @if($isActive)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-100 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                                    </span>
                                @elseif($emp)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Tidak Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-yellow-700 bg-yellow-100 px-2 py-0.5 rounded-full">
                                        Belum terhubung
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Card body --}}
                    <div class="px-5 py-4 space-y-2 text-sm">
                        @if($dp->email)
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m8 0a8 8 0 11-16 0 8 8 0 0116 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                                <span class="truncate">{{ $dp->email }}</span>
                            </div>
                        @endif

                        @if($dp->nomor_telepon)
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>{{ $dp->nomor_telepon }}</span>
                            </div>
                        @endif

                        @if($dp->tanggal_mulai_gabung)
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Bergabung {{ $dp->tanggal_mulai_gabung->format('d M Y') }}</span>
                            </div>
                        @endif

                        @if($dp->jenis_kelamin)
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ ucfirst($dp->jenis_kelamin) }}</span>
                            </div>
                        @endif

                        @if($emp && $emp->orderEvents()->exists())
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span>{{ $emp->orderEvents()->count() }} event ditugaskan</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($crewList->hasPages())
            <div class="mt-4">
                {{ $crewList->links() }}
            </div>
        @endif
    @endif

</div>
@endsection
