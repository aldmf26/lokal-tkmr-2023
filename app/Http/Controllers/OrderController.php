<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Distribusi;
use App\Models\Order;
use App\Models\Orderan;
use App\Models\Invoice;
use App\Models\Limit;
use App\Models\SoldOut;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $id_user = Auth::user()->id;
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)
            ->where('id_menu', 25)->first();
        if (empty($id_menu)) {

            return back();
        } else {
            $id_dis = $request->dis;

            if (empty($id_dis)) {
                $id_me = '1';
            } else {
                $id_me = $id_dis;
            }
            if (empty($id_dis)) {
                $id = '1';
            } elseif ($id_dis != '2') {
                $id = '1';
            } else {
                $id = '2';
            }
            $id_lokasi = $request->session()->get('id_lokasi');
            $date = date('Y-m-d');
            if ($id_lokasi == '1') {
                $lokasi = 'TAKEMORI';
            } else {
                $lokasi = 'SOONDOBU';
            }
            $tgl = date('Y-m-d');

            $meja = DB::select("SELECT *
                FROM tb_meja AS a
                WHERE a.id_meja NOT IN (SELECT b.id_meja from tb_order AS b WHERE b.tgl = '$date' or b.aktif = '1' ) and a.id_lokasi = '$id_lokasi' and a.id_distribusi = '$id_me'");
            $data = [
                'title' => 'Order',
                'logout' => $request->session()->get('logout'),
                'distribusi' => Distribusi::all(),
                'id' => $id,
                'id_dis' => $id_me,
                'meja' => $meja,
                'kategori' => DB::table('tb_kategori')
                    ->select(DB::raw('*, SUBSTRING(kategori, 1, 3) AS ket'))
                    ->where('lokasi', $lokasi)
                    ->orderBy('kategori', 'ASC')->groupBy('kategori')
                    ->get(),
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
        if (empty($request->id_dis2)) {
            $id_me = '1';
        } else {
            $id_me = $request->id_dis2;
        }
        if (empty($request->id_dis)) {
            $id = '1';
        } elseif ($request->id_dis != '2') {
            $id = '1';
        } else {
            $id = '2';
        }
        $tgl = date('Y-m-d');
        $id_lokasi = $request->session()->get('id_lokasi');



        // if($ids) {
        //     $notin = "->whereNotIn('view_menu.id_menu', $ids)";
        // } else {
        //     $notin = "";
        // }


        $ids = [];
        $sold_out = SoldOut::where('tgl', $tgl)->get();
        foreach ($sold_out as $s) {
            $ids[] = $s->id_menu;
        }

        $idl = [];
        $limit = DB::select("SELECT tb_menu.id_menu as id_menu FROM tb_menu 
        LEFT JOIN(SELECT SUM(qty) as jml_jual, tb_harga.id_menu FROM tb_order LEFT JOIN tb_harga ON tb_order.id_harga = tb_harga.id_harga WHERE tb_order.id_lokasi = $id_lokasi AND tb_order.tgl = '$tgl' AND tb_order.void = 0 GROUP BY tb_harga.id_menu) dt_order ON tb_menu.id_menu = dt_order.id_menu
        LEFT JOIN(SELECT id_menu,batas_limit FROM tb_limit WHERE tgl = '$tgl' AND id_lokasi = $id_lokasi GROUP BY id_menu)dt_limit ON tb_menu.id_menu = dt_limit.id_menu
        WHERE lokasi = $id_lokasi AND dt_order.jml_jual >= dt_limit.batas_limit");
        foreach ($limit as $l) {
            $idl[] = $l->id_menu;
        }

        $vm = DB::table('view_menu')
            ->where('lokasi', $id_lokasi)
            ->where('id_distribusi', $id)
            ->where('akv', 'on')
            ->whereNotIn('view_menu.id_menu', $ids)
            ->whereNotIn('view_menu.id_menu', $idl)
            ->paginate(12);

        $data = [
            'menu2' => $vm,

            'menu21' => DB::select("SELECT a.id_harga, a.id_distribusi, a.id_menu, b.nm_menu, c.nm_distribusi, a.harga,b.image
                FROM tb_harga AS a 
                LEFT JOIN tb_menu AS b ON b.id_menu = a.id_menu 
                LEFT JOIN tb_distribusi AS c ON c.id_distribusi = a.id_distribusi
                where a.id_distribusi = '$id' AND b.lokasi ='$id_lokasi' and b.aktif = 'on' AND b.id_menu NOT IN (SELECT tb_sold_out.id_menu FROM tb_sold_out WHERE tb_sold_out.tgl = '$tgl')
                GROUP BY a.id_harga"),

            'id_dis' => $id_me,
            'title' => 'Order',
        ];

        return view('order.get', ['page' => 1], $data)->render();
    }



    public function get_meja(Request $request)
    {
        $id_dis = $request->dis;

        if (empty($id_dis)) {
            $id_me = '1';
        } else {
            $id_me = $id_dis;
        }
        if (empty($id_dis)) {
            $id = '1';
        } elseif ($id_dis != '2') {
            $id = '1';
        } else {
            $id = '2';
        }
        $id_lokasi = $request->session()->get('id_lokasi');
        $date = date('Y-m-d');

        $meja = DB::select(
            DB::raw("SELECT *
        FROM tb_meja AS a
        WHERE a.id_meja NOT IN (SELECT b.id_meja from tb_order AS b WHERE b.tgl = '$date' or b.aktif = '1' ) and a.id_lokasi = '$id_lokasi' and a.id_distribusi = '$id_me'"),
        );

        foreach ($meja as $m) {
            echo '<option value="' . $m->id_meja . '">' . $m->nm_meja . '</option>';
        }
    }

    public function cari(Request $request)
    {
        if (empty($request->dis2)) {
            $id_me = '1';
        } else {
            $id_me = $request->dis2;
        }
        if (empty($request->dis)) {
            $id = '1';
        } elseif ($request->dis != '2') {
            $id = '1';
        } else {
            $id = '2';
        }
        $tgl = date('Y-m-d');
        $id_lokasi = $request->session()->get('id_lokasi');
        // soldout
        $ids = [];
        $sold_out = SoldOut::where('tgl', $tgl)->get();
        foreach ($sold_out as $s) {
            $ids[] = $s->id_menu;
        }

        // limit
        $idl = [];
        $limit = DB::select("SELECT tb_menu.id_menu as id_menu FROM tb_menu 
        LEFT JOIN(SELECT SUM(qty) as jml_jual, tb_harga.id_menu FROM tb_order LEFT JOIN tb_harga ON tb_order.id_harga = tb_harga.id_harga WHERE tb_order.id_lokasi = $id_lokasi AND tb_order.tgl = '$tgl' AND tb_order.void = 0 GROUP BY tb_harga.id_menu) dt_order ON tb_menu.id_menu = dt_order.id_menu
        LEFT JOIN(SELECT id_menu,batas_limit FROM tb_limit WHERE tgl = '$tgl' AND id_lokasi = $id_lokasi GROUP BY id_menu)dt_limit ON tb_menu.id_menu = dt_limit.id_menu
        WHERE lokasi = $id_lokasi AND dt_order.jml_jual >= dt_limit.batas_limit");
        foreach ($limit as $l) {
            $idl[] = $l->id_menu;
        }

        $vm = DB::table('view_menu')
            ->where('lokasi', $id_lokasi)
            ->where('id_distribusi', $id)
            ->where('nm_menu', 'LIKE', '%' . $request->keyword . '%')
            ->where('akv', 'on')
            ->whereNotIn('view_menu.id_menu', $ids)
            ->whereNotIn('view_menu.id_menu', $idl)
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
        $menu = DB::table('view_menu')
            ->where('id_harga', $id_harga)
            ->first();
        $data = [
            'menu' => $menu,
            'id_dis' => $id_dis,
        ];
        return view('order.item', $data)->render();
    }

    public function cart(Request $request)
    {
        $id_lokasi = $request->session()->get('id_lokasi');
        $date = date('Y-m-d');
        $id = $request->id_harga2;
        $price = $request->price;
        $nama = $request->name;
        $qty = $request->qty;
        $req = $request->req;
        $id_menu = $request->id_menu;
        $tipe = $request->tipe;
        $id_karyawan = [0 => '1'];
        $dis = $request->dis;
        $potongan = Discount::diskonPeritem($id_menu, $dis, $price);
        $potonganJumlah = $potongan['potongan'];
        $potonganJenis = $potongan['jenis'];
        foreach ($id_karyawan as $id_kr) {
            $kry = DB::table('tb_karyawan_majo')->where('kd_karyawan', $id_kr)->first();
            $karyawan[] = preg_replace("/[^a-zA-Z0-9]/", " ", $kry->nm_karyawan);
        }


        $detail = DB::selectOne("SELECT a.id_harga, a.id_menu, b.nm_menu, c.nm_distribusi, a.harga,b.image
        FROM tb_harga AS a 
        LEFT JOIN tb_menu AS b ON b.id_menu = a.id_menu 
        LEFT JOIN tb_distribusi AS c ON c.id_distribusi = a.id_distribusi
        where a.id_harga = '$id'
        GROUP BY a.id_harga");


        $dt_limit = DB::selectOne("SELECT dt_order.jml_jual as jml_jual, dt_limit.batas_limit as batas_limit  FROM tb_menu 
        LEFT JOIN(SELECT SUM(qty) as jml_jual, tb_harga.id_menu FROM tb_order LEFT JOIN tb_harga ON tb_order.id_harga = tb_harga.id_harga WHERE tb_order.id_lokasi = $id_lokasi AND tb_order.tgl = '$date' AND tb_order.void = 0 GROUP BY tb_harga.id_menu) dt_order ON tb_menu.id_menu = dt_order.id_menu
        LEFT JOIN(SELECT id_menu,batas_limit FROM tb_limit WHERE tgl = '$date' AND id_lokasi = $id_lokasi GROUP BY id_menu)dt_limit ON tb_menu.id_menu = dt_limit.id_menu
        WHERE lokasi = $id_lokasi AND tb_menu.id_menu = $detail->id_menu");
        if ($dt_limit->batas_limit > 0 && $dt_limit->jml_jual + $qty > $dt_limit->batas_limit) {
            echo $dt_limit->batas_limit - $dt_limit->jml_jual;
        } else {
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
            echo 'berhasil';
        }
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
        $orang = $request->orang;
        $id_distribusi = $request->distribusi;
        $now = date('Y-m-d');
        $warna =  $request->warna;
        $admin =  $request->admin;

        if (empty($id_distribusi)) {
            $id_me = '1';
        } else {
            $id_me = $id_distribusi;
        }
        $ongkir = DB::table('tb_ongkir')
            ->select(DB::raw('*, SUM(rupiah) AS rupiah'))
            ->first();

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
            'dis' => DB::table('tb_distribusi')
                ->where('id_distribusi', $id_distribusi)
                ->first(),
            'orang' => $orang,
            'warna' => $warna,
            'admin' => $admin,
            'distribusi' => $id_distribusi,
        ];

        return view('order.payment', $data)->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $id_dis = $request->id_distribusi;
        $loc = $request->session()->get('id_lokasi');
        $q = DB::select(
            DB::raw("SELECT MAX(RIGHT(a.no_order,4)) AS kd_max FROM tb_order AS a
        WHERE DATE(a.tgl)=CURDATE() AND a.id_lokasi = '$loc' AND a.id_distribusi = '$id_dis'"),
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
            ->where('id_distribusi', $id_dis)
            ->first();
        $kode = strtoupper(substr($dis->nm_distribusi, 0, 2));
        $loc = $loc;
        if ($loc == '1') {
            $hasil = "T$kode-$no_invoice";
        } else {
            $hasil = "S$kode-$no_invoice";
        }
        $data = [
            'no_invoice' => $hasil,
            'tanggal' => date('Y-m-d'),
        ];
        Invoice::create($data);

        // dd($request->req);
        $meja = $request->id_meja;
        $id_harga = $request->id_harga;
        $qty = $request->qty;
        $price = $request->harga;
        $ongkir = $request->ongkir;
        $orang = $request->orang;
        $lokasi = $request->session()->get('id_lokasi');
        $pesan = $request->req;
        $warna = $request->warna;
        $admin = $request->admin;

        $date = date('Y-m-d');
        $last_meja = DB::selectOne("SELECT *
        FROM tb_meja AS a
        WHERE a.id_meja NOT IN (SELECT b.id_meja from tb_order AS b WHERE b.tgl = '$date' or b.aktif = '1' ) and a.id_lokasi = '$lokasi' and a.id_distribusi = '$id_dis' ORDER BY a.id_meja ASC");

        $total = 0;
        foreach (Cart::content() as $c) {
            if ($c->options->program == 'majo') {
                $total += $c->price * $c->qty;
            } else {
                # code...
            }

            if ($c->options->program == 'resto') {

                if ($c->qty > 1) {
                    for ($x = 0; $x < $c->qty; $x++) {

                        $data2 = [
                            'no_order' => $hasil,
                            'id_harga' => $c->id,
                            'qty' => 1,
                            'harga' => $c->price,
                            'request' => $c->options->req,
                            'id_meja' => $last_meja->id_meja,
                            'id_distribusi' => $id_dis,
                            'selesai' => 'dimasak',
                            'id_lokasi' => $lokasi,
                            'tgl' => date('Y-m-d'),
                            'admin' => $admin,
                            'j_mulai' => date('Y-m-d H:i:s'),
                            'aktif' => '1',
                            'ongkir' => $ongkir,
                            'orang' => $orang,
                            'warna' => $warna
                        ];
                        Orderan::create($data2);
                    }
                } else {

                    $data2 = [
                        'no_order' => $hasil,
                        'id_harga' => $c->id,
                        'qty' => $c->qty,
                        'harga' => $c->price,
                        'request' => $c->options->req,
                        'id_meja' => $last_meja->id_meja,
                        'id_distribusi' => $id_dis,
                        'selesai' => 'dimasak',
                        'id_lokasi' => $lokasi,
                        'tgl' => date('Y-m-d'),
                        'admin' => $admin,
                        'j_mulai' => date('Y-m-d H:i:s'),
                        'aktif' => '1',
                        'ongkir' => $ongkir,
                        'orang' => $orang,
                        'warna' => $warna
                    ];
                    Orderan::create($data2);
                }
            } else {
                $nm_karyawan = '';
                $length = count($c->options->nm_karyawan[0]);
                $number = 1;
                foreach ($c->options->nm_karyawan as $key => $karyawan) {
                    foreach ($karyawan as $kar) {
                        $nm_karyawan .= $kar;
                        if ($number !== $length) {
                            $nm_karyawan .= ', ';
                        }
                        $number++;
                    }
                }
                $d_produk = DB::table('tb_produk')->where('id_produk', $c->id)->where('id_lokasi', $lokasi)->first();
                // dd(Auth::user()->nama);
                $data = [
                    'id_karyawan'  => $c->options->id_karyawan[0],
                    'id_produk' => $c->id,
                    'nm_karyawan' => $nm_karyawan,
                    'no_nota' => $hasil,
                    'jumlah' => $c->qty,
                    'harga' => $c->price,
                    'total' => $c->price * $c->qty,
                    'tanggal' => date('Y-m-d'),
                    'tgl_input' => date('Y-m-d H:i:s'),
                    'admin' => $admin,
                    'lokasi' => $lokasi,
                    'no_meja' => $last_meja->id_meja,
                    'jml_komisi' => $d_produk->komisi
                ];
                $dataInsert = Pembelian::create($data);

                $id_pembelian = $dataInsert->id;



                $stok_baru = [
                    'stok' => $d_produk->stok -  $c->qty
                ];

                DB::table('tb_produk')->where('id_produk', $c->id)->update($stok_baru);

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
                    'warna' => $warna
                ];
                Orderan::create($data2);




                if ($c->price > 0) {
                    $subharga = $c->qty * $c->price;
                } else {
                    $subharga = 0;
                }
                $komisi1 = $subharga * $d_produk->komisi / 100;
                $komisi = $komisi1 / count($c->options->id_karyawan);
                foreach ($c->options->id_karyawan as $id_karyawan) {
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


        Cart::destroy();
        return redirect()->route('meja');
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

        if (empty($id_karyawan)) {
            echo "null";
        } else {
            if ($jumlah > ($detail->debit - ($detail->kredit + $detail->kredit_penjualan))) {
                echo 'kosong';
            } elseif ($qty > ($detail->debit - ($detail->kredit + $detail->kredit_penjualan))) {
                echo 'kosong';
            } else {
                foreach ($id_karyawan as $id_kr) {
                    $kry = DB::table('tb_karyawan_majo')->where('kd_karyawan', $id_kr)->first();
                    $karyawan[] = preg_replace("/[^a-zA-Z0-9]/", " ", $kry->nm_karyawan);
                }
                $harga = $detail->harga;

                $data = array(
                    'id' => $id,
                    'qty'     => $r->jumlah,
                    'price'   => $harga,
                    'name'    => preg_replace("/[^a-zA-Z0-9]/", " ", $detail->nm_produk),
                    'options' => [
                        'satuan'  => $satuan,
                        'catatan' => $catatan,
                        'id_karyawan'   => $id_karyawan,
                        'nm_karyawan'   => [$karyawan],
                        'type'    => 'barang',
                        'program' => 'majo',
                        'id_karyawan' => $id_karyawan
                    ],
                );
                Cart::add($data);
            }
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
