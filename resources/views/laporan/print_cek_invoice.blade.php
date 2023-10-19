<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>CLOSINGAN</title>
    <style>
        table {
            font-size: 11px;
        }

        .dhead {
            background-color: #435EBE !important;
            color: white;
        }

        .dborder {
            border-color: #435EBE
        }
    </style>
</head>

<body>

    <div class="row">
        <div class="col-lg-12">
            <h5 class="text-center fw-bold mb-2">Laporan Closingan</h5>
            <h6 class="text-center fw-bold mb-2"> {{ date('d M Y', strtotime($tgl1)) }} ~
                {{ date('d M Y', strtotime($tgl2)) }}</h6>
            <br>
            <table width="100%" class="table table-bordered">
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
                            $total += $i->nominal;
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
                        <td style="font-weight: bold;" class="text-end">{{ number_format($total, 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>


</body>
<script>
    window.print()
</script>

</html>
