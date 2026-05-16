<?php

use App\Http\Controllers\Finance\AccountController;
use App\Http\Controllers\Finance\AssetController;
use App\Http\Controllers\Finance\AuditController;
use App\Http\Controllers\Finance\BankReconController;
use App\Http\Controllers\Finance\BudgetController;
use App\Http\Controllers\Finance\DashboardController as FinanceDashboardController;
use App\Http\Controllers\Finance\ExpenseRequestController;
use App\Http\Controllers\Finance\JournalController;
use App\Http\Controllers\Finance\KasBankController;
use App\Http\Controllers\Finance\KategoriController;
use App\Http\Controllers\Finance\LaporanController as FinanceLaporanController;
use App\Http\Controllers\Finance\LaporanPdfController;
use App\Http\Controllers\Finance\PayableController;
use App\Http\Controllers\Finance\PayrollController;
use App\Http\Controllers\Finance\PeriodeController;
use App\Http\Controllers\Finance\PettyCashController;
use App\Http\Controllers\Finance\SantriFinanceController;
use App\Http\Controllers\Finance\SettingController;
use App\Http\Controllers\Finance\TransaksiController;
use App\Http\Controllers\Finance\VendorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'must.change.pw', 'role:bendahara|admin|kepala_pondok', 'feature:finance'])
    ->prefix('finance')
    ->name('finance.')
    ->group(function () {
        Route::get('/', [FinanceDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [FinanceDashboardController::class, 'index'])->name('dashboard.index');

        // ── Master Data ────────────────────────────────────────
        Route::resource('accounts', AccountController::class)->except(['show']);
        Route::resource('kas-banks', KasBankController::class)->except(['show']);
        Route::resource('kategoris', KategoriController::class)->except(['show']);

        // ── Transaksi ──────────────────────────────────────────
        Route::get('transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('transaksi/masuk', [TransaksiController::class, 'createMasuk'])->name('transaksi.masuk');
        Route::post('transaksi/masuk', [TransaksiController::class, 'storeMasuk'])->name('transaksi.masuk.store');
        Route::get('transaksi/keluar', [TransaksiController::class, 'createKeluar'])->name('transaksi.keluar');
        Route::post('transaksi/keluar', [TransaksiController::class, 'storeKeluar'])->name('transaksi.keluar.store');
        Route::get('transaksi/transfer', [TransaksiController::class, 'createTransfer'])->name('transaksi.transfer');
        Route::post('transaksi/transfer', [TransaksiController::class, 'storeTransfer'])->name('transaksi.transfer.store');
        Route::get('transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
        Route::post('transaksi/{transaksi}/void', [TransaksiController::class, 'void'])->name('transaksi.void');

        // ── Jurnal Manual + Reverse/Void ───────────────────────
        Route::get('journal/manual', [JournalController::class, 'createManual'])->name('journal.manual');
        Route::post('journal/manual', [JournalController::class, 'storeManual'])->name('journal.store-manual');
        Route::get('journal/{journal}', [JournalController::class, 'show'])->name('journal.show');
        Route::post('journal/{journal}/reverse', [JournalController::class, 'reverse'])->name('journal.reverse');
        Route::post('journal/{journal}/void', [JournalController::class, 'void'])->name('journal.void');

        // ── Laporan ────────────────────────────────────────────
        Route::get('laporan/jurnal', [FinanceLaporanController::class, 'jurnalUmum'])->name('laporan.jurnal');
        Route::get('laporan/buku-besar', [FinanceLaporanController::class, 'bukuBesar'])->name('laporan.buku-besar');
        Route::get('laporan/trial-balance', [FinanceLaporanController::class, 'trialBalance'])->name('laporan.trial-balance');
        Route::get('laporan/laba-rugi', [FinanceLaporanController::class, 'labaRugi'])->name('laporan.laba-rugi');
        Route::get('laporan/neraca', [FinanceLaporanController::class, 'neraca'])->name('laporan.neraca');
        Route::get('laporan/arus-kas', [FinanceLaporanController::class, 'arusKas'])->name('laporan.arus-kas');

        Route::get('laporan/pdf/laba-rugi', [LaporanPdfController::class, 'labaRugi'])->name('laporan.pdf.laba-rugi');
        Route::get('laporan/pdf/neraca', [LaporanPdfController::class, 'neraca'])->name('laporan.pdf.neraca');
        Route::get('laporan/pdf/arus-kas', [LaporanPdfController::class, 'arusKas'])->name('laporan.pdf.arus-kas');
        Route::get('laporan/pdf/buku-besar', [LaporanPdfController::class, 'bukuBesar'])->name('laporan.pdf.buku-besar');
        Route::get('laporan/pdf/trial-balance', [LaporanPdfController::class, 'trialBalance'])->name('laporan.pdf.trial-balance');

        // ── Payroll (toggleable) ───────────────────────────────
        Route::middleware('finance.feature:payroll')->group(function () {
            Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
            Route::post('payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
            Route::get('payroll/master-salary', [PayrollController::class, 'masterSalary'])->name('payroll.master-salary');
            Route::get('payroll/master-salary/{ustadz}', [PayrollController::class, 'editSalary'])->name('payroll.salary.edit');
            Route::put('payroll/master-salary/{ustadz}', [PayrollController::class, 'updateSalary'])->name('payroll.salary.update');
            Route::get('payroll/{payroll}', [PayrollController::class, 'show'])->name('payroll.show');
            Route::post('payroll/{payroll}/approve', [PayrollController::class, 'approve'])->name('payroll.approve');
            Route::post('payroll/{payroll}/pay', [PayrollController::class, 'pay'])->name('payroll.pay');
            Route::put('payroll/slip/{slip}', [PayrollController::class, 'updateSlip'])->name('payroll.slip.update');
            Route::get('payroll/slip/{slip}/print', [PayrollController::class, 'slipPdf'])->name('payroll.slip.print');
        });

        // ── Aset (toggleable) ──────────────────────────────────
        Route::middleware('finance.feature:aset')->group(function () {
            Route::post('assets/depreciate', [AssetController::class, 'postDepreciation'])->name('assets.depreciate');
            Route::resource('assets', AssetController::class);
        });

        // ── Vendor & Hutang (toggleable) ───────────────────────
        Route::middleware('finance.feature:payable')->group(function () {
            Route::resource('vendors', VendorController::class)->except(['show']);
            Route::get('payables', [PayableController::class, 'index'])->name('payables.index');
            Route::get('payables/create', [PayableController::class, 'create'])->name('payables.create');
            Route::post('payables', [PayableController::class, 'store'])->name('payables.store');
            Route::get('payables/{payable}', [PayableController::class, 'show'])->name('payables.show');
            Route::post('payables/{payable}/pay', [PayableController::class, 'pay'])->name('payables.pay');
        });

        // ── Petty Cash (toggleable) ────────────────────────────
        Route::middleware('finance.feature:petty_cash')->group(function () {
            Route::resource('petty-cash', PettyCashController::class);
            Route::post('petty-cash/{pettyCash}/top-up', [PettyCashController::class, 'topUp'])->name('petty-cash.top-up');
            Route::post('petty-cash/{pettyCash}/spend', [PettyCashController::class, 'spend'])->name('petty-cash.spend');
        });

        // ── Budgeting (toggleable) ─────────────────────────────
        Route::middleware('finance.feature:budgeting')->group(function () {
            Route::get('budgets', [BudgetController::class, 'index'])->name('budgets.index');
            Route::post('budgets', [BudgetController::class, 'store'])->name('budgets.store');
            Route::delete('budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');
        });

        // ── Approval Workflow (toggleable) ─────────────────────
        Route::middleware('finance.feature:approval')->group(function () {
            Route::get('expense-requests', [ExpenseRequestController::class, 'index'])->name('expense-requests.index');
            Route::get('expense-requests/create', [ExpenseRequestController::class, 'create'])->name('expense-requests.create');
            Route::post('expense-requests', [ExpenseRequestController::class, 'store'])->name('expense-requests.store');
            Route::get('expense-requests/{expenseRequest}', [ExpenseRequestController::class, 'show'])->name('expense-requests.show');
            Route::post('expense-requests/{expenseRequest}/approve/{level}', [ExpenseRequestController::class, 'approve'])->name('expense-requests.approve');
            Route::post('expense-requests/{expenseRequest}/reject', [ExpenseRequestController::class, 'reject'])->name('expense-requests.reject');
            Route::post('expense-requests/{expenseRequest}/execute', [ExpenseRequestController::class, 'execute'])->name('expense-requests.execute');
        });

        // ── Bank Reconciliation (toggleable) ───────────────────
        Route::middleware('finance.feature:bank_recon')->group(function () {
            Route::get('bank-recon', [BankReconController::class, 'index'])->name('bank-recon.index');
            Route::post('bank-recon', [BankReconController::class, 'store'])->name('bank-recon.store');
        });

        // ── Periode / Tutup Buku ───────────────────────────────
        Route::get('periode', [PeriodeController::class, 'index'])->name('periode.index');
        Route::post('periode/close', [PeriodeController::class, 'close'])->name('periode.close');
        Route::patch('periode/{periode}/reopen', [PeriodeController::class, 'reopen'])->name('periode.reopen');

        // ── Audit Trail (toggleable) ───────────────────────────
        Route::middleware('finance.feature:audit_log')->group(function () {
            Route::get('audit', [AuditController::class, 'index'])->name('audit.index');
        });

        // ── Keuangan Santri (integrasi data wallet/SPP/topup) ─
        Route::get('santri', [SantriFinanceController::class, 'index'])->name('santri.index');
        Route::get('santri/{santri}', [SantriFinanceController::class, 'show'])->name('santri.show');

        // ── Settings ───────────────────────────────────────────
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
