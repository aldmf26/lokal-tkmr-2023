<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::select("SELECT no_order, no_order2, tgl, id_lokasi FROM tb_order2 WHERE no_order LIKE '%0027%' LIMIT 5");
echo json_encode($rows);
