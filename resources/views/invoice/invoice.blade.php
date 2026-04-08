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
                        <h4 style="color: #787878; font-weight: bold;">List Invoice</h4>
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
                    
                    <!-- Metric Cards Section -->
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-file-invoice"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Invoice</span>
                                    <span class="info-box-number"><?= count($invoice) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Pendapatan</span>
                                    <?php 
                                        $grand_total = 0;
                                        foreach($invoice as $i_total) {
                                            $grand_total += ($i_total->total_orderan + $i_total->tax + $i_total->service + $i_total->ongkir + $i_total->round);
                                        }
                                    ?>
                                    <span class="info-box-number">Rp <?= number_format($grand_total, 0) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-tags text-white"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Diskon</span>
                                    <?php 
                                        $total_disc_top = 0;
                                        foreach($invoice as $i_disc) $total_disc_top += ($i_disc->discount + $i_disc->voucher);
                                    ?>
                                    <span class="info-box-number text-white">Rp <?= number_format($total_disc_top, 0) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-wallet"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Net Sales</span>
                                    <?php 
                                        $total_net_top = 0;
                                        foreach($invoice as $i_net) $total_net_top += $i_net->total_bayar;
                                    ?>
                                    <span class="info-box-number">Rp <?= number_format($total_net_top, 0) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Section -->
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title text-primary"><i class="fas fa-chart-pie mr-1"></i> Distribusi Pembayaran</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <canvas id="paymentChart" style="min-height: 230px; height: 230px; max-height: 230px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">

                                <table class="table table-hover table-striped table-bordered" id="table" width="100%" style="font-size: 12px; margin-bottom: 0;">
                                    <thead class="bg-gray-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>No Order</th>
                                            <th>Meja</th>
                                            <th class="text-right">Total (Inc Tax)</th>
                                            <th class="text-right">Disc</th>
                                            <th class="text-right">Terbayar</th>
                                            <th class="text-center">Metode Bayar</th>
                                            <th>Admin</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $i = 1;
                                        $total_all = 0; $total_disc = 0; $total_bayar = 0;
                                        $akun_totals = [];
                                        foreach ($pembayaran_akun as $akun) $akun_totals[$akun->id_akun_pembayaran] = 0;

                                        foreach ($invoice as $inv) : 
                                            $total_inc = $inv->total_orderan + $inv->tax + $inv->service + $inv->ongkir + $inv->round;
                                            $total_all += $total_inc;
                                            $total_disc += ($inv->discount + $inv->voucher);
                                            $total_bayar += $inv->total_bayar;
                                        ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= date('d-m-Y', strtotime($inv->tgl_transaksi)) ?></td>
                                            <td>
                                                <a href="<?= route('print_nota', ['no' => $inv->no_order]) ?>" target="_blank" class="font-weight-bold">
                                                    <?= $inv->no_order ?>
                                                </a>
                                            </td>
                                            <td class="text-center"><span class="badge badge-secondary"><?= $inv->nm_meja ?></span></td>
                                            <td class="text-right"><?= number_format($total_inc, 0) ?></td>
                                            <td class="text-right text-danger"><?= number_format($inv->discount + $inv->voucher, 0) ?></td>
                                            <td class="text-right font-weight-bold">Rp <?= number_format($inv->total_bayar, 0) ?></td>
                                            
                                            <td class="text-center">
                                                <?php foreach ($pembayaran_akun as $akun) : 
                                                    $nominal = $inv->pembayaran_details[$akun->id_akun_pembayaran] ?? 0;
                                                    $akun_totals[$akun->id_akun_pembayaran] += $nominal;
                                                    if ($nominal > 0) : ?>
                                                        <span class="badge badge-info" title="Rp <?= number_format($nominal, 0) ?>"><?= $akun->nm_klasifikasi . ' ' . $akun->nm_akun ?></span>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </td>

                                            <td><?= $inv->admin ?></td>
                                            <td class="text-center">
                                                <a href="#" class="text-danger btn_hapus"
                                                    no_order="{{$inv->no_order}}" meja="{{$inv->nm_meja}}"
                                                    data-toggle="modal" data-target="#hapus_voucher" title="Hapus Invoice">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                    <tfoot class="bg-light font-weight-bold">
                                        <tr>
                                            <td colspan="4" class="text-center">TOTAL SUMMARY</td>
                                            <td class="text-right"><?= number_format($total_all, 0) ?></td>
                                            <td class="text-right text-danger"><?= number_format($total_disc, 0) ?></td>
                                            <td class="text-right text-primary">Rp <?= number_format($total_bayar, 0) ?></td>
                                            <td class="text-center">
                                                <?php foreach ($pembayaran_akun as $akun) : ?>
                                                    <?php if($akun_totals[$akun->id_akun_pembayaran] > 0) : ?>
                                                        <div style="font-size: 9px;"><?= $akun->nm_akun ?>: <?= number_format($akun_totals[$akun->id_akun_pembayaran], 0) ?></div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>

                            </div>

                        </div>
                    </div>

                </div>

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
                                <input type="date" name="tgl1" class="form-control">

                            </div>
                            <div class="col-lg-6">
                                <label for="">Sampai</label>
                                <input type="date" name="tgl2" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" target="_blank">Lanjutkan</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>


<form action="{{route('hapus_invoice')}}" method="post">
    @csrf
    <div class="modal fade" id="hapus_voucher">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header bg-danger ">
                    <h4 class="modal-title">Hapus data</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row justify-content-center">
                            <div class="col-lg-6">
                                <label for="">Inoice</label>
                                <input type="text" id="no_order" name="no_invoice" class="form-control" readonly>
                                <input type="hidden" id="meja" name="meja" class="form-control" readonly>
                            </div>
                            <div class="col-lg-6">
                                <label for="">Masukan voucher</label>
                                <input type="text" name="kd_voucher" class="form-control">
                            </div>
                            <div class="col-lg-12 mt-2">
                                <label for="">Alasan</label>
                                <input type="text" class="form-control" name="alasan">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-info" target="_blank">Lanjutkan</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@section('script')
<script>
    <?php if(Session::get('sukses')) { ?>
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        icon: 'success',
        title: " {{ Session::get('sukses') }}"
    });
    <?php }elseif(Session::get('error')) { ?>
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        icon: 'error',
        title: " {{ Session::get('error') }}"
    });
    <?php } ?>
</script>
<script>
    $(document).ready(function() {
        $(document).on('click', '.btn_hapus', function() {
            var no_order = $(this).attr("no_order");
            var meja = $(this).attr("meja");

            $('#no_order').val(no_order);
            $('#meja').val(meja);
        });

        // Chart.js Implementation
        var ctx = document.getElementById('paymentChart').getContext('2d');
        var paymentChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [
                    <?php foreach ($pembayaran_akun as $akun) : ?>
                        "<?= $akun->nm_klasifikasi . ' ' . $akun->nm_akun ?>",
                    <?php endforeach; ?>
                ],
                datasets: [{
                    data: [
                        <?php foreach ($pembayaran_akun as $akun) : ?>
                            <?= $akun_totals[$akun->id_akun_pembayaran] ?>,
                        <?php endforeach; ?>
                    ],
                    backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', '#ae81ff', '#66d9ef', '#a6e22e', '#fd971f'],
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                    position: 'bottom',
                    labels: {
                        fontSize: 11
                    }
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            var currentValue = dataset.data[tooltipItem.index];
                            return data.labels[tooltipItem.index] + ": Rp " + currentValue.toLocaleString();
                        }
                    }
                }
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>


@endsection
