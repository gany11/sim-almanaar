<?php

if (!function_exists('can_access')) {
    /**
     * Mengecek apakah user yang sedang login memiliki izin mengakses kode fitur tertentu
     */
    function can_access(string $kodeFitur): bool
    {
        // Jika Super Admin (id_peran = 1), berikan akses penuh secara mutlak
        // if (session()->get('id_peran') == 1) {
        //     return true;
        // }

        $allowedFeatures = session()->get('allowed_features') ?? [];
        return in_array($kodeFitur, $allowedFeatures);
    }
}