<?php

namespace App\Http\Controllers;

use App\Models\Harga;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Orderan;
use App\Models\Transaksi;
use App\Models\Pembelian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class MejaController extends Controller
{
    public function index(Request $request)
    {
        $id_user = Auth::user()->id;
        $id_menus = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)
            ->where('id_menu', 26)->first();
        if (empty($id_menus)) {
            return back();
        } else {
            if (empty($request->id)) {
                $id = '1';
            } else {
                $id = $request->id;
            }
            $tgl = date('Y-m-d');
            $lokasi = $request->session()->get('id_lokasi');

            $data = [
                'title' => 'Meja',
                'logout' => $request->session()->get('logout'),
                'id' => $id,
                'menu' => DB::table('view_menu')
                    ->where('id_distribusi', $id == 1 || $id == 3 ? 1 : 2)
                    ->where('akv', 'on')
                    ->where('lokasi', $lokasi)
                    ->get(),
                    'tgl' => $tgl,
                    'loc' => $lokasi
            ];

            return view('meja.meja', $data);
        }
    }

    public function distribusi(Request $request)
    {
        $id = $request->id;
        $tgl = date('Y-m-d');
        $lokasi = $request->session()->get('id_lokasi');
        $distribusi = DB::select(
            DB::raw("SELECT a.*, c.jumlah
            FROM tb_distribusi AS a 
            LEFT JOIN (SELECT b.id_distribusi , COUNT(b.id_order) AS jumlah
            FROM tb_order AS b
            WHERE b.selesai = 'diantar' and b.id_lokasi = '$lokasi' AND void = 0
            GROUP BY b.id_distribusi
            ) c ON c.id_distribusi = a.id_distribusi"),
        );
        $orderan = DB::select(
            DB::raw(
                "SELECT 
            COUNT(id_order) as jml_order 
            FROM tb_order as a 
            WHERE a.id_lokasi = '$lokasi' AND a.id_distribusi = '$id' AND selesai = 'diantar' AND void = 0 
            group by a.id_distribusi",
            ),
        );
        $data = [
            'title' => 'Menu | Buku Tugas',
            'distribusi' => $distribusi,
            'orderan' => $orderan,
            'id' => $id,
        ];

        return view('meja.distribusi', $data);
    }

    public function waitress(Request $request)
    {
        $loc = $request->session()->get('id_lokasi');
        $tgl = date('Y-m-d');


        if (empty($request->dis)) {
            $id_distribusi = '1';
        } else {
            $id_distribusi = $request->dis;
        }

        if ($id_distribusi == '3') {
            $waitress = DB::select(
                DB::raw(
                    "SELECT a.* , b.nama FROM tb_absen as a left join tb_karyawan as b on a.id_karyawan = b.id_karyawan
                     WHERE a.tgl = '$tgl' and b.id_status = 2  and a.id_lokasi = '$loc'",
                ),
            );
        } else {
            $waitress = DB::select(
                DB::raw(
                    "SELECT a.* , b.nama FROM tb_absen as a left join tb_karyawan as b on a.id_karyawan = b.id_karyawan
                     WHERE a.tgl = '$tgl' and b.id_status = 2  and a.id_lokasi = '$loc'",
                ),
            );
        }

        $meja = DB::select(
            DB::raw(
                "SELECT a.id_meja, c.nm_meja, a.warna, a.no_order, RIGHT(a.no_order,2) AS kd, b.nm_distribusi,a.selesai,
                a.pengantar,  SUM(a.qty) AS qty1 ,  e.qty2 , min(a.print) as prn , min(a.copy_print) as c_prn, min(a.checker_tamu) as t_prn, MIN(a.j_mulai) as jam_datang, timestampdiff(MINUTE, MIN(a.j_mulai), MAX(a.wait)) as total_jam_pesan
        FROM tb_order AS a
        left join tb_meja as c on c.id_meja = a.id_meja
        LEFT JOIN tb_distribusi AS b ON b.id_distribusi = a.id_distribusi
        LEFT JOIN ( 
        SELECT d.no_order , SUM(d.qty) qty2 FROM tb_order2 AS d 
        GROUP BY d.no_order
        ) AS e ON e.no_order = a.no_order
        WHERE a.aktif = '1' and  a.id_lokasi = '$loc' and a.id_distribusi = '$id_distribusi' AND a.void = 0
        group by a.no_order order by a.id_distribusi , a.id_meja  ASC",
            ),
        );

        $data = [
            'meja' => $meja,
            'waitress' => $waitress,
            'loc' => $loc,
            'id' => $id_distribusi,
        ];

        return view('meja.waitress', $data)->render();
    }

    public function pilih_waitress(Request $request)
    {
        date_default_timezone_set('Asia/Makassar');
        $id_order = $request->kode;
        $waitress = $request->kry;
        $id = $request->id;

        $data = [
            'pengantar' => $waitress,
            'wait' => date('Y-m-d H:i:s'),
        ];
        Orderan::where('id_order', $id_order)->update($data);
    }

    public function un_waitress(Request $request)
    {
        $id_order = $request->kode;
        $data = [
            'pengantar' => '',
        ];
        Orderan::where('id_order', $id_order)->update($data);
    }
    public function meja_selesai(Request $request)
    {
        $id_order = $request->kode;
        $data = [
            'selesai' => 'selesai',
        ];
        Orderan::where('id_order', $id_order)->update($data);
    }
    public function tambah_pesanan(Request $request)
    {
        $lokasi = $request->session()->get('id_lokasi');
        $no_order = $request->no;
        $id = $request->id;

        $order = DB::table('tb_order')
            ->where('no_order', $no_order)
            ->where('id_lokasi', $lokasi)
            ->groupBy('no_order')
            ->first();

        $data = [
            'order' => $order,
            'menu' => DB::table('view_menu')
                ->where('id_distribusi', $id == 1 || $id == 3 ? 1 : 2)
                ->where('akv', 'on')
                ->where('lokasi', $lokasi)
                ->get()
        ];

        return view('meja.tbh_menu', $data)->render();
    }

    public function get_harga(Request $request)
    {
        $id_harga = $request->id_harga;
        $dp = DB::table('tb_harga')
            ->where('id_harga', $id_harga)
            ->first();

        echo "$dp->harga";
    }
    public function save_pesanan(Request $request)
    {
        $id_dis = $request->id_dis;
        $kd_order = $request->kd_order;
        $orang = $request->orang;
        $harga = $request->harga;
        $qty = $request->qty;
        $req = $request->req;
        $id_harga = $request->id_harga;
        $meja = $request->meja;
        $admin = $request->admin;
        $warna = $request->warna;


        for ($i = 0; $i < sizeof($id_harga); $i++) {
            if ($qty[$i] == '' || $qty[$i] == '0') {
            } else {
                for ($q = 1; $q <= $qty[$i]; $q++) {
                    $dt_harga = Harga::where('id_harga', $id_harga[$i])->first();
                    $data = [
                        'no_order' => $kd_order,
                        'id_harga' => $id_harga[$i],
                        'qty' => 1,
                        'harga' => $dt_harga->harga,
                        'request' => $req[$i],
                        'orang' => $orang,
                        'id_meja' => $meja,
                        'id_distribusi' => $id_dis,
                        'id_lokasi' => $request->session()->get('id_lokasi'),
                        'tgl' => date('Y-m-d'),
                        'admin' => empty($admin) ? Auth::user()->nama : $admin,
                        'j_mulai' => date('Y-m-d H:i:s'),
                        'selesai' => 'dimasak',
                        'aktif' => '1',
                        'warna' => $warna,
                    ];
                    Orderan::create($data);
                }
            }
        }
    }

    public function edit_pembayaran(Request $request)
    {
        DB::table('pembayaran')->where('no_nota', $request->no_order)->delete();
        $lokasi = $request->session()->get('id_lokasi');
        for ($i = 0; $i < count($request->id_akun); $i++) {

            if ($request->pembayaran[$i] == 0) {
                # code...
            } else {
                $data = [
                    'id_akun_pembayaran' => $request->id_akun[$i],
                    'no_nota' => $request->no_order,
                    'nominal' => $request->pembayaran[$i],
                    // 'pengirim' => $request->nm_pengirim[$i],
                    'tgl' => date('Y-m-d'),
                    'id_lokasi' => $lokasi
                ];
                DB::table('pembayaran')->insert($data);
            }
        }
        return redirect()->route('meja');
    }

    public function get_pembayaran(Request $request)
    {
        $no_order = $request->no_order;
        $data = [
            'dt_pembayaran' => Transaksi::where('no_order', $no_order)->first(),
            'no_order' => $no_order,
            'klasifikasi_pembayaran' => DB::table('klasifikasi_pembayaran')->get(),
        ];
        return view('meja.edit_pembayaran', $data);
    }

    public function clear(Request $request)
    {
        $id_order = $request->kode;
        $data = array(
            'aktif'   => '2',
        );

        DB::table('tb_order')->where('no_order', $id_order)->update($data);
    }

    public function check_pembayaran(Request $request)
    {
    }

    public function bill(Request $request)
    {
        $id = $request->no;
        $order =  DB::select(
            DB::raw("SELECT a.id_order, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai, a.harga,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.ongkir, a.id_distribusi
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where   a.no_order = '$id' AND a.void = 0 
            GROUP BY a.id_harga
            "),
        );
        $majo = DB::select("SELECT a.tanggal, a.no_nota, a.nm_karyawan, b.nm_produk, a.id_karyawan,  a.jumlah, a.harga, a.total
        FROM tb_pembelian AS a
        LEFT JOIN tb_produk AS b ON b.id_produk = a.id_produk
        WHERE a.no_nota = '$id' AND a.lokasi = '1'
        ");
        $now = date('Y-m-d');
        $disc = DB::table('tb_discount')
            ->where([['lokasi', Session::get('id_lokasi')], ['dari', '<=', $now], ['expired', '>=', $now], ['aktif', '=', 'Y']])->first();
        $data = [
            'order' => $order,
            'no_order' => $id,
            'pesan_2'    => DB::table('tb_order as a')
                ->select(DB::raw('a.*, sum(a.qty) as sum_qty ,  b.nm_meja'))
                ->leftJoin('tb_meja as b', 'b.id_meja', '=', 'a.id_meja')
                ->where('a.no_order', $id)
                ->groupBy('a.no_order')
                ->first(),
            'ongkir2' => DB::table('tb_ongkir as a')
                ->select(DB::raw('a.*, sum(a.rupiah) as rupiah '))
                ->first(),
            'batas' => DB::table('tb_batas_ongkir')
                ->first(),
            'majo' => $majo,
            'disc' => $disc
        ];
        return view('meja.bill', $data);
    }
    public function get_karyawan(Request $request)
    {

        $loc = $request->session()->get('id_lokasi');
        $tgl = date('Y-m-d');
        $karyawan = DB::select("SELECT a.* , b.nama FROM tb_absen as a left join tb_karyawan as b on
                    a.id_karyawan = b.id_karyawan
                    WHERE a.tgl = '$tgl' and b.id_posisi in('5','15','16','17','18') and a.id_lokasi = '$loc'");
        echo '<div class="row">';
        foreach ($karyawan as $key => $value) {
            echo '<div class="col-lg-2 col-4">
                            <label class="btn btn-default buying-selling">
                            <div class="checkbox-group required">
                                <input type="radio"  name="admin" value="' . $value->nama . '" autocomplete="off" class="cart_id_karyawan option1">
                            </div>	
                                <span class="radio-dot"></span>
                                <span class="buying-selling-word">' . $value->nama . '</span>
                            </label>
                            </div>';
        }
        echo   '</div>';
    }

    public function checker(Request $request)
    {
        $id = $request->no;
        $order =  DB::select(
            DB::raw("SELECT a.id_order, b.tipe, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.no_checker , a.print, a.copy_print
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where a.aktif = '1' and a.print = 'T' and a.no_order = '$id' and b.tipe = 'food' and b.id_kategori not in('14','15','17','18')
            GROUP BY a.id_order
            ORDER BY a.id_order DESC"),
        );
        $order3 =  DB::select(
            DB::raw("SELECT a.id_order, b.tipe, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.no_checker , a.print, a.copy_print
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where a.aktif = '1' and a.print = 'T' and a.no_order = '$id' and b.tipe = 'food' and b.id_kategori = '18'
            GROUP BY a.id_order
            ORDER BY a.id_order DESC"),
        );
        $order4 =  DB::select(
            DB::raw("SELECT a.id_order, b.tipe, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.no_checker , a.print, a.copy_print
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where a.aktif = '1' and a.print = 'T' and a.no_order = '$id' and b.tipe = 'food' and b.id_kategori in('14','15','17') 
            GROUP BY a.id_order
            ORDER BY a.id_order DESC"),
        );
        $order2 =  DB::select(
            DB::raw("SELECT a.id_order, b.tipe, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.no_checker , a.print, a.copy_print
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where a.aktif = '1' and a.print = 'T' and a.no_order = '$id' and b.tipe = 'drink'
            GROUP BY a.id_order
            ORDER BY a.id_order DESC"),
        );

        $data = [
            'order2' => $order2,
            'order3' => $order3,
            'order4' => $order4,
            'no_order' => $id,
            'pesan_3'    => DB::table('tb_order as a')
                ->select(DB::raw('a.*, sum(a.qty) as sum_qty ,  b.nm_meja'))
                ->leftJoin('tb_meja as b', 'b.id_meja', '=', 'a.id_meja')
                ->leftJoin('view_menu as c', 'c.id_harga', '=', 'a.id_harga')
                ->where('a.no_order', $id)
                ->where('c.tipe', 'drink')
                ->where('a.no_checker', 'T')
                ->groupBy('a.no_order')
                ->first(),
            'pesan_4'    => DB::table('tb_order as a')
                ->select(DB::raw('a.*, sum(a.qty) as sum_qty ,  b.nm_meja'))
                ->leftJoin('tb_meja as b', 'b.id_meja', '=', 'a.id_meja')
                ->leftJoin('view_menu as c', 'c.id_harga', '=', 'a.id_harga')
                ->where('a.no_order', $id)
                ->where('c.tipe', 'food')
                ->where('c.id_kategori', '18')
                ->where('a.no_checker', 'T')
                ->groupBy('a.no_order')
                ->first(),
            'pesan_5'    => DB::table('tb_order as a')
                ->select(DB::raw('a.*, sum(a.qty) as sum_qty ,  b.nm_meja'))
                ->leftJoin('tb_meja as b', 'b.id_meja', '=', 'a.id_meja')
                ->leftJoin('view_menu as c', 'c.id_harga', '=', 'a.id_harga')
                ->where('a.no_order', $id)
                ->where('c.tipe', 'food')
                ->wherein('c.id_kategori', ['14','15','17'])
                ->where('a.no_checker', 'T')
                ->groupBy('a.no_order')
                ->first(),
                'majo' => DB::select("SELECT a.tanggal, a.no_nota, a.nm_karyawan, b.nm_produk, a.id_karyawan,  a.jumlah, a.harga, a.total
                FROM tb_pembelian AS a
                LEFT JOIN tb_produk AS b ON b.id_produk = a.id_produk
                WHERE a.no_nota= '$id'
                "),
            'majo' => DB::select("SELECT a.tanggal, a.no_nota, a.nm_karyawan, b.nm_produk, a.id_karyawan,  a.jumlah, a.harga, a.total
                FROM tb_pembelian AS a
                LEFT JOIN tb_produk AS b ON b.id_produk = a.id_produk
                WHERE a.no_nota= '$id'
                "),
                
            'majo_ttl' => DB::selectOne("SELECT a.tanggal, a.no_nota, a.nm_karyawan, b.nm_produk, a.id_karyawan,  sum(a.jumlah) as sum_qty, a.harga, a.total
                        FROM tb_pembelian AS a
                        LEFT JOIN tb_produk AS b ON b.id_produk = a.id_produk
                        WHERE a.no_nota= '$id'
                        group by a.no_nota
                        "),
            'meja' => DB::selectOne("SELECT a.warna, b.nm_meja
                        FROM tb_order AS a
                        LEFT JOIN tb_meja AS b ON b.id_meja = a.id_meja
                        WHERE a.no_order = '$id'
                        GROUP BY a.no_order ")
        ];

        
        return view('meja.checker', $data);
    }
    public function checker_tamu(Request $request)
    {
        $id = $request->no;
        $order =  DB::select(
            DB::raw("SELECT a.id_order, b.tipe, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.no_checker , a.print, a.copy_print
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where a.aktif = '1' and a.checker_tamu = 'T' and a.no_order = '$id' and b.tipe = 'food' and b.id_kategori not in('14','15','17','18')
            GROUP BY a.id_order
            ORDER BY a.id_order DESC"),
        );
        $order3 =  DB::select(
            DB::raw("SELECT a.id_order, b.tipe, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.no_checker , a.print, a.copy_print
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where a.aktif = '1' and a.checker_tamu = 'T' and a.no_order = '$id' and b.tipe = 'food' and b.id_kategori = '18'
            GROUP BY a.id_order
            ORDER BY a.id_order DESC"),
        );
        $order4 =  DB::select(
            DB::raw("SELECT a.id_order, b.tipe, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.no_checker , a.print, a.copy_print
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where a.aktif = '1' and a.checker_tamu = 'T' and a.no_order = '$id' and b.tipe = 'food' and b.id_kategori in('14','15','17') 
            GROUP BY a.id_order
            ORDER BY a.id_order DESC"),
        );
        $order2 =  DB::select(
            DB::raw("SELECT a.id_order, b.tipe, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.no_checker , a.print, a.copy_print
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where a.aktif = '1' and a.checker_tamu = 'T' and a.no_order = '$id' and b.tipe = 'drink'
            GROUP BY a.id_order
            ORDER BY a.id_order DESC"),
        );

        $data = [
            'order' => $order,
            'order2' => $order2,
            'order3' => $order3,
            'order4' => $order4,
            'no_order' => $id,
            'pesan_2'    => DB::table('tb_order as a')
                ->select(DB::raw('a.*, sum(a.qty) as sum_qty ,  b.nm_meja'))
                ->leftJoin('tb_meja as b', 'b.id_meja', '=', 'a.id_meja')
                ->leftJoin('view_menu as c', 'c.id_harga', '=', 'a.id_harga')
                ->where('a.no_order', $id)
                ->where('c.tipe', 'food')
                ->whereNotIn('c.id_kategori' ,['14','15','17','18'])
                ->where('a.checker_tamu', 'T')
                ->groupBy('a.no_order')
                ->first(),
            'pesan_3'    => DB::table('tb_order as a')
                ->select(DB::raw('a.*, sum(a.qty) as sum_qty ,  b.nm_meja'))
                ->leftJoin('tb_meja as b', 'b.id_meja', '=', 'a.id_meja')
                ->leftJoin('view_menu as c', 'c.id_harga', '=', 'a.id_harga')
                ->where('a.no_order', $id)
                ->where('c.tipe', 'drink')
                ->where('a.checker_tamu', 'T')
                ->groupBy('a.no_order')
                ->first(),
            'pesan_4'    => DB::table('tb_order as a')
                ->select(DB::raw('a.*, sum(a.qty) as sum_qty ,  b.nm_meja'))
                ->leftJoin('tb_meja as b', 'b.id_meja', '=', 'a.id_meja')
                ->leftJoin('view_menu as c', 'c.id_harga', '=', 'a.id_harga')
                ->where('a.no_order', $id)
                ->where('c.tipe', 'food')
                ->where('c.id_kategori', '18')
                ->where('a.checker_tamu', 'T')
                ->groupBy('a.no_order')
                ->first(),
            'pesan_5'    => DB::table('tb_order as a')
                ->select(DB::raw('a.*, sum(a.qty) as sum_qty ,  b.nm_meja'))
                ->leftJoin('tb_meja as b', 'b.id_meja', '=', 'a.id_meja')
                ->leftJoin('view_menu as c', 'c.id_harga', '=', 'a.id_harga')
                ->where('a.no_order', $id)
                ->where('c.tipe', 'food')
                ->wherein('c.id_kategori', ['14','15','17'])
                ->where('a.checker_tamu', 'T')
                ->groupBy('a.no_order')
                ->first(),
                'majo' => DB::select("SELECT a.tanggal, a.no_nota, a.nm_karyawan, b.nm_produk, a.id_karyawan,  a.jumlah, a.harga, a.total
                FROM tb_pembelian AS a
                LEFT JOIN tb_produk AS b ON b.id_produk = a.id_produk
                WHERE a.no_nota= '$id'
                "),
                
            'majo_ttl' => DB::selectOne("SELECT a.tanggal, a.no_nota, a.nm_karyawan, b.nm_produk, a.id_karyawan,  sum(a.jumlah) as sum_qty, a.harga, a.total
                        FROM tb_pembelian AS a
                        LEFT JOIN tb_produk AS b ON b.id_produk = a.id_produk
                        WHERE a.no_nota= '$id'
                        group by a.no_nota
                        "),
            'meja' => DB::selectOne("SELECT a.warna, b.nm_meja
                        FROM tb_order AS a
                        LEFT JOIN tb_meja AS b ON b.id_meja = a.id_meja
                        WHERE a.no_order = '$id'
                        GROUP BY a.no_order ")
        ];

        $data1 = [
            'checker_tamu' => 'Y',
        ];
        Orderan::where('no_order', $id)->update($data1);
        return view('meja.checker_tamu', $data);
    }
    public function copy_checker_tamu(Request $request)
    {
        $id = $request->no;
        $order =  DB::select(
            DB::raw("SELECT a.id_order, b.tipe, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.no_checker , a.print, a.copy_print
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where a.aktif = '1' and a.copy_checker_tamu = 'T' and a.no_order = '$id' and b.tipe = 'food' and b.id_kategori not in('14','15','17','18')
            GROUP BY a.id_order
            ORDER BY a.id_order DESC"),
        );
        $order3 =  DB::select(
            DB::raw("SELECT a.id_order, b.tipe, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.no_checker , a.print, a.copy_print
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where a.aktif = '1' and a.copy_checker_tamu = 'T' and a.no_order = '$id' and b.tipe = 'food' and b.id_kategori = '18'
            GROUP BY a.id_order
            ORDER BY a.id_order DESC"),
        );
        $order4 =  DB::select(
            DB::raw("SELECT a.id_order, b.tipe, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.no_checker , a.print, a.copy_print
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where a.aktif = '1' and a.copy_checker_tamu = 'T' and a.no_order = '$id' and b.tipe = 'food' and b.id_kategori in('14','15','17') 
            GROUP BY a.id_order
            ORDER BY a.id_order DESC"),
        );
        $order2 =  DB::select(
            DB::raw("SELECT a.id_order, b.tipe, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.no_checker , a.print, a.copy_print
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where a.aktif = '1' and a.copy_checker_tamu = 'T' and a.no_order = '$id' and b.tipe = 'drink'
            GROUP BY a.id_order
            ORDER BY a.id_order DESC"),
        );

        $data = [
            'order' => $order,
            'order2' => $order2,
            'order3' => $order3,
            'order4' => $order4,
            'no_order' => $id,
            'pesan_2'    => DB::table('tb_order as a')
                ->select(DB::raw('a.*, sum(a.qty) as sum_qty ,  b.nm_meja'))
                ->leftJoin('tb_meja as b', 'b.id_meja', '=', 'a.id_meja')
                ->leftJoin('view_menu as c', 'c.id_harga', '=', 'a.id_harga')
                ->where('a.no_order', $id)
                ->where('c.tipe', 'food')
                ->whereNotIn('c.id_kategori' ,['14','15','17','18'])
                ->where('a.copy_checker_tamu', 'T')
                ->groupBy('a.no_order')
                ->first(),
            'pesan_3'    => DB::table('tb_order as a')
                ->select(DB::raw('a.*, sum(a.qty) as sum_qty ,  b.nm_meja'))
                ->leftJoin('tb_meja as b', 'b.id_meja', '=', 'a.id_meja')
                ->leftJoin('view_menu as c', 'c.id_harga', '=', 'a.id_harga')
                ->where('a.no_order', $id)
                ->where('c.tipe', 'drink')
                ->where('a.copy_checker_tamu', 'T')
                ->groupBy('a.no_order')
                ->first(),
            'pesan_4'    => DB::table('tb_order as a')
                ->select(DB::raw('a.*, sum(a.qty) as sum_qty ,  b.nm_meja'))
                ->leftJoin('tb_meja as b', 'b.id_meja', '=', 'a.id_meja')
                ->leftJoin('view_menu as c', 'c.id_harga', '=', 'a.id_harga')
                ->where('a.no_order', $id)
                ->where('c.tipe', 'food')
                ->where('c.id_kategori', '18')
                ->where('a.copy_checker_tamu', 'T')
                ->groupBy('a.no_order')
                ->first(),
            'pesan_5'    => DB::table('tb_order as a')
                ->select(DB::raw('a.*, sum(a.qty) as sum_qty ,  b.nm_meja'))
                ->leftJoin('tb_meja as b', 'b.id_meja', '=', 'a.id_meja')
                ->leftJoin('view_menu as c', 'c.id_harga', '=', 'a.id_harga')
                ->where('a.no_order', $id)
                ->where('c.tipe', 'food')
                ->wherein('c.id_kategori', ['14','15','17'])
                ->where('a.copy_checker_tamu', 'T')
                ->groupBy('a.no_order')
                ->first(),

                'majo' => DB::select("SELECT a.tanggal, a.no_nota, a.nm_karyawan, b.nm_produk, a.id_karyawan,  a.jumlah, a.harga, a.total
                FROM tb_pembelian AS a
                LEFT JOIN tb_produk AS b ON b.id_produk = a.id_produk
                WHERE a.no_nota= '$id'
                "),
                
            'majo_ttl' => DB::selectOne("SELECT a.tanggal, a.no_nota, a.nm_karyawan, b.nm_produk, a.id_karyawan,  sum(a.jumlah) as sum_qty, a.harga, a.total
                        FROM tb_pembelian AS a
                        LEFT JOIN tb_produk AS b ON b.id_produk = a.id_produk
                        WHERE a.no_nota= '$id'
                        group by a.no_nota
                        "),
            'meja' => DB::selectOne("SELECT a.warna, b.nm_meja
                        FROM tb_order AS a
                        LEFT JOIN tb_meja AS b ON b.id_meja = a.id_meja
                        WHERE a.no_order = '$id'
                        GROUP BY a.no_order ")
        ];

        $data1 = [
            'copy_checker_tamu' => 'Y',
        ];
        Orderan::where('no_order', $id)->update($data1);
        return view('meja.copy_checker_tamu', $data);
    }

    public function copy_checker(Request $request)
    {
        $id = $request->no;
        $order =  DB::select(
            DB::raw("SELECT a.id_order, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.no_checker , a.print, a.copy_print
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where a.aktif = '1' and a.copy_print = 'T' and a.no_order = '$id' 
            GROUP BY a.id_order
            ORDER BY a.id_order DESC"),
        );

        $data = [
            'order' => $order,
            'no_order' => $id,
            'pesan_2'    => DB::table('tb_order as a')
                ->select(DB::raw('a.*, sum(a.qty) as sum_qty ,  b.nm_meja'))
                ->leftJoin('tb_meja as b', 'b.id_meja', '=', 'a.id_meja')
                ->where('a.no_order', $id)
                ->groupBy('a.no_order')
                ->first(),
        ];


        $data1 = [
            'copy_print' => 'Y',
        ];
        Orderan::where('no_order', $id)->update($data1);

        return view('meja.copy_checker', $data);
    }

    public function meja_selesai_majo(Request $request)
    {
        $id_pembelian = $request->kode;
        $data = [
            'selesai' => 'selesai',
        ];
        Pembelian::where('id_pembelian', $id_pembelian)->update($data);
    }
    public function pilih_waitress_majo(Request $request)
    {
        date_default_timezone_set('Asia/Makassar');
        $id_pembelian = $request->kode;
        $waitress = $request->kry;
        $id = $request->id;

        $data = [
            'pengantar' => $waitress,
        ];
        Pembelian::where('id_pembelian', $id_pembelian)->update($data);
    }
    public function un_waitress_majo(Request $request)
    {
        $id_pembelian = $request->kode;
        $data = [
            'pengantar' => '',
        ];
        Pembelian::where('id_pembelian', $id_pembelian)->update($data);
    }
    public function get_harga_majo(Request $request)
    {
        $id_harga = $request->id_harga;
        $dp = DB::table('tb_produk')
            ->where('id_produk', $id_harga)
            ->first();

        echo "$dp->harga";
    }

    public function tambah_pesanan_majo(Request $request)
    {
        $lokasi = $request->session()->get('id_lokasi');
        $no_order = $request->no;
        $id_dis = $request->id;

        if ($id_dis == '1' || $id_dis == '3') {
            $produk =  DB::select("SELECT a.id_produk, a.komisi,  a.nm_produk, a.sku, a.harga, b.satuan , c.nm_kategori, a.id_lokasi, d.debit, d.kredit,e.kredit_penjualan
            FROM tb_produk AS a
            LEFT JOIN tb_satuan_majo AS b ON b.id_satuan = a.id_satuan
            LEFT JOIN tb_kategori_majo AS c ON c.id_kategori = a.id_kategori
            
            LEFT JOIN (
            SELECT d.id_produk, SUM(d.debit) AS debit, SUM(d.kredit) AS kredit
            FROM tb_stok_produk AS d 
            GROUP BY d.id_produk
            ) AS d ON d.id_produk = a.id_produk

            LEFT JOIN (
            SELECT e.id_produk , SUM(e.jumlah) AS kredit_penjualan
            FROM tb_pembelian AS e 
            GROUP BY e.id_produk
            )AS e ON e.id_produk = a.id_produk
            
            WHERE a.id_lokasi = '$lokasi' and a.id_kategori != '11'  ");
        } else {
            $produk =  DB::select("SELECT a.id_produk, a.komisi,  a.nm_produk, a.sku, a.harga, b.satuan , c.nm_kategori, a.id_lokasi, d.debit, d.kredit,e.kredit_penjualan
            FROM tb_produk AS a
            LEFT JOIN tb_satuan_majo AS b ON b.id_satuan = a.id_satuan
            LEFT JOIN tb_kategori_majo AS c ON c.id_kategori = a.id_kategori
            
            LEFT JOIN (
            SELECT d.id_produk, SUM(d.debit) AS debit, SUM(d.kredit) AS kredit
            FROM tb_stok_produk AS d 
            GROUP BY d.id_produk
            ) AS d ON d.id_produk = a.id_produk

            LEFT JOIN (
            SELECT e.id_produk , SUM(e.jumlah) AS kredit_penjualan
            FROM tb_pembelian AS e 
            GROUP BY e.id_produk
            )AS e ON e.id_produk = a.id_produk
            
            WHERE a.id_lokasi = '$lokasi' and a.id_kategori = '11'  ");
        }
        $order = DB::table('tb_order')
            ->where('no_order', $no_order)
            ->where('id_lokasi', $lokasi)
            ->groupBy('no_order')
            ->first();

        $data = [
            'order' => $order,
            'produk' => $produk
        ];

        return view('meja.tbh_menu_majo', $data)->render();
    }


    public function save_pesanan_majo(Request $r)
    {
        $kd_order = $r->kd_order;
        $id_dis = $r->id_dis;
        $id_harga_majo = $r->id_harga_majo;
        $nota = $r->nota;
        $meja = $r->meja;
        $qty_majo = $r->qty_majo;
        $hrg_majo = $r->hrg_majo;
        $admin =  Auth::user()->nama;
        $lokasi = $r->session()->get('id_lokasi');


        $d_produk = DB::table('tb_produk')->where('id_produk', $id_harga_majo)->where('id_lokasi', $lokasi)->first();
        $data = [
            'id_karyawan'  => $nota[0],
            'id_produk' => $id_harga_majo,
            'no_nota' => $kd_order,
            'jumlah' => $qty_majo,
            'harga' => $hrg_majo,
            'total' => $qty_majo * $hrg_majo,
            'tanggal' => date('Y-m-d'),
            'tgl_input' => date('Y-m-d H:i:s'),
            'admin' => $admin,
            'lokasi' => $lokasi,
            'no_meja' => $meja,
            'jml_komisi' => $d_produk->komisi
        ];
        $dataInsert = Pembelian::create($data);
        $id_pembelian = $dataInsert->id;


        $data2 = [
            'no_order' => $kd_order,
            'qty' => '1',
            'id_meja' => $meja,
            'id_distribusi' => $id_dis,
            'selesai' => 'selesai',
            'id_lokasi' => $lokasi,
            'tgl' => date('Y-m-d'),
            'j_mulai' => date('Y-m-d H:i:s'),
            'aktif' => '1',
        ];
        Orderan::create($data2);

        // $data_invoice = [
        //     'no_nota' => $kd_order,
        //     'total' => $qty_majo * $hrg_majo,
        //     'tgl_jam' => date('Y-m-d'),
        //     'tgl_input' => date('Y-m-d H:i:s'),
        //     'admin' => $admin,
        //     'lokasi' => $lokasi,
        //     'no_meja' => $meja
        // ];
        // DB::table('tb_invoice')->insert($data_invoice);

        $stok_baru = [
            'stok' => $d_produk->stok -  $qty_majo
        ];

        DB::table('tb_produk')->where('id_produk', $id_harga_majo)->update($stok_baru);

        if ($hrg_majo > 0) {
            $subharga = $qty_majo * $hrg_majo;
        } else {
            $subharga = 0;
        }
        $komisi1 = $subharga * $d_produk->komisi / 100;
        $komisi = $komisi1 / count($nota);
        foreach ($nota as $id_karyawan) {
            $data_komisi = [
                'id_pembelian' => $id_pembelian,
                'id_kry'  => $id_karyawan,
                'komisi' => $komisi,
                'tgl' => date('Y-m-d'),
                'id_lokasi' => '1'
            ];
            DB::table('komisi')->insert($data_komisi);
        }
    }

}
