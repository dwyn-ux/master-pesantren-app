<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,             // 1. role harus ada sebelum user di-assign role
            SuperadminSeeder::class,       // 2. superadmin (pengelola fitur aplikasi)
            AdminSeeder::class,            // 3. admin, bendahara, kepala pondok
            SurahSeeder::class,            // 4. data statis 114 surah
            JenisTagihanSeeder::class,     // 5. master jenis tagihan
            OutletSeeder::class,           // 6. user + outlet kantin & laundry
            TarifLaundrySeeder::class,     // 7. tarif awal (butuh admin.id)
            ChartOfAccountSeeder::class,   // 8. COA finance
            KasBankSeeder::class,          // 9. master kas & bank
            KategoriKeuanganSeeder::class, // 10. kategori pemasukan & pengeluaran
            FinanceSettingSeeder::class,   // 11. settings finance
            FeatureSeeder::class,          // 12. seed default fitur aplikasi
        ]);
    }
}
