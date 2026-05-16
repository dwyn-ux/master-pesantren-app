<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Wali;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanupDuplicateWali extends Command
{
    protected $signature = 'app:cleanup-duplicate-wali {--dry-run : Only show what would be done}';
    protected $description = 'Clean up duplicate Wali records and merge their santri relations';

    public function handle()
    {
        $duplicates = DB::table('wali')
            ->select('nama', 'no_hp', DB::raw('COUNT(*) as total'))
            ->groupBy('nama', 'no_hp')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info("No duplicates found.");
            return;
        }

        foreach ($duplicates as $dup) {
            $this->warn("Processing duplicate: {$dup->nama} ({$dup->no_hp})");
            
            $walis = Wali::where('nama', $dup->nama)
                ->where('no_hp', $dup->no_hp)
                ->orderBy('id', 'asc') // Keep the oldest one
                ->get();

            $keep = $walis->shift();
            $this->info("Keeping Wali ID: {$keep->id}");

            foreach ($walis as $duplicate) {
                $this->line("Merging Wali ID: {$duplicate->id} into {$keep->id}...");

                if ($this->option('dry-run')) {
                    continue;
                }

                DB::transaction(function () use ($keep, $duplicate) {
                    // Update pivot table santri_wali
                    DB::table('santri_wali')
                        ->where('wali_id', $duplicate->id)
                        ->whereNotExists(function ($query) use ($keep, $duplicate) {
                            $query->select(DB::raw(1))
                                  ->from('santri_wali as sw2')
                                  ->whereColumn('sw2.santri_id', 'santri_wali.santri_id')
                                  ->where('sw2.wali_id', $keep->id);
                        })
                        ->update(['wali_id' => $keep->id]);

                    // Delete remaining relations that already exist for $keep
                    DB::table('santri_wali')->where('wali_id', $duplicate->id)->delete();

                    // Update orders (formerly marketplace_orders)
                    if (Schema::hasTable('orders')) {
                        DB::table('orders')->where('wali_id', $duplicate->id)->update(['wali_id' => $keep->id]);
                    }

                    // Update voice notes
                    if (Schema::hasTable('voice_notes')) {
                        DB::table('voice_notes')->where('pengirim_wali_id', $duplicate->id)->update(['pengirim_wali_id' => $keep->id]);
                        DB::table('voice_notes')->where('penerima_wali_id', $duplicate->id)->update(['penerima_wali_id' => $keep->id]);
                    }

                    // Update pembayaran
                    if (Schema::hasTable('pembayaran')) {
                        DB::table('pembayaran')->where('wali_id', $duplicate->id)->update(['wali_id' => $keep->id]);
                    }

                    // Update tagihan (not needed, tagihan is linked to santri)
                    // Update the duplicate wali and its user
                    $userId = $duplicate->user_id;
                    $duplicate->delete();
                    User::where('id', $userId)->delete();
                });
            }
        }

        $this->info("Cleanup completed!");
    }
}
