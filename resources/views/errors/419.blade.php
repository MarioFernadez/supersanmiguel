<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesión Expirada • {{ config('app.name', 'ZonaShop') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-[#0f172a] text-slate-100 flex items-center justify-center min-h-screen p-4 font-sans">
    <div class="max-w-md w-full bg-[#1e293b] rounded-3xl shadow-2xl border border-slate-700/80 p-8 text-center relative overflow-hidden">
        
        {{-- Background ambient glow --}}
        <div class="absolute -top-12 -right-12 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        {{-- Lock Icon --}}
        <div class="w-20 h-20 bg-amber-500/15 border border-amber-500/30 rounded-2xl flex items-center justify-center mx-auto mb-6 text-amber-400 shadow-inner">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>

        {{-- Badge --}}
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-black uppercase tracking-widest mb-3">
            <span>⏱️ Inactividad Detectada</span>
        </div>

        <h1 class="text-2xl font-black text-white tracking-tight mb-2">
            Tu sesión ha expirado
        </h1>
        
        <p class="text-slate-400 text-sm mb-8 font-normal leading-relaxed">
            Por seguridad, tu sesión se cerró automáticamente tras un período sin actividad. Por favor, vuelve a iniciar sesión para continuar operando.
        </p>

        {{-- Action Buttons --}}
        <div class="space-y-3">
            <a href="{{ route('login', ['expired' => 1]) }}"
               class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold text-sm rounded-xl transition shadow-lg shadow-emerald-950/40 active:scale-98">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span>Volver a Iniciar Sesión</span>
            </a>
        </div>

        {{-- Footer Brand --}}
        <div class="mt-8 pt-6 border-t border-slate-700/60 text-[11px] text-slate-500 font-mono">
            ZonaShop • Sistema de Gestión y POS
        </div>

    </div>
</body>
</html>
