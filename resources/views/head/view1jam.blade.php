<style>
    .table1 {
  width: 100%;
  margin-bottom: 1rem;
  color: #212529;
  background-color: transparent;
}

.table1 th,
.table1 td {
  padding: 0.75rem;
  vertical-align: top;
  border-top: 1px solid #dee2e6;
}

.table1 thead th {
  vertical-align: bottom;
  border-bottom: 2px solid #dee2e6;
}

.table1 tbody + tbody {
  border-top: 2px solid #dee2e6;
}

.table1-sm th,
.table1-sm td {
  padding: 0.3rem;
}
</style>
<div class="row mb-2">
    <div class="col-md-5 offset-md-7">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text bg-info text-white"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" id="search_history" class="form-control" placeholder="Cari Table / No Order...">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table1" id="tableJam">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No Order</th>
                    <th>Table</th>
                    <th>Menu</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Time Order</th>
                    <th>Time Selesai</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;
            foreach ($tb_order as $t) : ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $t->no_order ?></td>
                    <td>
                        @php
                            $prefix = 'Meja';
                            if ($t->id_distribusi == 2) { $prefix = 'Gojek'; }
                            if ($t->id_distribusi == 3) { $prefix = 'AYCE'; }
                        @endphp
                        {{ $prefix }} {{ $t->no_meja }}
                    </td>
                    <td><?= $t->nm_menu ?></td>
                    <td><?= $t->qty ?></td>
                    <td><?= number_format($t->qty * $t->harga, 0) ?></td>
                    <?php $waktu1 = new DateTime($t->j_mulai); ?>
                    <?php $waktu2 = new DateTime($t->j_selesai); ?>
                    <td><?= $waktu1->format('h:i A') ?></td>
                    <td><?= $waktu2->format('h:i A') ?></td>
                </tr>
                <?php endforeach ?>

            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#search_history").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#tableJam tbody tr").filter(function() {
                // Ambil text dari kolom No Order (index 1) dan Table (index 2)
                var noOrder = $(this).find('td').eq(1).text().toLowerCase();
                var table = $(this).find('td').eq(2).text().toLowerCase();
                
                // Cari kecocokan di kedua kolom tersebut
                $(this).toggle(noOrder.indexOf(value) > -1 || table.indexOf(value) > -1);
            });
        });
    });
</script>
