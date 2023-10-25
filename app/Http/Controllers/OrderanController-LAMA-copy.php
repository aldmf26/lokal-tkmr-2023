<?php

namespace App\Http\Controllers;

use App\Models\Ctt_driver;
use App\Models\Discount;
use App\Models\Dp;
use App\Models\Invoice;
use App\Models\Invoice2;
use App\Models\Order2;
use App\Models\Orderan;
use App\Models\Transaksi;
use App\Models\Voucher;
use App\Models\Akun;
use App\Models\Pembelian;
use App\Models\Jurnal;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class OrderanController extends Controller
{
    public function index(Request $request)
    {
        $tgl = date('Y-m-d');
        $id_lokasi = $request->session()->get('id_lokasi');
        $id = $request->id;
        $data = [
            'title'    => 'Order Permeja',
            'logout' => $request->session()->get('logout'),
            'tb_order' => DB::join('view_menu', 'view_menu.id_harga = tb_order.id_harga')->where('tb_order.aktif', 1)->where('tb_order.id_meja', $id)->get(),
            'no_meja' => DB::join('tb_distribusi', 'tb_distribusi.id_distribusi = tb_order.id_distribusi')->where('tb_order.no_order', $id)->get(),
            'waitress' => DB::table('tb_karyawan')->where('id_status', '2')->get(),
            'driver' => DB::table('tb_karyawan')->get(),
            'order' => DB::select("SELECT a.id_order, b.nm_menu, a.qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai, a.no_order, a.id_distribusi
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where a.aktif = '1' and a.no_order = '$id' "),

            'tb_menu' => DB::select("SELECT a.id_menu, a.id_harga, b.nm_menu, c.nm_distribusi, a.harga,b.image
            FROM tb_harga AS a 
            LEFT JOIN tb_menu AS b ON b.id_menu = a.id_menu 
            LEFT JOIN tb_distribusi AS c ON c.id_distribusi = a.id_distribusi
            where a.id_distribusi = '1' AND b.lokasi ='$id_lokasi'
            GROUP BY a.id_harga"),
        ];
        return view('orderan.orderan', $data);
    }
    public function check_pembayaran(Request $request)
    {
        $no = $request->no;
        $order = DB::select("SELECT a.id_order, a.id_harga, b.nm_menu, a.qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, a.id_distribusi,
        a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai, a.harga,
        timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, if(f.qty IS NULL ,0,f.qty) AS qty2
        FROM tb_order as a 
        left join view_menu as b on a.id_harga = b.id_harga 
        left join tb_karyawan as c on c.id_karyawan = a.id_koki1
        left join tb_karyawan as d on d.id_karyawan = a.id_koki2
        left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
        LEFT JOIN tb_order2 AS f ON f.id_order1 = a.id_order
        where   a.no_order = '$no' AND (a.qty - if(f.qty IS NULL ,0,f.qty)) != '0' AND a.selesai = 'selesai'");

        if ($order) {
            echo 'ada';
        } else {
            echo 'kosong';
        }
    }

    public function list_orderan(Request $request)
    {
        $no = $request->no;
        $transaksi = DB::table('tb_transaksi')->where('no_order', $no)->get();
        $order = DB::select("SELECT a.id_order, b.nm_menu, a.qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, a.id_distribusi,
        a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai, a.harga,
        timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih
        FROM tb_order as a 
        left join view_menu as b on a.id_harga = b.id_harga 
        left join tb_karyawan as c on c.id_karyawan = a.id_koki1
        left join tb_karyawan as d on d.id_karyawan = a.id_koki2
        left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
        where   a.no_order = '$no' AND a.selesai = 'selesai' AND a.void = 0 ");

        $data = [
            'title' => 'Pembayaran',
            'logout' => $request->session()->get('logout'),
            'order' => $order,
            'no' => $no,
            'transaksi' => $transaksi,
            'dp' => DB::table('tb_dp')->get(),
            'nav' => '2'
        ];

        return view('orderan.list_orderan', $data);
    }

    public function list_order2(Request $request)
    {
        $id_lokasi = $request->session()->get('id_lokasi');
        $no = $request->no;
        $transaksi = DB::table('tb_transaksi')->where('no_order', $no)->get();
        $order = DB::select("SELECT a.id_order, a.id_harga, b.nm_menu, a.qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, a.id_distribusi,
        a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai, a.harga,
        timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, if(f.qty IS NULL ,0,f.qty) AS qty2, g.nm_meja
        FROM tb_order as a 
        left join view_menu as b on a.id_harga = b.id_harga 
        left join tb_karyawan as c on c.id_karyawan = a.id_koki1
        left join tb_karyawan as d on d.id_karyawan = a.id_koki2
        left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
        LEFT JOIN tb_order2 AS f ON f.id_order1 = a.id_order
        LEFT JOIN tb_meja AS g ON g.id_meja = a.id_meja
        where   a.no_order = '$no' AND (a.qty - if(f.qty IS NULL ,0,f.qty)) != '0' AND a.selesai = 'selesai' AND a.void = 0 ");

        $majo = DB::select("SELECT a.id_pembelian, a.tanggal, a.no_nota, c.nm_meja, a.nm_karyawan, b.nm_produk, a.id_karyawan,  a.jumlah, a.harga, a.total
        FROM tb_pembelian AS a
        LEFT JOIN tb_produk AS b ON b.id_produk = a.id_produk
        left join tb_meja as c on c.id_meja = a.no_meja
        WHERE a.no_nota= '$no' and a.bayar = 'T'
        ");
        $now = date('Y-m-d');
        $disc = DB::table('tb_discount')
            ->where([['lokasi', Session::get('id_lokasi')], ['dari', '<=', $now], ['expired', '>=', $now], ['aktif', '>=', 'Y']])->first();

        $data = [
            'title' => 'Pembayaran',
            'logout' => $request->session()->get('logout'),
            'order2' => $order,
            'no' => $no,
            'id_distribusi' => $request->id_distribusi,
            'transaksi' => $transaksi,
            'dp' => DB::table('tb_dp')->where('id_lokasi', $id_lokasi)->where('status', 0)->get(),
            'nav' => '2',
            'ongkir_bayar' => DB::select("SELECT SUM(a.rupiah) AS rupiah
            FROM tb_ongkir AS a"),
            'majo' => $majo,
            'disc' => $disc,
            'klasifikasi_pembayaran' => DB::table('klasifikasi_pembayaran')->get()

        ];

        return view('orderan.list_orderan2', $data);
    }
    public function getPromoBank(Request $r)
    {
        $id_akun = $r->id_akun;
        $ttl_sub = $r->ttl_sub;
        $pembayaran = $r->total_pembayaran;

        $whereDiskon = [
            ['id_jenis_pembayaran', $id_akun],
            ['min_order', '<=', $ttl_sub],
            ['max_order', '>=', $ttl_sub],
        ];

        $diskon = DB::table('tb_paket_diskon')
            ->where($whereDiskon)
            ->first();
        if ($diskon) {
            $persentase_diskon = $diskon->persen_diskon / 100;
            $maksimal_jumlah_diskon = $diskon->max_diskon_rp;

            // Menghitung jumlah diskon
            $jumlah_diskon = min($pembayaran * $persentase_diskon, $maksimal_jumlah_diskon);

            // Mengurangkan jumlah diskon dari total pesanan
            $total_pesanan_setelah_diskon = $pembayaran - $jumlah_diskon;
        } else {
            $jumlah_diskon = 0;
            $total_pesanan_setelah_diskon = $ttl_sub;
        }

        return json_encode([
            'jumlah_diskon' => $jumlah_diskon,
            'persentase_diskon' => $diskon->persen_diskon,
            'ttl_setelah_diskon' => $total_pesanan_setelah_diskon,
        ]);
    }
    public function save_transaksi(Request $request)
    {
        DB::beginTransaction();
        try {

            $no_order = $request->no_order;
            $id_order = $request->id_order;
            $id_harga = $request->id_harga;
            $qty = $request->qty;
            $qty_majo = $request->qty_majo;
            $id_pembelian = $request->id_pembelian;
            $total_majo = $request->total_majo;
            $harga = $request->harga;
            $id_meja = $request->id_meja;
            $kode_dp = $request->kode_dp;
            $id_distribusi = $request->id_distribusi;
            $lokasi = $request->session()->get('id_lokasi');
            $voucher = $request->voucher;
            $disc = $request->disc;
            $dis = DB::table('tb_distribusi')->where('id_distribusi', $id_distribusi)->first();
            $kode = substr($dis->nm_distribusi, 0, 2);



            $q = DB::select(
                DB::raw("SELECT MAX(RIGHT(a.no_order2,4)) AS kd_max FROM tb_order2 AS a
                WHERE DATE(a.tgl)=CURDATE() AND a.id_lokasi = '$lokasi' AND a.id_distribusi = '$id_distribusi'"),
            );
            $kd = '';
            if (count($q) > 0) {
                foreach ($q as $k) {
                    $tmp = ((int) $k->kd_max) + 1;
                    $kd = sprintf('%04s', $tmp);
                }
            } else {
                $kd = '0001';
            }
            date_default_timezone_set('Asia/Makassar');
            $no_invoice = date('ymd') . $kd;

            $dis = DB::table('tb_distribusi')
                ->where('id_distribusi', $id_distribusi)
                ->first();
            $kode = substr($dis->nm_distribusi, 0, 2);
            if ($lokasi == '1') {
                $hasil = "T$kode-$no_invoice";
            } else {
                $hasil = "S$kode-$no_invoice";
            }
            $data = [
                'no_invoice' => $hasil,
                'tanggal' => date('Y-m-d'),
            ];



            Invoice2::create($data);

            for ($x = 0; $x < sizeof($id_order); $x++) {
                if ($qty[$x] == '' || $qty[$x] == '0') {
                } else {
                    // $ada = Order2::where([
                    //     ['id_order1' => $id_order[$x]],
                    //     ['created_at' => date('Y-m-d H:i:s')]
                    // ]);
                    $data = [
                        'id_order1' => $id_order[$x],
                        'no_order' => $no_order,
                        'no_order2' => $hasil,
                        'id_harga' => $id_harga[$x],
                        'qty' => $qty[$x],
                        'harga' => $harga[$x],
                        'tgl' => date('Y-m-d'),
                        'id_lokasi' => $lokasi,
                        'admin' => Auth::user()->nama,
                        'id_distribusi' => $id_distribusi,
                        'id_meja' => $id_meja[$x]
                    ];
                    Order2::create($data);
                }
            }
            if (empty($id_pembelian)) {
            } else {
                for ($x = 0; $x < sizeof($id_pembelian); $x++) {
                    if ($qty_majo[$x] == '' || $qty_majo[$x] == '0') {
                    } else {
                        $data = [
                            'bayar' => 'Y',
                            'no_nota2' => $hasil
                        ];
                        Pembelian::where('id_pembelian', $request->id_pembelian[$x])->update($data);
                    }
                }
                if ($request->total_dibayar < $total_majo) {
                } else {
                    $data6 = [
                        'bayar' => $request->total_dibayar < 1 ? 0 : $total_majo,
                        'no_nota' => $hasil,
                        'total' => $request->total_dibayar < 1 ? 0 : $total_majo,
                        'tgl_jam' => date('Y-m-d'),
                        'tgl_input' => date('Y-m-d H:i:s'),
                        'admin' => Auth::user()->nama,
                        'lokasi' => $lokasi,
                        'id_distribusi' => $id_distribusi
                    ];
                    DB::table('tb_invoice')->insert($data6);
                }
            }





            for ($i = 0; $i < count($request->id_akun); $i++) {

                if ($request->pembayaran[$i] == 0) {
                    # code...
                } else {
                    $id_akunBayar = $request->id_akun[$i];
                    $pembayaran = $request->pembayaran[$i];
                    $jumlah_diskon = 0;
                    $ttl_sub = $request->sub;
                    $getAkunPembayaran = DB::table('akun_pembayaran as a')
                        ->where('a.id_akun_pembayaran', $id_akunBayar)
                        ->first();
                    
                    $data = [
                        'id_akun_pembayaran' => $id_akunBayar,
                        'no_nota' => $hasil,
                        'nominal' => $pembayaran,
                        'pengirim' => $request->nm_pengirim[$i],
                        'diskon_bank' => $request->diskonPromo ?? 0,
                        'tgl' => date('Y-m-d'),
                        'id_lokasi' => $lokasi,
                        'tgl_waktu' => now()
                    ];
                    DB::table('pembayaran')->insert($data);
                }
            }

            $data = [
                'tgl_transaksi' => date('Y-m-d'),
                'no_order' => $hasil,
                'voucher' => ($request->voucher == '' ? 0 : $request->voucher),
                'discount' => $request->discount == '' ? 0 : $request->discount,
                'dp' => ($request->dp == '' ? 0 : $request->dp),
                'gosen' => $request->gosen,
                'diskon_bank' => $request->diskonPromo ?? 0,
                'round' => $request->round,
                'total_orderan' => $request->sub,
                'total_bayar' => $request->total_dibayar,
                // 'cash' => $request->cash,
                // 'd_bca' => $request->d_bca,
                // 'k_bca' => $request->k_bca,
                // 'd_mandiri' => $request->d_mandiri,
                // 'k_mandiri' => $request->k_mandiri,
                'admin' => Auth::user()->nama,
                'id_lokasi' => $lokasi,
                'ongkir' => $request->ongkir,
                'service' => $request->service,
                'tax' => $request->tax,
                'kembalian' => $request->kembalian1,
            ];
            DB::table('tb_transaksi')->insert($data);

            $data2 = [
                'terpakai' => 'sudah',
                'status' => 'off'
            ];

            Voucher::where('kode', $request->kd_voucher)->update($data2);
            $data3 = [
                'status' => '1'
            ];
            Dp::where('id_dp', $request->id_dp)->update($data3);

            $order = DB::table('tb_order')->select('pengantar')->where('no_order', $no_order)->groupBy('no_order')->first();
            $ongkir = DB::table('tb_ongkir')->select('rupiah')->where('id_ongkir', '1')->first();
            if ($id_distribusi == '3') {
                $data4 = [
                    'no_order' => $hasil,
                    'nm_driver' => $order->pengantar,
                    'nominal' =>  $ongkir->rupiah,
                    'tgl' => date('Y-m-d'),
                    'admin' => Auth::user()->nama
                ];
                Ctt_driver::create($data4);
            }
            // Jika tidak ada kesalahan, Anda dapat melakukan commit secara manual
            DB::commit();

            return json_encode([
                'nota' => $hasil,
                'code' => 'berhasil',
            ]);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, Anda dapat melakukan rollback secara manual
            DB::rollBack();
            return json_encode([
                'nota' => $hasil,
                'code' => 'error',
            ]);
            // Anda dapat menangani kesalahan atau logging di sini
        }
        
    }

    public function pembayaran2(Request $request)
    {
        $no = $request->no;
        $order = DB::select("SELECT a.*, SUM(a.qty) as qty_produk, b.nm_menu, c.nm_meja
        FROM tb_order2 as a 
        left join view_menu as b on a.id_harga = b.id_harga 
        left join tb_meja as c on c.id_meja = a.id_meja
        
        where   a.no_order2 = '$no' 
        GROUP BY a.id_harga
        ");

        $meja = DB::table('tb_order2')->where('no_order2', $no)->first();

        $dis = Order2::where('no_order2', $no)->first();
        $majo = DB::select("SELECT a.id_pembelian, a.tanggal, a.no_nota, c.nm_meja,
        a.nm_karyawan, b.nm_produk, a.id_karyawan, a.jumlah, a.harga, a.total
        FROM tb_pembelian AS a
        LEFT JOIN tb_produk AS b ON b.id_produk = a.id_produk
        left join tb_meja as c on c.id_meja = a.no_meja
        WHERE  a.no_nota2 = '$no'
        ");

        $data = [
            'title' => 'Pembayaran',
            'logout' => $request->session()->get('logout'),
            'transaksi' => DB::table('tb_transaksi')->where('no_order', $no)->first(),
            'order' => $order,
            'dis' => $dis->id_distribusi,
            'no' => $no,
            'meja' => $meja->id_meja,
            'majo' => $majo,
            'dp' => DB::table('tb_dp')->get(),
            'nav' => '2',
            'ongkir_bayar' => DB::select("SELECT SUM(a.rupiah) AS rupiah
            FROM tb_ongkir AS a"),
            'pembayaran' => DB::select("SELECT b.nm_akun, c.nm_klasifikasi, a.nominal, a.pengirim
            FROM pembayaran as a 
            left join akun_pembayaran as b on b.id_akun_pembayaran = a.id_akun_pembayaran
            left join klasifikasi_pembayaran as c on c.id_klasifikasi_pembayaran = b.id_klasifikasi
            where a.no_nota ='$no';"),
        ];

        return view('orderan.pembayaran2', $data);
    }

    public function print_nota(Request $request)
    {
        $no = $request->no;
        $kembalian = $request->kembalian;
        $order = DB::select("SELECT a.*, SUM(a.qty) as qty_produk, b.nm_menu, c.j_mulai , c.j_selesai, c.wait ,
        timestampdiff(MINUTE, MIN(c.j_mulai),MAX(c.j_selesai)) AS selisih, timestampdiff(MINUTE, MIN(c.j_selesai),MAX(c.wait)) AS selisih2
        FROM tb_order2 as a 
        left join tb_order as c on c.id_order = a.id_order1
        left join view_menu as b on a.id_harga = b.id_harga 
        where   a.no_order2 = '$no'
        GROUP BY a.id_harga
        ");

        $majo = DB::select("SELECT a.id_pembelian, a.tanggal, a.no_nota, c.nm_meja,
        a.nm_karyawan, b.nm_produk, a.id_karyawan, a.jumlah, a.harga, a.total
        FROM tb_pembelian AS a
        LEFT JOIN tb_produk AS b ON b.id_produk = a.id_produk
        left join tb_meja as c on c.id_meja = a.no_meja
        WHERE a.no_nota2= '$no'
        ");


        $data = [
            'title' => 'Pembayaran',
            'transaksi' => Transaksi::where('no_order', $no)->first(),
            'order' => $order,
            'no' => $no,
            'kembalian' => $kembalian,
            'dp' => Dp::all(),
            'majo' => $majo,
            'pesan_2' => DB::select("SELECT a.*, sum(a.qty) as sum_qty ,  b.nm_meja , c.j_mulai, c.j_selesai, c.wait,c.orang
            FROM tb_order2 as a 
            left join tb_meja as b on a.id_meja = b.id_meja 
            LEFT JOIN tb_order AS c ON c.id_order = a.id_order1
            where a.no_order2 = '$no' 
            group by a.no_order2"),
            'pembayaran' => DB::select("SELECT b.nm_akun, c.nm_klasifikasi, a.nominal, a.pengirim
            FROM pembayaran as a 
            left join akun_pembayaran as b on b.id_akun_pembayaran = a.id_akun_pembayaran
            left join klasifikasi_pembayaran as c on c.id_klasifikasi_pembayaran = b.id_klasifikasi
            where a.no_nota ='$no';"),
        ];

        return view('orderan.print_nota', $data);
    }

    public function all_checker(Request $request)
    {
        $id = $request->no;
        $order = DB::select("SELECT a.id_order, b.nm_menu, a.qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
        a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai,
        timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.no_checker , a.print, a.copy_print
        FROM tb_order as a 
        left join view_menu as b on a.id_harga = b.id_harga 
        left join tb_karyawan as c on c.id_karyawan = a.id_koki1
        left join tb_karyawan as d on d.id_karyawan = a.id_koki2
        left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
        where a.aktif = '1' and  a.no_order = '$id' 
        ORDER BY a.id_order DESC
        ");

        $data = [
            'order' => $order,
            'no_order' => $id,
            'pesan_2'    => DB::select("SELECT a.*, sum(a.qty) as sum_qty ,  b.nm_meja FROM tb_order as a left join tb_meja as b on a.id_meja = b.id_meja where a.no_order = '$id' group by a.no_order"),
        ];

        return view('meja.print_all', $data);
    }

    public function voucher(Request $request)
    {
    }

    public function get_dp(Request $request)
    {
        $id_dp = $request->id_dp;
        $dp = Dp::where('id_dp', $id_dp)->get();
        $output = [];
        foreach ($dp as $d) {
            $output['id_dp'] = $d->id_dp;
            $output['kd_dp'] = $d->kd_dp;
            $output['tgl'] = $d->tgl;
            $output['jumlah'] = $d->jumlah;
            $output['metode'] = $d->metode;
        }
        echo json_encode($output);
    }

    public function get_discount(Request $request)
    {
        $id_discount = $request->id_discount;
        $discount = Discount::where('id_discount', $id_discount)->get();
        $output = [];
        foreach ($discount as $d) {
            $output['id_discount'] = $d->id_discount;
            $output['ket'] = $d->ket;
            $output['dari'] = $d->dari;
            $output['expired'] = $d->expired;
            $output['jenis'] = $d->jenis;
            $output['disc'] = $d->disc;
        }
        echo json_encode($output);
    }
}
