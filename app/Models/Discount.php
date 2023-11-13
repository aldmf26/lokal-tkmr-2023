<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Discount extends Model
{
    use HasFactory;
    protected $table = 'tb_discount';
    protected $fillable = [
        'disc', 'ket', 'jenis', 'dari', 'expired', 'status', 'lokasi', 'id_discount'
    ];

    public static function diskonPeritem($id_menu, $id_distribusi)
    {
        $potongan = 0;
        $jenis = '';
        $cek = DB::table('tb_discount_peritem')->where([['id_menu', $id_menu], ['id_distribusi', $id_distribusi]])->first();
        if ($cek) {
            $tglDari = Carbon::parse($cek->tgl_dari)->startOfDay();
            $tglSampai = Carbon::parse($cek->tgl_sampai)->endOfDay();
            $tglHariIni = Carbon::now();
            $jenis = $cek->jenis;
            $potongan = $tglHariIni->between($tglDari, $tglSampai) ? $cek->jumlah : 0;

            
        }
        return [
            'potongan' => $potongan,
            'jenis' => $jenis,
        ];
    }
}
