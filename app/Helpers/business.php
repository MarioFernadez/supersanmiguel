<?php

if (!function_exists('businessType')) {
    /**
     * Get the current business type of the store.
     * Default to 'generic' if not set.
     */
    function businessType(): string
    {
        static $type = null;
        if ($type !== null) {
            return $type;
        }

        $type = \Illuminate\Support\Facades\Cache::remember('store_business_type', 3600, function () {
            return \App\Models\StoreSetting::where('key', 'business_type')->value('value') ?? 'generic';
        });

        return $type;
    }
}

if (!function_exists('hasModule')) {
    /**
     * Check if a specific module is active based on the business type.
     */
    function hasModule(string $module): bool
    {
        $type = businessType();
        
        $modules = [
            'generic' => [
                'meat_deboning',
                'weight_scale',
                'expiration_dates',
            ],
            'minimarket' => [
                'meat_deboning',
                'weight_scale',
                'expiration_dates',
            ],
            'boutique' => [
                'variations', // colors, sizes
            ],
            'perfumery' => [
                'decants', // fractionation
            ],
            'restaurant' => [
                'restaurant', // table map, comandas, waiters
            ],
        ];

        return in_array($module, $modules[$type] ?? []);
    }
}
