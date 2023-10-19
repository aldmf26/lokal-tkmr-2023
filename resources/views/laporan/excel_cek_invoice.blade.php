<?php

$file = 'LAPORAN CLOSINGAN.xls';
header('Content-type: application/vnd-ms-excel');
header("Content-Disposition: attachment; filename=$file");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CLOSINGAN</title>

    <style>
        p {
            font-size: 18px;
        }
    </style>

<body>

    <div class="row">
        <div class="col-lg-12">
            <h2 style="text-align: center;">Laporan Closingan</h2>
            <h3 style="text-align: center;"> {{ date('d M Y', strtotime($tgl1)) }} ~
                {{ date('d M Y', strtotime($tgl2)) }}</h3>
            <table width="100%" border="1">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Lokasi</th>
                        <th class="text-center">Nama Akun</th>
                        <th class="text-center">Jenis Pembayaran</th>
                        <th class="text-center">CFM</th>
                        <th class="text-center">Total Rp</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total = 0;
                    @endphp
                    @foreach ($invoice as $no => $i)
                        @php
                            $lokasi = $i->id_lokasi == '1' ? 'TAKEMORI' : 'SOONDOBU';
                        @endphp
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ date('d-m-Y', strtotime($i->tgl)) }}</td>
                            <td>{{ $lokasi }}</td>
                            <td>PENJUALAN {{ $i->id_distribusi == '1' ? $lokasi : 'GOJEK' }}</td>
                            <td>{{ $i->nm_akun . ' ' . $i->nm_klasifikasi }}</td>
                            <td>{{ $i->no_nota }}</td>
                            <td align="right">{{ number_format($i->nominal, 0) }}</td>
                        </tr>
                        @php
                            $total += $i->nominal;
                        @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="font-weight: bold">Total</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="font-weight: bold">{{ number_format($total, 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>


</body>

</html>
