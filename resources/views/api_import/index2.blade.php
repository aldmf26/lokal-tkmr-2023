@extends('template.master')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container">
            <div class="row mb-2 justify-content-center">
                <div class="col-sm-12">
                    <center>
                        <h4 style="color: #787878; font-weight: bold;">Import All Data</h4>
                    </center>

                </div><!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <style>
        .btn-big {
            height: 57px;
            width: 226px;
            padding: 14px;
            font-size: larger;
            font-weight: bold;
        }
    </style>
    <div class="content">

        <br>
        <br>
        <div class="container-fluid">
            <div class="row mt-4 justify-content-center">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <center>
                                <i class="fas text-success r fa-check-circle fa-6x"></i>
                                <h3 class="mt-4">Data berhasil di import</h3>
                                <br>
                                <br>
                                <a href="{{route('download')}}"><i class="fas fa-arrow-alt-circle-left"></i> Kembali
                                    import</a>
                            </center>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>
</div>
@endsection
