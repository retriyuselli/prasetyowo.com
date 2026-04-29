@php
    $typeLabels = [
        'lamaran'  => ['label' => 'Lamaran',  'color' => 'bg-pink-100 text-pink-700'],
        'akad'     => ['label' => 'Akad',     'color' => 'bg-purple-100 text-purple-700'],
        'resepsi'  => ['label' => 'Resepsi',  'color' => 'bg-blue-100 text-blue-700'],
        'lainnya'  => ['label' => 'Lainnya',  'color' => 'bg-gray-100 text-gray-600'],
    ];
    $typeInfo  = $typeLabels[$event->type] ?? ['label' => ucfirst($event->type ?? '-'), 'color' => 'bg-gray-100 text-gray-600'];
    $orderName = $event->order?->name ?? $event->order?->prospect?->name_event ?? '-';
    $myRole    = $event->pivot?->role ?? null;
    $crewNames = $event->employees->pluck('name')->implode(', ');
@endphp

<div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-3 {{ $past ? 'opacity-70' : '' }}">

    {{-- Date badge --}}
    <div class="flex-shrink-0 w-14 text-center">
        @if($event->event_date)
            <div class="text-xs font-semibold text-gray-400 uppercase">{{ $event->event_date->format('M') }}</div>
            <div class="text-2xl font-bold {{ $past ? 'text-gray-400' : 'text-blue-600' }}">{{ $event->event_date->format('d') }}</div>
            <div class="text-xs text-gray-400">{{ $event->event_date->format('Y') }}</div>
        @else
            <div class="text-xs text-gray-300">—</div>
        @endif
    </div>

    {{-- Details --}}
    <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-2 mb-1">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $typeInfo['color'] }}">
                {{ $typeInfo['label'] }}
            </span>
            @if($myRole)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">
                    {{ $myRole }}
                </span>
            @endif
            @if($past)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Selesai</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Mendatang</span>
            @endif
        </div>

        <div class="text-sm font-semibold text-gray-800 truncate">{{ $orderName }}</div>

        <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
            @if($event->name && $event->name !== $typeInfo['label'])
                <span>{{ $event->name }}</span>
            @endif
            @if($event->location)
                <span class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ $event->location }}
                </span>
            @endif
            @if($event->start_time)
                <span class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $event->start_time }}{{ $event->end_time ? ' – '.$event->end_time : '' }}
                </span>
            @endif
        </div>

        @if($event->employees->isNotEmpty())
            <div class="mt-2">
                <div class="text-xs text-gray-400 mb-1.5 font-medium">Tim Crew:</div>
                <div class="flex flex-wrap gap-2">
                    @foreach($event->employees as $crewMember)
                        @php
                            $dp   = $crewMember->dataPribadi;
                            $foto = $crewMember->photo ?? $dp?->foto ?? null;
                            $nama = $dp?->nama_lengkap ?? $crewMember->name ?? '-';
                            $posisi = $crewMember->position ?? $dp?->pekerjaan ?? null;
                            $crewRole = $crewMember->pivot?->role ?? null;
                            $avatarUrl = 'https://ui-avatars.com/api/?name='.urlencode($nama).'&size=64&background=e0e7ff&color=4338ca&font-size=0.4';
                        @endphp
                        <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-2 py-1.5 border border-gray-100">
                            <div class="w-7 h-7 rounded-full overflow-hidden flex-shrink-0">
                                @if($foto)
                                    <img src="{{ Storage::url($foto) }}"
                                         alt="{{ $nama }}"
                                         class="w-7 h-7 object-cover"
                                         onerror="this.src='{{ $avatarUrl }}'">
                                @else
                                    <img src="{{ $avatarUrl }}" alt="{{ $nama }}" class="w-7 h-7 object-cover">
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-medium text-gray-700 leading-tight">{{ $nama }}</div>
                                @if($crewRole || $posisi)
                                    <div class="text-xs text-gray-400 leading-tight">{{ $crewRole ?? $posisi }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

</div>
