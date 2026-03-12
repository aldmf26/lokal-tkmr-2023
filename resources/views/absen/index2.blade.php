@extends('template.master')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="float-left">Absen Tanggal : {{ date('d-F-Y', strtotime($tgl1)) }} ~
                                    {{ date('d-F-Y', strtotime($tgl2)) }}</h5>
                                <button type="button" class="btn btn-sm btn-info float-right ml-2" data-toggle="modal"
                                    data-target="#view"><i class="fas fa-print"></i> View</button>
                            </div>
                            <div class="card-body">
                                <p>Note : CTRL + F (untuk search)</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="dhead">#</th>
                                                <th class="text-nowrap dhead">Nama Karaywan</th>
                                                @foreach ($dates as $date)
                                                    <th class="text-center dhead">{{ $date }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($karyawan as $no => $k)
                                                <tr>
                                                    <td>{{ $no + 1 }}</td>
                                                    <td class="text-nowrap">{{ $k['nama'] }}</td>
                                                    @foreach ($dates as $date)
                                                        @php
                                                            $id_karyawan = $k['id_karyawan'];
                                                            $absen = DB::selectOne("SELECT  b.ket 
                                                        FROM absennew as a
                                                        left join tb_shift as b on b.id_shift = a.shift_id
                                                        where a.karyawan_id = $id_karyawan  and DAY(a.tgl) = '$date' and MONTH(a.tgl) = '$bulan' and YEAR(a.tgl) = '$tahun' 
                                                        ");
                                                        @endphp
                                                        <td class="text-center "
                                                            style="{{ empty($absen->ket) ? 'background-color: #D53343; color: white' : 'background-color: #188351;color: white' }}">
                                                            {{ $absen->ket ?? 'OFF' }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>


                            </div>

                            <form action="" method="get">
                                <div class="modal fade" role="dialog" id="view" aria-labelledby="exampleModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content ">
                                            <div class="modal-header btn-costume">
                                                <h5 class="modal-title text-light" id="exampleModalLabel">View Absen</h5>
                                                <button type="button" class="close text-light" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <label for="">Dari</label>
                                                        <input required class="form-control" type="date" name="tgl1"
                                                            id="dari">
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label for="">Sampai</label>
                                                        <input required class="form-control" type="date" name="tgl2"
                                                            id="sampai">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class=" btn btn-sm btn-info">View</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
@endsection
