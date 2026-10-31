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
use App\Models\PaymentMethod;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\StoreSetting;
use App\Services\SalesService;
use App\Livewire\Pos\SalesInterface;
use App\Livewire\Cash\SessionManager;
use App\Livewire\Dashboard;
use App\Livewire\Reports\Sales as SalesReport;
use App\Livewire\Reports\CashSessions;
use App\Livewire\Reports\Inventory as InventoryReport;
use App\Livewire\Settings\General as SettingsGeneral;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class UserSimulationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Branch $branch;
    protected CashRegister $register;
    protected PaymentMethod $cashMethod;
    protected PaymentMethod $transferMethod;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup base store settings
        StoreSetting::updateOrCreate(['key' => 'store_name'], ['value' => 'ZonaShop']);
        StoreSetting::updateOrCreate(['key' => 'license_max_users'], ['value' => '10']);
        StoreSetting::updateOrCreate(['key' => 'license_max_branches'], ['value' => '5']);
        StoreSetting::updateOrCreate(['key' => 'license_max_cash_registers'], ['value' => '5']);
        StoreSetting::updateOrCreate(['key' => 'license_tier_name'], ['value' => 'Plan Enterprise']);
        StoreSetting::updateOrCreate(['key' => 'license_expires_at'], ['value' => now()->addYear()->format('Y-m-d')]);

        // 2. Setup Branch & Cash Register
        $this->branch = Branch::create([
            'name' => 'Sucursal Central',
            'address' => 'Av. San Martín 1234',
            'phone' => '0981112233',
            'is_active' => true,
        ]);

        $this->register = CashRegister::create([
            'name' => 'Caja Principal 01',
            'branch_id' => $this->branch->id,
            'active' => true,
        ]);

        // 3. Setup Admin User
        $this->admin = User::factory()->create([
            'name' => 'Carlos Administrador',
            'email' => 'admin@zonapos.com',
            'role' => 'admin',
            'branch_id' => $this->branch->id,
        ]);

        // 4. Payment Methods
        $this->cashMethod = PaymentMethod::create([
            'name' => 'Efectivo',
            'code' => 'efectivo',
            'type' => 'cash',
            'is_active' => true,
        ]);

        $this->transferMethod = PaymentMethod::create([
            'name' => 'Transferencia Bancaria',
            'code' => 'transferencia',
            'type' => 'transfer',
            'is_active' => true,
        ]);

        // 5. Category
        $this->category = Category::create([
            'name' => 'Bebidas & Snacks',
            'slug' => 'bebidas-snacks',
            'is_active' => true,
        ]);
    }

    /**
     * Simulación 1: Inicio de sesión y navegación por el Dashboard
     */
    public function test_user_can_login_and_view_clean_dashboard()
    {
        $this->actingAs($this->admin);

        // Visitar login estando autenticado debe redirigir
        $response = $this->get('/login');
        $response->assertRedirect('/dashboard');

        // Acceder a la ruta raíz '/' debe redirigir a dashboard
        $rootResponse = $this->get('/');
        $rootResponse->assertRedirect('/dashboard');

        // Cargar vista del Dashboard
        $dashboardResponse = $this->get('/dashboard');
        $dashboardResponse->assertStatus(200);

        // Testear componente Livewire del Dashboard directamente
        Livewire::test(Dashboard::class)
            ->assertOk()
            ->assertSeeHtml('Ventas Hoy')
            ->assertDontSeeHtml('Pedidos Web Pendientes'); // Validar que NO existe e-commerce
    }

    /**
     * Simulación 2: Apertura de caja con saldo inicial
     */
    public function test_user_can_open_cash_register_session()
    {
        $this->actingAs($this->admin);

        // El usuario entra a gestión de caja
        $component = Livewire::test(SessionManager::class)
            ->set('selectedRegisterId', $this->register->id)
            ->set('openingAmount', 150000)
            ->call('openSession')
            ->assertHasNoErrors();

        // Validar que en BD la sesión esté abierta
        $session = CashSession::where('user_id', $this->admin->id)
            ->where('status', 'open')
            ->first();

        $this->assertNotNull($session, 'La sesión de caja debe estar abierta');
        $this->assertEquals(150000, (float) $session->opening_amount);
        $this->assertEquals($this->register->id, $session->cash_register_id);
    }

    /**
     * Simulación 3: Gestión de productos e inventario
     */
    public function test_user_can_manage_products_and_stock()
    {
        $this->actingAs($this->admin);

        // Crear producto
        $product = Product::create([
            'name' => 'Gaseosa Cola 500ml',
            'slug' => 'gaseosa-cola-500ml',
            'sku' => 'BEB-001',
            'category_id' => $this->category->id,
            'price_cost' => 4500,
            'price_sale' => 8000,
            'stock' => 100,
            'min_stock' => 10,
            'unit_type' => 'unit',
            'manage_stock' => true,
            'is_active' => true,
        ]);

        // Asignar stock a la sucursal
        $product->branchStocks()->create([
            'branch_id' => $this->branch->id,
            'stock' => 100,
        ]);

        $this->assertDatabaseHas('products', ['sku' => 'BEB-001']);
        $this->assertEquals(100, $product->branchStocks()->where('branch_id', $this->branch->id)->value('stock'));
    }

    /**
     * Simulación 4: Registro de cliente y venta completa en el POS (en Efectivo)
     */
    public function test_user_completes_pos_cash_sale_with_inventory_deduction()
    {
        $this->actingAs($this->admin);

        // 1. Abrir caja con 200.000 Gs
        $session = CashSession::create([
            'user_id' => $this->admin->id,
            'cash_register_id' => $this->register->id,
            'opening_amount' => 200000,
            'opened_at' => now(),
            'status' => 'open',
        ]);

        // 2. Producto de venta
        $product = Product::create([
            'name' => 'Alfajor Artesanal Chocolate',
            'slug' => 'alfajor-artesanal-chocolate',
            'sku' => 'SNK-100',
            'category_id' => $this->category->id,
            'price_cost' => 3000,
            'price_sale' => 6000,
            'stock' => 50,
            'manage_stock' => true,
            'is_active' => true,
        ]);
        $product->branchStocks()->create([
            'branch_id' => $this->branch->id,
            'stock' => 50,
        ]);

        // 3. Cliente
        $customer = Customer::create([
            'name' => 'María Benítez',
            'ruc' => '4567890-1',
            'phone' => '0982555666',
            'is_active' => true,
        ]);

        // 4. Venta en Livewire POS
        $component = Livewire::test(SalesInterface::class)
            ->call('addToCart', $product->id);

        // Establecer cantidad a 3 unidades (3 * 6.000 = 18.000 Gs)
        $component->call('setQuantity', 'p' . $product->id, 3);
        $this->assertEquals(18000, $component->get('total'));

        // Seleccionar cliente
        $component->set('selectedCustomer', [
            'id' => $customer->id,
            'name' => $customer->name,
            'ruc' => $customer->ruc,
        ]);

        // Configurar pago en efectivo
        $component->set('paymentCondition', 'contado')
            ->set('paymentMethod', 'efectivo')
            ->set('receivedAmount', 20000);

        // Procesar venta
        $component->call('processSale', app(SalesService::class))
            ->assertHasNoErrors();

        // 5. Validaciones post-venta
        // a) Venta creada en BD
        $this->assertEquals(1, Sale::count());
        $sale = Sale::first();
        $this->assertEquals(18000, (float) $sale->total);
        $this->assertEquals($customer->id, $sale->customer_id);
        $this->assertEquals('completed', $sale->status);

        // b) Stock descontado de 50 a 47
        $this->assertEquals(47, (float) $product->branchStocks()->where('branch_id', $this->branch->id)->value('stock'));

        // c) Detalle de ítems
        $this->assertEquals(1, $sale->items()->count());
        $item = $sale->items()->first();
        $this->assertEquals(3, $item->quantity);
        $this->assertEquals(6000, (float) $item->price_unit);
        $this->assertEquals(18000, (float) $item->subtotal);

        // d) Pago registrado
        $this->assertEquals(1, $sale->payments()->count());
        $payment = $sale->payments()->first();
        $this->assertEquals('efectivo', $payment->method);
        $this->assertEquals(18000, (float) $payment->amount);

        // e) Carrito reseteado en POS
        $this->assertEmpty($component->get('cart'));
    }

    /**
     * Simulación 5: Registro de gastos menores y verificación en balance de caja
     */
    public function test_user_can_register_store_expense()
    {
        $this->actingAs($this->admin);

        // Abrir caja
        $session = CashSession::create([
            'user_id' => $this->admin->id,
            'cash_register_id' => $this->register->id,
            'opening_amount' => 300000,
            'opened_at' => now(),
            'status' => 'open',
        ]);

        $expenseCat = ExpenseCategory::create([
            'name' => 'Limpieza e Insumos',
            'is_active' => true,
        ]);

        // Registrar gasto
        $expense = Expense::create([
            'expense_category_id' => $expenseCat->id,
            'user_id' => $this->admin->id,
            'branch_id' => $this->branch->id,
            'cash_session_id' => $session->id,
            'description' => 'Compra de detergente y lavandina',
            'amount' => 35000,
            'date' => now(),
            'payment_method' => 'efectivo',
        ]);

        $this->assertDatabaseHas('expenses', [
            'description' => 'Compra de detergente y lavandina',
            'amount' => 35000,
        ]);
    }

    /**
     * Simulación 6: Navegación de Reportes (Ventas, Inventario y Caja)
     */
    public function test_user_can_consult_reports_without_ecommerce_dependencies()
    {
        $this->actingAs($this->admin);

        // 1. Reporte de Ventas
        Livewire::test(SalesReport::class)
            ->assertOk();

        // 2. Reporte de Inventario
        Livewire::test(InventoryReport::class)
            ->assertOk();

        // 3. Reporte de Sesiones de Caja
        Livewire::test(CashSessions::class)
            ->assertOk();
    }

    /**
     * Simulación 7: Ajustes del sistema y comprobación de marca ZonaShop
     */
    public function test_user_can_view_and_save_general_settings()
    {
        $this->actingAs($this->admin);

        Livewire::test(SettingsGeneral::class)
            ->assertSet('store_name', 'ZonaShop')
            ->set('store_phone', '0981999888')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals('0981999888', StoreSetting::where('key', 'store_phone')->first()->value);
    }

    /**
     * Simulación 8: Arqueo y Cierre formal de caja
     */
    public function test_user_closes_cash_session_with_reconciliation()
    {
        $this->actingAs($this->admin);

        $session = CashSession::create([
            'user_id' => $this->admin->id,
            'cash_register_id' => $this->register->id,
            'opening_amount' => 100000,
            'opened_at' => now(),
            'status' => 'open',
        ]);

        // Simular venta en efectivo de 50.000 Gs
        $sale = Sale::create([
            'user_id' => $this->admin->id,
            'branch_id' => $this->branch->id,
            'total' => 50000,
            'status' => 'completed',
        ]);
        $sale->payments()->create([
            'method' => 'efectivo',
            'amount' => 50000,
        ]);

        // Cierre de caja en SessionManager
        // Apertura (100.000) + Venta (50.000) = 150.000 esperado
        $component = Livewire::test(SessionManager::class)
            ->call('loadCurrentSession');

        $component->set('closingAmount', 150000)
            ->set('notes', 'Cierre de turno correcto sin diferencias')
            ->call('closeSession')
            ->assertHasNoErrors();

        // Verificar que la sesión quedó cerrada
        $closedSession = $session->fresh();
        $this->assertEquals('closed', $closedSession->status);
        $this->assertEquals(150000, (float) $closedSession->closing_amount);
        $this->assertNotNull($closedSession->closed_at);
    }

    /**
     * Simulación 9: Venta a Crédito a un Cliente frecuente
     */
    public function test_user_can_process_credit_sale_generating_customer_debt()
    {
        $this->actingAs($this->admin);

        // Abrir caja
        CashSession::create([
            'user_id' => $this->admin->id,
            'cash_register_id' => $this->register->id,
            'opening_amount' => 50000,
            'opened_at' => now(),
            'status' => 'open',
        ]);

        $product = Product::create([
            'name' => 'Queso Paraguay 1kg',
            'slug' => 'queso-paraguay-1kg',
            'sku' => 'LAC-001',
            'category_id' => $this->category->id,
            'price_cost' => 20000,
            'price_sale' => 35000,
            'stock' => 20,
            'manage_stock' => true,
            'is_active' => true,
        ]);
        $product->branchStocks()->create([
            'branch_id' => $this->branch->id,
            'stock' => 20,
        ]);

        $customer = Customer::create([
            'name' => 'Roberto Gómez',
            'ruc' => '1234567-1',
            'current_balance' => 0,
            'is_active' => true,
        ]);

        $component = Livewire::test(SalesInterface::class)
            ->call('addToCart', $product->id)
            ->set('selectedCustomer', [
                'id' => $customer->id,
                'name' => $customer->name,
                'ruc' => $customer->ruc,
            ])
            ->set('paymentCondition', 'credito');

        $component->call('processSale', app(SalesService::class))
            ->assertHasNoErrors();

        $this->assertEquals(1, Sale::count());
        $sale = Sale::first();
        $this->assertEquals('credito', $sale->payment_condition);
        $this->assertEquals(35000, (float) $sale->total);

        // Validar que se registró la transacción a débito y el balance deudor en el cliente
        $this->assertEquals(1, $customer->transactions()->count());
        $this->assertEquals(35000, (float) $customer->balance);
    }

    /**
     * Simulación 10: Venta con Múltiples Medios de Pago (Efectivo + Transferencia)
     */
    public function test_user_can_process_split_payment_sale()
    {
        $this->actingAs($this->admin);

        // Abrir caja
        CashSession::create([
            'user_id' => $this->admin->id,
            'cash_register_id' => $this->register->id,
            'opening_amount' => 100000,
            'opened_at' => now(),
            'status' => 'open',
        ]);

        $product = Product::create([
            'name' => 'Aceite de Girasol 900ml',
            'slug' => 'aceite-girasol-900ml',
            'sku' => 'ALM-002',
            'category_id' => $this->category->id,
            'price_cost' => 8000,
            'price_sale' => 14000,
            'stock' => 30,
            'manage_stock' => true,
            'is_active' => true,
        ]);
        $product->branchStocks()->create([
            'branch_id' => $this->branch->id,
            'stock' => 30,
        ]);

        $component = Livewire::test(SalesInterface::class)
            ->call('addToCart', $product->id)
            ->set('paymentCondition', 'contado')
            ->set('paymentMethod', 'multiple')
            ->set('multiplePayments', [
                'efectivo' => 4000,
                'transferencia' => 10000,
            ])
            ->set('receivedAmount', 14000);

        $component->call('processSale', app(SalesService::class))
            ->assertHasNoErrors();

        $sale = Sale::first();
        $this->assertEquals(14000, (float) $sale->total);
        $this->assertEquals(2, $sale->payments()->count());

        $this->assertEquals(4000, (float) $sale->payments()->where('method', 'efectivo')->value('amount'));
        $this->assertEquals(10000, (float) $sale->payments()->where('method', 'transferencia')->value('amount'));
    }
}

