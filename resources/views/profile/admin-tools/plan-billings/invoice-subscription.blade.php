<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice Subscription</title>
    <style>
        @page { margin: 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .muted { color: #6b7280; }
        .title { font-size: 18px; font-weight: 700; margin: 0 0 6px; }
        .row { width: 100%; }
        .table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        .table th, .table td { border: 1px solid #e5e7eb; padding: 10px; vertical-align: top; }
        .table th { background: #f9fafb; text-align: left; }
        .right { text-align: right; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 999px; font-size: 11px; border: 0; }
    </style>
</head>
<body>
    <div class="row">
        <div class="title">INVOICE</div>
        <div class="muted">Subscription</div>
    </div>

    <table class="table">
        <tr>
            <th style="width: 50%;">Ditagihkan Kepada</th>
            <th style="width: 50%;">Detail Invoice</th>
        </tr>
        <tr>
            <td>
                <div><b>{{ $company->company_name ?? 'Perusahaan' }}</b></div>
                @if (!empty($company->address))
                    <div class="muted">{{ $company->address }}</div>
                @endif
                @php
                    $cityLineParts = array_filter([$company->city ?? null, $company->province ?? null, $company->postal_code ?? null]);
                    $cityLine = implode(', ', $cityLineParts);
                @endphp
                @if (!empty($cityLine))
                    <div class="muted">{{ $cityLine }}</div>
                @endif
            </td>
            <td>
                <div><span class="muted">Invoice #</span> <b>{{ $billing->id }}</b></div>
                <div><span class="muted">Tanggal</span> <b>{{ ($billing->billed_at ?? $billing->created_at)->format('d/m/Y H:i') }}</b></div>
                <div><span class="muted">Status</span> <b>{{(($billing->status ?? 'unpaid') === 'paid') || !empty($isSubscriptionPaidByAmount) ? 'Lunas' : 'Tagihan' }}</b>
                @php
                    $tanggalMulaiLangganan = $subscription?->usage_reset_at ?? $subscription?->created_at ?? null;
                    $tanggalBerakhirLangganan = $tanggalMulaiLangganan ? $tanggalMulaiLangganan->copy()->addYears(2) : null;
                @endphp
                <div>
                    <span class="muted">Periode Langganan</span>
                    <b>{{ $tanggalMulaiLangganan?->format('d/m/Y') ?? '-' }} - {{ $tanggalBerakhirLangganan?->format('d/m/Y') ?? '-' }}</b>
                </div>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 50%;">Item</th>
                <th style="width: 50%; text-align: right;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $dpAmount = (int) ($dpBilling?->amount ?? 0);
                $pelunasanAmount = (int) ($pelunasanBilling?->amount ?? 0);
                $totalAmount = $dpAmount + $pelunasanAmount;
            @endphp
            <tr>
                <td>
                    <div><b>Down Payment</b></div>
                    <div class="muted">{{ $subscription->plan_name }}</div>
                    <div class="muted">Periode 2 tahun</div>
                </td>
                <td class="right">{{ $dpBilling ? 'Rp '.number_format($dpAmount, 0, ',', '.') : '-' }}</td>
            </tr>
            <tr>
                <td>
                    <div><b>Pelunasan</b></div>
                    <div class="muted">{{ $subscription->plan_name }}</div>
                    <div class="muted">Periode 2 tahun</div>
                </td>
                <td class="right">{{ $pelunasanBilling ? 'Rp '.number_format($pelunasanAmount, 0, ',', '.') : '-' }}</td>
            </tr>
            <tr>
                <td class="right"><b>Total</b></td>
                <td class="right"><b>Rp {{ number_format((int) $totalAmount, 0, ',', '.') }}</b></td>
            </tr>
        </tbody>
    </table>

    <div class="muted" style="margin-top: 14px;">
        Invoice ini dibuat otomatis oleh sistem.
    </div>
</body>
</html>

