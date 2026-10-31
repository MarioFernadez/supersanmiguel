<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\CashRegister;
use App\Models\CashSession;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\StoreSetting;
use App\Services\DeboningService;
use App\Services\SalesService;
use App\Livewire\Pos\SalesInterface;
use App\Livewire\Admin\Deboning\Index as DeboningIndex;
use App\Livewire\Purchases\Create as PurchasesCreate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Exception;

class CarniceriaFixesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Branch $branch;
    protected CashRegister $register;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        StoreSetting::updateOrCreate(['key' => 'store_name'], ['value' => 'ZonaShop']);
        StoreSetting::updateOrCreate(['key' => 'license_expires_at'], ['value' => now()->addYear()->format('Y-m-d')]);

        $this->branch = Branch::create([
            'name' => 'Sucursal Central',
            'address' => 'Av. Central 100',
            'phone' => '0981111222',
            'is_active' => true,
        ]);

        $this->register = CashRegister::create([
            'name' => 'Caja Carnicería',
            'branch_id' => $this->branch->id,
            'active' => true,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Carnicero Master',
            'email' => 'carnicero@zonapos.com',
            'role' => 'admin',
            'branch_id' => $this->branch->id,
        ]);

        $this->category = Category::create([
            'name' => 'Carnes Vacunas',
            'slug' => 'carnes-vacunas',
            'is_active' => true,
        ]);
    }

    /**
     * Test 1: Desposte rechaza peso inicial cero o negativo y cortes negativos
     */
    public function test_deboning_rejects_negative_weight_and_zero_start()
    {
        $this->actingAs($this->admin);

        $res = Product::create([
            'name' => 'Media Res Novillo',
            'sku' => 'RES-01',
            'is_raw_material' => true,
            'unit_type' => 'kg',
            'stock' => 150.0,
            'is_active' => true,
        ]);
        $res->branchStocks()->create(['branch_id' => $this->branch->id, 'stock' => 150.0]);

        $corte = Product::create([
            'name' => 'Costilla',
            'sku' => 'COR-01',
            'unit_type' => 'kg',
            'stock' => 0,
            'is_active' => true,
        ]);

        $service = app(DeboningService::class);

        // Intento con peso inicial <= 0
        try {
            $service->createSession([
                'raw_material_product_id' => $res->id,
                'total_weight_start' => -10,
                'items' => [['product_id' => $corte->id, 'weight' => 5]],
                'user_id' => $this->admin->id,
            ]);
            $this->fail('Debió lanzar excepción por peso inicial negativo.');
        } catch (Exception $e) {
            $this->assertStringContainsString('mayor a 0', $e->getMessage());
        }

        // Intento con corte de peso negativo
        try {
            $service->createSession([
                'raw_material_product_id' => $res->id,
                'total_weight_start' => 50,
                'items' => [['product_id' => $corte->id, 'weight' => -5]],
                'user_id' => $this->admin->id,
            ]);
            $this->fail('Debió lanzar excepción por corte negativo.');
        } catch (Exception $e) {
            $this->assertStringContainsString('no puede ser negativo', $e->getMessage());
        }
    }

    /**
     * Test 2: Desposte valida stock contra la sucursal del usuario
     */
    public function test_deboning_validates_branch_stock()
    {
        $this->actingAs($this->admin);

        $res = Product::create([
            'name' => 'Media Res',
            'sku' => 'RES-02',
            'is_raw_material' => true,
            'stock' => 100.0, // Global tiene 100
            'is_active' => true,
        ]);
        // Pero en la sucursal Central solo hay 20
        $res->branchStocks()->create(['branch_id' => $this->branch->id, 'stock' => 20.0]);

        $corte = Product::create([
            'name' => 'Vacío',
            'sku' => 'COR-02',
            'unit_type' => 'kg',
            'stock' => 0,
            'is_active' => true,
        ]);

        $service = app(DeboningService::class);

        // Despostar 50 kg debe fallar aunque el global sea 100
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Stock insuficiente de materia prima en esta sucursal');

        $service->createSession([
            'raw_material_product_id' => $res->id,
            'total_weight_start' => 50.0,
            'items' => [['product_id' => $corte->id, 'weight' => 30.0]],
            'user_id' => $this->admin->id,
        ]);
    }

    /**
     * Test 3: Cierre mensual maneja sobrantes y sincroniza branch_stocks
     */
    public function test_deboning_monthly_closure_handles_surplus_and_syncs_branch()
    {
        $this->actingAs($this->admin);

        $res = Product::create([
            'name' => 'Res Pesaje',
            'sku' => 'RES-03',
            'is_raw_material' => true,
            'stock' => 50.0,
            'is_active' => true,
        ]);
        $res->branchStocks()->create(['branch_id' => $this->branch->id, 'stock' => 50.0]);

        // Cierre con sobrante físico real de 55 kg
        Livewire::test(DeboningIndex::class)
            ->set('selectedRawMaterialId', $res->id)
            ->set('physicalRemaining', 55.0)
            ->call('processMonthlyClosure')
            ->assertHasNoErrors();

        $this->assertEquals(55.0, (float)$res->fresh()->stock);
        $this->assertEquals(55.0, (float)$res->branchStocks()->where('branch_id', $this->branch->id)->value('stock'));
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $res->id,
            'type' => 'in',
            'quantity' => 5.0,
        ]);
    }

    /**
     * Test 4: Balanza EAN-13 prioriza PLU de 5 dígitos sobre colisión de 4 dígitos
     */
    public function test_scale_ean13_avoids_plu_collision_and_validates_checksum()
    {
        $this->actingAs($this->admin);

        CashSession::create([
            'user_id' => $this->admin->id,
            'cash_register_id' => $this->register->id,
            'opening_amount' => 100000,
            'opened_at' => now(),
            'status' => 'open',
        ]);

        // Carnaza con SKU '10'
        $carnaza = Product::create([
            'name' => 'Carnaza Negra',
            'sku' => '10',
            'price_sale' => 50000,
            'stock' => 50,
            'is_active' => true,
        ]);
        $carnaza->branchStocks()->create(['branch_id' => $this->branch->id, 'stock' => 50]);

        // Costilla con SKU '101'
        $costilla = Product::create([
            'name' => 'Costilla',
            'sku' => '101',
            'price_sale' => 40000,
            'stock' => 50,
            'is_active' => true,
        ]);
        $costilla->branchStocks()->create(['branch_id' => $this->branch->id, 'stock' => 50]);

        // Etiqueta Costilla 1.250 Kg: 20 + 00101 (PLU 101) + 01250 (1.250 Kg) + 4 (Check) = 2000101012504
        $component = Livewire::test(SalesInterface::class)
            ->call('handleSearchSubmit', '2000101012504');

        $cart = $component->get('cart');
        $this->assertTrue(isset($cart['p' . $costilla->id]), 'Debe agregar Costilla, no Carnaza');
        $this->assertFalse(isset($cart['p' . $carnaza->id]), 'No debe confundir con Carnaza');
        $this->assertEquals(1.250, (float)$cart['p' . $costilla->id]['quantity']);
        $this->assertEquals(50000, $component->get('total')); // 1.250 * 40.000 = 50.000
    }

    /**
     * Test 5: Balanza EAN-13 rechaza peso cero
     */
    public function test_scale_ean13_rejects_zero_weight()
    {
        $this->actingAs($this->admin);

        CashSession::create([
            'user_id' => $this->admin->id,
            'cash_register_id' => $this->register->id,
            'opening_amount' => 100000,
            'opened_at' => now(),
            'status' => 'open',
        ]);

        $costilla = Product::create([
            'name' => 'Costilla',
            'sku' => '101',
            'price_sale' => 40000,
            'stock' => 50,
            'is_active' => true,
        ]);
        $costilla->branchStocks()->create(['branch_id' => $this->branch->id, 'stock' => 50]);

        // Etiqueta peso 0: 2000101000009
        $component = Livewire::test(SalesInterface::class)
            ->call('handleSearchSubmit', '2000101000009')
            ->assertDispatched('error-movement');

        $this->assertEmpty($component->get('cart'), 'No debe agregar productos con peso cero');
    }

    /**
     * Test 6: Cálculo dinámico de IVA 5% en carnes para SIFEN
     */
    public function test_sales_service_calculates_5_percent_vat_for_meat()
    {
        $this->actingAs($this->admin);

        CashSession::create([
            'user_id' => $this->admin->id,
            'cash_register_id' => $this->register->id,
            'opening_amount' => 100000,
            'opened_at' => now(),
            'status' => 'open',
        ]);

        // Carne con unit_type = 'kg' tributa IVA 5%
        $carne = Product::create([
            'name' => 'Lomito Vacuno',
            'sku' => 'LOM-01',
            'unit_type' => 'kg',
            'price_sale' => 84000,
            'stock' => 10,
            'is_active' => true,
        ]);
        $carne->branchStocks()->create(['branch_id' => $this->branch->id, 'stock' => 10]);

        // Venta de 1 kg = 84.000 Gs (IVA 5% = 84.000 / 21 = 4.000 Gs)
        $service = app(SalesService::class);
        $sale = $service->processSale([
            'user_id' => $this->admin->id,
            'branch_id' => $this->branch->id,
            'total' => 84000,
            'payment_condition' => 'contado',
            'payment_method' => 'efectivo',
            'cart' => [
                'p' . $carne->id => [
                    'id' => $carne->id,
                    'name' => $carne->name,
                    'price' => 84000,
                    'quantity' => 1.0,
                    'subtotal' => 84000,
                ]
            ]
        ]);

        $this->assertEquals(4000, $sale->tax_5, 'El IVA de la carne debe liquidarse al 5% (4.000 Gs)');
        $this->assertEquals(0, $sale->tax_10, 'No debe liquidar IVA 10% en carne fresca');
    }

    /**
     * Test 7: Límite de crédito se respeta en ventas fiadas
     */
    public function test_customer_credit_limit_is_enforced()
    {
        $this->actingAs($this->admin);

        CashSession::create([
            'user_id' => $this->admin->id,
            'cash_register_id' => $this->register->id,
            'opening_amount' => 100000,
            'opened_at' => now(),
            'status' => 'open',
        ]);

        $customer = Customer::create([
            'name' => 'Cliente Con Tope',
            'ruc' => '998877-1',
            'credit_limit' => 100000, // Máximo 100.000 Gs
            'is_active' => true,
        ]);

        $carne = Product::create([
            'name' => 'Tapa Cuadril',
            'sku' => 'TAP-01',
            'unit_type' => 'kg',
            'price_sale' => 60000,
            'stock' => 20,
            'is_active' => true,
        ]);
        $carne->branchStocks()->create(['branch_id' => $this->branch->id, 'stock' => 20]);

        $service = app(SalesService::class);

        // Venta de 120.000 Gs (supera el límite de 100.000)
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Límite de crédito excedido');

        $service->processSale([
            'user_id' => $this->admin->id,
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'total' => 120000,
            'payment_condition' => 'credito',
            'cart' => [
                'p' . $carne->id => [
                    'id' => $carne->id,
                    'name' => $carne->name,
                    'price' => 60000,
                    'quantity' => 2.0,
                    'subtotal' => 120000,
                ]
            ]
        ]);
    }

    /**
     * Test 8: Compras a proveedores incrementan branch_stocks y registran movimiento
     */
    public function test_purchases_sync_branch_stock_and_record_movement()
    {
        $this->actingAs($this->admin);

        $supplier = Supplier::create([
            'name' => 'Frigorífico Concepción',
            'ruc' => '80011223-4',
            'active' => true,
        ]);

        $product = Product::create([
            'name' => 'Res Entera Frigorífico',
            'sku' => 'PUR-01',
            'price_cost' => 25000,
            'price_sale' => 35000,
            'stock' => 0,
            'manage_stock' => true,
            'is_active' => true,
        ]);

        Livewire::test(PurchasesCreate::class)
            ->set('supplier_id', $supplier->id)
            ->set('purchase_date', now()->format('Y-m-d'))
            ->set('items', [
                'p' . $product->id => [
                    'id' => $product->id,
                    'variation_id' => null,
                    'name' => $product->name,
                    'quantity' => 150.500, // 150.5 Kg
                    'cost_price' => 25000,
                ]
            ])
            ->call('calculateTotal')
            ->call('save')
            ->assertHasNoErrors();

        // Verificar que el stock subió tanto global como en la sucursal
        $this->assertEquals(150.5, (float)$product->fresh()->stock);
        $this->assertEquals(150.5, (float)$product->branchStocks()->where('branch_id', $this->branch->id)->value('stock'));

        // Verificar movimiento kardex
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 150.5,
        ]);
    }

    /**
     * Test 9: Anulación de venta reversa deuda del cliente y stock
     */
    public function test_void_sale_reverses_customer_debt_and_cash_movement()
    {
        $this->actingAs($this->admin);

        CashSession::create([
            'user_id' => $this->admin->id,
            'cash_register_id' => $this->register->id,
            'opening_amount' => 100000,
            'opened_at' => now(),
            'status' => 'open',
        ]);

        $customer = Customer::create([
            'name' => 'Cliente Deudor',
            'ruc' => '112233-4',
            'is_active' => true,
        ]);

        $corte = Product::create([
            'name' => 'Osobuco',
            'sku' => 'OSO-01',
            'price_sale' => 30000,
            'stock' => 10,
            'manage_stock' => true,
            'is_active' => true,
        ]);
        $corte->branchStocks()->create(['branch_id' => $this->branch->id, 'stock' => 10]);

        $service = app(SalesService::class);
        $sale = $service->processSale([
            'user_id' => $this->admin->id,
            'branch_id' => $this->branch->id,
            'customer_id' => $customer->id,
            'total' => 60000,
            'payment_condition' => 'credito',
            'cart' => [
                'p' . $corte->id => [
                    'id' => $corte->id,
                    'name' => $corte->name,
                    'price' => 30000,
                    'quantity' => 2.0,
                    'subtotal' => 60000,
                ]
            ]
        ]);

        $this->assertEquals(60000, (float)$customer->balance);
        $this->assertEquals(8, (float)$corte->fresh()->stock);

        // Anular venta desde POS
        Livewire::test(SalesInterface::class)
            ->call('voidSale', $sale->id);

        // Validar que la deuda quedó en 0 y el stock en 10
        $this->assertEquals(0, (float)$customer->fresh()->balance, 'La deuda del cliente debió reversarse');
        $this->assertEquals(10, (float)$corte->fresh()->stock, 'El stock debió restaurarse a 10');
        $this->assertEquals('voided', $sale->fresh()->status);
    }
}

