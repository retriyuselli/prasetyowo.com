@extends('profile.layout')

@section('profile-page-title', 'Plan & Billings')
@section('profile-page-subtitle', 'Lihat paket langganan, pengaturan billing, dan riwayat transaksi')

@section('profile-content')
@php
    $canEdit = (bool) ($canEdit ?? false);
@endphp
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden p-6">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-base font-semibold text-gray-900">Subscription Plan</h2>
            @if ($canEdit)
                @if (! $subscription)
                    <a href="{{ route('profile.admin-tools.plan-billings.create') }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800 transition">
                        Create
                    </a>
                @else
                    <a href="{{ route('profile.admin-tools.plan-billings.edit') }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                        Edit
                    </a>
                @endif
            @endif
        </div>

        <div class="mt-4 border border-gray-200 rounded-xl overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 px-4 py-4 border-b border-gray-200">
                <div class="text-sm text-gray-600">Current Plan</div>
                <div class="text-sm font-semibold text-emerald-600 md:text-right">
                    {{ $subscription?->plan_name ?? 'Anggota Hastana' }}
                    <span class="text-gray-500 font-medium">({{ 'Rp '.number_format((int) ($subscription?->plan_price ?? 8500000), 0, ',', '.') }}/2 tahun)</span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 px-4 py-4 border-b border-gray-200">
                @php
                    $tanggalMulaiLangganan = $subscription?->usage_reset_at ?? $subscription?->created_at ?? now();
                    $tanggalBerakhirLangganan = $tanggalMulaiLangganan->copy()->addYears(2);
                    $sisaHariPenggunaan = max(0, now()->startOfDay()->diffInDays($tanggalBerakhirLangganan->copy()->startOfDay(), false));
                @endphp
                <div class="text-sm text-gray-600">
                    Tanggal mulai/berakhir langganan
                    <br>
                    <span class="text-emerald-700 font-semibold">(Sisa {{ $sisaHariPenggunaan }} hari)</span>
                </div>
                <div class="text-sm font-medium text-gray-900 md:text-right">
                    {{ $tanggalMulaiLangganan->format('d/m/Y') }} - {{ $tanggalBerakhirLangganan->format('d/m/Y') }}
                </div>
            </div>
            <div class="flex items-center justify-between px-4 py-4">
                <div class="text-sm text-gray-600">Upgrade plan</div>
                <button type="button" {{ $canEdit ? '' : 'disabled' }} class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-semibold transition {{ $canEdit ? 'hover:bg-gray-800' : 'opacity-60 cursor-not-allowed' }}">
                    Upgrade plan
                </button>
            </div>
        </div>

        <div class="mt-3 flex items-center justify-between text-sm">
            <a href="{{ $canEdit ? '#' : 'javascript:void(0)' }}" class="text-gray-500 {{ $canEdit ? 'hover:text-gray-700' : 'opacity-60 cursor-not-allowed pointer-events-none' }}">View all plans & features</a>
            <a href="{{ $canEdit ? '#' : 'javascript:void(0)' }}" class="text-gray-500 {{ $canEdit ? 'hover:text-gray-700' : 'opacity-60 cursor-not-allowed pointer-events-none' }}">Redeem Promo Code</a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden p-6">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-base font-semibold text-gray-900">Billing History</h2>
            @if ($canEdit)
                <a href="{{ route('profile.admin-tools.plan-billings.billing-settings') }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                    Pengaturan Pembayaran
                </a>
            @else
                <button type="button" disabled class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-semibold opacity-60 cursor-not-allowed">
                    Pengaturan Pembayaran
                </button>
            @endif
        </div>

        @php
            $planPrice = (int) ($planPrice ?? (int) ($subscription?->plan_price ?? 8500000));
            $billingTotalActive = (int) ($billingTotalActive ?? 0);
            $billingTotalPaid = (int) ($billingTotalPaid ?? 0);
            $billingRemaining = (int) ($billingRemaining ?? max(0, $planPrice - $billingTotalPaid));
            $hasPelunasanInvoice = (bool) $latestPelunasanBilling && (($latestPelunasanBilling->status ?? '') !== 'canceled');
            $isBillingTotalMatch = $billingTotalActive === $planPrice;
        @endphp
        <div class="mt-2 text-sm text-gray-600">
            Nominal plan:
            <span class="font-semibold text-gray-900">Rp {{ number_format($planPrice, 0, ',', '.') }}</span>
            · Total pembayaran:
            <span class="font-semibold text-gray-900">Rp {{ number_format($billingTotalPaid, 0, ',', '.') }}</span>
            · Sisa:
            <span class="font-semibold text-gray-900">Rp {{ number_format($billingRemaining, 0, ',', '.') }}</span>
        </div>
        @if ($hasPelunasanInvoice && ! $isBillingTotalMatch)
            <div class="mt-2 text-sm font-semibold text-red-700">
                Total nominal invoice tidak sama dengan nominal plan. Sesuaikan nominal DP/Pelunasan agar totalnya sama.
            </div>
        @endif

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500">
                        <th class="text-left py-3 pr-4 font-medium">Current Plan</th>
                        <th class="text-left py-3 pr-4 font-medium">Harga</th>
                        <th class="text-left py-3 pr-4 font-medium">Tanggal Invoice</th>
                        <th class="text-left py-3 pr-4 font-medium">Status</th>
                        <th class="text-left py-3 font-medium">Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $tanggalMulaiLangganan = $subscription?->usage_reset_at ?? $subscription?->created_at ?? null;
                    @endphp
                    @if ($subscription)
                        @php
                            $dpPaid = ($latestDpBilling?->status ?? '') === 'paid';
                            $pelunasanPaid = ($latestPelunasanBilling?->status ?? '') === 'paid';

                            $dpStatus = $dpPaid ? 'LUNAS' : 'BELUM LUNAS';
                            $dpStatusClass = $dpPaid
                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                : 'bg-amber-50 text-amber-800 border-amber-200';
                            $pelunasanStatus = $pelunasanPaid ? 'LUNAS' : ($dpPaid ? 'LUNAS SEBAGIAN' : 'BELUM LUNAS');
                            $pelunasanStatusClass = $pelunasanPaid
                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                : ($dpPaid ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-amber-50 text-amber-800 border-amber-200');
                            $dpAmount = $latestDpBilling ? (int) $latestDpBilling->amount : null;
                            $pelunasanAmount = $latestPelunasanBilling ? (int) $latestPelunasanBilling->amount : null;
                            $dpDate = $latestDpBilling?->billed_at ?? $latestDpBilling?->created_at ?? null;
                            $pelunasanDate = $latestPelunasanBilling?->billed_at ?? $latestPelunasanBilling?->created_at ?? null;

                            $totalPembayaran = (int) ($dpAmount ?? 0) + (int) ($pelunasanAmount ?? 0);
                            $isTotalPembayaranMatch = $totalPembayaran === (int) $planPrice;
                            if ($isTotalPembayaranMatch) {
                                $dpStatus = 'LUNAS';
                                $dpStatusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                $pelunasanStatus = 'LUNAS';
                                $pelunasanStatusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            }
                        @endphp

                        <tr class="border-b border-gray-100">
                            <td class="py-3 pr-4 font-medium text-gray-900">Down Payment</td>
                            <td class="py-3 pr-4 text-gray-700">{{ is_null($dpAmount) ? '-' : 'Rp '.number_format($dpAmount, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4 text-gray-700">{{ $dpDate?->format('d/m/Y') ?? '-' }}</td>
                            <td class="py-3 pr-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $dpStatusClass }}">
                                    {{ $dpStatus }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if ($latestDpBilling)
                                    <div class="flex items-center gap-2">
                                        @if ($canEdit)
                                            <a href="{{ route('profile.admin-tools.plan-billings.billing.edit', ['billing' => $latestDpBilling->id]) }}"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
                                                Edit
                                            </a>
                                        @endif
                                        <a href="{{ route('profile.admin-tools.plan-billings.invoice.view', ['billing' => $latestDpBilling->id]) }}"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition">
                                            View
                                        </a>
                                        @if ($canEdit)
                                            <form action="{{ route('profile.admin-tools.plan-billings.billing.destroy', ['billing' => $latestDpBilling->id]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition"
                                                    onclick="return confirm('Hapus invoice ini?');">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-100 text-gray-500 text-xs font-semibold cursor-not-allowed">View</span>
                                @endif
                            </td>
                        </tr>

                        <tr class="border-b border-gray-100">
                            <td class="py-3 pr-4 font-medium text-gray-900">Pelunasan</td>
                            <td class="py-3 pr-4 text-gray-700">{{ is_null($pelunasanAmount) ? '-' : 'Rp '.number_format($pelunasanAmount, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4 text-gray-700">{{ $pelunasanDate?->format('d/m/Y') ?? '-' }}</td>
                            <td class="py-3 pr-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $pelunasanStatusClass }}">
                                    {{ $pelunasanStatus }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if ($latestPelunasanBilling)
                                    <div class="flex items-center gap-2">
                                        @if ($canEdit)
                                            <a href="{{ route('profile.admin-tools.plan-billings.billing.edit', ['billing' => $latestPelunasanBilling->id]) }}"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
                                                Edit
                                            </a>
                                        @endif
                                        <a href="{{ route('profile.admin-tools.plan-billings.invoice.view', ['billing' => $latestPelunasanBilling->id]) }}"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition">
                                            View
                                        </a>
                                        @if ($canEdit)
                                            <form action="{{ route('profile.admin-tools.plan-billings.billing.destroy', ['billing' => $latestPelunasanBilling->id]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition"
                                                    onclick="return confirm('Hapus invoice ini?');">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-100 text-gray-500 text-xs font-semibold cursor-not-allowed">View</span>
                                @endif
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td class="py-3 pr-4 text-gray-500" colspan="5">Belum ada riwayat billing.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

