<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::select("SELECT no_order, id_lokasi FROM tb_order WHERE tgl = '2026-04-08' LIMIT 10");
echo json_encode($rows);
