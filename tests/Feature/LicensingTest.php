<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\StoreSetting;
use App\Models\LicensePayment;
use App\Services\LicenseService;
use App\Livewire\Settings\Billing;
use App\Livewire\Admin\LicenseManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Carbon\Carbon;

class LicensingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic settings
        StoreSetting::updateOrCreate(['key' => 'license_max_users'], ['value' => '5']);
        StoreSetting::updateOrCreate(['key' => 'license_max_cash_registers'], ['value' => '2']);
        StoreSetting::updateOrCreate(['key' => 'license_tier_name'], ['value' => 'Plan Pro']);
        StoreSetting::updateOrCreate(['key' => 'license_enabled_at'], ['value' => Carbon::now()->subDays(15)->format('Y-m-d')]);
        StoreSetting::updateOrCreate(['key' => 'license_expires_at'], ['value' => Carbon::now()->addDays(15)->format('Y-m-d')]);
        StoreSetting::updateOrCreate(['key' => 'license_grace_period_used'], ['value' => '0']);
    }

    public function test_system_allows_access_when_license_is_active()
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertDontSeeText('Sistema Bloqueado por Falta de Pago');
    }

    public function test_system_blocks_access_to_non_developer_and_redirects_to_billing_when_license_is_expired()
    {
        // Expire license
        StoreSetting::updateOrCreate(['key' => 'license_expires_at'], ['value' => Carbon::now()->subDays(1)->format('Y-m-d')]);

        $this->assertTrue(LicenseService::isExpired());

        $user = User::factory()->create([
            'role' => 'gerente',
            'email' => 'gerente@test.com',
        ]);

        $this->actingAs($user);

        // Access dashboard -> should redirect to billing
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('settings.billing'));

        // Visit billing route -> should be allowed
        $responseBilling = $this->get(route('settings.billing'));
        $responseBilling->assertStatus(200);
        $responseBilling->assertSeeText('Sistema Bloqueado por Falta de Pago');
    }

    public function test_system_allows_access_to_developer_even_when_license_is_expired()
    {
        // Expire license
        StoreSetting::updateOrCreate(['key' => 'license_expires_at'], ['value' => Carbon::now()->subDays(1)->format('Y-m-d')]);

        $developer = User::factory()->create([
            'role' => 'developer',
            'email' => config('services.developer.email') ?: 'soteweb@gmail.com',
        ]);

        $this->actingAs($developer);

        // Access dashboard -> should be allowed
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_grace_period_can_be_activated_only_once_and_extends_license()
    {
        // Expire license
        StoreSetting::updateOrCreate(['key' => 'license_expires_at'], ['value' => Carbon::now()->subDays(1)->format('Y-m-d')]);
        StoreSetting::updateOrCreate(['key' => 'license_grace_period_used'], ['value' => '0']);

        $this->assertTrue(LicenseService::isExpired());
        $this->assertTrue(LicenseService::canActivateGracePeriod());

        $user = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
        ]);

        $this->actingAs($user);

        // Test Livewire Grace Activation
        Livewire::test(Billing::class)
            ->call('activateGracePeriod')
            ->assertRedirect(route('settings.billing'));

        // Verify the setting is now marked as used
        $this->assertEquals('1', StoreSetting::where('key', 'license_grace_period_used')->first()->value);
        $this->assertTrue(LicenseService::isGracePeriodUsed());
        
        // Verify system is now active (grace extended by 3 days)
        $this->assertFalse(LicenseService::isExpired());
        $this->assertEquals(3, LicenseService::getDaysRemaining());

        // Verify we can no longer activate the grace period
        $this->assertFalse(LicenseService::canActivateGracePeriod());
    }

    public function test_master_can_register_payment_which_extends_license_and_resets_grace_period()
    {
        // Simulate grace period already used and license expired
        StoreSetting::updateOrCreate(['key' => 'license_expires_at'], ['value' => Carbon::now()->subDays(1)->format('Y-m-d')]);
        StoreSetting::updateOrCreate(['key' => 'license_grace_period_used'], ['value' => '1']);

        $developer = User::factory()->create([
            'role' => 'developer',
            'email' => config('services.developer.email') ?: 'soteweb@gmail.com',
        ]);

        $this->actingAs($developer);

        $this->assertEquals(0, LicensePayment::count());

        // Register payment in master panel
        Livewire::test(LicenseManager::class)
            ->set('paymentAmount', 150000)
            ->set('paymentDate', Carbon::now()->format('Y-m-d'))
            ->set('paymentMethod', 'transferencia')
            ->set('paymentNotes', 'Pago mensual de Mayo')
            ->call('registerPayment')
            ->assertHasNoErrors();

        // Verify payment record in DB
        $this->assertEquals(1, LicensePayment::count());
        $payment = LicensePayment::first();
        $this->assertEquals(150000, $payment->amount);
        $this->assertEquals('transferencia', $payment->payment_method);
        $this->assertEquals('Pago mensual de Mayo', $payment->notes);

        // Verify expiration date extended (since expired, it adds 1 month from payment date)
        $newExpiresAt = Carbon::now()->addMonth()->format('Y-m-d');
        $this->assertEquals($newExpiresAt, StoreSetting::where('key', 'license_expires_at')->first()->value);

        // Verify grace period reset
        $this->assertEquals('0', StoreSetting::where('key', 'license_grace_period_used')->first()->value);
        $this->assertFalse(LicenseService::isGracePeriodUsed());
        $this->assertFalse(LicenseService::isExpired());
    }

    public function test_license_restricts_branch_creation_when_limit_is_reached()
    {
        // Set maximum branches to 1
        StoreSetting::updateOrCreate(['key' => 'license_max_branches'], ['value' => '1']);
        $this->assertEquals(1, LicenseService::getMaxBranches());

        // Create an existing branch
        \App\Models\Branch::create([
            'name' => 'Sucursal Principal',
            'address' => 'Av. Mariscal López 123',
            'phone' => '0981123456',
            'is_active' => true,
        ]);

        $this->assertEquals(1, \App\Models\Branch::count());
        $this->assertFalse(LicenseService::canAddBranch());

        $user = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
        ]);
        $this->actingAs($user);

        // Try to create another branch via Livewire
        Livewire::test(\App\Livewire\Branches\Index::class)
            ->set('name', 'Segunda Sucursal')
            ->set('address', 'Calle Palma 456')
            ->set('phone', '0981654321')
            ->set('is_active', true)
            ->call('store')
            ->assertHasNoErrors();

        // Verify the second branch was NOT created
        $this->assertEquals(1, \App\Models\Branch::count());
    }

    public function test_license_allows_updating_existing_branch_even_when_limit_is_reached()
    {
        // Set maximum branches to 1
        StoreSetting::updateOrCreate(['key' => 'license_max_branches'], ['value' => '1']);

        // Create an existing branch
        $branch = \App\Models\Branch::create([
            'name' => 'Sucursal Principal',
            'address' => 'Av. Mariscal López 123',
            'phone' => '0981123456',
            'is_active' => true,
        ]);

        $this->assertEquals(1, \App\Models\Branch::count());
        $this->assertFalse(LicenseService::canAddBranch());

        $user = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
        ]);
        $this->actingAs($user);

        // Update the existing branch via Livewire
        Livewire::test(\App\Livewire\Branches\Index::class)
            ->call('edit', $branch->id)
            ->set('name', 'Sucursal Principal Modificada')
            ->call('store')
            ->assertHasNoErrors();

        // Verify the branch was updated successfully
        $this->assertEquals(1, \App\Models\Branch::count());
        $this->assertEquals('Sucursal Principal Modificada', $branch->refresh()->name);
    }

    public function test_master_panel_can_update_branch_limit()
    {
        $developer = User::factory()->create([
            'role' => 'developer',
            'email' => config('services.developer.email') ?: 'soteweb@gmail.com',
        ]);

        $this->actingAs($developer);

        // Update license branch limit via Livewire
        Livewire::test(LicenseManager::class)
            ->set('maxBranches', 3)
            ->set('maxUsers', 10)
            ->set('maxCashRegisters', 4)
            ->set('tierName', 'Plan Gold')
            ->set('enabledAt', Carbon::now()->format('Y-m-d'))
            ->set('expiresAt', Carbon::now()->addMonths(2)->format('Y-m-d'))
            ->call('updateLicense')
            ->assertHasNoErrors();

        // Verify values in DB
        $this->assertEquals(3, LicenseService::getMaxBranches());
        $this->assertEquals(10, LicenseService::getMaxUsers());
        $this->assertEquals(4, LicenseService::getMaxCashRegisters());
        $this->assertEquals('Plan Gold', LicenseService::getTierName());
    }
}
