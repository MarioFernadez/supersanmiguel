<div class="py-12 bg-gray-50/50 min-h-screen font-sans dark:bg-slate-950">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-10">
        
        <!-- Header Principal -->
        <div class="bg-gradient-to-r from-gray-900 via-slate-900 to-indigo-950 px-8 py-8 rounded-[2.5rem] shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <span class="px-3 py-1 bg-yellow-400/10 text-yellow-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-yellow-400/20">Master Access</span>
                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-ping"></span>
                </div>
                <h2 class="text-3xl font-black text-white uppercase tracking-tight">Panel de Gestión Maestro</h2>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest leading-relaxed">Configuración de Licencias, Límites y Control de Cobros</p>
            </div>
            <div class="bg-gradient-to-tr from-yellow-400 to-amber-300 p-4 rounded-3xl shadow-lg shadow-yellow-500/20 shrink-0 transform hover:rotate-6 transition duration-300">
                <svg class="w-7 h-7 text-gray-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-800 p-5 rounded-3xl flex items-center shadow-sm dark:bg-emerald-950 dark:border-emerald-900/50 dark:text-emerald-400" role="alert">
                <div class="bg-emerald-100 p-2 rounded-2xl text-emerald-600 mr-4 dark:bg-emerald-900/50 dark:text-emerald-400">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                </div>
                <div>
                    <h4 class="text-xs font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Actualización Exitosa</h4>
                    <p class="font-bold text-xs text-emerald-800/80 mt-0.5 dark:text-emerald-400/80">{{ session('message') }}</p>
                </div>
            </div>
        @endif

        <!-- Card 1: Configuración de Licencia Maestra -->
        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-100/50 border border-slate-100 overflow-hidden dark:bg-slate-900 dark:border-slate-800">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center gap-3 bg-slate-50/50 dark:bg-slate-800/50 dark:border-slate-800">
                <span class="w-1.5 h-4 bg-indigo-600 rounded-full"></span>
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest dark:text-white">Configuración de Límites y Vigencia</h3>
            </div>
            <div class="p-8">
                <form wire:submit.prevent="updateLicense" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                        <!-- Plan Tier -->
                        <div class="col-span-3">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2.5 dark:text-slate-500">Nombre del Plan / Tier de Licencia</label>
                            <input type="text" wire:model="tierName" placeholder="Ej: Plan Corporativo Pro"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition duration-300 shadow-inner dark:bg-slate-950 dark:border-slate-800 dark:text-white">
                            @error('tierName') <span class="text-red-500 text-xs mt-1.5 block font-bold tracking-tight">{{ $message }}</span> @enderror
                        </div>
 
                        <!-- Max Users -->
                        <div class="bg-indigo-50/30 p-6 rounded-3xl border border-indigo-100/50 transition hover:shadow-md duration-300 dark:bg-indigo-950/20 dark:border-indigo-900/30">
                            <label class="block text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-4">Límite Máximo de Usuarios</label>
                            <div class="relative items-center flex">
                                <div class="absolute left-4 text-indigo-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                </div>
                                <input type="number" wire:model="maxUsers" 
                                       class="w-full pl-14 pr-5 py-4 bg-white border border-indigo-100 rounded-2xl text-2xl font-black text-indigo-700 focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition duration-300 shadow-sm dark:bg-slate-950 dark:border-slate-800 dark:text-indigo-400">
                            </div>
                            @error('maxUsers') <span class="text-red-500 text-xs mt-2 block font-bold tracking-tight">{{ $message }}</span> @enderror
                            <p class="text-[9px] text-indigo-400/80 mt-3.5 uppercase font-black tracking-wider dark:text-indigo-500/80">Afecta la creación de Administradores, Gerentes y Cajeros</p>
                        </div>
 
                        <!-- Max Registers -->
                        <div class="bg-purple-50/30 p-6 rounded-3xl border border-purple-100/50 transition hover:shadow-md duration-300 dark:bg-purple-950/20 dark:border-purple-900/30">
                            <label class="block text-[10px] font-black text-purple-500 uppercase tracking-widest mb-4">Límite Máximo de Cajas POS</label>
                            <div class="relative items-center flex">
                                <div class="absolute left-4 text-purple-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </div>
                                <input type="number" wire:model="maxCashRegisters" 
                                       class="w-full pl-14 pr-5 py-4 bg-white border border-purple-100 rounded-2xl text-2xl font-black text-purple-700 focus:ring-4 focus:ring-purple-100 focus:border-purple-400 transition duration-300 shadow-sm dark:bg-slate-950 dark:border-slate-800 dark:text-purple-400">
                            </div>
                            @error('maxCashRegisters') <span class="text-red-500 text-xs mt-2 block font-bold tracking-tight">{{ $message }}</span> @enderror
                            <p class="text-[9px] text-purple-400/80 mt-3.5 uppercase font-black tracking-wider dark:text-purple-500/80">Cajas activas que pueden operar simultáneamente</p>
                        </div>

                        <!-- Max Branches -->
                        <div class="bg-amber-50/30 p-6 rounded-3xl border border-amber-100/50 transition hover:shadow-md duration-300 dark:bg-amber-950/20 dark:border-amber-900/30">
                            <label class="block text-[10px] font-black text-amber-500 uppercase tracking-widest mb-4">Límite Máximo de Sucursales</label>
                            <div class="relative items-center flex">
                                <div class="absolute left-4 text-amber-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                                <input type="number" wire:model="maxBranches" 
                                       class="w-full pl-14 pr-5 py-4 bg-white border border-amber-100 rounded-2xl text-2xl font-black text-amber-700 focus:ring-4 focus:ring-amber-100 focus:border-amber-400 transition duration-300 shadow-sm dark:bg-slate-950 dark:border-slate-800 dark:text-amber-400">
                            </div>
                            @error('maxBranches') <span class="text-red-500 text-xs mt-2 block font-bold tracking-tight">{{ $message }}</span> @enderror
                            <p class="text-[9px] text-amber-400/80 mt-3.5 uppercase font-black tracking-wider dark:text-amber-500/80">Límite de sucursales físicas que el comercio puede configurar</p>
                        </div>       </div>

                        <!-- Subscription Dates -->
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2.5 dark:text-slate-500">Fecha de Habilitación Inicial</label>
                            <input type="date" wire:model="enabledAt" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition duration-300 shadow-inner dark:bg-slate-950 dark:border-slate-800 dark:text-white">
                            @error('enabledAt') <span class="text-red-500 text-xs mt-1.5 block font-bold tracking-tight">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2.5 dark:text-slate-500">Fecha de Vencimiento de Suscripción</label>
                            <input type="date" wire:model="expiresAt" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-slate-900 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition duration-300 shadow-inner dark:bg-slate-950 dark:border-slate-800 dark:text-white">
                            @error('expiresAt') <span class="text-red-500 text-xs mt-1.5 block font-bold tracking-tight">{{ $message }}</span> @enderror
                        </div>

                        <!-- Sifen Facturación Electrónica Toggle -->
                        <div class="col-span-2 md:col-span-1 bg-indigo-50/30 p-6 rounded-3xl border border-indigo-100/50 transition hover:shadow-md duration-300 flex items-center justify-between dark:bg-indigo-950/20 dark:border-indigo-900/30">
                            <div class="space-y-1 pr-3">
                                <label class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest dark:text-indigo-400">Módulo Facturación Electrónica</label>
                                <p class="text-[9px] text-slate-400 font-black tracking-widest uppercase dark:text-slate-500">Habilita SIFEN (DNIT - e-kuatia), CDC y Códigos QR</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" wire:model="sifenMasterEnabled" class="sr-only peer">
                                <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-600 dark:bg-slate-800"></div>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full bg-slate-900 hover:bg-black text-white font-black py-4 rounded-2xl transition duration-300 transform active:scale-[0.98] shadow-xl shadow-slate-900/10 text-xs uppercase tracking-widest flex items-center justify-center gap-2 group dark:bg-indigo-600 dark:hover:bg-indigo-500">
                            <span>Actualizar Licencia Maestra</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card 2: Registrar Pagos y Auditoría Histórica -->
        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-100/50 border border-slate-100 overflow-hidden dark:bg-slate-900 dark:border-slate-800">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center gap-3 bg-slate-50/50 dark:bg-slate-800/50 dark:border-slate-800">
                <span class="w-1.5 h-4 bg-indigo-600 rounded-full"></span>
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest dark:text-white">Cobros y Comprobantes de Suscripción</h3>
            </div>

            <div class="p-8">
                @if (session()->has('payment_message'))
                    <div class="mb-8 bg-emerald-50 border border-emerald-100 text-emerald-800 p-5 rounded-3xl flex items-center shadow-sm dark:bg-emerald-950 dark:border-emerald-900/50 dark:text-emerald-400" role="alert">
                        <div class="bg-emerald-100 p-2 rounded-2xl text-emerald-600 mr-4 dark:bg-emerald-900/50 dark:text-emerald-400">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Pago Registrado Correctamente</h4>
                            <p class="font-bold text-xs text-emerald-800/80 mt-0.5 dark:text-emerald-400/80">{{ session('payment_message') }}</p>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                    
                    <!-- Formulario de Registro de Pago -->
                    <div class="lg:col-span-5 bg-slate-50/80 border border-slate-100 rounded-3xl p-6 md:p-8 space-y-6 dark:bg-slate-800 dark:border-slate-700">
                        <div class="space-y-1">
                            <span class="inline-flex px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded text-[9px] font-black uppercase tracking-widest dark:bg-indigo-900 dark:text-indigo-300">Acción Contable</span>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-tight dark:text-white">Registrar Cobro</h4>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider leading-relaxed">Loguea un pago recibido para extender la suscripción.</p>
                        </div>

                        <form wire:submit.prevent="registerPayment" class="space-y-5">
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Monto del Pago (₲)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3 text-slate-300 font-black text-md font-mono">₲</span>
                                    <input type="number" wire:model="paymentAmount" placeholder="Ej: 150000"
                                           class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-4 py-3 text-sm font-black text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition shadow-sm dark:bg-slate-950 dark:border-slate-700 dark:text-white">
                                </div>
                                @error('paymentAmount') <span class="text-red-500 text-[10px] font-bold uppercase mt-1.5 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Fecha Cobro</label>
                                    <input type="date" wire:model="paymentDate"
                                           class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition shadow-sm dark:bg-slate-950 dark:border-slate-700 dark:text-white">
                                    @error('paymentDate') <span class="text-red-500 text-[10px] font-bold uppercase mt-1.5 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Método de Pago</label>
                                    <select wire:model="paymentMethod"
                                            class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition appearance-none shadow-sm dark:bg-slate-950 dark:border-slate-700 dark:text-white">
                                        <option value="transferencia">🏦 Transferencia</option>
                                        <option value="efectivo">💵 Efectivo</option>
                                    </select>
                                    @error('paymentMethod') <span class="text-red-500 text-[10px] font-bold uppercase mt-1.5 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <button type="submit"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3.5 rounded-xl transition shadow-lg shadow-indigo-200 text-xs uppercase tracking-widest flex items-center justify-center gap-2 transform active:scale-[0.98]">
                                <span>Registrar Cobro</span>
                            </button>
                        </form>
                    </div>

                    <!-- Historial de Pagos -->
                    <div class="lg:col-span-7 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest dark:text-white">Historial de Transacciones</h4>
                        </div>
                        
                        <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm dark:bg-slate-900 dark:border-slate-800">
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs">
                                    <thead class="bg-slate-50 uppercase text-[9px] tracking-widest font-black text-slate-400 border-b border-slate-100 dark:bg-slate-800 dark:border-slate-700">
                                        <tr>
                                            <th class="px-5 py-4 text-left">Fecha</th>
                                            <th class="px-5 py-4 text-center">Método</th>
                                            <th class="px-5 py-4 text-right">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50 font-semibold text-slate-700 dark:divide-slate-800 dark:text-slate-300">
                                        @forelse($payments as $p)
                                            <tr class="hover:bg-slate-50/50 transition dark:hover:bg-slate-800">
                                                <td class="px-5 py-4 font-mono font-bold">{{ $p->payment_date->format('d/m/Y') }}</td>
                                                <td class="px-5 py-4 text-center">
                                                    <span class="inline-flex px-2.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $p->payment_method === 'transferencia' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700' }}">
                                                        {{ $p->payment_method }}
                                                    </span>
                                                </td>
                                                <td class="px-5 py-4 text-right font-black tabular-nums">Gs. {{ number_format($p->amount, 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-5 py-12 text-center text-slate-400 font-bold uppercase tracking-wider">No hay registros.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer de Seguridad -->
        <div class="flex items-center justify-center gap-2 text-slate-300 pb-6">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
            <span class="text-[9px] uppercase font-black tracking-[0.35em] text-slate-400">ZonaPOS Master Authentication Layer</span>
        </div>
        
    </div>
</div>
