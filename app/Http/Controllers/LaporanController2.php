<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $id_user = Auth::user()->id;
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)->where('id_menu', 28)->first();
        if (empty($id_menu)) {
            return back();
        } else {
            $data = [
                'title' => 'Laporan',
                'logout' => $request->session()->get('logout'),
            ];

            return view('laporan.laporan', $data);
        }
    }

    public function summary(Request $request)
    {
        // $laporan = DB::select("")->result();
        $loc = $request->session()->get('id_lokasi');
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;

        $total_gojek = DB::selectOne("SELECT SUM(tb_transaksi.total_orderan - discount - voucher) as total FROM `tb_transaksi`
        LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
        WHERE tb_transaksi.id_lokasi = '$loc' AND dt_order.id_distribusi = '2' AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");

        $total_not_gojek = DB::selectOne("SELECT SUM(tb_transaksi.total_orderan - discount - voucher) as total FROM `tb_transaksi`
        LEFT JOIN(SELECT tb_order2.no_order2 as no_order, tb_order2.id_distribusi as id_distribusi FROM tb_order2 GROUP BY tb_order2.no_order2) dt_order ON tb_transaksi.no_order = dt_order.no_order
        WHERE tb_transaksi.id_lokasi = '$loc' AND dt_order.id_distribusi != '2' AND tb_transaksi.tgl_transaksi >= '$tgl1' AND tb_transaksi.tgl_transaksi <= '$tgl2'");

        $jml_telat = DB::selectOne("SELECT SUM(qty) AS jml_telat FROM view_koki_masak WHERE tgl >= '$tgl1' AND tgl <= '$tgl2' AND id_lokasi = $loc AND menit_bagi > 25");
        $jml_ontime = DB::selectOne("SELECT SUM(qty) AS jml_ontime FROM view_koki_masak WHERE tgl >= '$tgl1' AND tgl <= '$tgl2' AND id_lokasi = $loc AND menit_bagi <= 25");

        $majo = DB::selectOne("SELECT SUM(a.bayar) AS bayar_majo
        FROM tb_invoice AS a
        WHERE a.tgl_jam BETWEEN '$tgl1' AND '$tgl2' and a.id_distribusi = '1'");
        $majo_gojek = DB::selectOne("SELECT SUM(a.bayar) AS bayar_majo
        FROM tb_invoice AS a
        WHERE a.tgl_jam BETWEEN '$tgl1' AND '$tgl2' and a.id_distribusi = '2'");

        $data = [
            'title'    => 'Summary',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'transaksi' => DB::selectOne("SELECT COUNT(a.no_order) AS ttl_invoice, SUM(a.discount) as discount, SUM(a.voucher) as voucher, sum(a.round) as rounding, a.id_lokasi, 
            SUM(a.total_orderan) AS rp, d.unit, a.no_order, sum(a.dp) as dp, sum(a.gosen) as gosend, sum(a.service) as ser, sum(a.tax) as tax,f.qty_void, f.void,
            SUM(a.cash) as cash, SUM(a.d_bca) as d_bca, SUM(a.k_bca) as k_bca, SUM(a.d_mandiri) as d_mandiri, SUM(a.k_mandiri) as k_mandiri, SUM(total_bayar) as total_bayar
            
            FROM tb_transaksi AS a
            
            LEFT JOIN(
            SELECT SUM(b.qty) AS unit , b.no_order, b.id_lokasi, b.id_distribusi
            FROM tb_order AS b
            WHERE b.tgl BETWEEN '$tgl1' AND '$tgl2' AND b.id_lokasi = '$loc' AND b.void = 0
            GROUP BY b.id_lokasi
            )AS d ON d.id_lokasi = a.id_lokasi
            
            LEFT JOIN(
            SELECT SUM(e.void) AS void , COUNT(e.void) AS qty_void, e.no_order, e.id_lokasi
            FROM tb_order AS e
            WHERE e.tgl BETWEEN '$tgl1' AND '$tgl2' AND e.id_lokasi = '$loc' AND e.void != '0'
            GROUP BY e.id_lokasi
            )AS f ON f.id_lokasi = a.id_lokasi
            
            
            where a.tgl_transaksi BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc' 
            GROUP BY a.id_lokasi"),

            'kategori' => DB::select("SELECT b.nm_menu, c.kategori , sum(e.harga2) as harga, sum(a.qty) AS qty
FROM tb_order AS a 
LEFT JOIN view_menu2 AS b ON b.id_harga = a.id_harga
left join tb_kategori as c on c.kd_kategori = b.id_kategori

left join(select d.id_harga, d.id_order, (d.harga * d.qty) as harga2 from tb_order as d 
WHERE d.tgl BETWEEN '$tgl1' AND '$tgl2' and d.id_lokasi = '$loc' and d.id_distribusi = '1'
group by d.id_order) as e on e.id_order = a.id_order           
           
WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc' and a.id_distribusi = '1' 
 GROUP BY b.id_kategori"),

            'gojek' => DB::select("SELECT b.nm_menu, c.kategori, sum(e.harga2) as harga, sum(a.qty) AS qty
            FROM tb_order AS a 
            LEFT JOIN view_menu2 AS b ON b.id_harga = a.id_harga
            left join tb_kategori as c on c.kd_kategori = b.id_kategori
            left join(select d.id_harga, d.id_order, (d.harga * d.qty) as harga2 from tb_order as d 
WHERE d.tgl BETWEEN '$tgl1' AND '$tgl2' and d.id_lokasi = '$loc' and d.id_distribusi = '2'
group by d.id_order) as e on e.id_order = a.id_order  
            WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc' and a.id_distribusi = '2'
            GROUP BY b.id_kategori"),

            'total_gojek' => $total_gojek,
            'total_not_gojek' => $total_not_gojek,
            'jml_telat' => $jml_telat,
            'lokasi' => $loc,
            'jml_ontime' => $jml_ontime,
            'majo' => $majo,
            'majo_gojek' => $majo_gojek,
            'void' => DB::select("SELECT c.kategori,b.nm_menu,sum(a.void) as void, sum(a.harga) as harga FROM `tb_order` as a 
            LEFT JOIN view_menu2 as b on a.id_harga = b.id_harga
            left join tb_kategori as c on b.id_kategori = c.kd_kategori
            WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' AND a.void = 1 AND id_lokasi = '$loc'
            GROUP BY c.kd_kategori"),
        ];
        return view('laporan.summary', $data);
    }

    public function item(Request $request)
    {
        // $laporan = $this->db->query("")->result();
        $loc = $request->session()->get('id_lokasi');
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;
        $data = [
            'title'    => 'Summary',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,

            'kategori' => DB::select("SELECT b.nm_menu, a.harga, sum(a.qty) AS qty
            FROM tb_order AS a 
            LEFT JOIN view_menu AS b ON b.id_harga = a.id_harga
            WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc'
            GROUP BY a.id_harga")
        ];
        return view('laporan.item', $data);
    }

    public function export_item(Request $request)
    {
        $loc = $request->session()->get('id_lokasi');
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;

        $dt_item = DB::select("SELECT b.nm_menu, a.harga, sum(a.qty) AS qty
        FROM tb_order AS a 
        LEFT JOIN view_menu AS b ON b.id_harga = a.id_harga
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_lokasi = '$loc'
        GROUP BY a.id_harga");

        $spreadsheet = new Spreadsheet;

        $spreadsheet->setActiveSheetIndex(0);

        $spreadsheet->getActiveSheet()->setCellValue('A1', '#');
        $spreadsheet->getActiveSheet()->setCellValue('B1', 'Nama Menu');
        $spreadsheet->getActiveSheet()->setCellValue('C1', 'Qty');
        $spreadsheet->getActiveSheet()->setCellValue('D1', 'Subtotal');


        $style = array(
            'font' => array(
                'size' => 9
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ),
        );

        $spreadsheet->getActiveSheet()->getStyle('A1:D1')->applyFromArray($style);


        $spreadsheet->getActiveSheet()->getStyle('A1:D1')->getAlignment()->setWrapText(true);


        $kolom = 2;
        $no = 1;
        foreach ($dt_item as $d) {
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->setCellValue('A' . $kolom, $no++);
            $spreadsheet->getActiveSheet()->setCellValue('B' . $kolom, $d->nm_menu);
            $spreadsheet->getActiveSheet()->setCellValue('C' . $kolom, $d->qty);
            $spreadsheet->getActiveSheet()->setCellValue('D' . $kolom, $d->qty * $d->harga);
            $kolom++;
        }

        $border_collom = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ),
            )
        );

        $batas = $kolom - 1;
        $spreadsheet->getActiveSheet()->getStyle('A1:D' . $batas)->applyFromArray($style);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan Per Item.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function get_telat(Request $request)
    {
    }

    public function get_ontime(Request $request)
    {
    }

    public function server(Request $request)
    {
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;

        $server = DB::select("SELECT a.nama, b.komisi,sum(d.qty_m) AS M, sum(d.qty_e) AS E, sum(d.qty_sp) AS Sp
        FROM tb_karyawan AS a 

        LEFT JOIN (
        SELECT c.id_karyawan,  c.status,
        if(c.status = 'M', COUNT(c.status), 0) AS qty_m,
        if(c.status = 'E', COUNT(c.status), 0) AS qty_e,
        if(c.status = 'SP', COUNT(c.status), 0) AS qty_sp
        FROM tb_absen AS c 
        WHERE c.tgl BETWEEN '$tgl1' AND '$tgl2'
        GROUP BY c.id_karyawan, c.status
        ) AS d ON d.id_karyawan = a.id_karyawan
        
        LEFT JOIN (
            SELECT a.admin, SUM(if(a.hrg - a.voucher < 0 ,0, a.hrg - a.voucher)) AS komisi
            FROM view_summary_server AS a
            WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'
            GROUP BY a.admin
        ) AS b ON b.admin = a.nama

        WHERE a.id_status = '2'
        GROUP BY a.id_karyawan
        order by b.komisi DESC");
        $data = [
            'server' => $server,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2
        ];

        return view('laporan.server', $data);
    }

    public function get_order_server(Request $request)
    {
        $tgl1 = $request->tgl1;
        $tgl2 = $request->tgl2;
        $admin = $request->admin;

        $server = DB::select("SELECT a.admin,  d.nm_meja, c.nm_menu , a.harga
        FROM tb_order AS a
        LEFT JOIN tb_harga AS b ON b.id_harga = a.id_harga
        LEFT JOIN tb_menu AS c ON c.id_menu = b.id_menu
        LEFT JOIN tb_meja AS d ON d.id_meja = a.id_meja
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2' AND a.admin = '$admin' and a.id_distribusi !='2'");

        $data = [
            'server' => $server,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'admin' => $admin,
        ];

        return view('laporan.server_detail', $data);
    }

    
}
