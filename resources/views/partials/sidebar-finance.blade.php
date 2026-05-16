@php
    use App\Models\Finance\Setting;
    $isOn = fn(string $f) => Setting::isEnabled($f);
@endphp

<a href="{{ route('finance.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 mx-4 mt-6 rounded-xl transition-all {{ request()->routeIs('finance.dashboard*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
    <i class="fa-solid fa-chart-pie w-5 text-center"></i>
    <span class="font-medium text-sm">Dashboard</span>
</a>

<a href="{{ route('finance.santri.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.santri*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
    <i class="fa-solid fa-user-graduate w-5 text-center"></i>
    <span class="font-medium text-sm">Keuangan Santri</span>
</a>

<div class="px-4 py-2 mt-4 text-xs font-bold text-indigo-200/70 uppercase tracking-wider">Transaksi</div>

<a href="{{ route('finance.transaksi.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.transaksi*') ? 'bg-indigo-600/50 text-white shadow-sm' : 'text-indigo-100 hover:bg-white/10' }}">
    <i class="fa-solid fa-arrow-right-arrow-left w-5 text-center"></i>
    <span class="font-medium text-sm">Transaksi Kas</span>
</a>
<a href="{{ route('finance.transaksi.masuk') }}" class="flex items-center gap-3 px-4 py-2 mx-8 rounded-xl transition-all {{ request()->routeIs('finance.transaksi.masuk*') ? 'bg-green-600/40 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1 text-xs">
    <i class="fa-solid fa-circle-plus w-4 text-center text-green-300"></i>
    <span class="font-medium">Pemasukan</span>
</a>
<a href="{{ route('finance.transaksi.keluar') }}" class="flex items-center gap-3 px-4 py-2 mx-8 rounded-xl transition-all {{ request()->routeIs('finance.transaksi.keluar*') ? 'bg-red-600/40 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1 text-xs">
    <i class="fa-solid fa-circle-minus w-4 text-center text-red-300"></i>
    <span class="font-medium">Pengeluaran</span>
</a>
<a href="{{ route('finance.transaksi.transfer') }}" class="flex items-center gap-3 px-4 py-2 mx-8 rounded-xl transition-all {{ request()->routeIs('finance.transaksi.transfer*') ? 'bg-blue-600/40 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1 text-xs">
    <i class="fa-solid fa-right-left w-4 text-center text-blue-300"></i>
    <span class="font-medium">Transfer</span>
</a>
<a href="{{ route('finance.journal.manual') }}" class="flex items-center gap-3 px-4 py-2 mx-8 rounded-xl transition-all {{ request()->routeIs('finance.journal.manual') ? 'bg-amber-600/40 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1 text-xs">
    <i class="fa-solid fa-pen-to-square w-4 text-center text-amber-300"></i>
    <span class="font-medium">Jurnal Manual</span>
</a>

@if($isOn('approval'))
<div class="px-4 py-2 mt-4 text-xs font-bold text-indigo-200/70 uppercase tracking-wider">Approval</div>
<a href="{{ route('finance.expense-requests.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.expense-requests*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }}">
    <i class="fa-solid fa-user-check w-5 text-center"></i>
    <span class="font-medium text-sm">Permintaan Pengeluaran</span>
    @php $pending = \App\Models\Finance\ExpenseRequest::whereIn('status', ['pending_kepala', 'pending_yayasan'])->count(); @endphp
    @if($pending > 0)<span class="ml-auto px-1.5 py-0.5 bg-amber-400 text-amber-900 text-xs rounded-full font-bold">{{ $pending }}</span>@endif
</a>
@endif

@if($isOn('payroll'))
<div class="px-4 py-2 mt-4 text-xs font-bold text-indigo-200/70 uppercase tracking-wider">Penggajian</div>
<a href="{{ route('finance.payroll.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.payroll.index') || request()->routeIs('finance.payroll.show') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }}">
    <i class="fa-solid fa-money-check-dollar w-5 text-center"></i>
    <span class="font-medium text-sm">Payroll Ustadz</span>
</a>
<a href="{{ route('finance.payroll.master-salary') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.payroll.master-salary') || request()->routeIs('finance.payroll.salary*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
    <i class="fa-solid fa-users-gear w-5 text-center"></i>
    <span class="font-medium text-sm">Master Gaji</span>
</a>
@endif

@if($isOn('petty_cash') || $isOn('aset') || $isOn('payable'))
<div class="px-4 py-2 mt-4 text-xs font-bold text-indigo-200/70 uppercase tracking-wider">Operasional</div>

@if($isOn('petty_cash'))
<a href="{{ route('finance.petty-cash.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.petty-cash*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }}">
    <i class="fa-solid fa-piggy-bank w-5 text-center"></i>
    <span class="font-medium text-sm">Kas Kecil</span>
</a>
@endif

@if($isOn('aset'))
<a href="{{ route('finance.assets.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.assets*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
    <i class="fa-solid fa-warehouse w-5 text-center"></i>
    <span class="font-medium text-sm">Aset Tetap</span>
</a>
@endif

@if($isOn('payable'))
<a href="{{ route('finance.vendors.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.vendors*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
    <i class="fa-solid fa-truck w-5 text-center"></i>
    <span class="font-medium text-sm">Vendor / Supplier</span>
</a>
<a href="{{ route('finance.payables.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.payables*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
    <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i>
    <span class="font-medium text-sm">Hutang Vendor</span>
</a>
@endif
@endif

@if($isOn('budgeting') || $isOn('bank_recon'))
<div class="px-4 py-2 mt-4 text-xs font-bold text-indigo-200/70 uppercase tracking-wider">Kontrol</div>
@if($isOn('budgeting'))
<a href="{{ route('finance.budgets.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.budgets*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }}">
    <i class="fa-solid fa-chart-pie w-5 text-center"></i>
    <span class="font-medium text-sm">Anggaran (Budget)</span>
</a>
@endif
@if($isOn('bank_recon'))
<a href="{{ route('finance.bank-recon.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.bank-recon*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
    <i class="fa-solid fa-building-columns w-5 text-center"></i>
    <span class="font-medium text-sm">Rekonsiliasi Bank</span>
</a>
@endif
@endif

<div class="px-4 py-2 mt-4 text-xs font-bold text-indigo-200/70 uppercase tracking-wider">Master Data</div>
<a href="{{ route('finance.kas-banks.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.kas-banks*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }}">
    <i class="fa-solid fa-vault w-5 text-center"></i>
    <span class="font-medium text-sm">Kas & Bank</span>
</a>
<a href="{{ route('finance.kategoris.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.kategoris*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
    <i class="fa-solid fa-tags w-5 text-center"></i>
    <span class="font-medium text-sm">Kategori</span>
</a>
<a href="{{ route('finance.accounts.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.accounts*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
    <i class="fa-solid fa-sitemap w-5 text-center"></i>
    <span class="font-medium text-sm">Chart of Accounts</span>
</a>

<div class="px-4 py-2 mt-4 text-xs font-bold text-indigo-200/70 uppercase tracking-wider">Laporan & Audit</div>
<a href="{{ route('finance.laporan.jurnal') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.laporan.jurnal') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }}">
    <i class="fa-solid fa-book w-5 text-center"></i>
    <span class="font-medium text-sm">Jurnal Umum</span>
</a>
<a href="{{ route('finance.laporan.buku-besar') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.laporan.buku-besar') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
    <i class="fa-solid fa-book-open w-5 text-center"></i>
    <span class="font-medium text-sm">Buku Besar</span>
</a>
<a href="{{ route('finance.laporan.trial-balance') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.laporan.trial-balance') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
    <i class="fa-solid fa-scale-balanced w-5 text-center"></i>
    <span class="font-medium text-sm">Neraca Saldo</span>
</a>
<a href="{{ route('finance.laporan.laba-rugi') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.laporan.laba-rugi') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
    <i class="fa-solid fa-chart-line w-5 text-center"></i>
    <span class="font-medium text-sm">Laba Rugi</span>
</a>
<a href="{{ route('finance.laporan.neraca') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.laporan.neraca') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
    <i class="fa-solid fa-building-columns w-5 text-center"></i>
    <span class="font-medium text-sm">Neraca</span>
</a>
<a href="{{ route('finance.laporan.arus-kas') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.laporan.arus-kas') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
    <i class="fa-solid fa-money-bill-trend-up w-5 text-center"></i>
    <span class="font-medium text-sm">Arus Kas</span>
</a>

<a href="{{ route('finance.periode.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.periode*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-3">
    <i class="fa-solid fa-lock w-5 text-center"></i>
    <span class="font-medium text-sm">Tutup Buku / Periode</span>
</a>

@if($isOn('audit_log'))
<a href="{{ route('finance.audit.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.audit*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }} mt-1">
    <i class="fa-solid fa-shield-halved w-5 text-center"></i>
    <span class="font-medium text-sm">Audit Trail</span>
</a>
@endif

<div class="px-4 py-2 mt-4 text-xs font-bold text-indigo-200/70 uppercase tracking-wider">Setelan</div>
<a href="{{ route('finance.settings.index') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all {{ request()->routeIs('finance.settings*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-100 hover:bg-white/10' }}">
    <i class="fa-solid fa-gears w-5 text-center"></i>
    <span class="font-medium text-sm">Pengaturan Finance</span>
</a>

<a href="{{ route('bendahara.dashboard') }}" class="flex items-center gap-3 px-4 py-2 mx-4 rounded-xl transition-all text-indigo-100 hover:bg-white/10 mt-3 mb-4">
    <i class="fa-solid fa-arrow-left w-5 text-center"></i>
    <span class="font-medium text-sm">Kembali ke Bendahara</span>
</a>
