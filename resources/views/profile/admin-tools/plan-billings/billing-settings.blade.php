@extends('profile.layout')

@section('profile-page-title', 'Pengaturan Pembayaran')
@section('profile-page-subtitle', 'Kelola invoice DP dan pelunasan (admin only)')

@section('profile-content')
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden p-6">
    <div class="flex items-center justify-between gap-3">
        <div>
            <div class="text-sm font-semibold text-gray-900">Invoice</div>
            <div class="text-sm text-gray-600">Buat invoice DP atau pelunasan berdasarkan paket yang aktif.</div>
            <div class="text-sm text-gray-600 mt-1">
                Nominal plan:
                <span class="font-semibold text-gray-900">Rp {{ number_format((int) ($planPrice ?? 0), 0, ',', '.') }}</span>
                · Total pembayaran:
                <span class="font-semibold text-gray-900">Rp {{ number_format((int) ($billingTotalPaid ?? 0), 0, ',', '.') }}</span>
                · Sisa:
                <span class="font-semibold text-gray-900">Rp {{ number_format((int) ($billingRemaining ?? 0), 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="flex flex-wrap items-end gap-4">
            <form action="{{ route('profile.admin-tools.plan-billings.billing.store') }}" method="POST" class="flex flex-wrap items-end gap-2">
                @csrf
                @php
                    $dpPaid = ($latestDpBilling?->status ?? '') === 'paid';
                    $invoiceType = old('type', $dpPaid ? 'pelunasan' : 'dp');
                @endphp
                <select name="type" class="w-44 h-9 border border-gray-300 rounded-lg px-3 text-sm bg-white" required>
                    @if (! $dpPaid)
                        <option value="dp" {{ $invoiceType === 'dp' ? 'selected' : '' }}>Down Payment</option>
                    @endif
                    <option value="pelunasan" {{ $invoiceType === 'pelunasan' ? 'selected' : '' }}>Pelunasan</option>
                </select>
                <input type="number" name="amount" min="0" required
                    class="w-44 h-9 border border-gray-300 rounded-lg px-3 text-sm"
                    placeholder="Nominal Invoice"
                    value="{{ old('amount') }}">
                <input type="datetime-local" name="billed_at"
                    class="w-56 h-9 border border-gray-300 rounded-lg px-3 text-sm"
                    value="{{ old('billed_at', now()->format('Y-m-d\\TH:i')) }}">
                <button type="submit"
                    class="inline-flex items-center px-3 py-2 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition">
                    Buat Invoice
                </button>
            </form>
            <div class="w-full">
                @error('type')
                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                @enderror
                @error('amount')
                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                @enderror
                @error('billed_at')
                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    @php
        $dpPaymentPaid = (bool) $latestDpBilling && (($latestDpBilling->status ?? '') === 'paid');
        $pelunasanPaymentPaid = (bool) $latestPelunasanBilling && (($latestPelunasanBilling->status ?? '') === 'paid');
    @endphp
    @if ($dpPaymentPaid || $pelunasanPaymentPaid)
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
            @if ($dpPaymentPaid)
                <div class="border border-gray-200 rounded-xl px-4 py-3">
                    <div class="text-sm font-semibold text-gray-900">Down Payment</div>
                    <div class="text-sm text-gray-600 mt-1">
                        Status:
                        <span class="font-semibold {{ ($latestDpBilling->status ?? '') === 'paid' ? 'text-emerald-700' : 'text-amber-800' }}">
                            {{ ($latestDpBilling->status ?? 'unpaid') === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}
                        </span>
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <a href="{{ route('profile.admin-tools.plan-billings.billing.edit', ['billing' => $latestDpBilling->id]) }}"
                            class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
                            Edit Invoice
                        </a>
                        <a href="{{ route('profile.admin-tools.plan-billings.invoice.view', ['billing' => $latestDpBilling->id]) }}"
                            class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition">
                            View
                        </a>
                    </div>
                </div>
            @endif

            @if ($pelunasanPaymentPaid)
                <div class="border border-gray-200 rounded-xl px-4 py-3">
                    <div class="text-sm font-semibold text-gray-900">Pelunasan</div>
                    <div class="text-sm text-gray-600 mt-1">
                        Status:
                        <span class="font-semibold {{ ($latestPelunasanBilling->status ?? '') === 'paid' ? 'text-emerald-700' : 'text-amber-800' }}">
                            {{ ($latestPelunasanBilling->status ?? 'unpaid') === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}
                        </span>
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <a href="{{ route('profile.admin-tools.plan-billings.billing.edit', ['billing' => $latestPelunasanBilling->id]) }}"
                            class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
                            Edit Invoice
                        </a>
                        <a href="{{ route('profile.admin-tools.plan-billings.invoice.view', ['billing' => $latestPelunasanBilling->id]) }}"
                            class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition">
                            View
                        </a>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="mt-6 flex items-center justify-end">
        <a href="{{ route('profile.admin-tools.plan-billings') }}"
            class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
            Kembali
        </a>
    </div>
</div>
@endsection

