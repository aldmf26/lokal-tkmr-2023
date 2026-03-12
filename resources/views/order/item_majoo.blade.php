<div class="row">
    <div class="col-sm-4 col-md-4">
        <?php if (empty($value->foto)) : ?>
        <img class="img-thumbnail" width="170" src="{{ asset_custom('') }}/tb_menu/notfound.png" alt="">
        <?php else : ?>
        <img class=" img-thumbnail" width="170" src="{{ asset_custom('') }}/tb_menu/notfound.png" alt="">
        <?php endif ?>

    </div>
    <div class="col-sm-8 col-md-8">
        <h6 class="mt-2">
            <?= $value->nm_produk ?>
        </h6>
        <h6 style="font-weight: bold; color: #00B7B5; font-size: 20px;">Rp.
            <?= number_format($value->harga) ?>
        </h6>
        <p>Tersedia
            <?= $value->debit - ($value->kredit + $value->kredit_penjualan) ?> Stok Barang
        </p>
        <div class="row">
            <div class="col-sm-3 col-md-3">
                <div class="form-group">
                    <label for="">Jumlah *</label>
                    <input type="number" id="cart_jumlah" min="1"
                        max="<?= $value->debit - ($value->kredit + $value->kredit_penjualan) ?>" name="jumlah"
                        class="form-control" value="1" required="">
                    <input type="hidden" id="cart_id" name="id" value="<?= $value->id_produk ?>">
                </div>
            </div>
            <div class="col-sm-6 col-md-6">
                <div class="form-group">
                    <label for="">Satuan</label>
                    <input type="text" id="cart_satuan" name="satuan" value="<?= $value->satuan ?>" class="form-control"
                        readonly>
                </div>
            </div>
        </div>
    </div>
</div>
