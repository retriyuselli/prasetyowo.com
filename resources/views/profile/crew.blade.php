@extends('profile.layout')

@section('profile-page-title', 'Penugasan Crew')
@section('profile-page-subtitle', ($isSuperAdmin ?? false) ? 'Semua event wedding beserta crew yang ditugaskan' : 'Daftar event wedding yang Anda ditugaskan sebagai crew')

@section('profile-content')
<div class="space-y-6">

    @if(! $employee && ! ($isSuperAdmin ?? false))
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
            <svg class="w-10 h-10 text-yellow-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z" />
            </svg>
            <p class="text-yellow-700 font-medium">Akun Anda belum terhubung ke data karyawan.</p>
            <p class="text-yellow-600 text-sm mt-1">Hubungi admin untuk menghubungkan akun Anda.</p>
        </div>
    @else
        {{-- Upcoming Events --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h2 class="text-base font-semibold text-gray-800">Event Mendatang</h2>
                <span class="ml-auto bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                    {{ $upcomingEvents->count() }}
                </span>
            </div>

            @if($upcomingEvents->isEmpty())
                <div class="px-6 py-8 text-center text-gray-400 text-sm">Tidak ada event mendatang.</div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($upcomingEvents as $event)
                        @include('profile.partials.crew-event-row', ['event' => $event, 'past' => false])
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Past Events --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-base font-semibold text-gray-800">Riwayat Event</h2>
                <span class="ml-auto bg-gray-100 text-gray-500 text-xs font-semibold px-2 py-0.5 rounded-full">
                    {{ $pastEvents->count() }}
                </span>
            </div>

            @if($pastEvents->isEmpty())
                <div class="px-6 py-8 text-center text-gray-400 text-sm">Belum ada riwayat event.</div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($pastEvents as $event)
                        @include('profile.partials.crew-event-row', ['event' => $event, 'past' => true])
                    @endforeach
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
