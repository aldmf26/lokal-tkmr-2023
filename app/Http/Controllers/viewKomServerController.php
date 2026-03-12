<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class viewKomServerController extends Controller
{
  public function index(Request $r)
  {

    $tgl1 = $r->tgl1 ?? date('Y-m-01');
    $tgl2 = $r->tgl2 ?? date('Y-m-d');
    $data = [
      'title' => 'Komisi Server',
      'logout' => $r->session()->get('logout'),
      'tgl1' => $tgl1,
      'tgl2' => $tgl2,
    ];

    return redirect()->back();
    // return view('allKomisiServer.viewKomServer', $data);
  }

  public function loadTakemori(Request $r)
  {
    $id_lokasi = 1;
    $tgl1 = $r->tgl1 ?? date('Y-m-01');
    $tgl2 = $r->tgl2 ?? date('Y-m-d');
    // get api dari server
    $client = new Client();

    $response = $client->request('GET', "https://ptagafood.com/api/komisiServer/$id_lokasi/$tgl1/$tgl2");

    $data = json_decode($response->getBody(), true);

    $service = json_decode(json_encode($data['service']), false);
    $server = json_decode(json_encode($data['server']), false);
    $jumlah_orang = json_decode(json_encode($data['jumlah_orang']), false);
    $persen = json_decode(json_encode($data['persen']), false);

    $l = 1;
    $ttl_kom = 0;

    foreach ($server as $k) {
      $o = $l++;
      $ttl_kom += $k->komisi;
    }

    $bagi_kom = $service->total;
    $service_charge = $service->total * 0.07;
    $orang = $o ?? 0;

    $kom = ((($service_charge / 7) * $persen->jumlah_persen) / $jumlah_orang->jumlah) * $orang;

    // sdb
    $response = $client->request('GET', "https://ptagafood.com/api/komisiServer/2/$tgl1/$tgl2");
    $data2 = json_decode($response->getBody(), true);
    $service2 = json_decode(json_encode($data2['service']), false);
    $server2 = json_decode(json_encode($data2['server']), false);
    $jumlah_orang2 = json_decode(json_encode($data2['jumlah_orang']), false);
    $persen2 = json_decode(json_encode($data2['persen']), false);
    $settingOrang = json_decode(json_encode($data2['settingOrang']), false);
    $persenBagi = json_decode(json_encode($data2['persenBagi']), false);

    $l2 = 1;
    $ttl_kom2 = 0;

    foreach ($server2 as $k) {
      $o2 = $l2++;
      $ttl_kom2 += $k->komisi;
    }


    $service_charge2 = $service2->total * 0.07;
    $orang2 = $o2 ?? 0;

    $kom2 = ((($service_charge2 / 7) * $persen2->jumlah_persen) / $jumlah_orang2->jumlah) * $orang2;

    $data = [
      'title' => 'Kom Server',
      'tgl1' => $tgl1,
      'tgl2' => $tgl2,
      'orang' => $orang,
      'jumlah_orang' => $jumlah_orang,
      'service_charge' => $service_charge,
      'persen' => $persen,
      'kom' => $kom,
      'kom2' => $kom2,
      'server' => $server,
      'ttl_kom' => $ttl_kom,
      'bagi_kom' => $bagi_kom,
      'settingOrang' => $settingOrang,
      'persenBagi' => $persenBagi,

      'id_lokasi' => $id_lokasi,
      'komisi' => '0',
      'logout' => $r->session()->get('logout'),
    ];
    return view('allKomisiServer.loadTakemori', $data);
  }
  public function loadSoondobu(Request $r)
  {
    $id_lokasi = 2;
    $tgl1 = $r->tgl1 ?? date('Y-m-01');
    $tgl2 = $r->tgl2 ?? date('Y-m-d');

    // get api dari server
    $client = new Client();

    $response = $client->request('GET', "https://ptagafood.com/api/komisiServer/$id_lokasi/$tgl1/$tgl2");
    $data = json_decode($response->getBody(), true);

    $service = json_decode(json_encode($data['service']), false);
    $server = json_decode(json_encode($data['server']), false);
    $jumlah_orang = json_decode(json_encode($data['jumlah_orang']), false);
    $persen = json_decode(json_encode($data['persen']), false);

    $l = 1;
    $ttl_kom = 0;

    foreach ($server as $k) {
      $o = $l++;
      $ttl_kom += $k->komisi;
    }

    $bagi_kom = $service->total;
    $service_charge = $service->total * 0.07;
    $orang = $o ?? 0;

    $kom = ((($service_charge / 7) * $persen->jumlah_persen) / $jumlah_orang->jumlah) * $orang;

    // tkm
    // sdb
    $response = $client->request('GET', "https://ptagafood.com/api/komisiServer/1/$tgl1/$tgl2");
    $data2 = json_decode($response->getBody(), true);
    $service2 = json_decode(json_encode($data2['service']), false);
    $server2 = json_decode(json_encode($data2['server']), false);
    $jumlah_orang2 = json_decode(json_encode($data2['jumlah_orang']), false);
    $persen2 = json_decode(json_encode($data2['persen']), false);
    $settingOrang = json_decode(json_encode($data2['settingOrang']), false);
    $persenBagi = json_decode(json_encode($data2['persenBagi']), false);


    $l2 = 1;
    $ttl_kom2 = 0;

    foreach ($server2 as $k) {
      $o2 = $l2++;
      $ttl_kom2 += $k->komisi;
    }


    $service_charge2 = $service2->total * 0.07;
    $orang2 = $o2 ?? 0;

    $kom2 = ((($service_charge2 / 7) * $persen2->jumlah_persen) / $jumlah_orang2->jumlah) * $orang2;

    $data = [
      'title' => 'Kom Server',
      'tgl1' => $tgl1,
      'tgl2' => $tgl2,
      'orang' => $orang,
      'jumlah_orang' => $jumlah_orang,
      'service_charge' => $service_charge,
      'persen' => $persen,
      'kom' => $kom,
      'kom2' => $kom2,
      'server' => $server,
      'ttl_kom' => $ttl_kom,
      'bagi_kom' => $bagi_kom,
      'settingOrang' => $settingOrang,
      'persenBagi' => $persenBagi,
      
      'id_lokasi' => $id_lokasi,
      'komisi' => '0',
      'logout' => $r->session()->get('logout'),
    ];
    return view('allKomisiServer.loadSoondobu', $data);
  }
}
