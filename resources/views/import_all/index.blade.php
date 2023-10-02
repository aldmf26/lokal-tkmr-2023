@extends('template.master')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container">
            <div class="row mb-2 justify-content-center">
                <div class="col-sm-12">
                    <center>
                        <h4 style="color: #787878; font-weight: bold;">Export Table</h4>
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
                <div class="col-lg-4">
                    <center>
                        ( <img src="{{ asset('public/assets') }}/img_menu/order.png" alt="" width="20px"> / <img
                            src="{{ asset('public/assets') }}/img_menu/stack.png" alt="" width="20px"> / <img
                            src="{{ asset('public/assets') }}/img_menu/chef-hat.png" alt="" width="20px"> / <img
                            src="{{ asset('public/assets') }}/img_menu/add-user.png" alt="" width="20px"> )
                    </center>
                    <h3 class="text-bold text-center">Order / Meja / Head / Add Koki</h3>
                    <center>
                        <br>
                        <?php if(empty($tb_order->import)): ?>
                        <p class="text-success">Data sudah di export <i class="fas fa-check"></i></p>
                        <?php else: ?>
                        <a href="{{route('tb_order1')}}" class="btn btn-info" id="export1">export</a>
                        <button class="btn btn-info save_loading1" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <?php endif ?>
                    </center>
                </div>
                <div class="col-lg-4">
                    <center>
                        ( <img src="{{ asset('public/assets') }}/img_menu/order.png" alt="" width="20px"> )
                    </center>
                    <h3 class="text-bold text-center">Split bill</h3>
                    <center>
                        <br>
                        <?php if(empty($tb_order2->import)): ?>
                        <p class="text-success">Data sudah di export <i class="fas fa-check"></i></p>
                        <?php else: ?>
                        <a href="{{route('tb_order2')}}" class="btn btn-info" id="export2">export</a>
                        <button class="btn btn-info save_loading2" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <?php endif ?>
                    </center>
                </div>
                <div class="col-lg-4">
                    <center>
                        ( <img src="{{ asset('public/assets') }}/img_menu/order2.png" alt="" width="20px"> )
                    </center>
                    <h3 class="text-bold text-center">Transaksi</h3>
                    <center>
                        <br>
                        <?php if(empty($tb_transaksi->import)): ?>
                        <p class="text-success">Data sudah di export <i class="fas fa-check"></i></p>
                        <?php else: ?>
                        <a href="{{route('tb_transaksi')}}" class="btn btn-info" id="export3">export</a>
                        <button class="btn btn-info save_loading3" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <?php endif ?>

                    </center>
                </div>

                <div class="col-lg-4 mt-4">
                    <h3 class="text-bold text-center">Jurnal Pemasukan</h3>
                    <center>
                        <br>
                        <?php if(empty($tb_jurnal->import)): ?>
                        <p class="text-success">Data sudah di export <i class="fas fa-check"></i></p>
                        <?php else: ?>
                        <a href="{{route('tb_jurnal')}}" class="btn btn-info" id="export10">export</a>
                        <button class="btn btn-info save_loading10" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <?php endif ?>
                    </center>
                </div>
                <div class="col-lg-4 mt-4">
                    <h3 class="text-bold text-center">STK Pembelian</h3>
                    <center>
                        <br>
                        <?php if(empty($tb_pembelian->import)): ?>
                        <p class="text-success">Data sudah di export <i class="fas fa-check"></i></p>
                        <?php else: ?>
                        <a href="{{route('tb_pembelian')}}" class="btn btn-info" id="export11">export</a>
                        <button class="btn btn-info save_loading11" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <?php endif ?>
                    </center>
                </div>
                <div class="col-lg-4 mt-4">
                    <h3 class="text-bold text-center">Kerja lain-lain</h3>
                    <center>
                        <br>
                        <?php if(empty($tb_mencuci->import)): ?>
                        <p class="text-success">Data sudah di export <i class="fas fa-check"></i></p>
                        <?php else: ?>
                        <a href="{{route('tb_mencuci')}}" class="btn btn-info" id="export5">export</a>
                        <button class="btn btn-info save_loading5" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <?php endif ?>
                    </center>
                </div>
                <div class="col-lg-4 mt-4">
                    <h3 class="text-bold text-center">Driver</h3>
                    <center>
                        <br>
                        <?php if(empty($tb_driver->import)): ?>
                        <p class="text-success">Data sudah di export <i class="fas fa-check"></i></p>
                        <?php else: ?>
                        <a href="{{route('tb_driver')}}" class="btn btn-info" id="export6">export</a>
                        <button class="btn btn-info save_loading6" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <?php endif ?>
                    </center>
                </div>
                <div class="col-lg-4 mt-4">
                    <h3 class="text-bold text-center">Tips</h3>
                    <center>
                        <br>
                        <?php if(empty($tb_tips->import)): ?>
                        <p class="text-success">Data sudah di export <i class="fas fa-check"></i></p>
                        <?php else: ?>
                        <a href="{{route('tb_tips')}}" class="btn btn-info" id="export7">export</a>
                        <button class="btn btn-info save_loading7" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <?php endif ?>
                    </center>
                </div>
                <div class="col-lg-4 mt-4">
                    <h3 class="text-bold text-center">Denda</h3>
                    <center>
                        <br>
                        <?php if(empty($tb_denda->import)): ?>
                        <p class="text-success">Data sudah di export <i class="fas fa-check"></i></p>
                        <?php else: ?>
                        <a href="{{route('tb_denda')}}" class="btn btn-info" id="export8">export</a>
                        <button class="btn btn-info save_loading8" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <?php endif ?>
                    </center>
                </div>
                <div class="col-lg-4 mt-4">
                    <h3 class="text-bold text-center">Kasbon</h3>
                    <center>
                        <br>
                        <?php if(empty($tb_kasbon->import)): ?>
                        <p class="text-success">Data sudah di export <i class="fas fa-check"></i></p>
                        <?php else: ?>
                        <a href="{{route('tb_kasbon')}}" class="btn btn-info" id="export9">export</a>
                        <button class="btn btn-info save_loading9" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <?php endif ?>
                    </center>
                </div>
                <div class="col-lg-4 mt-4 mb-4">
                    <h3 class="text-bold text-center">Hapus invoice</h3>
                    <center>
                        <br>
                        <?php if(empty($tb_hapus_invoice->import)): ?>
                        <p class="text-success">Data sudah di export <i class="fas fa-check"></i></p>
                        <?php else: ?>
                        <a href="{{route('tb_invoice_hapus')}}" class="btn btn-info" id="export9">export</a>
                        <button class="btn btn-info save_loading9" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <?php endif ?>
                    </center>
                </div>
                <div class="col-lg-4 mt-4 mb-4">
                    <h3 class="text-bold text-center">Komisi</h3>
                    <center>
                        <br>
                        <?php if(empty($komisi->import)): ?>
                        <p class="text-success">Data sudah di export <i class="fas fa-check"></i></p>
                        <?php else: ?>
                        <a href="{{route('tb_komisi')}}" class="btn btn-info" id="export12">export</a>
                        <button class="btn btn-info save_loading12" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </button>
                        <?php endif ?>
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
        $('.save_loading8').hide();
        $('.save_loading9').hide();
        $('.save_loading10').hide();
        $('.save_loading11').hide();
        $('.save_loading12').hide();
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
        $(document).on('click', '#export8', function() {
            //   event.preventDefault();

            $('#export8').hide();
            $('.save_loading8').show();

        });
        $(document).on('click', '#export9', function() {
            //   event.preventDefault();

            $('#export9').hide();
            $('.save_loading9').show();

        });
        $(document).on('click', '#export10', function() {
            //   event.preventDefault();

            $('#export10').hide();
            $('.save_loading10').show();

        });
        $(document).on('click', '#export11', function() {
            //   event.preventDefault();

            $('#export11').hide();
            $('.save_loading11').show();

        });
        $(document).on('click', '#export12', function() {
            //   event.preventDefault();

            $('#export12').hide();
            $('.save_loading12').show();

        });
});
</script>

@endsection