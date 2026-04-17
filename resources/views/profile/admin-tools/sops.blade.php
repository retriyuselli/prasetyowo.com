@extends('profile.layout')

@section('profile-page-title', 'SOP')
@section('profile-page-subtitle', 'Daftar SOP (khusus super admin)')

@section('profile-content')
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden p-6">
    <form method="GET" class="flex items-center gap-3 mb-4">
        <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul / deskripsi / keyword"
            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold">Cari</button>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-gray-500 border-b">
                    <th class="py-3 pr-4">Judul</th>
                    <th class="py-3 pr-4">Kategori</th>
                    <th class="py-3 pr-4">Versi</th>
                    <th class="py-3 pr-4">Status</th>
                    <th class="py-3 pr-4">Updated</th>
                    <th class="py-3 pr-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($sops as $sop)
                    <tr class="text-gray-800">
                        <td class="py-3 pr-4 font-medium">{{ $sop->title }}</td>
                        <td class="py-3 pr-4 text-xs text-gray-700">{{ $sop->category?->name ?? '-' }}</td>
                        <td class="py-3 pr-4 text-xs text-gray-700">{{ $sop->formatted_version }}</td>
                        <td class="py-3 pr-4">
                            <span class="px-2 py-1 rounded-full text-xs {{ $sop->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $sop->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="py-3 pr-4 text-xs text-gray-600">{{ optional($sop->updated_at)->diffForHumans() }}</td>
                        <td class="py-3 pr-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('sop.print', $sop->id) }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold hover:bg-blue-100 transition"
                                    target="_blank"
                                    rel="noopener">
                                    View
                                </a>
                                <a href="{{ route('sop.pdf', $sop->id) }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition"
                                    target="_blank"
                                    rel="noopener">
                                    PDF
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sops->links() }}
    </div>
</div>
@endsection
