<div class="table-responsive">


    <table class="table " width="100%">
        <thead>
            <tr>
                <th class="sticky-top th-atas">Meja</th>
                <th class="sticky-top th-atas">Menu</th>
                <th class="sticky-top th-atas">Request</th>
                <th class="sticky-top th-atas">Qty</th>
                <th class="sticky-top th-atas">Status</th>
                <?php foreach ($tb_koki as $k) : ?>
                    <th class="sticky-top th-atas">
                        <?= $k->nama ?>
                    </th>
                <?php endforeach ?>
                <th class="sticky-top th-atas">Time In</th>
            </tr>
        </thead>
        <tbody style="font-size: 18px;">
            <?php foreach ($meja as $m) : ?>
                <tr>
                    <td class="bg-info">
                        <?= $m->nm_meja ?>
                    </td>
                    <td class="bg-info " style="white-space: nowrap;">
                        <button type="button" class="muncul btn btn-danger btn-round  status<?= $m->id_meja ?>" id_meja="<?= $m->id_meja ?>" value="Show"> <span>View</span> <span class="badge badge-light"><?= $m->qty_all ?></span></button>
                        <button type="button" class="muncul2 btn btn-warning btn-round text-white  status2<?= $m->id_meja ?>" id_meja="<?= $m->id_meja ?>" value="Show">View<span class="badge badge-danger"><?= $m->qty_all2 ?></span></button>
                    </td>
                    <td class="bg-info"></td>
                    <td class="bg-info"></td>
                    <td class="bg-info"></td>
                    <?php foreach ($tb_koki as $k) : ?>
                        <td class="bg-info" style="vertical-align: middle;">
                            <?= $k->nama ?>
                        </td>
                    <?php endforeach ?>
                    <td colspan="50" class="bg-info"></td>
                </tr>
        <tbody class="mytr<?= $m->id_meja ?> tutup">

        </tbody>

    <?php endforeach ?>
    </tbody>
    </table>
</div>
