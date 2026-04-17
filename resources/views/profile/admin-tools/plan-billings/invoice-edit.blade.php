@extends('profile.layout')

@section('profile-page-title', 'Edit Invoice')
@section('profile-page-subtitle', 'Ubah detail invoice subscription')

@section('profile-content')
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden p-6">
    @php
        $planPrice = (int) ($planPrice ?? ($subscription?->plan_price ?? 0));
        $billingTotalPaid = (int) ($billingTotalPaid ?? 0);
        $billingRemaining = max(0, $planPrice - $billingTotalPaid);
    @endphp
    <div class="text-sm text-gray-600 mb-4">
        Nominal plan:
        <span class="font-semibold text-gray-900">Rp {{ number_format($planPrice, 0, ',', '.') }}</span>
        · Total pembayaran:
        <span class="font-semibold text-gray-900">Rp {{ number_format($billingTotalPaid, 0, ',', '.') }}</span>
        · Sisa:
        <span class="font-semibold text-gray-900">Rp {{ number_format($billingRemaining, 0, ',', '.') }}</span>
    </div>
    <form id="invoice-update-form" action="{{ route('profile.admin-tools.plan-billings.billing.update', ['billing' => $billing->id]) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-1">Jenis Invoice</label>
            <input type="text" name="name" class="w-full h-10 border border-gray-300 rounded-lg px-3 text-sm"
                value="{{ old('name', $billing->name) }}" required>
            @error('name')
                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-1">Nominal (Rp)</label>
            <input type="number" name="amount" class="w-full h-10 border border-gray-300 rounded-lg px-3 text-sm"
                value="{{ old('amount', (int) $billing->amount) }}" min="0" required>
            @error('amount')
                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-1">Tanggal Invoice</label>
            <input type="datetime-local" name="billed_at" class="w-full h-10 border border-gray-300 rounded-lg px-3 text-sm"
                value="{{ old('billed_at', optional($billing->billed_at ?? $billing->created_at)->format('Y-m-d\\TH:i')) }}">
            @error('billed_at')
                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-1">Status</label>
            @php
                $status = old('status', $billing->status ?? 'unpaid');
            @endphp
            <select name="status" class="w-full h-10 border border-gray-300 rounded-lg px-3 text-sm bg-white" required>
                <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>unpaid</option>
                <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>paid</option>
                <option value="canceled" {{ $status === 'canceled' ? 'selected' : '' }}>canceled</option>
            </select>
            @error('status')
                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>

    </form>
    <div class="flex items-center justify-end gap-2 pt-2">
        <a href="{{ route('profile.admin-tools.plan-billings.billing-settings') }}"
            class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
            Kembali
        </a>
        <form action="{{ route('profile.admin-tools.plan-billings.billing.destroy', ['billing' => $billing->id]) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="inline-flex items-center px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition"
                onclick="return confirm('Hapus invoice ini?');">
                Hapus
            </button>
        </form>
        <a href="{{ route('profile.admin-tools.plan-billings.invoice.view', ['billing' => $billing->id]) }}"
            class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">
            View Invoice
        </a>
        <button form="invoice-update-form" type="submit"
            class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800 transition">
            Simpan
        </button>
    </div>
</div>
@endsection

