<!-- CSS only -->

<div class="card">
    <div class="card-header">
        
        <a href="<?= route('export_itemMajo') ?>?tgl1=<?= $tgl1 ?>&tgl2=<?= $tgl2 ?>"
            class="btn btn-sm btn-info float-left">Export</a>
    </div>
    <div class="card-body">
        <?php $total = 0;
        foreach ($kategori as $k) : 
        $total += $k->qty * $k->harga;
        ?>
                <?php endforeach ?>
        
        <table width="100%" class="table" id="tb-item-majo">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Menu</th>
                    <th>Qty</th>
                    <th>Subtotal <br>
                    <?= number_format($total,0) ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;
                foreach ($kategori as $k) : ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $k->nm_produk ?></td>
                    <td><?= $k->qty ?></td>
                    <td><?= number_format($k->qty * $k->harga, 0) ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>

        </table>
    </div>

</div>
<script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets') }}/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>

<script>
    $("#tb-item-majo").DataTable({
        "lengthChange": true,
        "autoWidth": false,
        "paging": true
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
