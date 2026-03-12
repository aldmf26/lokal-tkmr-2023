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
                <div class="col-lg-12">
                    <a class="btn btn-info float-right" data-toggle="modal" data-target="#view"><i
                            class="fas fa-eye"></i> View</a>
                    <br>
                    <br>
                    <div class="card">
                        <div class="card-body">
                            <table class="table  " id="table" width="100%" style="font-size: 12px;">
                                <thead>
                                    <tr>
                                        <th style="font-size: 12px;">No</th>
                                        <th style="font-size: 12px;">No Order</th>
                                        <th style="font-size: 12px;">Table</th>
                                        <th style="font-size: 12px;">Produk</th>
                                        <th style="font-size: 12px;">Qty</th>
                                        <th style="font-size: 12px;">Harga</th>
                                        <th style="font-size: 12px;">Time Order</th>
                                        <th style="font-size: 12px;">Admin</th>
                                        <th style="font-size: 12px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;
                                    foreach ($tb_order as $t) : ?>
                                    <tr>
                                        <td>
                                            <?= $i++ ?>
                                        <td>
                                            <?= $t->no_nota ?>
                                        </td>
                                        <td>
                                            <?= $t->nm_meja ?>
                                        </td>
                                        <td>
                                            <?= $t->nm_produk ?>
                                        </td>
                                        <td>
                                            <?= $t->jumlah ?>
                                        </td>
                                        <td>
                                            <?= number_format($t->jumlah * $t->harga, 0) ?>
                                        </td>
                                        <td>
                                            <?= date('h.i A',strtotime($t->tgl_input)) ?>
                                        </td>
                                        <td>
                                            <?= $t->admin ?>
                                        </td>
                                        <td>
                                            @if (empty($t->no_nota2))
                                            <a href="{{route('hapus_majo', ['id_pembelian' => $t->id_pembelian, 'id_produk'=>$t->id_produk,'qty'=>$t->jumlah])}}"
                                                onclick="return confirm('Apakah anda yakin ingn menghapus pesanan?')"
                                                class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
                                            @else

                                            @endif

                                        </td>

                                    </tr>
                                    <?php endforeach ?>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <form action="<?= route('order1') ?>" method="get">
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
