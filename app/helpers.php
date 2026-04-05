<?php

if (!function_exists('asset_custom')) {
    /**
     * Helper untuk mempermudah pengaturan path asset secara global.
     * Path utama diatur melalui variabel ASSET_DIR di file .env
     */
    function asset_custom($path = '')
    {
        $directory = env('ASSET_DIR', 'public/assets'); // default ke public/assets jika tidak ada di .env
        
        return asset($directory . ($path ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('cat_beverages')) {
    // 5 = Takemori, 23 = Soondobu
    function cat_beverages() { return [5, 23]; }
}

if (!function_exists('cat_beverages_ongkir')) {
    // Minuman & Ongkir untuk filter Dapur
    function cat_beverages_ongkir() { return [5, 11, 23, 24]; }
}

if (!function_exists('cat_shabu_sushi')) {
    // Makanan spesifik checker pisah
    function cat_shabu_sushi() { return ['14', '15', '17', '18']; }
}

if (!function_exists('cat_shabu_only')) {
    function cat_shabu_only() { return ['14', '15', '17']; }
}

if (!function_exists('cat_sushi_only')) {
    function cat_sushi_only() { return '18'; }
}
if (!function_exists('is_ayce_distribusi')) {
    function is_ayce_distribusi($distribusi_name) {
        return stripos($distribusi_name, 'AYCE') !== false;
    }
}

if (!function_exists('ayce_harga_paket')) {
    function ayce_harga_paket() {
        // You can change this value as needed, or fetch it from a setting/database
        return 0; 
    }
}
