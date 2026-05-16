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

        DB::transaction(function () use ($dataRows, $header, $request, &$success, &$failed, &$errors) {
            foreach ($dataRows as $index => $row) {
                $rowArr = $row->toArray();

                // Sesuaikan jumlah kolom dengan header
                $rowArr = array_slice($rowArr, 0, count($header));
                while (count($rowArr) < count($header)) {
                    $rowArr[] = null;
                }

                $data = array_combine($header, $rowArr);

                try {
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
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }
        });

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
        // Normalisasi key untuk toleran terhadap variasi spasi/huruf kapital
        $nama    = $data['nama_orang_tua'] ?? $data['nama'] ?? null;
        $noHp    = $data['no_hp'] ?? $data['no._hp'] ?? $data['nohp'] ?? null;
        $nis     = $data['nis'] ?? null;
        $namaSantri = $data['nama_santri'] ?? null;
        $hubungan   = $data['hubungan'] ?? 'wali';

        if (empty($nama) || empty($noHp) || empty($nis)) {
            return false;
        }

        $santri = Santri::firstOrCreate(
            ['nis' => $nis],
            [
                'nama'     => $namaSantri ?? 'Santri',
                'is_aktif' => true,
            ]
        );

        $namaDepan = strtolower(preg_replace('/[^a-zA-Z]/', '', explode(' ', $nama)[0] ?? 'wali'));
        $username  = $namaDepan . '_' . $santri->nis;

        // Pastikan username unik
        $suffix = '';
        $attempt = 0;
        while (User::where('username', $username . $suffix)->exists()) {
            $attempt++;
            $suffix = '_' . $attempt;
        }
        $username .= $suffix;

        $password = Str::random(8);
        $user = User::create([
            'name'          => $nama,
            'username'      => $username,
            'password'      => Hash::make($password),
            'must_change_pw'=> true,
            'is_active'     => true,
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
