<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportLog;
use App\Models\Santri;
use App\Models\User;
use App\Models\Wali;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Collection as SupportCollection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function index()
    {
        $logs = ImportLog::with('admin')->latest('created_at')->paginate(10);

        return view('admin.import.index', compact('logs'));
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate(Request $request)
    {
        $request->validate(['tipe' => 'required|in:wali,santri']);

        if ($request->tipe === 'wali') {
            return Excel::download(
                new \App\Exports\ImportWaliTemplate(),
                'template-import-wali.xlsx'
            );
        }

        return Excel::download(
            new \App\Exports\ImportSantriTemplate(),
            'template-import-santri.xlsx'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:wali,santri',
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            $rows = $this->parseExcelFile($request->file('file'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        if ($rows->isEmpty()) {
            return back()->with('error', 'File tidak berisi data yang valid.');
        }

        // Ambil baris pertama sebagai header
        $headerRow = $rows->shift();
        $header = $headerRow
            ->map(fn ($value) => Str::of((string) $value)->trim()->lower()->replace(' ', '_')->__toString())
            ->toArray();

        // Filter baris kosong
        $dataRows = $rows->filter(fn ($row) => $row->filter()->isNotEmpty());

        $total   = $dataRows->count();
        $success = 0;
        $failed  = 0;
        $errors  = [];

        // Proses tiap baris dalam transaksi TERPISAH supaya satu baris gagal
        // tidak membatalkan baris lainnya (fix: transaction abort cascade)
        foreach ($dataRows as $index => $row) {
            $rowArr = $row->toArray();

            $rowArr = array_slice($rowArr, 0, count($header));
            while (count($rowArr) < count($header)) {
                $rowArr[] = null;
            }

            $data = array_combine($header, $rowArr);

            try {
                DB::transaction(function () use ($data, $request, &$success, &$failed, &$errors, $index) {
                    if ($request->tipe === 'wali') {
                        $result = $this->importWaliRow($data);
                    } else {
                        $result = $this->importSantriRow($data);
                    }

                    if ($result) {
                        $success++;
                    } else {
                        $failed++;
                        $errors[] = "Baris " . ($index + 2) . ": Data tidak lengkap atau tidak valid.";
                    }
                });
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        ImportLog::create([
            'admin_id'    => auth()->id(),
            'tipe'        => $request->tipe,
            'total_baris' => $total,
            'berhasil'    => $success,
            'gagal'       => $failed,
            'file_asli'   => $request->file('file')->getClientOriginalName(),
        ]);

        $message = "Import selesai. Berhasil: {$success}, Gagal: {$failed}.";
        if (!empty($errors)) {
            $message .= " Detail: " . implode('; ', array_slice($errors, 0, 3));
            if (count($errors) > 3) {
                $message .= " ... dan " . (count($errors) - 3) . " lainnya.";
            }
        }

        $flashType = $failed === 0 ? 'success' : ($success === 0 ? 'error' : 'warning');
        return back()->with($flashType, $message);
    }

    private function parseExcelFile($file): SupportCollection
    {
        $import = new class implements ToCollection {
            public SupportCollection $rows;

            public function collection(SupportCollection $rows)
            {
                $this->rows = $rows;
            }
        };

        Excel::import($import, $file);

        return $import->rows ?? collect();
    }

    private function importWaliRow(array $data): bool
    {
        $nama       = trim($data['nama_orang_tua'] ?? $data['nama'] ?? '');
        $noHp       = trim($data['no_hp'] ?? $data['no._hp'] ?? $data['nohp'] ?? '');
        $nis        = trim((string) ($data['nis'] ?? ''));
        $namaSantri = trim($data['nama_santri'] ?? '');
        // Normalisasi hubungan: lowercase, trim, fallback ke 'wali'
        $hubunganRaw = strtolower(trim($data['hubungan'] ?? 'wali'));
        $hubungan    = in_array($hubunganRaw, ['ayah', 'ibu', 'wali']) ? $hubunganRaw : 'wali';

        if (empty($nama) || empty($noHp) || empty($nis)) {
            return false;
        }

        // Cari santri berdasarkan NIS
        $santri = Santri::where('nis', $nis)->first();
        if (!$santri) {
            return false; // Santri tidak ditemukan, skip
        }

        // Cek apakah wali dengan nama yang sama sudah terhubung ke santri ini
        $sudahAda = Wali::where('nama', $nama)
            ->whereHas('santri', fn($q) => $q->where('santri_id', $santri->id))
            ->exists();

        if ($sudahAda) {
            return true; // Skip duplikat, anggap sukses
        }

        // Cek apakah user dengan username NIS sudah ada (wali lain pakai NIS yang sama)
        $username = $nis;
        $suffix   = 1;
        while (User::where('username', $username)->exists()) {
            $username = $nis . '_' . $suffix++;
        }

        $password = $nis; // password default = NIS santri
        $user = User::create([
            'name'           => $nama,
            'username'       => $username,
            'password'       => Hash::make($password),
            'must_change_pw' => true,
            'is_active'      => true,
        ]);
        $user->assignRole('wali');

        $wali = Wali::create([
            'user_id' => $user->id,
            'nama'    => $nama,
            'no_hp'   => $noHp,
        ]);

        $wali->santri()->attach($santri->id, ['hubungan' => $hubungan]);

        Storage::disk('local')->append('credentials-wali.txt',
            "[import] nama: {$nama} | username: {$username} | password: {$password}\n"
        );

        return true;
    }

    private function importSantriRow(array $data): bool
    {
        $nis    = $data['nis'] ?? null;
        $nama   = $data['nama'] ?? null;

        if (empty($nis) || empty($nama)) {
            return false;
        }

        $nik           = $data['nik'] ?? null;
        $jenisKelamin  = strtoupper(trim($data['jenis_kelamin_(l/p)'] ?? $data['jenis_kelamin'] ?? ''));
        $tanggalLahir  = $data['tanggal_lahir_(yyyy-mm-dd)'] ?? $data['tanggal_lahir'] ?? null;
        $kelas         = $data['kelas'] ?? null;
        $alamat        = $data['alamat'] ?? null;
        $noHpOrtu      = $data['no_hp_orang_tua'] ?? $data['no_hp_ortu'] ?? null;

        Santri::updateOrCreate(
            ['nis' => $nis],
            array_filter([
                'nik'           => $nik ?: null,
                'nama'          => $nama,
                'jenis_kelamin' => in_array($jenisKelamin, ['L', 'P']) ? $jenisKelamin : null,
                'tanggal_lahir' => $tanggalLahir ?: null,
                'kelas'         => $kelas ?: null,
                'alamat'        => $alamat ?: null,
                'no_hp_ortu'    => $noHpOrtu ?: null,
                'is_aktif'      => true,
            ], fn($v) => $v !== null)
        );

        return true;
    }
}
