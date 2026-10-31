<div class="py-10 bg-gray-50/50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Welcome Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Panel de Control</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm font-normal mt-1">Resumen operativo para {{ auth()->user()->name }} • {{ now()->isoFormat('LL') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('pos.index') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold uppercase tracking-wider rounded-xl transition-all shadow-sm hover:shadow hover:-translate-y-0.5 active:translate-y-0">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Nueva Venta (POS)
                </a>
            </div>
        </div>

        @php
            $calcSparkline = function(array $data, int $width = 72, int $height = 24) {
                if (empty($data)) return '';
                $max = max($data) ?: 1;
                $min = min($data);
                $range = ($max - $min) ?: 1;
                $points = [];
                $count = count($data);
                foreach ($data as $i => $val) {
                    $x = ($count > 1) ? round(($i / ($count - 1)) * $width, 1) : 0;
                    $y = round($height - (($val - $min) / $range) * ($height - 6) - 3, 1);
                    $points[] = "{$x},{$y}";
                }
                return implode(' ', $points);
            };
        @endphp

        <!-- KPI Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Sales Today -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700/80 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 dark:bg-indigo-900/20 rounded-full opacity-50 group-hover:scale-125 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ventas Hoy</p>
                        {{-- Mini Sparkline --}}
                        <svg class="w-[72px] h-[24px] overflow-visible" viewBox="0 0 72 24">
                            <polyline fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                                class="text-indigo-500 dark:text-indigo-400"
                                points="{{ $calcSparkline($salesData ?? []) }}" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white leading-none tabular-nums"><span class="text-sm font-normal text-gray-400 mr-0.5">₲</span>{{ number_format($totalSalesToday, 0, ',', '.') }}</h3>
                    <div class="mt-4 flex items-center gap-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 dark:bg-indigo-950/40 dark:text-indigo-300 px-2.5 py-1 rounded-lg w-fit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        Tendencia 7 Días
                    </div>
                </div>
            </div>

            <!-- Profit -->
            @can('gerente')
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700/80 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 dark:bg-emerald-900/20 rounded-full opacity-50 group-hover:scale-125 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Ganancia (Est.)</p>
                    <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 leading-none tabular-nums"><span class="text-sm font-normal text-emerald-400/80 mr-0.5">₲</span>{{ number_format($estimatedProfit, 0, ',', '.') }}</h3>
                    <div class="mt-4 text-xs font-normal text-gray-400 dark:text-gray-500">Margen s/ costo de compra</div>
                </div>
            </div>
            @endcan

            <!-- Customers -->
            <a href="{{ route('customers.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700/80 relative overflow-hidden group hover:border-blue-200 dark:hover:border-blue-500/50 hover:shadow-md transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-full opacity-50 group-hover:scale-125 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Clientes</p>
                        <span class="text-sm">👥</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white leading-none tabular-nums">{{ $totalCustomers }}</h3>
                    <div class="mt-4 text-xs font-normal text-gray-400 dark:text-gray-500">Base de clientes registrados</div>
                </div>
            </a>

            <!-- Open Sessions -->
            <a href="{{ route('cash.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700/80 relative overflow-hidden group hover:border-purple-200 dark:hover:border-purple-500/50 hover:shadow-md transition-all duration-300">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-50 dark:bg-purple-900/20 rounded-full opacity-50 group-hover:scale-125 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cajas Abiertas</p>
                        <span class="text-sm">💻</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white leading-none tabular-nums">{{ $openSessionsCount }}</h3>
                    <div class="mt-4 flex items-center text-xs font-medium text-purple-700 bg-purple-50 dark:bg-purple-950/40 dark:text-purple-300 border border-purple-200/60 px-2.5 py-1 rounded-lg w-fit">
                        En operación
                    </div>
                </div>
            </a>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-7 shadow-sm border border-gray-100 dark:border-gray-700/80">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Tendencia de Ventas (7 Días)</h3>
                    <span class="text-xs font-normal text-gray-400 dark:text-gray-500">Valores en Guaraníes (₲)</span>
                </div>
                <div class="h-[280px]">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-7 shadow-sm border border-gray-100 dark:border-gray-700/80">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Top Productos</h3>
                    <span class="text-xs font-normal text-gray-400 dark:text-gray-500">Últimos 30 días</span>
                </div>
                <div class="h-[280px]">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Lists Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Sales -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700/80">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700/80 flex items-center justify-between bg-gray-50/50 dark:bg-gray-900/30">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Últimas Ventas</h3>
                    <a href="{{ route('reports.sales') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:underline dark:text-indigo-400">Ver todas →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-sans">
                        <thead>
                            <tr class="bg-gray-50/80 dark:bg-gray-900/50 text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                                <th class="px-6 py-3.5">Hora</th>
                                <th class="px-6 py-3.5">Cliente</th>
                                <th class="px-6 py-3.5 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/60">
                            @forelse($recentSales as $sale)
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition-colors group">
                                <td class="px-6 py-3.5">
                                    <span class="text-xs font-mono text-gray-400 dark:text-gray-500">{{ $sale->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $sale->customer ? $sale->customer->name : 'Consumidor Final' }}</p>
                                    <p class="text-xs font-normal text-gray-400 dark:text-gray-500">{{ $sale->user->name }}</p>
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 tabular-nums"><span class="text-xs text-gray-400 font-normal mr-0.5">₲</span>{{ number_format($sale->total, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-6 py-10 text-center text-gray-400 text-xs italic dark:text-gray-500">Sin actividad reciente</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Low Stock Alerts -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700/80">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700/80 flex items-center justify-between bg-gray-50/50 dark:bg-gray-900/30">
                    <h3 class="text-sm font-bold text-rose-600 dark:text-rose-400 flex items-center">
                        <span class="relative flex h-2 w-2 mr-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                        </span>
                        Alertas de Stock
                    </h3>
                    <a href="{{ route('products.index') }}" class="text-xs font-medium text-gray-400 hover:text-rose-600 hover:underline dark:text-gray-500">Gestionar →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-sans">
                        <thead>
                            <tr class="bg-rose-50/30 dark:bg-rose-950/20 text-[11px] font-semibold text-rose-500 dark:text-rose-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                                <th class="px-6 py-3.5">Producto</th>
                                <th class="px-6 py-3.5 text-center">Actual</th>
                                <th class="px-6 py-3.5 text-center">Mínimo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/60">
                            @forelse($lowStockProducts as $product)
                            <tr class="hover:bg-rose-50/20 dark:hover:bg-rose-950/10 transition-colors">
                                <td class="px-6 py-3.5 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $product->name }}</td>
                                <td class="px-6 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200/60 text-xs font-semibold tabular-nums">{{ $product->stock }}</span>
                                </td>
                                <td class="px-6 py-3.5 text-center text-xs font-normal text-gray-400 dark:text-gray-500 tabular-nums">{{ $product->min_stock }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-6 py-10 text-center text-emerald-600 dark:text-emerald-400 font-medium text-xs">✨ Todo el stock se encuentra en niveles óptimos</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:navigated', () => initDashboardCharts());
        window.addEventListener('load', () => initDashboardCharts());

        window.addEventListener('theme-changed', () => initDashboardCharts());

        function initDashboardCharts() {
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? '#374151' : '#f9fafb';
            const textColor = isDark ? '#d1d5db' : '#9ca3af';

            // Line Chart: Sales Trend
            const salesCtx = document.getElementById('salesChart');
            if (salesCtx) {
                if (window.salesChartInstance) window.salesChartInstance.destroy();
                window.salesChartInstance = new Chart(salesCtx, {
                    type: 'line',
                    data: {
                        labels: @json($salesLabels),
                        datasets: [{
                            data: @json($salesData),
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.05)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            borderWidth: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { grid: { color: gridColor }, ticks: { font: { size: 10, weight: 'bold' }, color: textColor } },
                            x: { grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' }, color: textColor } }
                        }
                    }
                });
            }

            // Bar Chart: Top Products
            const topCtx = document.getElementById('topProductsChart');
            if (topCtx) {
                if (window.topChartInstance) window.topChartInstance.destroy();
                window.topChartInstance = new Chart(topCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($topProducts->pluck('product_name')),
                        datasets: [{
                            data: @json($topProducts->pluck('total_qty')),
                            backgroundColor: '#6366f1',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' }, color: textColor } },
                            y: { grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' }, color: textColor } }
                        }
                    }
                });
            }
        }
    </script>
    @endpush
</div>
