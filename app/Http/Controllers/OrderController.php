<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Distribusi;
use App\Models\Order;
use App\Models\Orderan;
use App\Models\Invoice;
use App\Models\Pembelian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    private function resolveDistribusi($value): array
    {
        $idMe = empty($value) ? '1' : (string) $value;
        $id = empty($value) ? '1' : (string) $value;

        return [$idMe, $id];
    }

    private function getAvailableMeja($idLokasi, $idDistribusi, $date)
    {
        return DB::select("SELECT *
            FROM tb_meja AS a
            WHERE a.id_meja NOT IN (SELECT b.id_meja from tb_order AS b WHERE b.tgl = '$date' or b.aktif = '1' )
            and a.id_lokasi = '$idLokasi' and a.id_distribusi = '$idDistribusi'");
    }

    private function menuOrderLightQuery($idLokasi, $idDistribusi)
    {
        return DB::table('tb_harga as a')
            ->join('tb_menu as b', 'a.id_menu', '=', 'b.id_menu')
            ->select(
                'a.id_harga',
                'a.id_menu',
                'a.harga',
                'b.nm_menu',
                'b.tipe',
                'b.id_kategori'
            )
            ->where('b.lokasi', $idLokasi)
            ->where('a.id_distribusi', $idDistribusi)
            ->where('b.aktif', 'on');
    }

    public function index(Request $request)
    {
        $id_user = Auth::user()->id;
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)
            ->where('id_menu', 25)->first();
        if (empty($id_menu)) {

            return back();
        } else {
            $id_dis = $request->dis;
            [$id_me, $id] = $this->resolveDistribusi($id_dis);
            $id_lokasi = $request->session()->get('id_lokasi');
            $date = date('Y-m-d');
            if ($id_lokasi == '1') {
                $lokasi = 'TAKEMORI';
            } else {
                $lokasi = 'SOONDOBU';
            }
            $tgl = date('Y-m-d');

            $meja = $this->getAvailableMeja($id_lokasi, $id_me, $date);
            $menusPerKategori = $this->menuOrderLightQuery($id_lokasi, $id)
                ->get()
                ->groupBy('id_kategori');
            $data = [
                'title' => 'Order',
                'logout' => $request->session()->get('logout'),
                'distribusi' => Cache::remember("order:distribusi", 120, function () {
                    return Distribusi::all();
                }),
                'id' => $id,
                'id_dis' => $id_me,
                'meja' => $meja,
                'kategori' => Cache::remember("order:kategori:$lokasi", 120, function () use ($lokasi) {
                    return DB::table('tb_kategori')
                        ->select(DB::raw('*, SUBSTRING(kategori, 1, 3) AS ket'))
                        ->where('lokasi', $lokasi)
                        ->orderBy('kategori', 'ASC')->groupBy('kategori')
                        ->get();
                }),
                'menu_kategori' => $menusPerKategori,
                'cart' => Cart::content(),
                'id_distri' => DB::table('tb_distribusi')
                    ->where('id_distribusi', $id_me)
                    ->first(),
                'admin' => Auth::user()->nama,
                'absen' => DB::select("SELECT b.nama FROM tb_absen as a left join tb_karyawan as b on a.id_karyawan = b.id_karyawan where a.tgl = '$tgl' and a.id_lokasi = '$id_lokasi' and b.id_status = '2' group by a.id_karyawan  ")
            ];
            Cart::destroy();
            return view('order.index', $data);
        }
    }

    public function get(Request $request)
    {
        [$id_me] = $this->resolveDistribusi($request->id_dis2);
        [, $id] = $this->resolveDistribusi($request->id_dis);
        $id_lokasi = $request->session()->get('id_lokasi');

        $vm = $this->menuOrderLightQuery($id_lokasi, $id)->paginate(12);

        $data = [
            'menu2' => $vm,
            'id_dis' => $id_me,
            'title' => 'Order',
        ];

        return view('order.get', array_merge(['page' => 1], $data))->render();
    }



    public function get_meja(Request $request)
    {
        $id_dis = $request->dis;
        [$id_me] = $this->resolveDistribusi($id_dis);
        $id_lokasi = $request->session()->get('id_lokasi');
        $date = date('Y-m-d');
        $meja = $this->getAvailableMeja($id_lokasi, $id_me, $date);

        foreach ($meja as $m) {
            echo '<option value="' . $m->id_meja . '">' . $m->nm_meja . '</option>';
        }
    }

    public function cari(Request $request)
    {
        [$id_me] = $this->resolveDistribusi($request->dis2);
        [, $id] = $this->resolveDistribusi($request->dis);
        $id_lokasi = $request->session()->get('id_lokasi');
        $vm = $this->menuOrderLightQuery($id_lokasi, $id)
            ->where('b.nm_menu', 'LIKE', '%' . $request->keyword . '%')
            ->get();

        $data = [
            'menu2' => $vm,
            'id_dis' => $id_me,
            'title' => 'Order',
        ];

        return view('order.search', $data)->render();
    }

    public function get_harga(Request $request)
    {
        $id_harga = $request->id_harga;
        $id_dis = $request->id_dis;
        $menu = DB::table('tb_harga as a')
            ->select(
                'b.tipe',
                'a.id_harga',
                'a.id_menu',
                'a.id_distribusi',
                'a.harga',
                'b.nm_menu',
                'c.nm_distribusi',
                'b.image',
                'b.aktif as akv',
                'b.lokasi',
                'b.id_station',
                'b.id_kategori'
            )
            ->leftJoin('tb_menu as b', 'a.id_menu', '=', 'b.id_menu')
            ->leftJoin('tb_distribusi as c', 'a.id_distribusi', '=', 'c.id_distribusi')
            ->where('a.id_harga', $id_harga)
            ->first();
        $data = [
            'menu' => $menu,
            'id_dis' => $id_dis,
        ];
        return view('order.item', $data)->render();
    }

    public function cart(Request $request)
    {
        $id = $request->id_harga2;
        $price = $request->price;
        $nama = $request->name;
        $qty = $request->qty;
        $req = $request->req;
        $id_menu = $request->id_menu;
        $tipe = $request->tipe;
        $id_karyawan = [0 => '1'];
        $dis = $request->dis;
        $potongan = Discount::diskonPeritem($id_menu, $dis);
        $potonganJumlah = $potongan['potongan'];
        $potonganJenis = $potongan['jenis'];
        foreach ($id_karyawan as $id_kr) {
            $kry = DB::table('tb_karyawan_majo')->where('kd_karyawan', $id_kr)->first();
            $karyawan[] = preg_replace("/[^a-zA-Z0-9]/", " ", $kry->nm_karyawan);
        }
        if ($potonganJumlah > 0) {
            $pricePotongan = $potonganJenis == 'rp' ? $price - $potonganJumlah : ($price * $potonganJumlah) / 100;
        } else {
            $pricePotongan = $price;
        }
        Cart::add(
            [
                'id' => $id,
                'name' => $nama,
                'price' => $pricePotongan,
                'qty' => $qty,
                'options' => [
                    'req' => $req,
                    'nm_karyawan' => [$karyawan],
                    'program' => 'resto',
                    'id_menu' => $id_menu,
                    'tipe' => $tipe,
                    'hargaNormal' => $price,
                    'potongan' => $potonganJumlah
                ]
            ]
        );
        return $this->keranjang($request);
    }

    public function destroy_card()
    {
        Cart::destroy();
    }

    public function keranjang(Request $request)
    {
        $id_dis = $request->dis;

        if (empty($id_dis)) {
            $id_me = '1';
        } else {
            $id_me = $id_dis;
        }
        $ongkir = DB::table('tb_ongkir')
            ->select(DB::raw('*, SUM(rupiah) AS rupiah'))
            ->first();

        $data = [
            'cart' => Cart::content(),
            'id_menu' => $request->id_menu,
            'id_distri' => DB::table('tb_distribusi')
                ->where('id_distribusi', $id_me)
                ->first(),
            'batas' => DB::table('tb_batas_ongkir')->first(),
            'onk' => $ongkir,
        ];

        return view('order.keranjang', $data)->render();
    }
    public function delete_order(Request $request)
    {
        $rowId = $request->rowid;
        Cart::remove($rowId);
    }
    public function min_cart(Request $request)
    {
        $rowId = $request->rowid;
        $qty = $request->qty - 1;
        Cart::update($rowId, ['qty' => $qty]);
    }

    public function plus_cart(Request $request)
    {
        $rowId = $request->rowid;
        $qty = $request->qty + 1;
        Cart::update($rowId, ['qty' => $qty]);
    }
    public function payment(Request $request)
    {
        $meja = $request->meja;
        $no_meja = $request->no_meja;
        $orang = $request->orang;
        $id_distribusi = $request->distribusi;
        $now = date('Y-m-d');
        // $warna =  $request->warna;
        // $admin =  $request->admin;

        if (empty($id_distribusi)) {
            $id_me = '1';
        } else {
            $id_me = $id_distribusi;
        }
        $ongkir = DB::table('tb_ongkir')
            ->select(DB::raw('*, SUM(rupiah) AS rupiah'))
            ->first();

        $dis = DB::table('tb_distribusi')
            ->where('id_distribusi', $id_distribusi)
            ->first();

        // AYCE Logic: Calculate based on number of people if AYCE
        $is_ayce = is_ayce_distribusi($dis->nm_distribusi);
        $ayce_harga_total = $is_ayce ? ($orang * ayce_harga_paket()) : 0;

        $data = [
            'cart' => Cart::content(),
            'id_distri' => DB::table('tb_distribusi')
                ->where('id_distribusi', $id_me)
                ->first(),
            'batas' => DB::table('tb_batas_ongkir')->first(),
            'onk' => $ongkir,
            'page' => DB::table('tb_meja')
                ->where('id_meja', $meja)
                ->first(),
            'dis' => $dis,
            'orang' => $orang,
            'no_meja' => $no_meja,
            // 'warna' => $warna,
            // 'admin' => $admin,
            'distribusi' => $id_distribusi,
            'is_ayce' => $is_ayce,
            'ayce_harga_total' => $ayce_harga_total,
            'ayce_harga_paket' => ayce_harga_paket(),
        ];

        return view('order.payment', $data)->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        $id_dis = $request->id_distribusi;
        $loc = $request->session()->get('id_lokasi');

        return DB::transaction(function () use ($request, $id_dis, $loc) {

            // 1. Ambil nomor terakhir dengan PENGUNCIAN (lockForUpdate)
            // Ini memaksa request kedua mengantri sampai request pertama selesai
            $q = DB::table('tb_order')
                ->whereDate('tgl', now())
                ->where('id_lokasi', $loc)
                ->where('id_distribusi', $id_dis)
                ->select(DB::raw('MAX(RIGHT(no_order, 4)) as kd_max'))
                ->lockForUpdate()
                ->first();

            $kd = '0001';
            if ($q && $q->kd_max) {
                $tmp = ((int) $q->kd_max) + 1;
                $kd = sprintf('%04s', $tmp);
            }

            date_default_timezone_set('Asia/Makassar');
            $no_invoice = date('ymd') . $kd;

            $dis = DB::table('tb_distribusi')
                ->where('id_distribusi', $id_dis)
                ->first();
            $kode = strtoupper(substr($dis->nm_distribusi, 0, 2));

            $hasil = "T$kode-$no_invoice";

            // 2. Double Check: Pastikan nomor ini belum benar-benar dipakai
            // Jika ada (karena tabrakan ekstrim), geser ke nomor berikutnya
            while (DB::table('tb_order')->where('no_order', $hasil)->exists() || DB::table('tb_pembelian')->where('no_nota', $hasil)->exists()) {
                $kd = sprintf('%04s', ((int)$kd) + 1);
                $no_invoice = date('ymd') . $kd;
                $hasil = "T$kode-$no_invoice";
            }

            $id_meja_req = $request->id_meja;
            $no_meja = $request->no_meja;
            $lokasi = $loc;
            $orang = $request->orang;
            $date = date('Y-m-d');

            if ($id_meja_req) {
                $last_meja = DB::table('tb_meja')->where('id_meja', $id_meja_req)->first();
            } else {
                $last_meja = DB::selectOne("SELECT *
                FROM tb_meja AS a
                WHERE a.id_meja NOT IN (SELECT b.id_meja from tb_order AS b WHERE b.tgl = '$date' or b.aktif = '1' ) and a.id_lokasi = '$lokasi' and a.id_distribusi = '$id_dis' ORDER BY a.id_meja ASC");
            }

            foreach (Cart::content() as $c) {
                if ($c->options->program == 'resto') {
                    if ($c->qty > 1) {
                        for ($x = 0; $x < $c->qty; $x++) {
                            $data = [
                                'no_order' => $hasil,
                                'id_harga' => $c->id,
                                'qty' => 1,
                                'harga' => $c->price,
                                'id_meja' => $last_meja->id_meja,
                                'id_distribusi' => $id_dis,
                                'id_lokasi' => $lokasi,
                                'tgl' => date('Y-m-d'),
                                'j_mulai' => date('Y-m-d H:i:s'),
                                'aktif' => '1',
                                'orang' => $orang,
                                'no_meja' => $no_meja,
                                'warna' => '',
                                'request' => $c->options->req,
                            ];
                            Orderan::create($data);
                        }
                    } else {
                        $data = [
                            'no_order' => $hasil,
                            'id_harga' => $c->id,
                            'qty' => $c->qty,
                            'harga' => $c->price,
                            'id_meja' => $last_meja->id_meja,
                            'id_distribusi' => $id_dis,
                            'id_lokasi' => $lokasi,
                            'tgl' => date('Y-m-d'),
                            'j_mulai' => date('Y-m-d H:i:s'),
                            'aktif' => '1',
                            'orang' => $orang,
                            'no_meja' => $no_meja,
                            'warna' => '',
                            'request' => $c->options->req,

                        ];
                        Orderan::create($data);
                    }
                } else {
                    $d_produk = DB::table('tb_produk')->where('id_produk', $c->id)->where('id_lokasi', $lokasi)->first();
                    $data = [
                        'id_karyawan'  => '1',
                        'id_produk' => $c->id,
                        'nm_karyawan' => 'kosong',
                        'no_nota' => $hasil,
                        'jumlah' => $c->qty,
                        'harga' => $c->price,
                        'total' => $c->price * $c->qty,
                        'tanggal' => date('Y-m-d'),
                        'tgl_input' => date('Y-m-d H:i:s'),
                        'admin' => Auth::user()->nama,
                        'lokasi' => $lokasi,
                        'no_meja' => $last_meja->id_meja,
                    ];
                    Pembelian::create($data);

                    $data_stok = [
                        'id_produk' => $c->id,
                        'kredit' => $c->qty,
                        'tgl' => date('Y-m-d'),
                        'ket' => 'Penjualan'
                    ];
                    DB::table('tb_stok_produk')->insert($data_stok);

                    $data2 = [
                        'no_order' => $hasil,
                        'qty' => '1',
                        'id_meja' => $last_meja->id_meja,
                        'id_distribusi' => $id_dis,
                        'selesai' => 'selesai',
                        'id_lokasi' => $lokasi,
                        'tgl' => date('Y-m-d'),
                        'j_mulai' => date('Y-m-d H:i:s'),
                        'aktif' => '1',
                        'orang' => $orang,
                        'no_meja' => $no_meja,
                        'warna' => ''
                    ];
                    Orderan::create($data2);
                }
            }

            Cart::destroy();
            return redirect()->route('meja');
        });
    }

    public function get_majo(Request $request)
    {
        $id_lokasi = $request->session()->get('id_lokasi');
        $id_dis = $request->id_dis;
        if ($id_dis == '1') {
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
            
            WHERE a.id_lokasi = '$id_lokasi' and a.id_kategori != '11'");
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
            
            WHERE a.id_lokasi = '$id_lokasi' and a.id_kategori = '11'");
        }

        $data = [
            'produk' => $produk
        ];
        return view('order.majoo', $data);
    }

    public function cari_majo(Request $request)
    {
        $id_lokasi = $request->session()->get('id_lokasi');
        if (empty($request->dis)) {
            $id_dis = '1';
        } else {
            $id_dis = $request->dis;
        }

        if ($id_dis == '1') {


            $vm = DB::select("SELECT a.id_produk, a.komisi,  a.nm_produk, a.sku, a.harga, b.satuan , c.nm_kategori, a.id_lokasi, d.debit, d.kredit,e.kredit_penjualan
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
                
                WHERE a.id_lokasi = '$id_lokasi' and a.id_kategori != '11' and a.nm_produk LIKE '%$request->keyword%'");
        } else {

            $vm = DB::select("SELECT a.id_produk, a.komisi,  a.nm_produk, a.sku, a.harga, b.satuan , c.nm_kategori, a.id_lokasi, d.debit, d.kredit,e.kredit_penjualan
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
            
            WHERE a.id_lokasi = '$id_lokasi' and a.id_kategori = '11' and a.nm_produk LIKE '%$request->keyword%'");
        }



        $data = [
            'produk' => $vm,
            'id_dis' => $id_dis
        ];

        return view('order.search_majo', $data)->render();
    }
    public function get_harga_majoo(Request $request)
    {
        $id_produk = $request->id_produk;
        // $menu = DB::table('tb_produk')
        //     ->join('tb_satuan_majo', 'tb_satuan_majo.id_satuan', '=', 'tb_produk.id_satuan')
        //     ->where('id_produk', $id_produk)
        //     ->first();
        $menu = DB::selectOne("SELECT a.id_produk, a.komisi,  a.nm_produk, a.sku, a.harga, b.satuan , c.nm_kategori, a.id_lokasi, d.debit, d.kredit,e.kredit_penjualan
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
        
        WHERE a.id_produk = '$id_produk'");
        $data = [
            'value' => $menu,
        ];
        return view('order.item_majoo', $data)->render();
    }
    public function get_karyawan(Request $request)
    {


        $karyawan = DB::table('tb_karyawan_majo')->where('posisi', '!=', 'KITCHEN')->get();

        $data = [
            'karyawan' => $karyawan
        ];
        return view('order.get_karyawan', $data);
    }

    public function cart_majoo(Request $r)
    {
        $id = $r->id;
        $jumlah = $r->jumlah;
        $satuan = $r->satuan;
        $catatan = $r->catatan;
        $id_karyawan = $r->kd_karyawan;


        $qty = 0;
        foreach (Cart::content() as $cart) {
            if ($cart->options->type == 'barang') {
                if ($id == $cart->id) {
                    $qty = $cart->qty + $jumlah;
                }
            }
        }
        $detail = DB::selectOne("SELECT a.id_produk, a.komisi,  a.nm_produk, a.sku, a.harga, b.satuan , c.nm_kategori, a.id_lokasi, d.debit, d.kredit,e.kredit_penjualan
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
        
        WHERE a.id_produk = '$id'");


        if ($jumlah > ($detail->debit - ($detail->kredit + $detail->kredit_penjualan))) {
            echo 'kosong';
        } elseif ($qty > ($detail->debit - ($detail->kredit + $detail->kredit_penjualan))) {
            echo 'kosong';
        } else {
            // foreach ($id_karyawan as $id_kr) {
            //     $kry = DB::table('tb_karyawan_majo')->where('kd_karyawan', $id_kr)->first();
            //     $karyawan[] = preg_replace("/[^a-zA-Z0-9]/", " ", $kry->nm_karyawan);
            // }
            $harga = $detail->harga;

            $data = array(
                'id' => $id,
                'qty'     => $r->jumlah,
                'price'   => $harga,
                'name'    => preg_replace("/[^a-zA-Z0-9]/", " ", $detail->nm_produk),
                'options' => [
                    'satuan'  => $satuan,
                    'catatan' => $catatan,
                    // 'id_karyawan'   => $id_karyawan,
                    // 'nm_karyawan'   => [$karyawan],
                    'type'    => 'barang',
                    'program' => 'majo',
                    // 'id_karyawan' => $id_karyawan
                ],
            );
            Cart::add($data);
        }
    }
    public function produk(Request $r)
    {
        $id_user = Auth::user()->id;
        $id_lokasi = $r->session()->get('id_lokasi');
        $data = [
            'title' => 'Produk Majo',
            'produk' => DB::select("SELECT a.id_produk, a.komisi,  a.nm_produk, a.sku, a.harga, b.satuan , c.nm_kategori, a.id_lokasi, d.debit, d.kredit,e.kredit_penjualan
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
            
            WHERE a.id_lokasi = '$id_lokasi'"),
            'kategori' => DB::table('tb_kategori_majo')->get(),
            'satuan' => DB::table('tb_satuan_majo')->get(),

            'logout' => $r->session()->get('logout'),
        ];
        return view("produk.index", $data);
    }
}
