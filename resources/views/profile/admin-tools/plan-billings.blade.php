@extends('profile.layout')

@section('profile-page-title', 'Plan & Billings')
@section('profile-page-subtitle', 'Lihat paket langganan, pengaturan billing, dan riwayat transaksi')

@section('profile-content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden p-6">
        <h2 class="text-base font-semibold text-gray-900">Subscription Plan</h2>

        <div class="mt-4 border border-gray-200 rounded-xl overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 px-4 py-4 border-b border-gray-200">
                <div class="text-sm text-gray-600">Current Plan</div>
                <div class="text-sm font-semibold text-emerald-600 md:text-right">Pro+</div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 px-4 py-4 border-b border-gray-200">
                <div class="text-sm text-gray-600">Billed monthly, usage reset date</div>
                <div class="text-sm font-medium text-gray-900 md:text-right">{{ now()->format('Y/m/d H:i') }}</div>
            </div>
            <div class="flex items-center justify-between px-4 py-4">
                <div class="text-sm text-gray-600">Upgrade plan</div>
                <button type="button" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800 transition">
                    Upgrade plan
                </button>
            </div>
        </div>

        <div class="mt-3 flex items-center justify-between text-sm">
            <a href="#" class="text-gray-500 hover:text-gray-700">View all plans & features</a>
            <a href="#" class="text-gray-500 hover:text-gray-700">Redeem Promo Code</a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden p-6">
        <h2 class="text-base font-semibold text-gray-900">On-Demand Usage</h2>
        <div class="mt-4 border border-gray-200 rounded-xl px-4 py-4 flex items-center justify-between gap-4">
            <p class="text-sm text-gray-700">
                Setelah kuota bulanan habis, On-Demand Usage memungkinkan penggunaan berlanjut.
            </p>
            <label class="inline-flex items-center cursor-pointer shrink-0">
                <input type="checkbox" class="sr-only peer">
                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
            </label>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden p-6">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-base font-semibold text-gray-900">Billing History</h2>
            <button type="button" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                Payment settings
            </button>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500">
                        <th class="text-left py-3 pr-4 font-medium">Name</th>
                        <th class="text-left py-3 pr-4 font-medium">Amount</th>
                        <th class="text-left py-3 pr-4 font-medium">Date</th>
                        <th class="text-left py-3 font-medium">Operation</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100">
                        <td class="py-3 pr-4 font-medium text-gray-900">Pro+ plan</td>
                        <td class="py-3 pr-4 text-gray-700">$30</td>
                        <td class="py-3 pr-4 text-gray-700">{{ now()->subMonth()->format('Y/m/d H:i') }}</td>
                        <td class="py-3"><a href="#" class="text-indigo-600 hover:text-indigo-800">Obtain invoice</a></td>
                    </tr>
                    <tr class="border-b border-gray-100">
                        <td class="py-3 pr-4 font-medium text-gray-900">Pro+ plan (Renew Failed)</td>
                        <td class="py-3 pr-4 text-gray-700">$0</td>
                        <td class="py-3 pr-4 text-gray-700">{{ now()->subMonth()->addDay()->format('Y/m/d H:i') }}</td>
                        <td class="py-3 text-gray-500">No invoice</td>
                    </tr>
                    <tr>
                        <td class="py-3 pr-4 font-medium text-gray-900">Pro+ plan</td>
                        <td class="py-3 pr-4 text-gray-700">$30</td>
                        <td class="py-3 pr-4 text-gray-700">{{ now()->subMonths(2)->format('Y/m/d H:i') }}</td>
                        <td class="py-3"><a href="#" class="text-indigo-600 hover:text-indigo-800">Obtain invoice</a></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-3 text-sm text-gray-500">
            Only latest 3 records. <a href="#" class="text-gray-700 hover:text-gray-900">View more billing history</a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-red-100 overflow-hidden p-6">
        <h2 class="text-base font-semibold text-red-700">Danger Zone</h2>
        <div class="mt-4 border border-red-200 rounded-xl px-4 py-4 flex items-center justify-between gap-4">
            <p class="text-sm text-red-700">
                Jika dibatalkan sekarang, akses paket tetap aktif sampai masa langganan berakhir.
            </p>
            <button type="button" class="inline-flex items-center px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition">
                Unsubscribe
            </button>
        </div>
    </div>
</div>
@endsection
