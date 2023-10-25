<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Ctt_driver;
use App\Models\Denda;
use App\Models\Dp;
use App\Models\Kasbon;
use App\Models\Mencuci;
use App\Models\Order2;
use App\Models\Orderan;
use App\Models\Tb_hapus_invoice;
use App\Models\Tips;
use App\Models\Transaksi;
use App\Models\Jurnal;
use App\Models\Voucher;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class ApiController extends Controller
{
    public function tb_order(Request $request)
    {
        $tb_order = Orderan::where('import', 'T')->get();

        $id_order = [];
        $data1 = [];
        foreach ($tb_order as $t) {
            $id_order[] = $t->id_order;
            array_push($data1, [
                'no_order' => $t->no_order,
                'id_harga' => $t->id_harga,
                'qty' => $t->qty,
                'harga' => $t->harga,
                'request2' => $t->request,
                'tambahan' => $t->tambahan,
                'page' => $t->page,
                'id_meja' => $t->id_meja,
                'selesai' => $t->selesai,
                'id_lokasi' => $t->id_lokasi,
                'pengantar' => $t->pengantar,
                'tgl' => $t->tgl,
                'void' => $t->void,
                'round' => $t->round,
                'alasan' => $t->alasan,
                'nm_void' => $t->nm_void,
                'j_mulai' => $t->j_mulai,
                'j_selesai' => $t->j_selesai,
                'admin' => $t->admin,
                'diskon' => $t->diskon,
                'wait' => $t->wait,
                'aktif' => $t->aktif,
                'id_koki1' => $t->id_koki1,
                'id_koki2' => $t->id_koki2,
                'id_koki3' => $t->id_koki3,
                'ongkir' => $t->ongkir,
                'id_distribusi' => $t->id_distribusi,
                'orang' => $t->orang,
                'no_checker' => $t->no_checker,
                'voucher' => $t->voucher

            ]);
        }
    

        $response = Http::acceptJson()->post('https://ptagafood.com/api/tb_order', $data1);

        // return $response;
        Orderan::whereIn('id_order', $id_order)->update(['import' => 'Y']);
        return redirect()->route('sukses')->with('sukses', 'Sukses');
    }

    public function tb_jurnal(Request $request)
    {
        
        $data = Jurnal::where('import', 'T')->get();

        $id_jurnal = [];
        $data1 = [];
        foreach ($data as $t) {
            $id_jurnal[] = $t->id_jurnal;
            array_push($data1, [
                'id_buku' => $t->id_buku,
                'id_akun' => $t->id_akun,
                'kd_gabungan' => $t->kd_gabungan,
                'no_nota' => $t->no_nota,
                'id_lokasi' => $t->id_lokasi,
                'debit' => $t->debit,
                'kredit' => $t->kredit,
                'admin' => $t->admin,
                'tgl' => $t->tgl,
                'ket' => $t->ket,
                'status' => $t->status,
                'created_at' => $t->created_at,
                'updated_at' => $t->updated_at,
            ]);
        }

        $response = Http::acceptJson()->post('https://ptagafood.com/api/tb_jurnal', $data1);

        $dataDp = DB::table('tb_dp')->get();
        $id_dp = [];
        $data2 = [];
        foreach ($dataDp as $t) {
            $id_dp[] = $t->id_dp;
            array_push($data2, [
                'kd_dp' => $t->kd_dp,
                'nm_customer' =>  $t->nm_customer,
                'server' =>  $t->server,
                'jumlah' =>  $t->jumlah,
                'tgl' =>  $t->tgl,
                'ket' =>  $t->ket,
                'metode' =>  $t->metode,
                'tgl_input' =>  $t->tgl_input,
                'tgl_digunakan' =>  $t->tgl_digunakan,
                'status' =>  $t->status,
                'admin' => $t->admin,
                'id_lokasi' =>  $t->id_lokasi,
                'created_at' =>  $t->created_at,
                'updated_at' =>  $t->updated_at,
            ]);
        }
        $response = Http::acceptJson()->post('https://ptagafood.com/api/tb_dp', $data2);

        // return $response;
        Jurnal::whereIn('id_jurnal', $id_jurnal)->update(['import' => 'Y']);
        return redirect()->route('sukses')->with('sukses', 'Sukses');
    }

    public function tb_denda()
    {
        $data = Denda::where('import', 'T')->get();
        $id_denda = [];
        $data1 = [];
        foreach ($data as $t) {
            $id_denda[] = $t->id_denda;
            array_push($data1, [
                'nama' => $t->nama,
                'alasan' =>  $t->alasan,
                'nominal' =>  $t->nominal,
                'tgl' =>  $t->tgl,
                'id_lokasi' =>  $t->id_lokasi,
                'admin' => $t->admin,
            ]);
        }
        $response = Http::acceptJson()->post('https://ptagafood.com/api/tb_denda', $data1);
        Denda::whereIn('id_denda', $id_denda)->update(['import' => 'Y']);

        return redirect()->route('sukses')->with('sukses', 'Sukses');
    }

    public function tb_pembelian()
    {
        $tb_pembelian = DB::table('tb_pembelian')->where('import', 'T')->get();
        $id_pembelian = [];
        $data3 = [];
        foreach ($tb_pembelian as $t) {
            $id_pembelian[] = $t->id_pembelian;
            array_push($data3, [
                'no_nota' => $t->no_nota,
                'no_nota2' => $t->no_nota2,
                'id_karyawan' => $t->id_karyawan,
                'id_produk' => $t->id_produk,
                // 'nm_karyawan' => $t->nm_karyawan,
                'tanggal' => $t->tanggal,
                'tgl_input' => $t->tgl_input,
                'jumlah' => $t->jumlah,
                'harga' => $t->harga,
                // 'diskon' => $t->diskon,
                'jml_komisi' => $t->jml_komisi,
                'total' => $t->total,
                // 'catatan' => $t->catatan,
                'admin' => $t->admin,
                'no_meja' => $t->no_meja,
                'lokasi' => $t->lokasi,
                'void' => $t->void,
                // 'pengantar' => $t->pengantar,
                'selesai' => $t->selesai,
                'bayar' => $t->bayar,
            ]);
        }
        // dd($data2);
        $response = Http::acceptJson()->post('https://ptagafood.com/api/tb_pembelian_majo', $data3);
        DB::table('tb_pembelian')->whereIn('id_pembelian', $id_pembelian)->update(['import' => 'Y']);

        return redirect()->route('sukses')->with('sukses', 'Sukses');
    }

    public function tb_transaksi()
    {

        // pembayaran baru
        $pembayaran = DB::table('pembayaran')->where('import', 'T')->get();
        $id_pembayaran = [];
        $data_pembayaran = [];
        foreach ($pembayaran as $t) {
            $id_pembayaran[] = $t->id_pembayaran;
            array_push($data_pembayaran, [
                'id_akun_pembayaran' => $t->id_akun_pembayaran,
                'no_nota' => $t->no_nota,
                'nominal' => $t->nominal,
                'diskon_bank' => $t->diskon_bank,
                'pengirim' => $t->pengirim,
                'tgl' => $t->tgl,
                'tgl_waktu' => $t->tgl_waktu,
                'id_lokasi' => $t->id_lokasi,
            ]);
        }
        $response = Http::acceptJson()->post('https://ptagafood.com/api/pembayaran', $data_pembayaran);
        DB::table('pembayaran')->whereIn('id_pembayaran', $id_pembayaran)->update(['import' => 'Y']);
        // end pembayaran baru
        // tb produk majo
        $tb_produk_majo = DB::table('tb_produk')->where([['id_lokasi' , 1]])->get();
        $id_produk_majo = [];
        $datap = [];
        foreach ($tb_produk_majo as $t) {
            $id_produk_majo[] = $t->id_produk;
            array_push($datap, [
                'stok' => $t->stok,
                'id_produk' => $t->id_produk,
                'id_lokasi' => $t->id_lokasi,
            ]);
        }
        // dd($datap);
        $response = Http::acceptJson()->post('https://ptagafood.com/api/tb_produk_majo', $datap);
        


        $tb_invoice = DB::table('tb_invoice')->where('import', 'T')->get();
        $id_invoice = [];
        $data_invoice = [];
        foreach ($tb_invoice as $t) {
            $id_invoice[] = $t->id;
            array_push($data_invoice, [
                'no_nota' => $t->no_nota,
                'total' => $t->total,
                'bayar' => $t->bayar,
                'tgl_jam' => $t->tgl_jam,
                'tgl_input' => $t->tgl_input,
                'admin' => $t->admin,
                'no_meja' => $t->no_meja,
                'lokasi' => $t->lokasi,
                'id_distribusi' => $t->id_distribusi,
            ]);
        }
        // dd($datap);
        $response = Http::acceptJson()->post('https://ptagafood.com/api/tb_invoice_new', $data_invoice);
        DB::table('tb_invoice')->whereIn('id', $id_invoice)->update(['import' => 'Y']);
     
        // tb pembelian majoo
        

        // transaksi resto
        $tb_transaksi = Transaksi::where('import', 'T')->get();

        $id_transaksi = [];
        $data1 = [];
        foreach ($tb_transaksi as $t) {
            $id_transaksi[] = $t->id_transaksi;
            array_push($data1, [
                'tgl_transaksi' => $t->tgl_transaksi,
                'no_order' => $t->no_order,
                'total_orderan' => $t->total_orderan,
                'discount' => $t->discount,
                'voucher' => $t->voucher,
                'dp' => $t->dp,
                'gosen' => $t->gosen,
                'diskon_bank' => $t->diskon_bank,
                'total_bayar' => $t->total_bayar,
                'admin' => $t->admin,
                'round' => $t->round,
                'id_lokasi' => $t->id_lokasi,
                'cash' => $t->cash,
                'd_bca' => $t->d_bca,
                'k_bca' => $t->k_bca,
                'd_mandiri' => $t->d_mandiri,
                'k_mandiri' => $t->k_mandiri,
                'ongkir' => $t->ongkir,
                'service' => $t->service,
                'tax' => $t->tax,
                'kembalian' => $t->kembalian,
            ]);
        }
        $response = Http::acceptJson()->post('https://ptagafood.com/api/tb_transaksi', $data1);
        Transaksi::whereIn('id_transaksi', $id_transaksi)->update(['import' => 'Y']);


        return redirect()->route('sukses')->with('sukses', 'Sukses');
    }

    public function tb_order2()
    {
        $get = Order2::where('import', 'T')->get();

        $id_order = [];
        $data1 = [];

        foreach ($get as $t) {
            $id_order[] = $t->id_order2;
            array_push($data1, [
                'no_order' =>  $t->no_order,
                'no_order2' =>  $t->no_order2,
                'id_harga' =>  $t->id_harga,
                'qty' => $t->qty,
                'harga' => $t->harga,
                'tgl' => $t->tgl,
                'id_lokasi' => $t->id_lokasi,
                'admin' => $t->admin,
                'id_distribusi' => $t->id_distribusi,
                'id_meja' => $t->id_meja,
            ]);
        }
        $response = Http::acceptJson()->post('https://ptagafood.com/api/tb_order2', $data1);

        Order2::whereIn('id_order2', $id_order)->update(['import' => 'Y']);

        return redirect()->route('sukses')->with('sukses', 'Sukses');
    }



    public function tb_absen(Request $request)
    {
        $tb_absen = Absen::where('import', 'T')->get();

        $data = [];
        $id_absen = [];
        foreach ($tb_absen as $t) {
            $id_absen[] = $t->id_absen;
            array_push($data, [
                'id_karyawan' => $t->id_karyawan,
                'status' => $t->status,
                'tgl' => $t->tgl,
                'id_lokasi' => $t->id_lokasi,
            ]);
        }
        Http::post('https://ptagafood.com/api/tb_absen', $data);
        Absen::whereIn('id_absen', $id_absen)->update(['import' => 'Y']);

        $tb_voucher = Voucher::where('lokasi', '1')->get();

        $data = [];
        $id_mencuci = [];
        foreach ($tb_voucher as $t) {
            array_push($data, [
                'kode' => $t->kode,
                'updated_at' => $t->updated_at,
                'terpakai' => $t->terpakai,
            ]);
        }
        Http::post('https://ptagafood.com/api/tb_voucherUpdate', $data);

        return redirect()->route('sukses')->with('sukses', 'Sukses');
    }
    public function mencuci(Request $request)
    {
        $tb_mencuci = Mencuci::where('import', 'T')->get();

        $data = [];
        $id_mencuci = [];
        foreach ($tb_mencuci as $t) {
            $id_mencuci[] = $t->id_mencuci;
            array_push($data, [
                'nm_karyawan' => $t->nm_karyawan,
                'id_ket' => $t->id_ket,
                'j_awal' => $t->j_awal,
                'j_akhir' => $t->j_akhir,
                'tgl' => $t->tgl,
                'admin' => $t->admin,
                'ket2' => $t->ket2,
            ]);
        }
        Http::post('https://ptagafood.com/api/tb_mencuci', $data);
        Mencuci::whereIn('id_mencuci', $id_mencuci)->update(['import' => 'Y']);
        return redirect()->route('sukses')->with('sukses', 'Sukses');
    }
    public function driver(Request $request)
    {
        $tb_driver = Ctt_driver::where('import', 'T')->get();

        $data = [];
        $id_driver = [];
        foreach ($tb_driver as $t) {
            $id_driver[] = $t->id_driver;
            array_push($data, [
                'no_order' => $t->no_order,
                'nm_driver' => $t->nm_driver,
                'nominal' => $t->nominal,
                'tgl' => $t->tgl,
                'admin' => $t->admin,
            ]);
        }
        $response =  Http::post('https://ptagafood.com/api/tb_driver', $data);

        // return $response;
        Ctt_driver::whereIn('id_driver', $id_driver)->update(['import' => 'Y']);

        return redirect()->route('sukses')->with('sukses', 'Sukses');
    }
    public function tips(Request $request)
    {
        $tb_tips = Tips::where('import', 'T')->get();

        $data = [];
        $id_tips = [];
        foreach ($tb_tips as $t) {
            $id_tips[] = $t->id_tips;
            array_push($data, [
                'tgl' => $t->tgl,
                'nominal' => $t->nominal,
                'admin' => $t->admin,
            ]);
        }
        $response =  Http::post('https://ptagafood.com/api/tips_tb', $data);
        Tips::whereIn('id_tips', $id_tips)->update(['import' => 'Y']);
        return redirect()->route('sukses')->with('sukses', 'Sukses');
    }

    public function tb_kasbon()
    {
        $tb_kasbon = Kasbon::where('import', 'T')->get();


        $data = [];
        $id_kasbon = [];
        foreach ($tb_kasbon as $t) {
            $id_kasbon[] = $t->id_kasbon;
            array_push($data, [
                'tgl' => $t->tgl,
                'nm_karyawan' => $t->nm_karyawan,
                'admin' =>  $t->admin,
                'nominal' =>  $t->nominal,
            ]);
        }
        $response =  Http::post('https://ptagafood.com/api/tb_kasbon', $data);
        Kasbon::whereIn('id_kasbon', $id_kasbon)->update(['import' => 'Y']);
        return redirect()->route('sukses')->with('sukses', 'Sukses');
    }

    public function tb_invoice_hapus()
    {
        $tb_invoice_hapus = Tb_hapus_invoice::where('import', 'T')->get();


        $data = [];
        $id_hapus_invoice = [];
        foreach ($tb_invoice_hapus as $t) {
            $id_hapus_invoice[] = $t->id_hapus_invoice;
            array_push($data, [
                'no_order' => $t->no_order,
                'tgl_order' => $t->tgl_order,
                'alasan' =>  $t->alasan,
                'nominal_invoice' =>  $t->nominal_invoice,
                'id_lokasi' =>  $t->id_lokasi,
                'meja' =>  $t->meja,
                'admin' =>  $t->admin,
            ]);
        }
        $response =  Http::post('https://ptagafood.com/api/tb_hapus_invoice', $data);
        Tb_hapus_invoice::whereIn('id_hapus_invoice', $id_hapus_invoice)->update(['import' => 'Y']);
        return redirect()->route('sukses')->with('sukses', 'Sukses');
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
            return view('api_import.index', $data);
        }
    }

    public function tb_komisi()
    {
        $tb_komisi = DB::table('komisi')->where('import', 'T')->get();
        $data = [];
        $id_komisi = [];
        foreach ($tb_komisi as $t) {
            $id_komisi[] = $t->id;
            array_push($data, [
                'id_pembelian' => $t->id_pembelian,
                'id_kry' => $t->id_kry,
                'komisi' =>  $t->komisi,
                'tgl' =>  $t->tgl,
                'id_lokasi' =>  $t->id_lokasi,
            ]);
        }
        $response =  Http::post('https://ptagafood.com/api/komisi', $data);
        DB::table('komisi')->whereIn('id', $id_komisi)->update(['import' => 'Y']);
        return redirect()->route('sukses')->with('sukses', 'Sukses');
    }
}