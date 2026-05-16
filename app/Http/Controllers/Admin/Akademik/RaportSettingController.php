<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class RaportSettingController extends Controller
{
    /**
     * Daftar field setting raport.
     */
    protected array $fields = [
        // Identitas Pesantren
        'raport.nama_pesantren'      => ['label' => 'Nama Pesantren',           'group' => 'kop',     'type' => 'string', 'default' => ''],
        'raport.alamat'              => ['label' => 'Alamat',                   'group' => 'kop',     'type' => 'string', 'default' => ''],
        'raport.kontak'              => ['label' => 'Kontak / Telepon / Website','group' => 'kop',    'type' => 'string', 'default' => ''],
        'raport.logo_url'            => ['label' => 'URL Logo',                 'group' => 'kop',     'type' => 'string', 'default' => ''],

        // Mudir / Pejabat penandatangan
        'raport.mudir'               => ['label' => 'Nama Mudir',               'group' => 'pejabat', 'type' => 'string', 'default' => ''],
        'raport.mudir_jabatan'       => ['label' => 'Jabatan Mudir',            'group' => 'pejabat', 'type' => 'string', 'default' => 'Mudir'],
        'raport.tempat_terbit'       => ['label' => 'Tempat Terbit',            'group' => 'pejabat', 'type' => 'string', 'default' => ''],
    ];

    public function index()
    {
        $settings = [];
        foreach ($this->fields as $key => $meta) {
            $settings[$key] = AppSetting::get($key, $meta['default']);
        }

        $grouped = [
            'kop'     => array_filter($this->fields, fn ($f) => $f['group'] === 'kop'),
            'pejabat' => array_filter($this->fields, fn ($f) => $f['group'] === 'pejabat'),
        ];

        return view('admin.akademik.raport-setting.index', compact('settings', 'grouped'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo_file'   => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'remove_logo' => 'nullable|boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo_file')) {
            $existing = AppSetting::get('raport.logo_url');
            if ($existing && \Illuminate\Support\Str::startsWith($existing, 'storage/raport/')) {
                $oldPath = str_replace('storage/', '', $existing);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }

            $file = $request->file('logo_file');
            $filename = 'logo-' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('raport', $filename, 'public');

            AppSetting::set('raport.logo_url', 'storage/raport/' . $filename, 'string', [
                'group' => 'raport',
                'label' => 'URL Logo Pesantren',
            ]);
        } elseif ($request->boolean('remove_logo')) {
            $existing = AppSetting::get('raport.logo_url');
            if ($existing && \Illuminate\Support\Str::startsWith($existing, 'storage/raport/')) {
                $oldPath = str_replace('storage/', '', $existing);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }
            AppSetting::set('raport.logo_url', '', 'string', ['group' => 'raport']);
        }

        foreach ($this->fields as $key => $meta) {
            if (str_contains($key, 'logo_url')) {
                continue;
            }

            $val = $request->input(str_replace('.', '_', $key));

            AppSetting::set($key, $val ?? '', $meta['type'], [
                'group' => 'raport',
                'label' => $meta['label'],
            ]);
        }

        return back()->with('success', 'Pengaturan raport berhasil disimpan.');
    }

    /**
     * Helper untuk ambil semua settings raport sebagai array.
     */
    public static function all(): array
    {
        $controller = new self();
        $out = [];
        foreach ($controller->fields as $key => $meta) {
            $shortKey = str_replace('raport.', '', $key);
            $out[$shortKey] = AppSetting::get($key, $meta['default']);
        }
        return $out;
    }
}
