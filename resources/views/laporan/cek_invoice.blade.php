<div class="card">
    <div class="card-header">
        <a href="{{ route('excel_cek_invoice', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
            class="btn btn-sm btn-info float-right"><i class="fas fa-file-excel"></i> Export</a>
        <a href="{{ route('print_cek_invoice', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"
            class="btn btn-sm btn-info float-right mr-2"><i class="fas fa-print"></i> Print</a>
    </div>
    <div class="card-body">
        <table width="100%" class="table table-bordered" id="tb-cek">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                    <th>Nama Akun</th>
                    <th>Jenis pembayaran</th>
                    <th>CFM</th>
                    <th class="text-right">Total Rp</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice as $no => $i)
                    @php
                        $lokasi = $i->id_lokasi == '1' ? 'TAKEMORI' : 'SOONDOBU';
                    @endphp
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td>{{ date('d-m-Y', strtotime($i->tgl)) }}</td>
                        <td>{{ $lokasi }}</td>
                        <td>PENJUALAN {{ $i->id_distribusi == '1' ? $lokasi : 'GOJEK' }}</td>
                        <td>{{ $i->nm_akun . ' ' . $i->nm_klasifikasi }} {{ $i->pengirim }}</td>
                        <td>{{ $i->no_nota }}</td>
                        <td align="right">{{ number_format($i->nominal, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>
<script src="{{ asset('assets') }}/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>

<script>
    $("#tb-cek").DataTable({
        "lengthChange": false,
        "autoWidth": false,
        "paging": true
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
