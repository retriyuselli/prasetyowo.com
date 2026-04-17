@extends('profile.layout')

@section('profile-page-title', 'Edit Plan & Billings')
@section('profile-page-subtitle', 'Pengaturan subscription (admin only)')

@section('profile-content')
<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden p-6">
    <form action="{{ route('profile.admin-tools.plan-billings.update') }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-1">Plan</label>
            @php
                $planCode = old('plan_code', $subscription?->plan_code ?? 'hastana');
                $planNominal = $planCode === 'hastana' ? 8500000 : 10000000;
            @endphp
            <select id="plan_code" name="plan_code" class="w-full h-10 border border-gray-300 rounded-lg px-3 text-sm bg-white" required>
                <option value="hastana" data-nominal="8500000" {{ $planCode === 'hastana' ? 'selected' : '' }}>Anggota Hastana — Rp 8.500.000 / 2 tahun</option>
                <option value="non_hastana" data-nominal="10000000" {{ $planCode === 'non_hastana' ? 'selected' : '' }}>Non Hastana — Rp 10.000.000 / 2 tahun</option>
            </select>
            @error('plan_code')
                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-1">Nominal Plan (Rp)</label>
            <input id="plan_price" type="number" name="plan_price" class="w-full h-10 border border-gray-300 rounded-lg px-3 text-sm bg-gray-50"
                value="{{ (int) old('plan_price', $planNominal) }}" readonly>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-1">Tanggal Mulai Langganan</label>
            <input type="datetime-local" name="usage_reset_at" class="w-full h-10 border border-gray-300 rounded-lg px-3 text-sm"
                value="{{ old('usage_reset_at', optional($subscription?->usage_reset_at)->format('Y-m-d\\TH:i')) }}">
            @error('usage_reset_at')
                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="on_demand_enabled" name="on_demand_enabled" value="1"
                class="h-4 w-4 border-gray-300 rounded"
                {{ old('on_demand_enabled', (int) ($subscription?->on_demand_enabled ?? 0)) ? 'checked' : '' }}>
            <label for="on_demand_enabled" class="text-sm text-gray-900">On-Demand Enabled</label>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-1">Status</label>
            <select name="status" class="w-full h-10 border border-gray-300 rounded-lg px-3 text-sm bg-white" required>
                @php
                    $status = old('status', $subscription?->status ?? 'active');
                @endphp
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>active</option>
                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>inactive</option>
                <option value="canceled" {{ $status === 'canceled' ? 'selected' : '' }}>canceled</option>
            </select>
            @error('status')
                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-2 pt-2">
            <a href="{{ route('profile.admin-tools.plan-billings') }}"
                class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                Kembali
            </a>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800 transition">
                Simpan
            </button>
        </div>
    </form>
</div>
<script>
    (function () {
        var planSelect = document.getElementById('plan_code');
        var nominalInput = document.getElementById('plan_price');
        if (!planSelect || !nominalInput) return;

        function syncNominal() {
            var opt = planSelect.options[planSelect.selectedIndex];
            var nominal = opt ? opt.getAttribute('data-nominal') : null;
            if (!nominal) return;
            nominalInput.value = parseInt(nominal, 10) || 0;
        }

        planSelect.addEventListener('change', syncNominal);
        syncNominal();
    })();
</script>
@endsection

