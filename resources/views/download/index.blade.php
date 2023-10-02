@extends('template.master')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container">
            <div class="row mb-2 justify-content-center">
                <div class="col-sm-12">
                    <center>
                        <h4 style="color: #787878; font-weight: bold;">Import Table</h4>
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
        <div class="container-fluid">
            <div class="row mt-4">
                <div class="col-lg-3">
                    <h3 class="text-bold text-center">Voucher</h3>
                    <center>
                        <br>
                        <a href="{{route('tb_voucher')}}" class="btn btn-info" id="export1">import</a>
                        <button class="btn btn-info save_loading1" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                    </center>
                </div>
                <div class="col-lg-3">
                    <h3 class="text-bold text-center">Discount</h3>
                    <center>
                        <br>
                        <a href="{{route('i_discount')}}" class="btn btn-info" id="export2">import</a>
                        <button class="btn btn-info save_loading2" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                    </center>
                </div>
                <div class="col-lg-3">
                    <h3 class="text-bold text-center">Menu</h3>
                    <center>
                        <br>
                        <a href="{{route('i_menu')}}" class="btn btn-info" id="export3">import</a>
                        <button class="btn btn-info save_loading3" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                    </center>
                </div>
                <div class="col-lg-3">
                    <h3 class="text-bold text-center">STK</h3>
                    <center>
                        <br>
                        <button type="button" class="btn btn-info" id="stk">import</button>
                        <button class="btn btn-info save_loadingStk" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                    </center>
                </div>
                <div class="col-lg-3 mt-4">
                    <h3 class="text-bold text-center">Karyawan</h3>
                    <center>
                        <br>
                        <a href="{{route('i_karyawan')}}" class="btn btn-info" id="export4">import</a>
                        <button class="btn btn-info save_loading4" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                    </center>
                </div>
                <div class="col-lg-3 mt-4">
                    <h3 class="text-bold text-center">Absen</h3>
                    <center>
                        <br>
                        <a href="{{route('i_absen')}}" class="btn btn-info" id="export7">import</a>
                        <button class="btn btn-info save_loading7" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                    </center>
                </div>
                <div class="col-lg-3 mt-4">
                    <h3 class="text-bold text-center">User</h3>
                    <center>
                        <br>
                        <a href="{{route('i_user')}}" class="btn btn-info" id="export5">import</a>
                        <button class="btn btn-info save_loading5" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                    </center>
                </div>
                <div class="col-lg-3 mt-4">
                    <h3 class="text-bold text-center">Voucher hapus</h3>
                    <center>
                        <br>
                        <a href="{{route('tb_voucher_hapus')}}" class="btn btn-info" id="export6">import</a>
                        <button class="btn btn-info save_loading6" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                    </center>
                </div>
            </div>
        </div>
    </div>



</div>
</div>
@endsection
@section('script')
<script src="{{ asset('public/assets') }}/css/csshome/jquery.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.save_loading1').hide();
        $('.save_loading2').hide();
        $('.save_loading3').hide();
        $('.save_loading4').hide();
        $('.save_loading5').hide();
        $('.save_loading6').hide();
        $('.save_loading7').hide();
        $('.save_loadingStk').hide();
        $(document).on('click', '#export1', function() {
            //   event.preventDefault();

            $('#export1').hide();
            $('.save_loading1').show();

        });
        $(document).on('click', '#export2', function() {
            //   event.preventDefault();

            $('#export2').hide();
            $('.save_loading2').show();

        });
        $(document).on('click', '#export3', function() {
            //   event.preventDefault();

            $('#export3').hide();
            $('.save_loading3').show();

        });
        $(document).on('click', '#export4', function() {
            //   event.preventDefault();

            $('#export4').hide();
            $('.save_loading4').show();

        });
        $(document).on('click', '#export5', function() {
            //   event.preventDefault();

            $('#export5').hide();
            $('.save_loading5').show();

        });
        $(document).on('click', '#export6', function() {
            //   event.preventDefault();

            $('#export6').hide();
            $('.save_loading6').show();

        });
        $(document).on('click', '#export7', function() {
            //   event.preventDefault();

            $('#export7').hide();
            $('.save_loading7').show();

        });
        $(document).on('click', '#stk', function() {
            //   event.preventDefault();
            if(confirm('Export dulu STK PEMBELIAN baru import')) {
                window.location.href = "{{route('tb_majo')}}"
            }
            $("#stk").show()

        });
    });
</script>

@endsection