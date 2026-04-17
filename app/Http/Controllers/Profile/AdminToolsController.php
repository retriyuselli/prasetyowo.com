<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyLogo;
use App\Models\CompanySubscription;
use App\Models\CompanySubscriptionBilling;
use App\Models\BankReconciliationItem;
use App\Models\BankStatement;
use App\Models\BankTransaction;
use App\Models\DocumentCategory;
use App\Models\Documentation;
use App\Models\Expense;
use App\Models\NotaDinas;
use App\Models\NotaDinasDetail;
use App\Models\Order;
use App\Models\Sop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AdminToolsController extends Controller
{
    public function index()
    {
        return view('profile.admin-tools.index', [
            'usersCount' => User::query()->count(),
            'rolesCount' => Role::query()->count(),
            'companiesCount' => Company::query()->count(),
            'logosCount' => CompanyLogo::query()->count(),
            'sopsCount' => Sop::query()->count(),
            'documentationsCount' => Documentation::query()->count(),
            'documentCategoriesCount' => DocumentCategory::query()->count(),
            'projectsCount' => Order::query()->count(),
            'notaDinasCount' => NotaDinas::query()->count(),
            'notaDinasDetailsCount' => NotaDinasDetail::query()->count(),
            'bankStatementsCount' => BankStatement::query()->count(),
        ]);
    }

    public function users(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $usersQuery = User::query()
            ->with('roles')
            ->orderBy('name');

        if ($q !== '') {
            $usersQuery->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        return view('profile.admin-tools.users', [
            'q' => $q,
            'users' => $usersQuery->paginate(20)->withQueryString(),
        ]);
    }

    public function roles()
    {
        return view('profile.admin-tools.roles', [
            'roles' => Role::query()->withCount('permissions')->orderBy('name')->get(),
        ]);
    }

    public function company()
    {
        return view('profile.admin-tools.company', [
            'company' => Company::query()->latest('id')->first(),
        ]);
    }

    public function branding()
    {
        return view('profile.admin-tools.branding', [
            'logos' => CompanyLogo::query()->ordered()->paginate(20),
        ]);
    }

    public function brandingDestroy(CompanyLogo $logo)
    {
        $logo->delete();

        return redirect()
            ->route('profile.admin-tools.branding')
            ->with('success', 'Logo berhasil dihapus.');
    }

    public function sops(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $sopsQuery = Sop::query()
            ->with('category')
            ->orderByDesc('updated_at');

        if ($q !== '') {
            $sopsQuery->search($q);
        }

        return view('profile.admin-tools.sops', [
            'q' => $q,
            'sops' => $sopsQuery->paginate(15)->withQueryString(),
        ]);
    }

    public function documentations(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $docsQuery = Documentation::query()
            ->with('category')
            ->orderBy('order')
            ->orderBy('title');

        if ($q !== '') {
            $docsQuery->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('keywords', 'like', "%{$q}%");
            });
        }

        return view('profile.admin-tools.documentations', [
            'q' => $q,
            'docs' => $docsQuery->paginate(20)->withQueryString(),
        ]);
    }

    public function documentCategories()
    {
        return view('profile.admin-tools.document-categories', [
            'categories' => DocumentCategory::query()
                ->with('parent')
                ->orderBy('type')
                ->orderBy('name')
                ->paginate(30),
        ]);
    }

    public function projects(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $period = (string) $request->get('period', 'all');
        $month = trim((string) $request->get('month', ''));
        $monthYear = null;
        $monthMonth = null;

        if ($month !== '' && preg_match('/^\d{4}-\d{2}$/', $month) === 1) {
            [$monthYear, $monthMonth] = array_map('intval', explode('-', $month, 2));
            if ($monthYear > 0 && $monthMonth >= 1 && $monthMonth <= 12) {
                $period = 'custom';
            } else {
                $monthYear = null;
                $monthMonth = null;
                $month = '';
            }
        } else {
            $month = '';
        }

        $projectsQuery = Order::query()
            ->with([
                'prospect',
                'user',
                'employee',
                'items:id,order_id,product_id,quantity,unit_price',
                'dataPengeluaran:id,order_id,amount',
            ])
            ->orderByDesc('created_at');

        if ($q !== '') {
            $projectsQuery->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('number', 'like', "%{$q}%")
                    ->orWhere('no_kontrak', 'like', "%{$q}%");
            });
        }

        if ($period === 'year') {
            $projectsQuery->whereHas('prospect', function ($q) {
                $q->whereYear('date_resepsi', now()->year)
                  ->orWhereYear('date_akad', now()->year)
                  ->orWhereYear('date_lamaran', now()->year);
            });
        } elseif ($period === 'month') {
            $projectsQuery->whereHas('prospect', function ($q) {
                $q->where(function ($m) {
                    $m->whereYear('date_resepsi', now()->year)
                      ->whereMonth('date_resepsi', now()->month);
                })->orWhere(function ($m) {
                    $m->whereYear('date_akad', now()->year)
                      ->whereMonth('date_akad', now()->month);
                })->orWhere(function ($m) {
                    $m->whereYear('date_lamaran', now()->year)
                      ->whereMonth('date_lamaran', now()->month);
                });
            });
        } elseif ($period === 'custom' && $monthYear !== null && $monthMonth !== null) {
            $projectsQuery->whereHas('prospect', function ($q) use ($monthYear, $monthMonth) {
                $q->where(function ($m) use ($monthYear, $monthMonth) {
                    $m->whereYear('date_resepsi', $monthYear)
                      ->whereMonth('date_resepsi', $monthMonth);
                })->orWhere(function ($m) use ($monthYear, $monthMonth) {
                    $m->whereYear('date_akad', $monthYear)
                      ->whereMonth('date_akad', $monthMonth);
                })->orWhere(function ($m) use ($monthYear, $monthMonth) {
                    $m->whereYear('date_lamaran', $monthYear)
                      ->whereMonth('date_lamaran', $monthMonth);
                });
            });
        } else {
            $period = 'all';
        }

        $projectsCount = (int) (clone $projectsQuery)->count();
        $grandTotalSum = (int) (clone $projectsQuery)->sum('grand_total');
        $orderIdsQuery = (clone $projectsQuery)->reorder()->select('id');
        $expensesSum = (int) Expense::query()->whereIn('order_id', $orderIdsQuery)->sum('amount');
        $profitSum = $grandTotalSum - $expensesSum;
        $profitAvg = $projectsCount > 0 ? (int) round($profitSum / $projectsCount) : 0;

        return view('profile.admin-tools.projects.index', [
            'q' => $q,
            'projects' => $projectsQuery->paginate(20)->withQueryString(),
            'projectsCount' => $projectsCount,
            'grandTotalSum' => $grandTotalSum,
            'expensesSum' => $expensesSum,
            'profitSum' => $profitSum,
            'profitAvg' => $profitAvg,
            'period' => $period,
            'month' => $month,
        ]);
    }

    public function project(Order $order)
    {
        $order->loadMissing([
            'prospect',
            'user',
            'employee',
            'items.product',
            'dataPengeluaran.vendor',
            'dataPengeluaran.paymentMethod',
            'dataPengeluaran.notaDinasDetail.vendor',
            'dataPengeluaran.notaDinasDetail.notaDinas',
        ]);

        return view('profile.admin-tools.projects.show', [
            'order' => $order,
        ]);
    }

    public function projectProduct(Order $order)
    {
        $order->loadMissing([
            'items.product.category',
            'items.product.items.vendor',
            'items.product.pengurangans',
            'items.product.penambahanHarga.vendor',
        ]);

        $products = ($order->items ?? collect())
            ->pluck('product')
            ->filter()
            ->unique('id')
            ->values();

        return view('profile.admin-tools.projects.product', [
            'order' => $order,
            'products' => $products,
        ]);
    }

    public function notaDinas(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));
        $month = trim((string) $request->get('month', ''));
        $monthYear = null;
        $monthMonth = null;

        if ($month !== '' && preg_match('/^\d{4}-\d{2}$/', $month) === 1) {
            [$monthYear, $monthMonth] = array_map('intval', explode('-', $month, 2));
            if ($monthYear <= 0 || $monthMonth < 1 || $monthMonth > 12) {
                $monthYear = null;
                $monthMonth = null;
                $month = '';
            }
        } else {
            $month = '';
        }

        $notaDinasQuery = NotaDinas::query()
            ->with(['pengirim:id,name', 'penerima:id,name', 'approver:id,name'])
            ->withCount('details')
            ->withCount(['details as details_paid_count' => fn ($q) => $q->where('status_invoice', 'sudah_dibayar')])
            ->withSum('details as details_sum', 'jumlah_transfer')
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($q !== '') {
            $notaDinasQuery->where(function ($sub) use ($q) {
                $sub->where('no_nd', 'like', "%{$q}%")
                    ->orWhere('hal', 'like', "%{$q}%")
                    ->orWhere('catatan', 'like', "%{$q}%");
            });
        }

        if ($status !== '') {
            $notaDinasQuery->where('status', $status);
        }

        if ($monthYear !== null && $monthMonth !== null) {
            $notaDinasQuery->whereYear('tanggal', $monthYear)->whereMonth('tanggal', $monthMonth);
        }

        $notaDinasCount = (int) (clone $notaDinasQuery)->count();
        $notaDinasIdsQuery = (clone $notaDinasQuery)->reorder()->select('id');
        $detailsCount = (int) NotaDinasDetail::query()->whereIn('nota_dinas_id', $notaDinasIdsQuery)->count();
        $detailsPaidCount = (int) NotaDinasDetail::query()
            ->whereIn('nota_dinas_id', $notaDinasIdsQuery)
            ->where('status_invoice', 'sudah_dibayar')
            ->count();
        $detailsSum = (float) NotaDinasDetail::query()->whereIn('nota_dinas_id', $notaDinasIdsQuery)->sum('jumlah_transfer');

        $statusSummary = NotaDinas::query()
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status')
            ->toArray();

        return view('profile.admin-tools.nota-dinas.index', [
            'q' => $q,
            'status' => $status,
            'month' => $month,
            'notaDinas' => $notaDinasQuery->paginate(20)->withQueryString(),
            'notaDinasCount' => $notaDinasCount,
            'detailsCount' => $detailsCount,
            'detailsPaidCount' => $detailsPaidCount,
            'detailsSum' => $detailsSum,
            'statusSummary' => $statusSummary,
        ]);
    }

    public function notaDinasShow(NotaDinas $notaDinas, Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $statusInvoice = trim((string) $request->get('status_invoice', ''));

        $notaDinas->loadMissing(['pengirim:id,name', 'penerima:id,name', 'approver:id,name']);

        $detailsQuery = $notaDinas->details()
            ->with(['vendor:id,name', 'order:id,name'])
            ->orderByDesc('id');

        if ($q !== '') {
            $detailsQuery->where(function ($sub) use ($q) {
                $sub->where('keperluan', 'like', "%{$q}%")
                    ->orWhere('event', 'like', "%{$q}%")
                    ->orWhere('invoice_number', 'like', "%{$q}%")
                    ->orWhereHas('vendor', fn ($v) => $v->where('name', 'like', "%{$q}%"));
            });
        }

        if ($statusInvoice !== '') {
            $detailsQuery->where('status_invoice', $statusInvoice);
        }

        $detailsCount = (int) (clone $detailsQuery)->count();
        $detailsPaidCount = (int) (clone $detailsQuery)->where('status_invoice', 'sudah_dibayar')->count();
        $detailsSum = (float) (clone $detailsQuery)->sum('jumlah_transfer');

        $paymentStageSummary = (clone $detailsQuery)
            ->reorder()
            ->selectRaw('payment_stage, COUNT(*) as c')
            ->groupBy('payment_stage')
            ->pluck('c', 'payment_stage')
            ->toArray();

        return view('profile.admin-tools.nota-dinas.show', [
            'notaDinas' => $notaDinas,
            'q' => $q,
            'statusInvoice' => $statusInvoice,
            'details' => $detailsQuery->paginate(30)->withQueryString(),
            'detailsCount' => $detailsCount,
            'detailsPaidCount' => $detailsPaidCount,
            'detailsSum' => $detailsSum,
            'paymentStageSummary' => $paymentStageSummary,
        ]);
    }

    public function bankStatements()
    {
        $baseQuery = BankStatement::query();

        $totalCount = (int) (clone $baseQuery)->count();
        $pendingCount = (int) (clone $baseQuery)->where('status', 'pending')->count();
        $processingCount = (int) (clone $baseQuery)->where('status', 'processing')->count();
        $parsedCount = (int) (clone $baseQuery)->where('status', 'parsed')->count();
        $failedCount = (int) (clone $baseQuery)->where('status', 'failed')->count();

        $reconUploadedCount = (int) (clone $baseQuery)->where('reconciliation_status', 'uploaded')->count();
        $reconProcessingCount = (int) (clone $baseQuery)->where('reconciliation_status', 'processing')->count();
        $reconCompletedCount = (int) (clone $baseQuery)->where('reconciliation_status', 'completed')->count();
        $reconFailedCount = (int) (clone $baseQuery)->where('reconciliation_status', 'failed')->count();

        $latestStatements = BankStatement::query()
            ->with('paymentMethod')
            ->withCount(['transactions', 'reconciliationItems'])
            ->orderByDesc('period_end')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $monthlySummary = BankStatement::query()
            ->selectRaw("DATE_FORMAT(period_end, '%Y-%m') as ym")
            ->selectRaw('COUNT(*) as statements_count')
            ->selectRaw('SUM(COALESCE(tot_debit, 0)) as tot_debit_sum')
            ->selectRaw('SUM(COALESCE(tot_credit, 0)) as tot_credit_sum')
            ->selectRaw("SUM(status = 'failed') as failed_count")
            ->selectRaw("SUM(reconciliation_status = 'failed') as recon_failed_count")
            ->whereNotNull('period_end')
            ->groupBy('ym')
            ->orderByDesc('ym')
            ->limit(12)
            ->get();

        return view('profile.admin-tools.bank-statements.index', [
            'totalCount' => $totalCount,
            'pendingCount' => $pendingCount,
            'processingCount' => $processingCount,
            'parsedCount' => $parsedCount,
            'failedCount' => $failedCount,
            'reconUploadedCount' => $reconUploadedCount,
            'reconProcessingCount' => $reconProcessingCount,
            'reconCompletedCount' => $reconCompletedCount,
            'reconFailedCount' => $reconFailedCount,
            'latestStatements' => $latestStatements,
            'monthlySummary' => $monthlySummary,
        ]);
    }

    public function bankStatementsGuide()
    {
        return view('profile.admin-tools.bank-statements.guide');
    }

    public function bankStatementsFailed()
    {
        $statements = BankStatement::query()
            ->with('paymentMethod')
            ->withCount(['transactions', 'reconciliationItems'])
            ->where(function ($q) {
                $q->where('status', 'failed')
                    ->orWhere('reconciliation_status', 'failed');
            })
            ->orderByDesc('period_end')
            ->orderByDesc('id')
            ->paginate(20);

        return view('profile.admin-tools.bank-statements.failed', [
            'statements' => $statements,
        ]);
    }

    public function bankStatementsReconciliation()
    {
        $statements = BankStatement::query()
            ->with('paymentMethod')
            ->withCount(['transactions', 'reconciliationItems'])
            ->where(function ($q) {
                $q->whereIn('reconciliation_status', ['processing', 'completed', 'failed'])
                    ->orWhereHas('reconciliationItems');
            })
            ->orderByDesc('period_end')
            ->orderByDesc('id')
            ->paginate(20);

        return view('profile.admin-tools.bank-statements.reconciliation', [
            'statements' => $statements,
        ]);
    }

    public function bankStatementShow(BankStatement $bankStatement)
    {
        $bankStatement->loadMissing(['paymentMethod']);

        $transactions = BankTransaction::query()
            ->where('bank_statement_id', $bankStatement->id)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $reconciliationItems = BankReconciliationItem::query()
            ->where('bank_reconciliation_id', $bankStatement->id)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return view('profile.admin-tools.bank-statements.show', [
            'bankStatement' => $bankStatement,
            'transactions' => $transactions,
            'reconciliationItems' => $reconciliationItems,
        ]);
    }

    public function helpCenter()
    {
        return view('profile.admin-tools.help-center');
    }

    public function planBillings()
    {
        $user = Auth::user();
        $canEdit = $user && strtolower((string) $user->email) === 'ramadhona.utama@gmail.com';

        $company = Company::query()->latest('id')->first();
        $subscription = null;
        $billings = collect();

        $latestDpBilling = null;
        $latestPelunasanBilling = null;
        $planPrice = 8500000;
        $billingTotalActive = 0;
        $billingTotalPaid = 0;
        $billingRemaining = 0;

        if ($company) {
            $subscription = CompanySubscription::query()->where('company_id', $company->id)->first();

            if ($subscription) {
                $planPrice = (int) ($subscription->plan_price ?? 8500000);
                $billingTotalActive = (int) $subscription->billings()->where('status', '!=', 'canceled')->sum('amount');

                $billings = $subscription->billings()->latest('billed_at')->latest('id')->limit(3)->get();
                $latestDpBilling = $subscription->billings()->where('name', 'Down Payment')->latest('billed_at')->latest('id')->first();
                $latestPelunasanBilling = $subscription->billings()->where('name', 'Pelunasan')->latest('billed_at')->latest('id')->first();

                $dpAmount = (int) ($latestDpBilling?->amount ?? 0);
                $pelunasanAmount = (int) ($latestPelunasanBilling?->amount ?? 0);
                $billingTotalPaid = $dpAmount + $pelunasanAmount;
                $billingRemaining = max(0, $planPrice - $billingTotalPaid);
            }
        }

        return view('profile.admin-tools.plan-billings.index', [
            'canEdit' => $canEdit,
            'subscription' => $subscription,
            'billings' => $billings,
            'latestDpBilling' => $latestDpBilling,
            'latestPelunasanBilling' => $latestPelunasanBilling,
            'planPrice' => $planPrice,
            'billingTotalActive' => $billingTotalActive,
            'billingTotalPaid' => $billingTotalPaid,
            'billingRemaining' => $billingRemaining,
        ]);
    }

    public function planBillingsCreate()
    {
        $user = Auth::user();
        abort_unless($user && strtolower((string) $user->email) === 'ramadhona.utama@gmail.com', 403);

        $company = Company::query()->latest('id')->first();
        abort_unless((bool) $company, 404);

        CompanySubscription::query()->firstOrCreate(
            ['company_id' => $company->id],
            [
                'plan_code' => 'hastana',
                'plan_name' => 'Anggota Hastana',
                'plan_price' => 8500000,
                'billing_cycle' => '2_years',
                'usage_reset_at' => now(),
                'on_demand_enabled' => false,
                'status' => 'active',
            ]
        );

        return redirect()->route('profile.admin-tools.plan-billings.edit');
    }

    public function planBillingsEdit()
    {
        $user = Auth::user();
        abort_unless($user && strtolower((string) $user->email) === 'ramadhona.utama@gmail.com', 403);

        $company = Company::query()->latest('id')->first();
        abort_unless((bool) $company, 404);

        $subscription = CompanySubscription::query()->where('company_id', $company->id)->first();
        if (! $subscription) {
            return redirect()
                ->route('profile.admin-tools.plan-billings')
                ->with('error', 'Subscription belum ada. Silakan klik Create terlebih dahulu.');
        }

        return view('profile.admin-tools.plan-billings.edit', [
            'subscription' => $subscription,
        ]);
    }

    public function planBillingsBillingSettings()
    {
        $user = Auth::user();
        abort_unless($user && strtolower((string) $user->email) === 'ramadhona.utama@gmail.com', 403);

        $company = Company::query()->latest('id')->first();
        abort_unless((bool) $company, 404);

        $subscription = CompanySubscription::query()->where('company_id', $company->id)->first();
        if (! $subscription) {
            return redirect()
                ->route('profile.admin-tools.plan-billings')
                ->with('error', 'Subscription belum ada. Silakan klik Create terlebih dahulu.');
        }

        $latestDpBilling = $subscription->billings()->where('name', 'Down Payment')->latest('billed_at')->latest('id')->first();
        $latestPelunasanBilling = $subscription->billings()->where('name', 'Pelunasan')->latest('billed_at')->latest('id')->first();

        $planPrice = (int) ($subscription->plan_price ?? 8500000);
        $billingTotalActive = (int) $subscription->billings()->where('status', '!=', 'canceled')->sum('amount');
        $billingTotalPaid = (int) ($latestDpBilling?->amount ?? 0) + (int) ($latestPelunasanBilling?->amount ?? 0);
        $billingRemaining = max(0, $planPrice - $billingTotalPaid);

        return view('profile.admin-tools.plan-billings.billing-settings', [
            'subscription' => $subscription,
            'latestDpBilling' => $latestDpBilling,
            'latestPelunasanBilling' => $latestPelunasanBilling,
            'planPrice' => $planPrice,
            'billingTotalActive' => $billingTotalActive,
            'billingTotalPaid' => $billingTotalPaid,
            'billingRemaining' => $billingRemaining,
        ]);
    }

    public function planBillingsUpdate(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && strtolower((string) $user->email) === 'ramadhona.utama@gmail.com', 403);

        $company = Company::query()->latest('id')->first();
        abort_unless((bool) $company, 404);

        $subscription = CompanySubscription::query()->where('company_id', $company->id)->first();
        abort_unless((bool) $subscription, 404);

        $validated = $request->validate([
            'plan_code' => ['required', 'in:hastana,non_hastana'],
            'usage_reset_at' => ['nullable', 'date'],
            'on_demand_enabled' => ['nullable', 'boolean'],
            'status' => ['required', 'in:active,inactive,canceled'],
        ]);

        $planName = $validated['plan_code'] === 'hastana' ? 'Anggota Hastana' : 'Non Hastana';
        $planPrice = $validated['plan_code'] === 'hastana' ? 8500000 : 10000000;

        $billingTotalActive = (int) $subscription->billings()->where('status', '!=', 'canceled')->sum('amount');
        if ($billingTotalActive > (int) $planPrice) {
            throw ValidationException::withMessages([
                'plan_code' => 'Total invoice saat ini (Rp ' . number_format($billingTotalActive, 0, ',', '.') . ') melebihi nominal plan yang dipilih (Rp ' . number_format((int) $planPrice, 0, ',', '.') . '). Sesuaikan invoice atau batalkan invoice terlebih dahulu.',
            ]);
        }

        $subscription->update([
            'plan_code' => $validated['plan_code'],
            'plan_name' => $planName,
            'plan_price' => $planPrice,
            'billing_cycle' => '2_years',
            'usage_reset_at' => $validated['usage_reset_at'] ?? null,
            'on_demand_enabled' => (bool) ($validated['on_demand_enabled'] ?? false),
            'status' => $validated['status'],
            'canceled_at' => $validated['status'] === 'canceled' ? now() : null,
        ]);

        return redirect()
            ->route('profile.admin-tools.plan-billings')
            ->with('success', 'Plan & Billings berhasil diperbarui.');
    }

    public function planBillingsInvoiceView(CompanySubscriptionBilling $billing)
    {
        $user = Auth::user();
        abort_unless((bool) $user, 403);

        $company = Company::query()->latest('id')->first();
        abort_unless((bool) $company, 404);

        $subscription = CompanySubscription::query()->where('company_id', $company->id)->first();
        abort_unless((bool) $subscription && (int) $billing->company_subscription_id === (int) $subscription->id, 404);

        $dpPaid = $subscription->billings()->where('name', 'Down Payment')->where('status', 'paid')->exists();
        $pelunasanPaid = $subscription->billings()->where('name', 'Pelunasan')->where('status', 'paid')->exists();
        $dpBilling = $subscription->billings()
            ->where('name', 'Down Payment')
            ->where('status', '!=', 'canceled')
            ->latest('billed_at')
            ->latest('id')
            ->first();
        $pelunasanBilling = $subscription->billings()
            ->where('name', 'Pelunasan')
            ->where('status', '!=', 'canceled')
            ->latest('billed_at')
            ->latest('id')
            ->first();

        $dpAmount = (int) ($dpBilling?->amount ?? 0);
        $pelunasanAmount = (int) ($pelunasanBilling?->amount ?? 0);

        $totalPembayaran = $dpAmount + $pelunasanAmount;
        $planPrice = (int) ($subscription->plan_price ?? 0);
        $isSubscriptionPaidByAmount = $planPrice > 0 && $totalPembayaran === $planPrice;

        $isSubscriptionPaid = $isSubscriptionPaidByAmount || ($dpPaid && $pelunasanPaid);
        $isSubscriptionPartiallyPaid = ! $isSubscriptionPaid && ($totalPembayaran > 0);

        $pdf = app('dompdf.wrapper')->loadView('profile.admin-tools.plan-billings.invoice-subscription', [
            'company' => $company,
            'subscription' => $subscription,
            'billing' => $billing,
            'isSubscriptionPaid' => $isSubscriptionPaid,
            'isSubscriptionPartiallyPaid' => $isSubscriptionPartiallyPaid,
            'isSubscriptionPaidByAmount' => $isSubscriptionPaidByAmount,
            'dpBilling' => $dpBilling,
            'pelunasanBilling' => $pelunasanBilling,
        ]);

        return $pdf->stream('invoice-subscription-' . $billing->id . '.pdf');
    }

    public function planBillingsBillingStore(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && strtolower((string) $user->email) === 'ramadhona.utama@gmail.com', 403);

        $company = Company::query()->latest('id')->first();
        abort_unless((bool) $company, 404);

        $subscription = CompanySubscription::query()->where('company_id', $company->id)->first();
        if (! $subscription) {
            return redirect()
                ->route('profile.admin-tools.plan-billings')
                ->with('error', 'Subscription belum ada. Silakan klik Create terlebih dahulu.');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:dp,pelunasan'],
            'amount' => ['required', 'integer', 'min:0'],
            'billed_at' => ['nullable', 'date'],
        ]);

        $planPrice = (int) ($subscription->plan_price ?? 8500000);
        $billingTotalActive = (int) $subscription->billings()->where('status', '!=', 'canceled')->sum('amount');
        $amount = (int) $validated['amount'];
        $totalAfter = $billingTotalActive + $amount;
        if ($totalAfter > $planPrice) {
            throw ValidationException::withMessages([
                'amount' => 'Total nominal invoice (Rp ' . number_format($totalAfter, 0, ',', '.') . ') melebihi nominal plan (Rp ' . number_format($planPrice, 0, ',', '.') . ').',
            ]);
        }
        if ($validated['type'] === 'pelunasan' && $totalAfter !== $planPrice) {
            throw ValidationException::withMessages([
                'amount' => 'Jika membuat invoice Pelunasan, total DP + Pelunasan harus sama dengan nominal plan (Rp ' . number_format($planPrice, 0, ',', '.') . ').',
            ]);
        }

        if ($validated['type'] === 'dp') {
            $name = 'Down Payment';
        } elseif ($validated['type'] === 'pelunasan') {
            $name = 'Pelunasan';
        } else {
            abort(404);
        }

        $existingBilling = $subscription->billings()
            ->where('name', $name)
            ->where('status', '!=', 'canceled')
            ->latest('billed_at')
            ->latest('id')
            ->first();

        if ($existingBilling && ($existingBilling->status ?? '') === 'paid') {
            throw ValidationException::withMessages([
                'type' => 'Invoice ' . $name . ' sudah LUNAS. Tidak bisa menambahkan nominal lagi.',
            ]);
        }

        if ($existingBilling) {
            $existingBilling->update([
                'amount' => (int) $existingBilling->amount + (int) $validated['amount'],
                'billed_at' => $validated['billed_at'] ?? $existingBilling->billed_at ?? now(),
                'status' => 'unpaid',
            ]);
        } else {
            $subscription->billings()->create([
                'name' => $name,
                'amount' => (int) $validated['amount'],
                'currency' => 'IDR',
                'billed_at' => $validated['billed_at'] ?? now(),
                'status' => 'unpaid',
                'invoice_url' => null,
            ]);
        }

        return redirect()
            ->route('profile.admin-tools.plan-billings.billing-settings')
            ->with('success', 'Invoice ' . $name . ' berhasil disimpan.');
    }

    public function planBillingsBillingMarkPaid(CompanySubscriptionBilling $billing)
    {
        $user = Auth::user();
        abort_unless($user && strtolower((string) $user->email) === 'ramadhona.utama@gmail.com', 403);

        $company = Company::query()->latest('id')->first();
        abort_unless((bool) $company, 404);

        $subscription = CompanySubscription::query()->where('company_id', $company->id)->first();
        abort_unless((bool) $subscription && (int) $billing->company_subscription_id === (int) $subscription->id, 404);

        $billing->update([
            'status' => 'paid',
        ]);

        return redirect()
            ->route('profile.admin-tools.plan-billings.billing-settings')
            ->with('success', 'Invoice berhasil ditandai lunas.');
    }

    public function planBillingsBillingEdit(CompanySubscriptionBilling $billing)
    {
        $user = Auth::user();
        abort_unless($user && strtolower((string) $user->email) === 'ramadhona.utama@gmail.com', 403);

        $company = Company::query()->latest('id')->first();
        abort_unless((bool) $company, 404);

        $subscription = CompanySubscription::query()->where('company_id', $company->id)->first();
        abort_unless((bool) $subscription && (int) $billing->company_subscription_id === (int) $subscription->id, 404);

        return view('profile.admin-tools.plan-billings.invoice-edit', [
            'subscription' => $subscription,
            'billing' => $billing,
            'planPrice' => (int) ($subscription->plan_price ?? 8500000),
            'billingTotalPaid' => (int) ($subscription->billings()->where('name', 'Down Payment')->latest('billed_at')->latest('id')->value('amount') ?? 0)
                + (int) ($subscription->billings()->where('name', 'Pelunasan')->latest('billed_at')->latest('id')->value('amount') ?? 0),
        ]);
    }

    public function planBillingsBillingUpdate(Request $request, CompanySubscriptionBilling $billing)
    {
        $user = Auth::user();
        abort_unless($user && strtolower((string) $user->email) === 'ramadhona.utama@gmail.com', 403);

        $company = Company::query()->latest('id')->first();
        abort_unless((bool) $company, 404);

        $subscription = CompanySubscription::query()->where('company_id', $company->id)->first();
        abort_unless((bool) $subscription && (int) $billing->company_subscription_id === (int) $subscription->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:0'],
            'billed_at' => ['nullable', 'date'],
            'status' => ['required', 'in:unpaid,paid,canceled'],
        ]);

        $planPrice = (int) ($subscription->plan_price ?? 8500000);
        $otherTotalActive = (int) $subscription->billings()
            ->where('status', '!=', 'canceled')
            ->where('id', '!=', $billing->id)
            ->sum('amount');
        $amount = (int) $validated['amount'];
        $totalAfter = $otherTotalActive + ($validated['status'] === 'canceled' ? 0 : $amount);

        if ($totalAfter > $planPrice) {
            throw ValidationException::withMessages([
                'amount' => 'Total nominal invoice (Rp ' . number_format($totalAfter, 0, ',', '.') . ') melebihi nominal plan (Rp ' . number_format($planPrice, 0, ',', '.') . ').',
            ]);
        }

        if ($validated['name'] === 'Pelunasan' && $validated['status'] !== 'canceled' && $totalAfter !== $planPrice) {
            throw ValidationException::withMessages([
                'amount' => 'Jika ada invoice Pelunasan, total DP + Pelunasan harus sama dengan nominal plan (Rp ' . number_format($planPrice, 0, ',', '.') . ').',
            ]);
        }

        $billing->update([
            'name' => $validated['name'],
            'amount' => (int) $validated['amount'],
            'billed_at' => $validated['billed_at'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('profile.admin-tools.plan-billings.billing-settings')
            ->with('success', 'Invoice berhasil diperbarui.');
    }

    public function planBillingsBillingDestroy(CompanySubscriptionBilling $billing)
    {
        $user = Auth::user();
        abort_unless($user && strtolower((string) $user->email) === 'ramadhona.utama@gmail.com', 403);

        $company = Company::query()->latest('id')->first();
        abort_unless((bool) $company, 404);

        $subscription = CompanySubscription::query()->where('company_id', $company->id)->first();
        abort_unless((bool) $subscription && (int) $billing->company_subscription_id === (int) $subscription->id, 404);

        $billing->delete();

        return redirect()
            ->route('profile.admin-tools.plan-billings.billing-settings')
            ->with('success', 'Invoice berhasil dihapus.');
    }
}
