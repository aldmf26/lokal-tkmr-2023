<h5 style="font-weight: bold">
    Nama server :
    <?= $admin ?>
</h5>
<br>
<?php $ttl = 0; foreach ($server as $k) : $ttl += $k->harga ?>
<?php endforeach ?>
<table width="100%" class="table" id="tb-server">
    <thead>
        <tr>
            <th>#</th>
            <th>Meja</th>
            <th>Menu</th>
            <th>Harga <br>
                (
                <?= number_format($ttl,0) ?>)
            </th>
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
                <?= $k->nm_meja ?>
            </td>
            <td>
                <?= $k->nm_menu ?>
            </td>
            <td>
                <?= number_format($k->harga,0) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>

</table>

<script src="{{ asset('public/assets') }}/plugins/jquery/jquery.min.js"></script>
<script src="{{ asset('public/assets') }}/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{ asset('public/assets') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('public/assets') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('public/assets') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset('public/assets') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('public/assets') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>

<script>
    $("#tb-server").DataTable({
        "lengthChange": true,
        "autoWidth": false,
        "paging": true
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>