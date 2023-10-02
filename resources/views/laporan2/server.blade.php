<!-- CSS only -->
<?php $ttl=0; foreach ($server as $k) :
$ttl += $k->komisi
?>
<?php endforeach ?>
<div class="card">
    <div class="card-header">
        <h5>Total Terima Orderan Takemori & Soondobu</h5>
        <h5>
            <?= date('d-F-Y',strtotime($tgl1)) ?> ~
            <?= date('d-F-Y',strtotime($tgl2))?>
        </h5>
    </div>
    <div class="card-body">
        {{-- --}}
        <table width="100%" class="table" id="tb-item">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Server</th>
                    <th>M</th>
                    <th>E</th>
                    <th>SP</th>
                    <th style="text-align: right">Total Orderan <br>
                        (
                        <?= number_format($ttl,0) ?>)
                    </th>
                    <th style="text-align: right">Bonus</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;
                foreach ($server as $k) : ?>
                <tr>
                    <td>
                        <?= $i++ ?>
                    </td>
                    <td>
                        <a href="#" data-target="#orderan" class="btn_orderan" tgl1="<?= $tgl1 ?>" tgl2="<?= $tgl2 ?>"
                            admin="<?= $k->nama ?>" data-toggle="modal">
                            <?= strtolower($k->nama) ?>
                        </a>
                    </td>
                    <td>
                        <?=  $k->M == ''?'0':$k->M ?>
                    </td>
                    <td>
                        <?=  $k->E == '' ? '0':$k->E ?>
                    </td>
                    <td>
                        <?=  $k->Sp == '' ? '0' : $k->Sp ?>
                    </td>
                    <td style="text-align: right">
                        <?= number_format($k->komisi,0) ?>
                    </td>
                    <?php $tujuh =(($ttl* 0.07) / 7) * 1 ?>
                    <?php $komisi = ($tujuh / $ttl) * $k->komisi ?>
                    <td style="text-align: right">
                        <?= number_format($komisi,0) ?>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>

        </table>
    </div>

</div>
<script src="{{ asset('public/assets') }}/plugins/jquery/jquery.min.js"></script>
<script src="{{ asset('public/assets') }}/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{ asset('public/assets') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('public/assets') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('public/assets') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('public/assets') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('public/assets') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>

<script>
    $("#tb-item").DataTable({
        "lengthChange": true,
        "autoWidth": false,
        "paging": true
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>