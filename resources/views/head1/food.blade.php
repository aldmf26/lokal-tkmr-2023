<div class="table-responsive">


    <table class="table " width="100%">
        <thead>
            <tr>
                <th class="sticky-top th-atas">#</th>
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
            <?php $i = 1;
            foreach ($menu_food as $m) : ?>
                <tr>
                    <td class="bg-info"><?= $i++ ?></td>
                    <td class="bg-info" style="vertical-align: middle;">

                    </td>
                    <td class="bg-info" style="white-space: nowrap;">
                        <?= $m->nm_menu ?>
                    </td>

                    <td class="bg-info " style="white-space: nowrap;">
                        <button type="button" class="muncul btn btn-danger btn-round btn-sm status<?= $m->id_menu ?>" id_menu="<?= $m->id_menu ?>" value="Show"> <span>View</span> <span class="badge badge-light"><?= $m->qty_all + $m->qty_all3 ?></span></button>
                        <button type="button" class="muncul2 btn btn-warning btn-round text-white btn-sm status2<?= $m->id_menu ?>" id_menu="<?= $m->id_menu ?>" value="Show"> <span>View</span> <span class="badge badge-danger"><?= $m->qty_all2 ?></span></button>
                    </td>
                    <td class="bg-info"></td>
                    <td class="bg-info"></td>
                    <?php foreach ($tb_koki as $k) : ?>
                        <td class="bg-info" style="vertical-align: middle;">
                            <?= $k->nama ?>
                        </td>
                    <?php endforeach ?>
                    <td colspan="50" class="bg-info"></td>
                </tr>
        <tbody class="mytr<?= $m->id_menu ?> tutup">

        </tbody>

    <?php endforeach ?>
    </tbody>
    </table>
</div>
