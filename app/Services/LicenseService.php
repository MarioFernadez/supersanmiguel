<?php

namespace App\Services;

use App\Models\User;
use App\Models\CashRegister;
use App\Models\StoreSetting;

class LicenseService
{
    public static function getMaxUsers(): int
    {
        return (int) StoreSetting::where('key', 'license_max_users')->first()?->value ?: 3;
    }

    public static function getMaxCashRegisters(): int
    {
        return (int) StoreSetting::where('key', 'license_max_cash_registers')->first()?->value ?: 1;
    }

    public static function getMaxBranches(): int
    {
        return (int) StoreSetting::where('key', 'license_max_branches')->first()?->value ?: 1;
    }

    public static function getTierName(): string
    {
        return StoreSetting::where('key', 'license_tier_name')->first()?->value ?: 'Básico';
    }

    public static function isSifenEnabled(): bool
    {
        $setting = StoreSetting::where('key', 'sifen_master_enabled')->first();
        return $setting ? ($setting->value === '1' || $setting->value === 1) : true;
    }

    public static function getCurrentUserCount(): int
    {
        return User::where('email', '!=', config('services.developer.email'))->count();
    }

    public static function getCurrentCashRegisterCount(): int
    {
        return CashRegister::count();
    }

    public static function getCurrentBranchCount(): int
    {
        return \App\Models\Branch::count();
    }

    public static function canAddUser(): bool
    {
        return self::getCurrentUserCount() < self::getMaxUsers();
    }

    public static function canAddCashRegister(): bool
    {
        return self::getCurrentCashRegisterCount() < self::getMaxCashRegisters();
    }

    public static function canAddBranch(): bool
    {
        return self::getCurrentBranchCount() < self::getMaxBranches();
    }

    public static function getUsageStats(): array
    {
        return [
            'tier' => self::getTierName(),
            'users' => [
                'current' => self::getCurrentUserCount(),
                'max' => self::getMaxUsers(),
                'available' => max(0, self::getMaxUsers() - self::getCurrentUserCount()),
            ],
            'cash_registers' => [
                'current' => self::getCurrentCashRegisterCount(),
                'max' => self::getMaxCashRegisters(),
                'available' => max(0, self::getMaxCashRegisters() - self::getCurrentCashRegisterCount()),
            ],
            'branches' => [
                'current' => self::getCurrentBranchCount(),
                'max' => self::getMaxBranches(),
                'available' => max(0, self::getMaxBranches() - self::getCurrentBranchCount()),
            ],
        ];
    }

    public static function getEnabledAt(): \Carbon\Carbon
    {
        $date = StoreSetting::where('key', 'license_enabled_at')->first()?->value;
        return $date ? \Carbon\Carbon::parse($date)->startOfDay() : now()->startOfDay();
    }

    public static function getExpiresAt(): \Carbon\Carbon
    {
        $date = StoreSetting::where('key', 'license_expires_at')->first()?->value;
        return $date ? \Carbon\Carbon::parse($date)->endOfDay() : now()->addMonth()->endOfDay();
    }

    public static function isExpired(): bool
    {
        return now()->gt(self::getExpiresAt());
    }

    public static function getDaysRemaining(): int
    {
        $expires = self::getExpiresAt();
        if (now()->gt($expires)) {
            return 0;
        }
        return now()->startOfDay()->diffInDays($expires->startOfDay());
    }

    public static function isNearExpiration(): bool
    {
        if (self::isExpired()) {
            return false;
        }
        return self::getDaysRemaining() <= 5;
    }

    public static function isGracePeriodUsed(): bool
    {
        $setting = StoreSetting::where('key', 'license_grace_period_used')->first();
        return $setting ? $setting->value === '1' : false;
    }

    public static function canActivateGracePeriod(): bool
    {
        return self::isExpired() && !self::isGracePeriodUsed();
    }
}
