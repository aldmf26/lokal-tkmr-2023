<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Koki;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddKokiController extends Controller
{
    public function index(Request $request)
    {
        $id_user = Auth::user()->id;
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)
            ->where('id_menu', 2)->first(); // Use existing permission ID 2 for Bar task
        
        if (empty($id_menu)) {
            return back();
        } else {
            $lokasi = $request->session()->get('id_lokasi');
            $id = $request->id ?? '1';

            $data = [
                'title' => 'Tugas Bar',
                'logout' => $request->session()->get('logout'),
                'id' => $id,
                'id_lokasi' => $lokasi,
                'orderan' => DB::select("SELECT COUNT(id_order) as jml_order FROM tb_order WHERE id_lokasi = '$lokasi' AND selesai = 'dimasak' AND void = 0"),
            ];
            return view('addKoki.addKoki', $data);
        }
    }

    public function get_bar(Request $request)
    {
        $limit = $request->limit ?? '3';
        $meja_search = $request->meja ?? '';

        $whereMeja = '';
        if ($meja_search != '') {
            $whereMeja = " AND a.no_meja LIKE '%$meja_search%' ";
        }
        $lokasi = $request->session()->get('id_lokasi');
        $tgl = date('Y-m-d');
        $cat_bev = implode(',', cat_beverages());
        
        // Priority: Only show tables that have beverages waiting to be cooked
        $meja = DB::select("SELECT a.id_meja, a.no_meja as nm_meja, a.no_order, RIGHT(a.no_order,2) AS kd, b.nm_distribusi, a.selesai, a.id_distribusi
        FROM tb_order AS a
        LEFT JOIN tb_distribusi AS b ON b.id_distribusi = a.id_distribusi
        JOIN tb_harga h ON a.id_harga = h.id_harga
        JOIN tb_menu m_table ON h.id_menu = m_table.id_menu
        WHERE a.aktif = '1' AND a.id_lokasi = '$lokasi' AND a.selesai = 'dimasak' AND a.void = 0 AND m_table.id_kategori IN ($cat_bev) $whereMeja
        GROUP BY a.no_order 
        ORDER BY MIN(a.j_mulai) ASC
        LIMIT $limit;
        ");

        $no_orders = [];
        foreach ($meja as $m) {
            $no_orders[] = $m->no_order;
        }

        $menus = [];
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
                where a.id_lokasi = '$lokasi' and a.no_order IN ($order_list) and a.selesai = 'dimasak' and a.aktif = '1' and a.void = 0 AND m_table.id_kategori IN ($cat_bev)
                ORDER BY a.id_order");

            foreach ($all_menus as $menu) {
                $menus[$menu->no_order][] = $menu;
            }
        }

        $setMenit = DB::table('tb_menit')->where('id_lokasi', $lokasi)->first();
        $tb_koki = DB::table('tb_koki')->join('tb_karyawan', 'tb_karyawan.id_karyawan', '=', 'tb_koki.id_karyawan')->where('tb_koki.tgl', $tgl)->where('tb_koki.id_lokasi', $lokasi)->get();

        $data = [
            'meja' => $meja,
            'menus_all' => $menus,
            'majos_all' => [], // Bar usually doesn't have majos items
            'tb_koki' => $tb_koki,
            'lokasi' => $lokasi,
            'setMenit' => $setMenit,
        ];
        return view('addKoki.bar_tugas', $data);
    }

    public function absenKoki(Request $request)
    {
        // Keeping original methods in case they are needed for other staff
        $id_karyawan = $request->id_karyawan;
        for ($i=0; $i < count($id_karyawan); $i++) { 
            $data = [
                'id_karyawan' => $id_karyawan[$i],
                'tgl' => date('Y-m-d'),
                'status' => 1,
                'id_lokasi' => $request->session()->get('id_lokasi'),
            ];
            Koki::create($data);
        }
        return redirect()->route('addKoki');
    }

    public function delAbsKoki(Request $request)
    {
        Koki::where('id_koki', $request->id_koki)->delete();
        return redirect()->route('addKoki');
    }
}
