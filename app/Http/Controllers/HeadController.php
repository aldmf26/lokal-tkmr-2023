<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Distribusi;
use App\Models\Koki;

class HeadController extends Controller
{
    public function index(Request $request)
    {
        $id_user = Auth::user()->id;
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)->where('id_menu', 27)->first();

        if (empty($id_menu)) {
            return back();
        } else {

            $id = $request->id ?? '1';
            $lokasi = $request->session()->get('id_lokasi');

            $menu = DB::table('tb_harga as vh')
                ->join('tb_menu as vm', 'vh.id_menu', '=', 'vm.id_menu')
                ->select('vh.id_harga', 'vh.harga', 'vm.nm_menu', 'vm.tipe', 'vm.id_kategori', 'vh.id_distribusi', 'vm.aktif as akv', 'vm.lokasi', 'vh.id_menu')
                ->where([['vh.id_distribusi', $id], ['vm.aktif', 'on'], ['vm.lokasi', $lokasi]])
                ->get();
            $data = [
                'title' => 'Tugas Head',
                'logout' => $request->session()->get('logout'),
                'id' => $id,
                'id_lokasi' => $lokasi,
                'menu' => $menu,
                'orderan' => DB::select("SELECT COUNT(id_order) as jml_order FROM tb_order WHERE id_lokasi = '$lokasi' AND id_distribusi = '$id' AND selesai = 'dimasak' AND void = 0"),
            ];
            return view('head.head', $data);
        }
    }
    public function view1jam(Request $r)
    {
        date_default_timezone_set('Asia/Makassar');
        $tgl = date('Y-m-d');
        $loc = Session::get('id_lokasi');
        $lokasi = $loc == 1 ? 'TAKEMORI' : 'SOONDOBU';
        $data = [
            'title' => 'Data Orderan',
            'logout' => $r->session()->get('logout'),
        'tb_order' => DB::select("SELECT a.*,b.id_menu ,b.nm_menu, c.nm_meja, d.nm_distribusi,
        timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, tb_order2.id_order1 as cek_bayar
        FROM tb_order as a 
        left join tb_harga as vh on a.id_harga = vh.id_harga
        left join tb_menu as b on vh.id_menu = b.id_menu
        LEFT JOIN tb_meja AS c ON c.id_meja = a.id_meja
        LEFT JOIN tb_distribusi AS d ON d.id_distribusi = a.id_distribusi
        LEFT JOIN tb_order2 ON a.id_order = tb_order2.id_order1
        WHERE a.j_selesai BETWEEN NOW() - INTERVAL 1 HOUR AND NOW() AND a.selesai = 'selesai' and a.tgl = '$tgl' and a.id_lokasi = '$loc'  and a.void = 0 order by a.id_order DESC"),

            'kategori' => DB::table('tb_kategori')->where('lokasi', $lokasi)->get(),
            'distribusi' => Distribusi::all(),
            'tb_koki' => Koki::join('tb_karyawan', 'tb_karyawan.id_karyawan', '=', 'tb_koki.id_karyawan')->where('tb_koki.tgl', $tgl)->get(),
            'nav' => '5'
        ];

        return view('head.view1jam', $data);
    }
    public function getSearchHead(Request $request)
    {
        if (empty($request->id)) {
            $id_distribusi = '1';
        } else {
            $id_distribusi = $request->id;
        }
        $s = $request->s;

        $lokasi = $request->session()->get('id_lokasi');
        $tgl = date('Y-m-d');

        // $tb_koki = DB::join('tb_karyawan', 'tb_karyawan.id_karyawan', '=', 'tb_koki.id_karyawan')->where('tb_koki.tgl', $tgl)->where('tb_koki.id_lokasi', $lokasi)->get();
        $tb_koki = DB::table('tb_koki')->join('tb_karyawan', 'tb_karyawan.id_karyawan', '=', 'tb_koki.id_karyawan')->where('tb_koki.tgl', $tgl)->where('tb_koki.id_lokasi', $lokasi)->get();

        $meja = DB::select("SELECT a.id_meja, d.nm_meja, a.no_order, RIGHT(a.no_order,2) AS kd, b.nm_distribusi,a.selesai, c.no_order AS bayar
        FROM tb_order AS a
        LEFT JOIN tb_distribusi AS b ON b.id_distribusi = a.id_distribusi
        left join tb_meja as d on d.id_meja = a.id_meja
        LEFT JOIN tb_transaksi AS c ON c.no_order = a.no_order
        LEFT JOIN tb_harga as vh on a.id_harga = vh.id_harga
        LEFT JOIN tb_menu as e on vh.id_menu = e.id_menu
        WHERE e.nm_menu LIKE '%$s%' and a.aktif = '1' and a.selesai = 'dimasak' AND a.id_lokasi = '$lokasi' and a.id_distribusi = '$id_distribusi'
        group by a.no_order order by a.id_distribusi , a.id_meja ASC
        ");

        $data = [
            'title' => 'Tugas Head',
            'meja' => $meja,
            'tb_koki' => $tb_koki,
            'lokasi' => $lokasi,
            'distribusi' => DB::select("SELECT a.*, c.jumlah
                            FROM tb_distribusi AS a 
                            LEFT JOIN (SELECT b.id_distribusi , COUNT(b.id_order) AS jumlah
                            FROM tb_order AS b
                            WHERE b.selesai = 'dimasak'
                            GROUP BY b.id_distribusi
                            ) c ON c.id_distribusi = a.id_distribusi
                            "),
            'id' => $id_distribusi,
            'search' => $s,

        ];
        return view('head.getSearchHead', $data);
    }

    public function get_head(Request $request)
    {
        $id_distribusi = $request->id ?? '1';
        $lokasi = $request->session()->get('id_lokasi');
        $tgl = date('Y-m-d');
        $tb_koki = DB::table('tb_koki')->join('tb_karyawan', 'tb_karyawan.id_karyawan', '=', 'tb_koki.id_karyawan')->where('tb_koki.tgl', $tgl)->where('tb_koki.id_lokasi', $lokasi)->get();
        $limit = $request->limit ?? '3';
        $meja_search = $request->meja ?? '';

        $whereMeja = '';
        if ($meja_search != '') {
            $whereMeja = " AND a.no_meja LIKE '%$meja_search%' ";
        }
        $cat_bev_ongkir = implode(',', cat_beverages_ongkir());

        // Priority: Only show non-beverage and non-STK items waiting to be cooked.
        $meja = DB::select("SELECT a.id_meja, a.no_meja as nm_meja, a.no_order, RIGHT(a.no_order,2) AS kd, b.nm_distribusi, a.selesai, a.id_distribusi
        FROM tb_order AS a
        LEFT JOIN tb_distribusi AS b ON b.id_distribusi = a.id_distribusi
        JOIN tb_harga h ON a.id_harga = h.id_harga
        JOIN tb_menu m_table ON h.id_menu = m_table.id_menu
        WHERE a.aktif = '1' AND a.id_lokasi = '$lokasi' AND a.selesai = 'dimasak' AND a.void = 0 AND m_table.id_kategori NOT IN ($cat_bev_ongkir) $whereMeja
        GROUP BY a.no_order 
        ORDER BY MIN(a.j_mulai) ASC
        LIMIT $limit;
        ");

        $no_orders = [];
        foreach ($meja as $m) {
            $no_orders[] = $m->no_order;
        }

        $menus = [];
        $majos = [];

        if (!empty($no_orders)) {
            $order_list = "'" . implode("','", $no_orders) . "'";

            $all_menus = DB::select("SELECT m_table.nm_menu, a.no_meja as nm_meja, a.request, a.qty, a.selesai, a.id_order, a.j_mulai, a.no_order, f.ttlMenuSemua, g.other_tables 
                FROM tb_order AS a 
                JOIN tb_harga h ON a.id_harga = h.id_harga
                JOIN tb_menu m_table ON h.id_menu = m_table.id_menu
                LEFT JOIN (
                    SELECT d.id_harga, SUM(d.qty) as ttlMenuSemua 
                    FROM `tb_order` as d 
                    where d.id_lokasi = '$lokasi' and d.selesai = 'dimasak' and d.aktif = '1' and d.void = 0 
                    GROUP BY d.id_harga
                ) as f on a.id_harga = f.id_harga
                LEFT JOIN (
                    SELECT id_harga, GROUP_CONCAT(CONCAT('Meja ', no_meja, '(', sum_qty, ')') SEPARATOR ', ') as other_tables
                    FROM (
                        SELECT d.id_harga, d.no_meja, SUM(d.qty) as sum_qty
                        FROM `tb_order` as d
                        where d.id_lokasi = '$lokasi' and d.selesai = 'dimasak' and d.aktif = '1' and d.void = 0
                        GROUP BY d.id_harga, d.no_meja
                    ) as grouped
                    GROUP BY id_harga
                ) as g on a.id_harga = g.id_harga
                where a.id_lokasi = '$lokasi' and a.no_order IN ($order_list) and a.selesai = 'dimasak' and a.aktif = '1' and a.void = 0 AND m_table.id_kategori NOT IN ($cat_bev_ongkir)
                ORDER BY a.id_order");

            foreach ($all_menus as $menu) {
                $menus[$menu->no_order][] = $menu;
            }

            $all_majos = DB::select("SELECT a.jumlah, a.id_pembelian, c.nm_produk, a.no_nota
                    FROM tb_pembelian AS a
                    LEFT JOIN tb_produk AS c ON c.id_produk = a.id_produk
                    WHERE a.no_nota IN ($order_list) AND a.lokasi = '$lokasi' AND IFNULL(a.bayar, 'T') = 'T'
                    GROUP BY a.id_pembelian");

            foreach ($all_majos as $majo) {
                $majos[$majo->no_nota][] = $majo;
            }
        }

        $setMenit = DB::table('tb_menit')->where('id_lokasi', $lokasi)->first();

        $data = [
            'title' => 'Tugas Head',
            'meja' => $meja,
            'menus_all' => $menus,
            'majos_all' => $majos,
            'tb_koki' => $tb_koki,
            'lokasi' => $lokasi,
            'id' => $id_distribusi,
            'setMenit' => $setMenit,
        ];
        return view('head.tugas', $data);
    }

    public function distribusi(Request $request)
    {

        if (empty($request->id)) {
            $id = '1';
        } else {
            $id = $request->id;
        }
        $tgl = date('Y-m-d');
        $lokasi = $request->session()->get('id_lokasi');
        $data = [
            'title'    => 'Menu | Buku Tugas',
            'id' => $id,
            'distribusi' => DB::select("SELECT a.*, c.jumlah
            FROM tb_distribusi AS a 
            LEFT JOIN (SELECT b.id_distribusi , COUNT(b.id_order) AS jumlah
            FROM tb_order AS b
            WHERE b.selesai = 'dimasak' AND b.id_lokasi = $lokasi AND b.void = 0
            GROUP BY b.id_distribusi
            ) c ON c.id_distribusi = a.id_distribusi
            "),
            'tb_koki' => DB::table('tb_koki')->join('tb_karyawan', 'tb_karyawan.id_karyawan', '=', 'tb_koki.id_karyawan')->where('tb_koki.tgl', $tgl)->where('tb_koki.id_lokasi', $lokasi)->get(),
            'orderan' => DB::select("SELECT COUNT(id_order) as jml_order FROM `tb_order` WHERE id_lokasi = $lokasi AND id_distribusi = $id AND selesai = 'dimasak'")

        ];
        return view('head.distribusi', $data);
    }

    public function jumlah(Request $request)
    {
        if (empty($request->id)) {
            $id = '1';
        } else {
            $id = $request->id;
        }
        $tgl = date('Y-m-d');
        $lokasi = $request->session()->get('id_lokasi');
        $data = [
            'title'    => 'Menu | Buku Tugas',
            'tb_order' => DB::table('tb_order')->join('tb_harga as vh', 'vh.id_harga', '=', 'tb_order.id_harga')->join('tb_menu as vm', 'vh.id_menu', '=', 'vm.id_menu')->where('tb_order.aktif', '1')->get(),
            'kategori' => DB::table('tb_kategori')->where('lokasi', 'TAKEMORI')->get(),
            'distribusi' => DB::select("SELECT a.*, c.jumlah
            FROM tb_distribusi AS a 
            LEFT JOIN (SELECT b.id_distribusi , COUNT(b.id_order) AS jumlah
            FROM tb_order AS b
            WHERE b.selesai = 'dimasak' AND b.id_lokasi = $lokasi AND b.void = 0
            GROUP BY b.id_distribusi
            ) c ON c.id_distribusi = a.id_distribusi
            "),
            'tb_koki' => DB::table('tb_koki')->join('tb_karyawan', 'tb_karyawan.id_karyawan', '=', 'tb_koki.id_karyawan')->where('tb_koki.tgl', $tgl)->where('tb_koki.id_lokasi', $lokasi)->get(),
            'id' => $id

        ];
        return view('head.jumlah', $data);
    }


    public function head_selesei(Request $request)
    {
        date_default_timezone_set('Asia/Makassar');
        $id_order = $request->kode;
        $data = array(
            'selesai'   => 'selesai',
            'j_selesai' => date('Y-m-d H:i:s'),
            'wait' => date('Y-m-d H:i:s'),
        );

        DB::table('tb_order')->where('id_order', $id_order)->update($data);
    }

    public function head_cancel(Request $request)
    {
        date_default_timezone_set('Asia/Makassar');
        $id_order = $request->kode;

        // Validasi: Cek apakah sudah dibayar
        $cek_bayar = DB::table('tb_order2')->where('id_order1', $id_order)->first();
        if ($cek_bayar) {
            return response()->json(['status' => 'error', 'message' => 'Item sudah diproses ke pembayaran/dibayar, tidak bisa di-cancel!']);
        }

        // Validasi: Cek apakah sudah lewat 1 jam
        $order = DB::table('tb_order')->where('id_order', $id_order)->first();
        if ($order && $order->j_selesai) {
            $selisih = (strtotime(date('Y-m-d H:i:s')) - strtotime($order->j_selesai)) / 3600;
            if ($selisih > 1) {
                return response()->json(['status' => 'error', 'message' => 'Sudah lewat 1 jam dari waktu selesai, tidak bisa di-cancel!']);
            }
        }

        $data = array(
            'selesai'   => 'dimasak',
        );
        DB::table('tb_order')->where('id_order', $id_order)->update($data);

        return response()->json(['status' => 'success', 'message' => 'Berhasil membatalkan status selesai.']);
    }

    public function head2(Request $r)
    {
        $id_meja =  $r->id_meja;
        $lokasi = $r->session()->get('id_lokasi');

        $meja = DB::selectOne("SELECT a.id_meja, a.no_meja as nm_meja, a.no_order,  b.nm_distribusi,a.selesai, a.id_distribusi
        FROM tb_order AS a
        LEFT JOIN tb_distribusi AS b ON b.id_distribusi = a.id_distribusi
        WHERE a.aktif = '1' AND a.id_meja = '$id_meja' AND a.no_order = '$r->no_order'
        group by a.no_order order by a.id_distribusi , a.id_meja ASC
        ");

        $menu = DB::select(
            "SELECT b.nm_menu, c.nm_meja, a.*,f.ttlMenuSemua FROM tb_order AS a 
            LEFT JOIN tb_harga as vh ON a.id_harga = vh.id_harga
            LEFT JOIN tb_menu AS b ON vh.id_menu = b.id_menu
            LEFT JOIN (SELECT d.id_harga, COUNT(id_harga) as ttlMenuSemua FROM `tb_order` as d where d.id_lokasi = '$lokasi' and d.selesai = 'dimasak' and d.aktif = '1' and void = 0 GROUP BY d.id_harga) as f on a.id_harga = f.id_harga
            LEFT JOIN tb_meja AS c ON c.id_meja = a.id_meja where a.id_lokasi = '$lokasi' and a.id_meja = '$id_meja' and a.selesai = 'dimasak' and b.aktif = '1' and void = 0 ORDER BY a.id_order"
        );



        $data = [
            'm' => $meja,
            'menu' => $menu,
            'lokasi' => $lokasi,
        ];

        return view('head.tugas2', $data);
    }

    public function load_menu_selesai(Request $r)
    {
        $lokasi = $r->session()->get('id_lokasi');
        $tgl = date('Y-m-d');
        $menu2 = DB::select("SELECT b.nm_menu, a.*,f.ttlMenuSemua 
        FROM tb_order AS a
        LEFT JOIN tb_harga as vh ON a.id_harga = vh.id_harga
        LEFT JOIN tb_menu AS b ON vh.id_menu = b.id_menu
        LEFT JOIN (SELECT d.id_harga, COUNT(id_harga) as ttlMenuSemua FROM `tb_order` as d where d.id_lokasi = '$lokasi' and d.selesai != 'dimasak' and d.aktif = '1' and void = 0 GROUP BY d.id_harga) as f on a.id_harga = f.id_harga
        
        where a.id_lokasi = '$lokasi' and a.id_meja = '$r->id_meja' and a.selesai != 'dimasak' and b.aktif = '1' and void = 0 ORDER BY a.id_order");
        $tb_koki = DB::table('tb_koki')->join('tb_karyawan', 'tb_karyawan.id_karyawan', '=', 'tb_koki.id_karyawan')->where('tb_koki.tgl', $tgl)->where('tb_koki.id_lokasi', $lokasi)->get();

        $majo_hide = DB::select("SELECT a.no_nota, c.nm_produk,a.jumlah
        FROM tb_pembelian AS a
        LEFT JOIN tb_produk AS c ON c.id_produk = a.id_produk
        WHERE  a.lokasi = '$lokasi' and a.selesai = 'selesai' and a.no_nota = '$r->no_order'
        GROUP BY a.id_pembelian");

        $data = [
            'menu2' => $menu2,
            'tb_koki' => $tb_koki,
            'lokasi' => $lokasi,
            'majo_hide' => $majo_hide,
        ];
        return view('head.load_menu_selesai', $data);
    }
}
