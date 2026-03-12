@extends('template.master')
@section('content')
<style>
    /* .icon-menu:hover{
                                                                                                                                                background: #C8BED8;
                                                                                                                                                border-radius: 50px;
                                                                                                                                            } */

    h6 {
        color: #155592;
        font-weight: bold;
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container">
            <div class="row mb-2 justify-content-center">
                <div class="col-sm-12">
                    <center>
                        <h4 style="color: #787878; font-weight: bold;">Orderan</h4>
                    </center>

                </div><!-- /.col -->

            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <a class="btn btn-info float-right" data-toggle="modal" data-target="#view"><i
                            class="fas fa-eye"></i> View</a>
                    <br>
                    <br>
                    <div class="card">
                        <div class="card-header">
                            <h5>Laporan Penjualan Stk {{$lokasi}} : {{date('d-m-Y',strtotime($tgl1))}} ~
                                {{date('d-m-Y',strtotime($tgl2))}}</h5>
                        </div>
                        <div class="card-body">
                            <table class="table  " width="100%" style="font-size: 12px; ">
                                <thead>
                                    <tr>
                                        <th style="font-size: 12px; text-align: center">Kategori</th>
                                        <th style="font-size: 12px;text-align: center">Nama Produk</th>
                                        <th style="font-size: 12px;text-align: center">Harga Satuan</th>
                                        <th style="font-size: 12px;text-align: center">Qty</th>
                                        <th style="font-size: 12px;text-align: center">Satuan</th>
                                        <th style="font-size: 12px;text-align: center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;
                                    $total = 0;
                                    foreach ($tb_order as $t) : 
                                    $total += $t->qty * $t->harga;
                                    ?>
                                    <tr>
                                        <td style="text-align: center">
                                            <?= $t->nm_kategori ?>
                                        </td>
                                        <td style="text-align: center">
                                            <?= $t->nm_produk ?>
                                        </td>
                                        <td style="text-align: center">
                                            <?= number_format($t->harga,0) ?>
                                        </td>
                                        <td style="text-align: center">
                                            <?= number_format($t->qty, 0) ?>
                                        </td>
                                        <td style="text-align: center">{{$t->satuan}}</td>
                                        <td style="text-align: center">
                                            <?= number_format($t->qty * $t->harga, 0) ?>
                                        </td>

                                    </tr>
                                    <?php endforeach ?>

                                </tbody>
                                <tfoot>
                                    <th colspan="5">Total</th>
                                    <th style="text-align: center">{{number_format($total,0)}}</th>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>

                <form action="" method="get">
                    <div class="modal fade" id="view">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-info">
                                    <h4 class="modal-title">View</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                    <div class="form-group">
                                        <div class="row">

                                            <div class="col-lg-6">
                                                <label for="">Dari</label>
                                                <input type="date" name="tgl" class="form-control">
                                            </div>
                                            <div class="col-lg-6">
                                                <label for="">Sampai</label>
                                                <input type="date" name="tgl2" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary" target="_blank">Lanjutkan</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </form>





            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
</aside>
<style>
    .modal-lg-max {
        max-width: 900px;
    }
</style>

@endsection
