<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Http;

class AbsenController extends Controller
{
    public function index(Request $request)
    {
        $agent = new Agent();
        $id_user = Auth::user()->id;
        $id_menu = DB::table('tb_permission')->select('id_menu')->where('id_user', $id_user)
            ->where('id_menu', 2)->first();
        if (empty($id_menu)) {
            return back();
        } else {
            $tg = $request->tgl;
            if (empty($tg)) {
                $tgl = date('Y-m-d');
            } else {
                $tgl = $tg;
            }
            $data = [
                'title' => 'Absen',
                'logout' => $request->session()->get('logout'),
                'karyawan' => Karyawan::paginate(10),
                'tgl' => $tgl
            ];
            if ($agent->isMobile()) {

                return view('absenMobile.absen', $data);
            } else {
                return view('absen.absen', $data);
            }
        }
    }

    public function tabelAbsenM(Request $request)
    {
        $tg = $request->tgl;
        if (empty($tg)) {
            $tgl = date('Y-m-d');
        } else {
            $tgl = $tg;
        }
        $data = [
            'tb_karyawan' => Karyawan::all(),
            'tgl' => $tgl
        ];
        return view('absenMobile.tabelAbsen', $data);
    }

    public function addAbsenM(Request $request)
    {
        $status = $request->ket;
        $id_karyawan = $request->id_karyawan;
        $tgl = $request->tgl;
        $ada = Absen::where([
            ['id_karyawan', $id_karyawan],
            ['status', $status],
            ['tgl', $tgl],
        ])->first();

        if($ada) {
            return true;
        } else {
           $data =  [
            'status' => $request->ket,
            'id_karyawan' => $request->id_karyawan,
            'tgl' => $request->tgl,
            'id_lokasi' => $request->session()->get('id_lokasi')
        ];
        Absen::create($data);
        }
       
    }

    public function deleteAbsenM(Request $request)
    {
        Absen::where('id_absen', $request->id_absen)->delete();
    }

    public function updateAbsenM(Request $request)
    {
        $data = [
            'status' => $request->ket2,
        ];

        Absen::where('id_absen', $request->id_absen_edit)->update($data);
        return true;
    }

    public function addAbsen(Request $request)
    {
        $id_karyawan = $request->id_karyawan;
        $tgl = $request->tanggal;
        $ada = Absen::where([
            ['id_karyawan', $id_karyawan],
            ['tgl', $tgl],
        ])->first();
        
        if($ada) {
            return true;
        } else {
            $data = [
                'id_karyawan' => $id_karyawan,
                'status' => 'M',
                'tgl' => $tgl,
                'id_lokasi' => $request->session()->get('id_lokasi'),
                'page' => $request->page,
            ];
            //    dd($data['page']);
            Absen::create($data);
        }
        
        return redirect()->route('absen', ['page' => $request->page, 'bulan' => $request->bulan, 'tahun' => $request->tahun]);
    }

    public function updateAbsen(Request $request)
    {
        $data = [
            'status' => $request->status,
        ];

        Absen::where('id_absen', $request->id_absen)->update($data);
        return true;
    }

    public function deleteAbsen(Request $request)
    {
        Absen::where('id_absen', $request->id_absen)->delete();
        return true;
    }

    public function downloadAbsen(Request $request)
    {
        $bulan = $request->bulanDwn;
        $tahun = $request->tahunDwn;

        $data = [
            'absensi' => Absen::select('tb_absen.*', 'tb_karyawan.nama')->join('tb_karyawan', 'tb_absen.id_karyawan', '=', 'tb_karyawan.id_karyawan')->orderBy('id_absen', 'desc')->get(),
            'karyawan' => Karyawan::all(),
            'bulan' => $bulan,
            'tahun' => $tahun,
        ];

        return view('absen.excel',$data);
    }

    public function print_absen2(Request $request)
    {
        if (empty($request->tgl1)) {
            $tgl1 = date('Y-m-01');
            $tgl2 = date('Y-m-d');
        } else {
            $tgl1 = $request->tgl1;
            $tgl2 = $request->tgl2;
        }
        $karyawan = Http::get("https://ptagafood.com/api/absenBaru?tgl1=$tgl1&tgl2=$tgl2");
        $dt_karyawan = json_decode($karyawan, TRUE);

        $absen = Http::get("https://ptagafood.com/api/absenPrint?tgl1=$tgl1&tgl2=$tgl2",);
        $dt_absen = json_decode($absen, true);

        DB::table('absennew')->whereBetween('tgl', [$tgl1, $tgl2])->delete();

        foreach ($dt_absen['data']['absen'] as $a) {
            $data = [
                'karyawan_id' => $a['karyawan_id'],
                'tgl' => $a['tgl'],
                'shift_id' => $a['shift_id'],
            ];
            DB::table('absennew')->insert($data);
        }

        $data = [
            'title' => 'Absen',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'logout' => $request->session()->get('logout'),
            'karyawan' => $dt_karyawan['data']['karyawan'],
            'dates' => $dt_karyawan['data']['dates'],
            'tahun' => empty($request->tgl1) ? date('Y') : date('Y', strtotime($request->tgl1)),
            'bulan' => empty($request->tgl1) ? date('m') : date('m', strtotime($request->tgl1)),
        ];

        return view('absen.index2', $data);
    }
}
