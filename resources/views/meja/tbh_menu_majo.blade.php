<input type="hidden" id="kd_order_majo" name="kd_order" value="{{ $order->no_order }}">
<input type="hidden" id="meja_majo" name="meja" value="{{ $order->id_meja }}  ">
<input type="hidden" id="warna_majo" name="warna" value="{{ $order->warna }}  ">
<input type="hidden" id="id_dis_majo" name="id_dis" value="{{ $order->id_distribusi }}">
<div class="row">
    <div class="col-lg-4">
        <label for="">Menu</label>
        <select name="id_harga[]" class="form-control id_harga_majo id_harga_majo1 select2bs4" detail="1" required>
            <option value="">-Pilih Menu-</option>
            <?php foreach ($produk as $m) : 
            if (($m->debit - ($m->kredit + $m->kredit_penjualan)) <= 0) {
                continue;
            }
            ?>
            <option value="{{ $m->id_produk }}">
                {{ $m->nm_produk }}
            </option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="col-lg-2">
        <label for="">Qty</label>
        <input type="number" name="qty" id="qty_majo" value="1" min="1" class="form-control">
    </div>
    <div class="col-lg-2">
        <label for="">Harga</label>
        <input type="text" name="harga" id="hrg_majo" class="form-control harga_majo harga_majo1" detail="1" readonly>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).on("change", ".id_harga_majo", function() {

            var id_harga = $(this).val();
            var detail = $(this).attr('detail')
            $.ajax({
                url: "{{ route('get_harga_majo') }}?id_harga=" + id_harga,
                method: "GET",
                dataType: "json",
                success: function(data) {
                    $(".harga_majo" + detail).val(data);

                }
            });

        });


        // disini 


    });
</script>