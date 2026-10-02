<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DemoAccountSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Tests\TestCase;

class DemoAccountSeederTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100)->nullable()->unique();
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->boolean('must_change_pw')->default(true);
            $table->string('avatar')->nullable();
            $table->string('fcm_token')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('model_has_roles', function (Blueprint $table): void {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['role_id', 'model_id', 'model_type']);
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_it_creates_an_active_admin_demo_account_that_can_log_in_immediately(): void
    {
        config()->set('demo', [
            'name' => 'Demo Pesantren',
            'username' => 'demo-test',
            'password' => 'DemoPassword123!',
        ]);

        $this->seed(DemoAccountSeeder::class);

        $user = User::where('username', 'demo-test')->firstOrFail();

        $this->assertSame('Demo Pesantren', $user->name);
        $this->assertTrue($user->is_active);
        $this->assertFalse($user->must_change_pw);
        $this->assertTrue($user->hasRole('admin'));
        $this->assertTrue(Hash::check('DemoPassword123!', $user->password));
    }

    public function test_it_resets_the_existing_demo_account_instead_of_creating_a_duplicate(): void
    {
        config()->set('demo', [
            'name' => 'Akun Demo',
            'username' => 'demo-test',
            'password' => 'DemoPassword123!',
        ]);

        $this->seed(DemoAccountSeeder::class);
        $this->seed(DemoAccountSeeder::class);

        $this->assertSame(1, User::where('username', 'demo-test')->count());
    }

    public function test_it_refuses_to_create_an_account_without_a_secure_password(): void
    {
        config()->set('demo', [
            'name' => 'Akun Demo',
            'username' => 'demo-test',
            'password' => null,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('DEMO_ACCOUNT_PASSWORD wajib diisi');

        $this->seed(DemoAccountSeeder::class);
    }
}
