<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Cek apakah sistem masih bisa diakses
     * Batas akhir: 26 Mei 2026
     * 
     * @return bool
     */
    public static function canAccess(): bool
    {
        $expiryDate = Carbon::create(2026, 5, 26, 23, 59, 59);
        $now = Carbon::now();

        return $now->lessThanOrEqualTo($expiryDate);
    }

    /**
     * Get remaining days
     * 
     * @return int
     */
    public static function getRemainingDays(): int
    {
        $expiryDate = Carbon::create(2026, 5, 26, 23, 59, 59);
        $now = Carbon::now();

        if ($now->greaterThan($expiryDate)) {
            return 0;
        }

        return $now->diffInDays($expiryDate);
    }

    /**
     * Get formatted expiry message
     * 
     * @return string
     */
    public static function getExpiryMessage(): string
    {
        if (self::canAccess()) {
            $days = self::getRemainingDays();
            return "Sistem dapat diakses hingga 26 Mei 2026. Sisa waktu: {$days} hari.";
        }

        return "Sistem telah melewati masa berlaku (26 Mei 2026). Silakan hubungi administrator untuk perpanjangan.";
    }
}