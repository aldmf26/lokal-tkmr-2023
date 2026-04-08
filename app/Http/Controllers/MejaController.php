<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Harga;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Orderan;
use App\Models\Transaksi;
use App\Models\Pembelian;
use Carbon\Carbon;
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
                'menu' => DB::table('tb_harga as vh')
                    ->join('tb_menu as vm', 'vh.id_menu', '=', 'vm.id_menu')
                    ->select('vh.id_harga', 'vh.harga', 'vm.nm_menu', 'vm.tipe', 'vm.id_kategori', 'vh.id_distribusi', 'vm.aktif as akv', 'vm.lokasi', 'vh.id_menu')
                    ->where('vh.id_distribusi', $id)
                    ->where('vm.aktif', 'on')
                    ->where('vm.lokasi', $lokasi)
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
            "SELECT a.id_distribusi, REPLACE(REPLACE(a.nm_distribusi, 'GOJEK', ''), 'GRAB', '') as nm_distribusi
            FROM tb_distribusi AS a 
            WHERE a.id_distribusi != '4'
            ",
        );
        // $orderan = DB::selectOne(
        //     "SELECT 
        //     COUNT(id_order) as jml_order 
        //     FROM tb_order as a 
        //     WHERE a.id_lokasi = '$lokasi' AND a.id_distribusi = '$id' AND selesai = 'diantar' AND void = 0 
        //     group by a.id_distribusi"
        // );
        $data = [
            'title' => 'Menu | Buku Tugas',
            'distribusi' => $distribusi,
            // 'orderan' => $orderan,
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



        $meja = DB::table('tb_meja as c')
            ->joinSub(
                DB::table('tb_order')
                    ->select('id_meja', DB::raw('MAX(id_order) as last_id_order'))
                    ->where('aktif', '1')
                    ->where('void', 0)
                    ->where('id_lokasi', $loc)
                    ->groupBy('id_meja'),
                'o_last',
                'c.id_meja',
                '=',
                'o_last.id_meja'
            )
            ->join('tb_order as o_ref', 'o_ref.id_order', '=', 'o_last.last_id_order')
            ->join('tb_order as a', function ($join) {
                $join->on('a.id_meja', '=', 'o_last.id_meja')
                    ->on('a.no_order', '=', 'o_ref.no_order')
                    ->where('a.aktif', '1')
                    ->where('a.void', 0);
            })
            ->leftJoinSub(
                DB::table('tb_pembelian')
                    ->select('no_nota', DB::raw('SUM(total) as total_majo'))
                    ->where('lokasi', $loc)
                    ->where('void', 0)
                    ->groupBy('no_nota'),
                'm',
                'm.no_nota',
                '=',
                'o_ref.no_order'
            )
            ->leftJoin('tb_order2 as o2', function ($join) {
                $join->on('o2.no_order', '=', 'o_ref.no_order')
                     ->where('o2.tgl', date('Y-m-d'));
            })
            ->leftJoin('tb_transaksi as t2', function ($join) {
                $join->on('t2.no_order', '=', 'o2.no_order2')
                     ->where('t2.tgl_transaksi', date('Y-m-d'));
            })
            ->select(
                'c.id_meja',
                DB::raw('MAX(o2.no_order2) as no_order2'),
                DB::raw("COALESCE(NULLIF(MAX(a.no_meja), ''), c.nm_meja) as nm_meja"),
                'o_ref.no_order',
                DB::raw("RIGHT(o_ref.no_order, 2) AS kd"),
                DB::raw("SUM(a.qty) AS qty1"),
                DB::raw("
                    CASE 
                        WHEN (MAX(t2.id_transaksi) IS NOT NULL)
                             AND COUNT(CASE WHEN a.aktif = '1' AND a.void = 0 AND IFNULL(a.selesai, 'dimasak') != 'selesai' THEN 1 END) = 0
                        THEN o_ref.no_order 
                        ELSE NULL 
                    END as paid_order
                "),
                DB::raw("SUM(a.qty * a.harga) + MAX(IFNULL(m.total_majo, 0)) as subtotal"),
                DB::raw("COUNT(CASE WHEN IFNULL(a.selesai, 'dimasak') != 'selesai' THEN 1 END) as items_cooking"),
                DB::raw("MIN(a.j_mulai) as j_mulai"),
                DB::raw("MIN(a.print) as prn"),
                DB::raw("MIN(a.copy_print) as c_prn"),
                DB::raw("MIN(a.checker_tamu) as t_prn"),
                DB::raw("MIN(a.copy_checker_tamu) as ct_prn")
            )
            ->where('c.id_lokasi', $loc)
            ->where('c.id_distribusi', $id_distribusi)
            ->groupBy('c.id_meja', 'c.nm_meja', 'o_ref.no_order')
            ->orderBy('o_ref.no_order', 'ASC')
            ->get();

        $data = [
            'meja' => $meja,
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
            'menu' => DB::table('tb_harga as vh')
                ->join('tb_menu as vm', 'vh.id_menu', '=', 'vm.id_menu')
                ->select('vh.id_harga', 'vh.harga', 'vm.nm_menu', 'vm.tipe', 'vm.id_kategori', 'vh.id_distribusi', 'vm.aktif as akv', 'vm.lokasi','vh.id_menu')
                ->where('vh.id_distribusi', $id)
                ->where('vm.aktif', 'on')
                ->where('vm.lokasi', $lokasi)
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
        $potongan = Discount::diskonPeritem($dp->id_menu, $dp->id_distribusi);
        $potonganJumlah = $potongan['potongan'];
        $potonganJenis = $potongan['jenis'];
        $pricePotongan = 0;
        if ($potonganJumlah > 0) {
            $pricePotongan = $potonganJenis == 'rp' ? $dp->harga - $potonganJumlah : ($dp->harga * $potonganJumlah) / 100;
        } else {
            $pricePotongan = $dp->harga;
        }
        echo $pricePotongan;
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
        $no_meja = $request->no_meja;
        $admin = $request->admin;
        $warna = $request->warna;



        for ($i = 0; $i < sizeof($id_harga); $i++) {
            if ($qty[$i] == '' || $qty[$i] == '0') {
            } else {
                for ($q = 1; $q <= $qty[$i]; $q++) {
                    $dt_harga = Harga::where('id_harga', $id_harga[$i])->first();
                    $potongan = Discount::diskonPeritem($dt_harga->id_menu, $dt_harga->id_distribusi);
                    $potonganJumlah = $potongan['potongan'];
                    $potonganJenis = $potongan['jenis'];
                    $pricePotongan = 0;
                    if ($potonganJumlah > 0) {
                        $pricePotongan = $potonganJenis == 'rp' ? $dt_harga->harga - $potonganJumlah : ($dt_harga->harga * $potonganJumlah) / 100;
                    } else {
                        $pricePotongan = $dt_harga->harga;
                    }

                    $data = [
                        'no_order' => $kd_order,
                        'id_harga' => $id_harga[$i],
                        'qty' => 1,
                        'harga' => $pricePotongan,
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
                        'no_meja' => $no_meja,
                        'warna' => $warna,
                    ];
                    Orderan::create($data);
                }
            }
        }
        return redirect()->back()->with('success', 'berhasil ditambahkan');
    }

    public function edit_pembayaran(Request $request)
    {
        $no_order = $request->no_order;
        $id_akun = $request->id_akun;
        $nominal = $request->nominal;
        $lokasi = $request->session()->get('id_lokasi');

        // Hapus pembayaran lama
        DB::table('pembayaran')->where('no_nota', $no_order)->delete();

        // Input pembayaran baru (Single payment method)
        $data = [
            'id_akun_pembayaran' => $id_akun,
            'no_nota' => $no_order,
            'nominal' => $nominal,
            'tgl' => date('Y-m-d'),
            'id_lokasi' => $lokasi
        ];
        DB::table('pembayaran')->insert($data);

        return redirect()->route('meja');
    }

    public function get_pembayaran(Request $request)
    {
        $no_order = $request->no_order;

        // Cek apakah ada mapping ke no_order2 (Nota baru) di tb_order2
        $order2 = DB::table('tb_order2')->where('no_order', $no_order)->first();
        $no_tagihan = $order2 ? $order2->no_order2 : $no_order;

        $data = [
            'dt_pembayaran' => Transaksi::where('no_order', $no_tagihan)->first(),
            'no_order' => $no_tagihan, // Kirim nomor nota yang benar ke view agar sinkron saat save
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

    public function check_pembayaran(Request $request) {}

    public function get_list_bill(Request $request)
    {
        $loc = $request->session()->get('id_lokasi');

        $meja = DB::select(
            "SELECT a.no_meja as nm_meja, a.no_order, a.id_distribusi,
            SUM(a.qty) AS total_qty, MIN(a.j_mulai) AS waktu_mulai
            FROM tb_order AS a
            WHERE a.aktif = '1' AND a.id_lokasi = '$loc' AND a.void = 0
            GROUP BY a.no_order
            ORDER BY a.no_meja ASC"
        );

        $distribusi = DB::table('tb_distribusi')->get();
        $batas = DB::table('tb_batas_ongkir')->first();
        $ongkir2 = DB::table('tb_ongkir as a')
            ->select(DB::raw('a.*, sum(a.rupiah) as rupiah'))
            ->first();

        $result = [];
        foreach ($meja as $m) {
            $dis = null;
            $dis_name = '';
            foreach ($distribusi as $d) {
                if ($d->id_distribusi == $m->id_distribusi) {
                    $dis = $d;
                    $dis_name = $d->nm_distribusi ?? '';
                    break;
                }
            }

            // Calculate subtotal from regular orders
            $orders = DB::select(
                "SELECT SUM(a.harga * a.qty) as bayar
                FROM tb_order AS a 
                WHERE a.no_order = '{$m->no_order}' AND a.void = 0"
            );
            $bayar = $orders[0]->bayar ?? 0;

            // Calculate majo total
            $majo = DB::selectOne(
                "SELECT SUM(a.harga * a.jumlah) as total
                FROM tb_pembelian AS a
                WHERE a.no_nota = '{$m->no_order}' AND a.lokasi = '$loc'"
            );
            $t_majo = $majo->total ?? 0;

            $subtotal = $bayar + $t_majo;

            // Calculate ongkir
            $ongkir = 0;
            if ($dis && $dis->ongkir == 'Y') {
                if ($bayar < ($batas->rupiah ?? 0)) {
                    $ongkir = $ongkir2->rupiah ?? 0;
                }
            }

            // Calculate service
            $service = 0;
            if ($dis && $dis->service == 'Y') {
                $service = $bayar * 0.07;
            }

            // Calculate tax
            $tax = 0;
            if ($dis && $dis->tax == 'Y') {
                $tax = ($bayar + $service + $ongkir + $t_majo) * 0.1;
            }

            // Grand total with rounding
            $total = $bayar + $t_majo + $service + $tax + $ongkir;
            $a = round($total);
            $b = (int) substr($a, -3);
            if ($b == 0) {
                $grand_total = $a;
            } elseif ($b < 1000) {
                $grand_total = $a - $b + 1000;
            } else {
                $grand_total = $a;
            }

            $result[] = [
                'nm_meja' => $m->nm_meja,
                'no_order' => $m->no_order,
                'dis_name' => $dis_name,
                'total_qty' => $m->total_qty,
                'waktu' => date('H:i', strtotime($m->waktu_mulai)),
                'subtotal' => $subtotal,
                'grand_total' => $grand_total,
                'url' => route('list_orderan', ['no' => $m->no_order]),
            ];
        }

        return response()->json($result);
    }

    public function bill(Request $request)
    {
        $id = $request->no;
        $order =  DB::select(
            DB::raw("SELECT a.id_order, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai, a.harga,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.ongkir, a.id_distribusi
            FROM tb_order as a 
            left join tb_harga as vh on a.id_harga = vh.id_harga
            left join tb_menu as b on vh.id_menu = b.id_menu 
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
                ->select(DB::raw('a.*, sum(a.qty) as sum_qty ,  a.no_meja as nm_meja, a.no_meja'))
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

        $data = [
            'order'    => $this->getOrderData($id, 'checker', 'food', null, null, cat_shabu_sushi()),
            'order2'   => $this->getOrderData($id, 'checker', 'drink'),
            'order3'   => $this->getOrderData($id, 'checker', 'food', cat_sushi_only()),
            'order4'   => $this->getOrderData($id, 'checker', 'food', null, cat_shabu_only()),
            'no_order' => $id,
            'pesan_3'  => $this->getPesanData($id, 'checker', 'drink'),
            'pesan_4'  => $this->getPesanData($id, 'checker', 'food', cat_sushi_only()),
            'pesan_5'  => $this->getPesanData($id, 'checker', 'food', null, cat_shabu_only()),
            'majo'     => DB::select("SELECT a.tanggal, a.no_nota, a.nm_karyawan, b.nm_produk, a.id_karyawan, a.jumlah, a.harga, a.total
                FROM tb_pembelian AS a
                LEFT JOIN tb_produk AS b ON b.id_produk = a.id_produk
                WHERE a.no_nota= '$id'"),
            'majo_ttl' => DB::selectOne("SELECT a.tanggal, a.no_nota, a.nm_karyawan, b.nm_produk, a.id_karyawan, sum(a.jumlah) as sum_qty, a.harga, a.total
                FROM tb_pembelian AS a
                LEFT JOIN tb_produk AS b ON b.id_produk = a.id_produk
                WHERE a.no_nota= '$id'
                GROUP BY a.no_nota"),
            'meja'     => DB::selectOne("SELECT a.warna, a.no_meja as nm_meja
                FROM tb_order AS a
                LEFT JOIN tb_meja AS b ON b.id_meja = a.id_meja
                WHERE a.no_order = '$id'
                GROUP BY a.no_order")
        ];

        return view('meja.checker', $data);
    }
    public function checker_tamu(Request $request)
    {
        $id = $request->no;
        $order = $this->getOrderData($id, 'checker_tamu', 'food', null, null, cat_shabu_sushi());
        $order2 = $this->getOrderData($id, 'checker_tamu', 'drink');
        $order3 = $this->getOrderData($id, 'checker_tamu', 'food', cat_sushi_only());
        $order4 = $this->getOrderData($id, 'checker_tamu', 'food', null, cat_shabu_only());

        $data = [
            'order' => $order,
            'order2' => $order2,
            'order3' => $order3,
            'order4' => $order4,
            'no_order' => $id,
            'pesan_2'  => $this->getPesanData($id, 'checker_tamu', 'food', null, null, cat_shabu_sushi()),
            'pesan_3'  => $this->getPesanData($id, 'checker_tamu', 'drink'),
            'pesan_4'  => $this->getPesanData($id, 'checker_tamu', 'food', cat_sushi_only()),
            'pesan_5'  => $this->getPesanData($id, 'checker_tamu', 'food', null, cat_shabu_only()),
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
            'meja' => DB::selectOne("SELECT a.warna, a.no_meja as nm_meja
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

        $data = [
            'order'    => $this->getOrderData($id, 'copy_checker_tamu', 'food', null, null, cat_shabu_sushi()),
            'order2'   => $this->getOrderData($id, 'copy_checker_tamu', 'drink'),
            'order3'   => $this->getOrderData($id, 'copy_checker_tamu', 'food', cat_sushi_only()),
            'order4'   => $this->getOrderData($id, 'copy_checker_tamu', 'food', null, cat_shabu_only()),
            'no_order' => $id,
            'pesan_2'  => $this->getPesanData($id, 'copy_checker_tamu', 'food', null, null, cat_shabu_sushi()),
            'pesan_3'  => $this->getPesanData($id, 'copy_checker_tamu', 'drink'),
            'pesan_4'  => $this->getPesanData($id, 'copy_checker_tamu', 'food', cat_sushi_only()),
            'pesan_5'  => $this->getPesanData($id, 'copy_checker_tamu', 'food', null, cat_shabu_only()),
            'majo'     => DB::select("SELECT a.tanggal, a.no_nota, a.nm_karyawan, b.nm_produk, a.id_karyawan, a.jumlah, a.harga, a.total
                FROM tb_pembelian AS a
                LEFT JOIN tb_produk AS b ON b.id_produk = a.id_produk
                WHERE a.no_nota= '$id'"),
            'majo_ttl' => DB::selectOne("SELECT a.tanggal, a.no_nota, a.nm_karyawan, b.nm_produk, a.id_karyawan, sum(a.jumlah) as sum_qty, a.harga, a.total
                FROM tb_pembelian AS a
                LEFT JOIN tb_produk AS b ON b.id_produk = a.id_produk
                WHERE a.no_nota= '$id'
                GROUP BY a.no_nota"),
            'meja'     => DB::selectOne("SELECT a.warna, a.no_meja as nm_meja
                FROM tb_order AS a
                LEFT JOIN tb_meja AS b ON b.id_meja = a.id_meja
                WHERE a.no_order = '$id'
                GROUP BY a.no_order")
        ];

        Orderan::where('no_order', $id)->update(['copy_checker_tamu' => 'Y']);
        return view('meja.copy_checker_tamu', $data);
    }

    public function copy_checker(Request $request)
    {
        $id = $request->no;
        
        $data = [
            'order' => $this->getOrderData($id, 'copy_checker'),
            'no_order' => $id,
            'pesan_2' => $this->getPesanData($id, 'copy_checker'),
        ];

        Orderan::where('no_order', $id)->update(['copy_print' => 'Y']);

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

        if ($id_dis != '2') {
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
            'id_karyawan'  => 1,
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
            'jml_komisi' => $d_produk->komisi,
            'selesai' => 'selesai'
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

        // if ($hrg_majo > 0) {
        //     $subharga = $qty_majo * $hrg_majo;
        // } else {
        //     $subharga = 0;
        // }
        // $komisi1 = $subharga * $d_produk->komisi / 100;
        // $komisi = $komisi1 / count($nota);
        // foreach ($nota as $id_karyawan) {
        //     $data_komisi = [
        //         'id_pembelian' => $id_pembelian,
        //         'id_kry'  => $id_karyawan,
        //         'komisi' => $komisi,
        //         'tgl' => date('Y-m-d'),
        //         'id_lokasi' => '1'
        //     ];
        //     DB::table('komisi')->insert($data_komisi);
        // }
    }

    public function load_waitress_selesai(Request $r)
    {
        $loc = $r->session()->get('id_lokasi');
        $menu2 = DB::select("SELECT a.*, b.nm_menu, b.tipe 
            FROM tb_order as a 
            LEFT JOIN tb_harga as h ON a.id_harga = h.id_harga
            LEFT JOIN tb_menu as b ON h.id_menu = b.id_menu
            WHERE a.id_meja = '$r->id_meja' AND a.no_order = '$r->no_order' AND a.aktif = '1' AND a.void = 0");
        $majo_hide = DB::select("SELECT a.*, c.nm_produk
                            FROM tb_pembelian AS a
                            LEFT JOIN tb_produk AS c ON c.id_produk = a.id_produk
                            WHERE a.lokasi = '$loc' AND a.no_nota = '$r->no_order' AND a.void = 0
                            GROUP BY a.id_pembelian");
        $tgl = date('Y-m-d');
        $waitress = DB::select(
            "SELECT a.* , b.nama FROM tb_absen as a left join tb_karyawan as b on a.id_karyawan = b.id_karyawan
                 WHERE a.tgl = '$tgl' AND b.id_status = 2 and a.id_lokasi = '$loc'"
        );

        $ord = DB::table('tb_order as a')
            ->leftJoin('tb_distribusi as b', 'a.id_distribusi', '=', 'b.id_distribusi')
            ->where('a.no_order', $r->no_order)
            ->select('a.no_meja', 'b.nm_distribusi', 'a.id_distribusi')
            ->first();

        $prefix = 'Meja';
        if ($ord->id_distribusi == 2) { $prefix = 'Gojek'; }
        if ($ord->id_distribusi == 3) { $prefix = 'AYCE'; }

        $data = [
            'menu2' => $menu2,
            'majo_hide' => $majo_hide,
            'waitress' => $waitress,
            'meja' => $prefix . ' ' . $ord->no_meja
        ];

        return view('meja.load_waitress_selesai', $data);
    }
    private function getOrderData($id, $checker_type, $tipe = null, $kategori = null, $kategori_in = null, $kategori_not_in = null)
    {
        $query = DB::table('tb_order as a')
            ->select(
                'a.id_order', 'b.tipe', 'b.nm_menu', DB::raw('SUM(a.qty) as qty'), 'a.request', 
                'c.nama AS koki1', 'd.nama AS koki2', 'e.nama AS koki3',
                'a.pengantar', 'a.id_meja', 'a.j_mulai', 'a.j_selesai', 'a.wait', 'a.selesai',
                DB::raw('timestampdiff(MINUTE, a.j_mulai, a.wait) AS selisih'), 
                'a.no_checker', 'a.print', 'a.copy_print'
            )
            ->leftJoin('tb_harga as b2', 'a.id_harga', '=', 'b2.id_harga')
            ->leftJoin('tb_menu as b', 'b2.id_menu', '=', 'b.id_menu')
            ->leftJoin('tb_karyawan as c', 'c.id_karyawan', '=', 'a.id_koki1')
            ->leftJoin('tb_karyawan as d', 'd.id_karyawan', '=', 'a.id_koki2')
            ->leftJoin('tb_karyawan as e', 'e.id_karyawan', '=', 'a.id_koki3')
            ->where('a.aktif', '1')
            ->where('a.no_order', $id);

        if ($checker_type === 'checker') {
            $query->where('a.print', 'T');
        } elseif ($checker_type === 'checker_tamu') {
            $query->where('a.checker_tamu', 'T');
        }

        if ($tipe) {
            $query->where('b.tipe', $tipe);
        }
        if ($kategori) {
            $query->where('b.id_kategori', $kategori);
        }
        if ($kategori_in) {
            $query->whereIn('b.id_kategori', $kategori_in);
        }
        if ($kategori_not_in) {
            $query->whereNotIn('b.id_kategori', $kategori_not_in);
        }

        return $query->groupBy('a.id_order')
                     ->orderBy('a.id_order', 'DESC')
                     ->get()
                     ->toArray();
    }

    private function getPesanData($id, $checker_type, $tipe = null, $kategori = null, $kategori_in = null, $kategori_not_in = null)
    {
        $query = DB::table('tb_order as a')
            ->select('a.*', DB::raw('sum(a.qty) as sum_qty'), DB::raw('CONCAT("Meja ", a.no_meja) as nm_meja'), 'a.no_meja as nm_meja_2')
            ->leftJoin('tb_meja as b', 'b.id_meja', '=', 'a.id_meja')
            ->leftJoin('tb_harga as c2', 'c2.id_harga', '=', 'a.id_harga')
            ->leftJoin('tb_menu as c', 'c.id_menu', '=', 'c2.id_menu')
            ->where('a.no_order', $id);

        if ($checker_type === 'checker') {
            $query->where('a.no_checker', 'T');
        } elseif ($checker_type === 'checker_tamu') {
            $query->where('a.checker_tamu', 'T');
        }

        if ($tipe) {
            $query->where('c.tipe', $tipe);
        }
        if ($kategori) {
            $query->where('c.id_kategori', $kategori);
        }
        if ($kategori_in) {
            $query->whereIn('c.id_kategori', $kategori_in);
        }
        if ($kategori_not_in) {
            $query->whereNotIn('c.id_kategori', $kategori_not_in);
        }

        $result = $query->groupBy('a.no_order')->first();
        if ($result && empty($result->nm_meja) && !empty($result->nm_meja_2)) {
            $result->nm_meja = $result->nm_meja_2;
        }
        return $result;
    }
}
