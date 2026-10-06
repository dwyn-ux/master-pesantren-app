<?php

namespace Tests\Feature;

use App\Models\JenisTagihan;
use App\Models\Pembayaran;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\User;
use App\Models\Wali;
use App\Services\PaymentSettlementService;
use App\Http\Middleware\CheckFeature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ManualPaymentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_wali_can_submit_transfer_proof_and_admin_can_confirm_it_once(): void
    {
        Storage::fake('local');

        [$admin, $waliUser, $tagihan] = $this->makePaymentContext();

        $response = $this->withoutMiddleware(CheckFeature::class)
            ->actingAs($waliUser)
            ->post(route('wali.tagihan.pay.process', $tagihan), [
                'metode' => 'manual_transfer',
                'proof' => UploadedFile::fake()->create('bukti-transfer.pdf', 120, 'application/pdf'),
                'manual_note' => 'Transfer dari rekening wali.',
            ]);

        $response->assertRedirect(route('wali.tagihan.show', $tagihan));

        $payment = Pembayaran::firstOrFail();
        $this->assertSame('manual', $payment->metode);
        $this->assertSame('transfer', $payment->manual_type);
        $this->assertSame('pending', $payment->status);
        $this->assertNotNull($payment->proof_path);
        Storage::disk('local')->assertExists($payment->proof_path);

        $this->withoutMiddleware(CheckFeature::class)
            ->actingAs($admin)
            ->post(route('admin.pembayaran.confirm-manual', $payment), [
                'manual_note' => 'Nominal dan rekening penerima sudah cocok.',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pembayaran', [
            'id' => $payment->id,
            'status' => 'paid',
            'confirmed_by' => $admin->id,
        ]);
        $this->assertDatabaseHas('tagihan', [
            'id' => $tagihan->id,
            'status' => 'lunas',
        ]);

        $this->withoutMiddleware(CheckFeature::class)
            ->actingAs($admin)
            ->post(route('admin.pembayaran.confirm-manual', $payment))
            ->assertSessionHas('error');
    }

    public function test_settlement_is_idempotent_for_batch_top_up(): void
    {
        [, $waliUser, $tagihan] = $this->makePaymentContext();
        $santri = $tagihan->santri;
        $wali = $waliUser->wali;

        $payment = Pembayaran::create([
            'wali_id' => $wali->id,
            'tagihan_ids' => [$tagihan->id],
            'topup_items' => [['santri_id' => $santri->id, 'nominal' => 25000]],
            'nominal' => $tagihan->nominal + 25000,
            'metode' => 'qris',
            'tripay_ref' => 'TEST-BATCH-1',
            'status' => 'pending',
        ]);

        $settlement = app(PaymentSettlementService::class);
        $settlement->applyPaymentStatus($payment, 'PAID');
        $settlement->applyPaymentStatus($payment, 'PAID');

        $this->assertSame(25000, $santri->fresh()->saldo);
        $this->assertDatabaseCount('wallet_transactions', 1);
        $this->assertDatabaseHas('tagihan', ['id' => $tagihan->id, 'status' => 'lunas']);
    }

    public function test_admin_can_record_cash_payment(): void
    {
        [$admin, , $tagihan] = $this->makePaymentContext();

        $this->actingAs($admin)
            ->post(route('admin.pembayaran.store'), [
                'tagihan_id' => $tagihan->id,
                'metode' => 'manual_cash',
                'manual_note' => 'Diterima di loket administrasi.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pembayaran', [
            'tagihan_id' => $tagihan->id,
            'metode' => 'manual',
            'manual_type' => 'cash',
            'status' => 'paid',
            'confirmed_by' => $admin->id,
        ]);
        $this->assertDatabaseHas('tagihan', ['id' => $tagihan->id, 'status' => 'lunas']);
    }

    private function makePaymentContext(): array
    {
        $admin = User::factory()->create(['must_change_pw' => false]);
        $waliUser = User::factory()->create(['must_change_pw' => false]);
        $admin->assignRole(Role::findOrCreate('admin'));
        $waliUser->assignRole(Role::findOrCreate('wali'));
        $wali = Wali::create([
            'user_id' => $waliUser->id,
            'nama' => 'Wali Uji',
            'no_hp' => '08123456789',
        ]);
        $santri = Santri::create([
            'nis' => 'TST-' . fake()->unique()->numerify('#####'),
            'nama' => 'Santri Uji',
            'saldo' => 0,
            'is_aktif' => true,
        ]);
        $wali->santri()->attach($santri->id, ['hubungan' => 'wali']);
        $jenis = JenisTagihan::create([
            'nama' => 'SPP',
            'kelompok' => 'bulanan',
            'nominal' => 100000,
            'is_nominal_tetap' => true,
            'is_aktif' => true,
        ]);
        $tagihan = Tagihan::create([
            'santri_id' => $santri->id,
            'jenis_tagihan_id' => $jenis->id,
            'nominal' => 100000,
            'periode' => '2026-10',
            'status' => 'belum_bayar',
            'due_date' => '2026-10-31',
        ]);

        return [$admin, $waliUser, $tagihan];
    }
}
