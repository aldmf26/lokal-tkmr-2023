<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Ctt_driver;
use App\Models\Denda;
use App\Models\Discount;
use App\Models\Harga;
use App\Models\Karyawan;
use App\Models\Jumlah_orang;
use App\Models\Kasbon;
use App\Models\Mencuci;
use App\Models\Handicap;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Order2;
use App\Models\Orderan;
use App\Models\Permission;
use App\Models\Tips;
use App\Models\Transaksi;
use App\Models\Users;
use App\Models\Voucher;
use App\Models\Gaji;
use App\Models\Voucher_hapus;
use App\Models\Persentase_kom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DownloadController extends Controller
{
    public function index(Request $request)
    {
        $id_user = Auth::user()->id;
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)
            ->where('id_menu', 7)->first();
        $voucher = Http::get("https://ptagafood.com/api/voucher");
        $dt_voucher = json_decode($voucher, TRUE);


        $date = date('Y-m-d');
        $data = [
            'title' => 'Import',
            'voucher' => $dt_voucher,
            'logout' => $request->session()->get('logout'),

        ];
        return view('download.index', $data);
    }

    public function absen(Request $r)
    {
        $absen = Http::get("https://ptagafood.com/api/importAbsen");
        $dt_absen = json_decode($absen, TRUE);
        DB::table('tb_absen')->whereBetween('tgl', [date('Y-m-1'), date('Y-m-t')])->delete();

        foreach ($dt_absen['absenTkmr'] as $v) {
            $data = [
                'id_absen' => $v['id_absen'],
                'id_karyawan' => $v['id_karyawan'],
                'status' => $v['status'],
                'tgl' => $v['tgl'],
                'id_lokasi' => $v['id_lokasi'],
            ];
            DB::table('tb_absen')->insert($data);
        }
        return redirect()->route('sukses2')->with('sukses', 'Sukses');
    }

    public function voucher(Request $request)
    {
        $voucher = Http::get("https://ptagafood.com/api/voucher_tkmr");
        $dt_voucher = json_decode($voucher, TRUE);
        foreach ($dt_voucher['voucher'] as $v) {
            $v_lokal = Voucher::where('kode', $v['kode'])->first();

            if (!$v_lokal) {
                $data = [
                    'jumlah' => $v['jumlah'],
                    'ket' => $v['ket'],
                    'expired' => $v['expired'],
                    'status' => $v['status'],
                    'lokasi' => $v['lokasi'],
                    'kode' => $v['kode'],
                    'terpakai' => $v['terpakai'],
                    'admin' => $v['admin'],
                ];
                Voucher::create($data);
            } else {
            }
        }
        return redirect()->route('sukses2')->with('sukses', 'Sukses');
    }
    public function user(Request $request)
    {
        $user = Http::get("https://ptagafood.com/api/users");
        $dt_user = json_decode($user, TRUE);
        Users::truncate();

        foreach ($dt_user['users'] as $v) {
            $data = [
                'id' => $v['id'],
                'nama' => $v['nama'],
                'username' => $v['username'],
                'password' => $v['password'],
                'id_posisi' => $v['id_posisi'],
                'jenis' => $v['jenis'],
            ];
            Users::create($data);
        }

        $permision = Http::get("https://ptagafood.com/api/tb_permission");
        $dt_permision = json_decode($permision, TRUE);
        Permission::truncate();
        foreach ($dt_permision['tb_permission'] as $v) {
            $data = [
                'id_user' => $v['id_user'],
                'id_menu' => $v['id_menu'],
                'lokasi' => $v['lokasi'],
            ];
            Permission::create($data);
        }

        return redirect()->route('sukses2')->with('sukses', 'Sukses');
    }

    public function discount(Request $request)
    {
        $id_lokasi = session()->get('id_lokasi') == 1 ? 'tkm' : 'sdb';


        $discount = Http::get("https://ptagafood.com/api/diskon_tkm");
        $dt_discount = json_decode($discount, TRUE);
        Discount::truncate();
        foreach ($dt_discount['diskon'] as $v) {
            $data = [
                'id_discount' => $v['id_discount'],
                'disc' => $v['disc'],
                'ket' => $v['ket'],
                'jenis' => $v['jenis'],
                'dari' => $v['dari'],
                'expired' => $v['expired'],
                'status' => $v['status'],
                'lokasi' => $v['lokasi'],
            ];
            Discount::create($data);
        }

        $discount = Http::get("https://ptagafood.com/api/diskon_peritem");
        $dt_discount = json_decode($discount, TRUE);
        DB::table('tb_discount_peritem')->truncate();
        foreach ($dt_discount[$id_lokasi] as $v) {
            $data = [
                'id_diskon' => $v['id_diskon'],
                'id_lokasi' => $v['id_lokasi'],
                'id_menu' => $v['id_menu'],
                'id_distribusi' => $v['id_distribusi'],
                'jenis' => $v['jenis'],
                'jumlah' => $v['jumlah'],
                'tgl_dari' => $v['tgl_dari'],
                'tgl_sampai' => $v['tgl_sampai'],
                'ket' => $v['ket'],
                'status' => $v['status'],
                'admin' => $v['admin'],
            ];
            DB::table('tb_discount_peritem')->insert($data);
        }
        
        return redirect()->route('sukses2')->with('sukses', 'Sukses');
    }
    public function karyawan(Request $request)
    {
        $karyawan = Http::get("https://ptagafood.com/api/karyawan_tb");
        $dt_karyawan = json_decode($karyawan, TRUE);
        Karyawan::truncate();
        foreach ($dt_karyawan['karyawan'] as $v) {
            $data = [
                'id_karyawan' => $v['id_karyawan'],
                'nama' => $v['nama'],
                'id_status' => $v['id_status'],
                'tgl_masuk' => $v['tgl_masuk'],
                'id_posisi' => $v['id_posisi'],
                'posisi' => $v['posisi'],
                'point' => $v['point'],
            ];
            Karyawan::create($data);
        }
        DB::table('tb_karyawan_majo')->truncate();
        foreach ($dt_karyawan['karyawan_majo'] as $v) {
            $datam = [
                'kd_karyawan' => $v['kd_karyawan'],
                'id_karyawan' => $v['id_karyawan'],
                'nm_karyawan' => $v['nm_karyawan'],
                'posisi' => $v['posisi'],
                'pangkat' => $v['pangkat'],
                'gaji_e' => $v['gaji_e'],
                'gaji_m' => $v['gaji_m'],
                'gaji_sp' => $v['gaji_sp'],
                'gaji_off' => $v['gaji_off'],
                'bonus' => $v['bonus'],
                'bonus_posisi' => $v['bonus_posisi'],
                'tgl_join' => $v['tgl_join'],
                'tkmr' => $v['tkmr'],
                'sdb' => $v['sdb'],
            ];
            DB::table('tb_karyawan_majo')->insert($datam);
        }

        $gaji = Http::get("https://ptagafood.com/api/gaji");
        $dt_gaji = json_decode($gaji, TRUE);
        Gaji::truncate();
        foreach ($dt_gaji['gaji'] as $v) {
            $data = [
                'id_gaji' => $v['id_gaji'],
                'id_karyawan' => $v['id_karyawan'],
                'rp_e' => $v['rp_e'],
                'rp_m' => $v['rp_m'],
                'rp_sp' => $v['rp_sp'],
                'g_bulanan' => $v['g_bulanan'],
            ];
            Gaji::create($data);
        }

        $tb_jumlah_orang = Http::get("https://ptagafood.com/api/tb_jumlah_orang");
        $dt_tb_jumlah_orang = json_decode($tb_jumlah_orang, TRUE);
        Jumlah_orang::truncate();
        foreach ($dt_tb_jumlah_orang['tb_jumlah_orang'] as $v) {
            $data = [
                'id_orang' => $v['id_orang'],
                'ket_karyawan' => $v['ket_karyawan'],
                'jumlah' => $v['jumlah'],
                'id_lokasi' => $v['id_lokasi']
            ];
            Jumlah_orang::create($data);
        }

        $tb_persentase = Http::get("https://ptagafood.com/api/persentase_komisi");
        $dt_tb_persentase = json_decode($tb_persentase, TRUE);
        Persentase_kom::truncate();
        foreach ($dt_tb_persentase['persentase_komisi'] as $v) {
            $data = [
                'id_pesentase' => $v['id_persentase'],
                'nama_persentase' => $v['nama_persentase'],
                'jumlah_persen' => $v['jumlah_persen'],
                'id_lokasi' => $v['id_lokasi']
            ];
            Persentase_kom::create($data);
        }

        $tb_menit = Http::get("https://ptagafood.com/api/tb_menit");
        $dt_menit = json_decode($tb_menit, TRUE);
        DB::table('tb_menit')->truncate();
        foreach ($dt_menit['tb_menit'] as $v) {
            $data = [
                'id_menit' => $v['id_menit'],
                'nm_menit' => $v['nm_menit'],
                'menit' => $v['menit'],
                'id_lokasi' => $v['id_lokasi'],
            ];
            DB::table('tb_menit')->insert($data);
        }

        return redirect()->route('sukses2')->with('sukses', 'Sukses');
    }
    public function tb_majo(Request $r)
    {
        $tb_majo = Http::get("https://ptagafood.com/api/tb_stok_majo");
        $data3 = [];
        // foreach ($tb_majo['stok_masuk_tkm'] as $t) {
        //     array_push($data3, [
        //         'id_stok_produk' => $t['id_stok_produk'],
        //         'kode_stok_produk' => $t['kode_stok_produk'],
        //         'id_lokasi' => $t['id_lokasi'],
        //     ]);
        // }
        // $response = Http::acceptJson()->post('https://ptagafood.com/api/edit_stok_server', $data3);

        DB::table('tb_stok_produk')->truncate();

         foreach ($tb_majo['stok_masuk_tkm'] as $v) {
            $data = [
                'id_stok_produk' => $v['id_stok_produk'],
                'kode_stok_produk' => $v['kode_stok_produk'],
                'id_produk' => $v['id_produk'],
                'stok_program' => $v['stok_program'],
                'harga' => $v['harga'],
                'debit' => $v['debit'],
                'kredit' => $v['kredit'],
                'ttl_stok' => $v['ttl_stok'],
                'tgl' => $v['tgl'],
                'tgl_input' => $v['tgl_input'],
                'ket' => $v['ket'],
                // 'admin' => $v['admin'],
                // 'jenis' => $v['jenis'],
                'status' => $v['status'],
                'id_lokasi' => $v['id_lokasi'],
                'catatan' => $v['catatan'],
                'stok_aktual' => $v['stok_aktual'],
            ];
            DB::table('tb_stok_produk')->insert($data);
        }

        return redirect()->route('sukses2')->with('sukses', 'Sukses');

    }
    public function menu(Request $request)
    {
        $menu = Http::get("https://ptagafood.com/api/menu_tb");
        $dt_menu = json_decode($menu, TRUE);
        Menu::truncate();
        Harga::truncate();
        DB::table('tb_station')->truncate();
        Kategori::truncate();
        Handicap::truncate();
        DB::table('tb_produk')->truncate();
        DB::table('tb_kategori_majo')->truncate();
        DB::table('tb_satuan_majo')->truncate();
        
        foreach ($dt_menu['menu'] as $v) {
            $kode_server = $v['id_menu'];
            $v_local = DB::selectOne("SELECT * from tb_menu as a where a.id_menu = '$kode_server'");
            $data = [
                'id_menu' => $v['id_menu'],
                'id_kategori' => $v['id_kategori'],
                'id_handicap' => $v['id_handicap'],
                'kd_menu' => $v['kd_menu'],
                'nm_menu' => $v['nm_menu'],
                'tipe' => $v['tipe'],
                'id_station' => $v['id_station'],
                'image' => $v['image'],
                'jenis' => $v['jenis'],
                'lokasi' => $v['lokasi'],
                'aktif' => $v['aktif'],
                'tgl_sold' => $v['tgl_sold'],
                'id_handicap' => $v['id_handicap']
            ];
            Menu::create($data);
        }

        foreach ($dt_menu['harga'] as $v) {
            $data = [
                'id_harga' => $v['id_harga'],
                'id_menu' => $v['id_menu'],
                'id_distribusi' => $v['id_distribusi'],
                'harga' => $v['harga'],
            ];
            Harga::create($data);
        }

        foreach ($dt_menu['handicap'] as $v) {
            $data = [
                'id_handicap' => $v['id_handicap'],
                'handicap' => $v['handicap'],
                'point' => $v['point'],
                'ket' => $v['ket'],
                'id_lokasi' => $v['id_lokasi'],
            ];
            Handicap::create($data);
        }

        foreach ($dt_menu['kategori_menu'] as $v) {
            $data = [
                'kd_kategori' => $v['kd_kategori'],
                'kategori' => $v['kategori'],
                'lokasi' => $v['lokasi'],
            ];
            Kategori::create($data);
        }

        foreach ($dt_menu['station'] as $v) {
            $data = [
                'id_station' => $v['id_station'],
                'nm_station' => $v['nm_station'],
                'id_lokasi' => $v['id_lokasi'],
            ];
            DB::table('tb_station')->insert($data);
        }

        foreach ($dt_menu['produk_majo_tkm'] as $v) {
            $data = [
                'id_produk' => $v['id_produk'],
                'id_kategori' => $v['id_kategori'],
                'id_satuan' => $v['id_satuan'],
                'sku' => $v['sku'],
                'nm_produk' => $v['nm_produk'],
                'harga_modal' => $v['harga_modal'],
                'harga' => $v['harga'],
                'stok' => $v['stok'],
                'terjual' => $v['terjual'],
                'foto' => $v['foto'],
                'diskon' => $v['diskon'],
                'komisi' => $v['komisi'],
                'monitoring' => $v['monitoring'],
                'id_lokasi' => $v['id_lokasi'],
            ];
            DB::table('tb_produk')->insert($data);
        }

        foreach($dt_menu['kategori_majo'] as $v) {
            $data = [
                'id_kategori' => $v['id_kategori'],
                'nm_kategori' => $v['nm_kategori'],
            ];
            DB::table('tb_kategori_majo')->insert($data);
        }
        foreach($dt_menu['satuan_majo'] as $v) {
            $data = [
                'id_satuan' => $v['id_satuan'],
                'satuan' => $v['satuan'],
            ];
            DB::table('tb_satuan_majo')->insert($data);
        }

        return redirect()->route('sukses2')->with('sukses', 'Sukses');
    }

    public function tb_voucher_hapus()
    {
        $tb_voucher_hapus = Http::get("https://ptagafood.com/api/tb_voucher_hapus");
        $dt_voucher_hapus = json_decode($tb_voucher_hapus, TRUE);

        foreach ($dt_voucher_hapus['tb_voucher_hapus'] as $v) {
            $v_lokal = Voucher_hapus::where('id_voucher', $v['id_voucher'])->first();
            if (!$v_lokal) {
                $data = [
                    'id_voucher' => $v['id_voucher'],
                    'kode_voucher' => $v['kode_voucher'],
                    'status' => $v['status'],
                ];
                Voucher_hapus::create($data);
            } else {
            }
        }
        return redirect()->route('sukses2')->with('sukses', 'Sukses');
    }


    public function sukses(Request $request)
    {
        $id_user = Auth::user()->id;
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)
            ->where('id_menu', 7)->first();
        if (empty($id_menu)) {
            return back();
        } else {

            $data = [
                'title' => 'Import',
                'logout' => $request->session()->get('logout'),
            ];
            return view('api_import.index2', $data);
        }
    }
}
