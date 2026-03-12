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
