<table class="table" x-data="{
    @foreach ($klasifikasi_pembayaran as $k)
    {{ $k->nm_klasifikasi }}: false, @endforeach
}">
    <thead>
        <th>No</th>
        <th>Meja</th>
        <th>Nama Menu</th>
        <th width="15%">Qty</th>
        <th style="text-align: center;">Harga</th>
        <th>Total Harga</th>
        <th>Aksi</th>
    </thead>
    <tbody>
        <?php $i = 1;
        $l = 1;
        $qty = 0;
        $harga = 0;
        $total2 = 0;
        foreach ($order2 as $o) :

        if ($o->nm_menu == '') {
                $id_distribusi = $o->id_distribusi;
            }else{
            $qty += $o->qty;
            $harga += $o->harga;
            $id_distribusi = $o->id_distribusi;
            $total2 += $o->qty * $o->harga;
            }

        ?>
        @if ($o->nm_menu == '')
        <input name="qty[]" type="hidden" max="<?= $o->qty ?>" min="0" detail="<?= $no ?>"
            class="text-center form-control" value="<?=  $o->qty ?>">
        <input type="hidden" max="<?= $o->qty ?>" min="0" detail="<?= $no ?>" class="text-center qty form-control"
            value="<?=  $o->qty ?>">
        <input name="harga[]" type="hidden" class="harga<?= $no ?>" value="<?= $o->harga ?>">
        <input type="hidden" name="id_order[]" value="<?= $o->id_order ?>">
        <input type="hidden" name="id_harga[]" value="<?= $o->id_harga ?>">
        <input type="hidden" name="id_meja[]" value="<?= $o->id_meja ?>">
        @else
        <tr>
            @php
            $no = $i++;
            @endphp

            <td>{{$no}}</td>
            <td style="white-space: nowrap">
                <?= $o->nm_meja ?>
            </td>
            <td>
                <?= $o->nm_menu ?>
            </td>
            <td style="white-space: nowrap;">
                <!-- <a href=""><i class="fas fa-minus"></i></a> -->
                <input name="qty[]" type="number" max="<?= $o->qty ?>" min="0" detail="<?= $no ?>"
                    class="text-center qty form-control" value="<?= $o->qty ?>">
                <!-- <a href=""><i class="fas fa-plus"></i></a> -->

                <input name="harga[]" type="hidden" class="harga<?= $no ?>" value="<?= $o->harga ?>">
                <input type="hidden" name="id_order[]" value="<?= $o->id_order ?>">
                <input type="hidden" name="id_harga[]" value="<?= $o->id_harga ?>">
                <input type="hidden" name="id_meja[]" value="<?= $o->id_meja ?>">
            </td>
            <td style="text-align: center;">
                <?= number_format($o->harga, 0) ?>
            </td>
            <td style="text-align: center;" class="total<?= $no ?>">
                <?= number_format($o->qty * $o->harga, 0) ?>
            </td>
            <td><input type="hidden" class="tl" id="total_id<?= $no ?>" value="<?= $o->qty * $o->harga ?>">
            </td>
        </tr>
        @endif
        <?php endforeach ?>

        {{-- majo --}}
        <?php
        $qty_majo = 0;
        $total2_majo =0;
        foreach ($majo as $o):
        $qty_majo += $o->jumlah;
        $total2_majo += $o->jumlah * $o->harga;
        ?>
        <tr>
            @php
            $no = $i++;
            @endphp
            <td>{{$no}}</td>
            <td>{{$o->nm_meja}}</td>
            <td>{{$o->nm_produk}}</td>
            <td>
                <input name="qty_majo[]" type="number" max="{{$o->jumlah}}" min="0" detail="<?= $no ?>"
                    class="text-center qty form-control" value="{{$o->jumlah}}" readonly>
            </td>
            <td style="text-align: center;">
                <?= number_format($o->harga, 0) ?>
                <input name="harga[]" type="hidden" class="harga<?= $no ?>" value="<?= $o->harga ?>">
            </td>
            <td style="text-align: center;" class="total<?= $no ?>">
                <?= number_format($o->jumlah * $o->harga, 0) ?>
            </td>
            <td>
                <input type="hidden" class="tl_majo" id="total_id<?= $no ?>" value="<?= $o->jumlah * $o->harga ?>">
                <input type="hidden" name="id_pembelian[]" value="{{$o->id_pembelian}}">
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
    <?php $tb_dis = DB::table('tb_distribusi')
        ->where('id_distribusi', $id_distribusi)
        ->first(); ?>
    <tbody>
        <tr>
            <th style=" background-color: #25C584;color:white;font-size: 16px;" colspan="2">Subtotal</th>
            <th style="background-color: #25C584;color:white;font-size: 16px;"></th>
            <th style="background-color: #25C584;color:white;font-size: 16px;" class="total_qty">
                <?= $qty ?>
            </th>
            <th style="background-color: #25C584;color:white;font-size: 16px;text-align: center;"> <input type="hidden"
                    id="hrg" value="<?= $harga ?>"></th>
            <th style="background-color: #25C584;color:white;font-size: 16px; text-align: center; " class="total_hrg">
                <?= number_format($total2 + $total2_majo, 0) ?>
            </th>
            <th style="background-color: #25C584;color:white;font-size: 16px;">
                <input type="hidden" class="ttl_hrg" id="ttl_hrg" value="<?= $total2 ?>">
                <input type="hidden" class="ttl_hrg2" id="ttl_hrg2" value="<?= $total2 ?>">
                <input type="hidden" class="order" value="<?= $no ?>">
                <input type="hidden" name="id_distribusi" value="<?= $id_distribusi ?>">
                <input type="hidden" name="total_majo" id="ttl_majo" value="<?= $total2_majo ?>">
            </th>
        </tr>
    </tbody>
    <input type="hidden" id="a_okr" value="<?= $tb_dis->ongkir ?>">
    <input type="hidden" id="a_ser" value="<?= $tb_dis->service ?>">

    <?php $batas = DB::table('tb_batas_ongkir')->first(); ?>
    <?php if ($total2 < $batas->rupiah) : ?>
    <?php if ($tb_dis->ongkir == 'Y') :  ?>
    <?php $ongkir = $ongkir_bayar[0]->rupiah; ?>
    <?php else : ?>
    <?php $ongkir = '0'; ?>
    <?php endif ?>
    <?php else : ?>
    <?php $ongkir = '0'; ?>
    <?php endif ?>
    <input type="hidden" id="batas" value="<?= $batas->rupiah ?>">
    <input type="hidden" id="ong" value="<?= $batas->rupiah ?>">
    <tbody>
        <?php $tb_dis = DB::table('tb_distribusi')
            ->where('id_distribusi', $id_distribusi)
            ->first(); ?>
        <?php if ($tb_dis->service == 'Y') : ?>
        <?php $service = $total2 * 0.07; ?>
        <?php else : ?>
        <?php $service = 0; ?>
        <?php endif ?>


        <?php if ($tb_dis->tax == 'Y') : ?>
        <?php $tax = ($total2 + $total2_majo +  $service + $ongkir) * 0.1; ?>
        <?php else : ?>
        <?php $tax = 0; ?>
        <?php endif ?>

        <?php $total = $total2 + $total2_majo + $service + $tax + $ongkir; ?>

        <?php
        
        
        $a = $total;
        $b = number_format(substr($a, -3), 0);

        if ($b == '00') {
            $c = $a;
            $round = '00';
        } elseif ($b < 1000) {
            $c = $a - $b + 1000;
            $round = 1000 - $b;
        }

        if (empty($disc->minimum_rp)) {
            $gran_total = $c;
        } else {
            if ($c < $disc->minimum_rp) {
           $gran_total = $c;
        }else {
            if ($disc->jenis == 'Persen') {
                $gran_total = $c * ((100 - $disc->disc) / 100);
            } else {
                $gran_total = $c * $disc->disc;
            }
        }
        }
        
        

        $d = $gran_total;
        $e = number_format(substr($d, -3), 0);

        if ($e == '00') {
            $f = $d;
            $round = '00';
        } elseif ($b < 1000) {
            $f = $d - $e + 1000;
            $round = 1000 - $e;
        }
        ?>
        @php
        $now = date('Y-m-d');
        // $diskon = DB::table('tb_discount')
        // ->where([['lokasi', Session::get('id_lokasi')], ['dari', '<=', $now], ['expired', '>=' , $now]]) ->get();
            @endphp

            <tr>

                <td colspan="2">Voucher</td>
                <td></td>
                <td></td>
                <td>-</td>
                <td width="30%"><input type="text" name="kd_voucher" class="form-control kd_voucher"> </td>
                <td style="white-space: nowrap;">
                    <a id="cek_voucher" class="btn btn-info btn-sm"><i class="fas fa-sync-alt"></i> cek</a>
                    <a id="btl_voucher" class="btn btn-danger btn-sm"><i class="fas fa-undo-alt"></i> batal</a>
                </td>
            </tr>
            <tr>
                <td colspan="5"></td>
                <td>
                    rp voucher <br>
                    <input type="text" class="form-control" id="rupiah" name="voucher" readonly>
                    <input type="hidden" class="form-control ttl_hrg" name="sub" value="<?= $total2 ?>" readonly>
                    <input type="hidden" class="form-control servis1" name="service" value="<?= $service ?>" readonly>
                    <input type="hidden" class="form-control tax1" name="tax" value="<?= $tax ?>" readonly>

                    <input type="hidden" class="form-control servis2" value="<?= $service ?>" readonly>
                    <input type="hidden" class="form-control tax2" value="<?= $tax ?>" readonly>

                    <input type="hidden" class="form-control" name="ongkir" value="<?= $ongkir ?>" readonly>
                </td>
                <td></td>
            </tr>

            {{-- diskon --}}

            {{-- --}}
            <?php if ($tb_dis->service == 'Y') : ?>
            <tr>
                <td colspan="3">Service charge</td>
                <td></td>
                <td>-</td>
                <td width="20%" class="servis">
                    <?= number_format($service, 0) ?>
                </td>
                <td></td>
            </tr>
            <?php else : ?>
            <?php endif ?>

            <?php if ($tb_dis->ongkir == 'Y') :  ?>
            <tr>
                <td colspan="2">Ongkir </td>
                <td></td>
                <td></td>
                <td>-</td>
                <td width="20%" class="ongkir">
                    <?= number_format($ongkir, 0) ?>
                </td>
                <td></td>
            </tr>
            <?php else : ?>
            <?php endif ?>

            <tr>
                <td colspan="2">Tax </td>
                <td></td>
                <td></td>
                <td>-</td>
                <td width="20%" class="tax">
                    <?= number_format($tax, 0) ?>
                </td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2" style="font-weight: bold;">Total </td>
                <td></td>
                <td></td>
                <td>-</td>
                <td width="20%" class="ttl_ttp_sebelum" style="font-weight: bold;">
                    <?= number_format($c, 0) ?>
                </td>
                <td>
                    <input type="hidden" id="hidden_ttl_ttp_sebelum" value=" <?= number_format($c, 0) ?>">
                </td>
            </tr>
          
            <tr>
                <td colspan="2">Discount</td>
                <td></td>
                <td></td>
                <td>-</td>
                <td width="20%">
                    <input type="hidden" id="jenis_discount" value="{{$disc->jenis ?? ''}}">
                    <input type="hidden" id="minimum_rp" value="{{$disc->minimum_rp ?? 0 }}">
                    <input type="hidden" id="disc" value="{{$disc->disc ?? 0}}">
                    @if (empty($disc->minimum_rp))
                    <input type="text" value="" class="form-control" readonly>
                    <input type="hidden" name="discount" value="0" class="form-control" readonly>
                    @else
                    @if ($c < $disc->minimum_rp)
                    <input type="text" value="" class="form-control" readonly>
                    <input type="hidden" name="discount" value="0" class="form-control" readonly>


                    @else
                    <input type="text"
                        value="{{ $disc->jenis == 'Persen' ? $disc->disc . '%' : 'Rp.' . $disc->disc }}"
                        class="form-control" readonly>
                    <input type="hidden" name="discount" value="{{$disc->disc}}" class="form-control" readonly>
                    @endif
                    @endif
                    

                </td>
                <td></td>
            </tr>

            <tr>
                <td colspan="2">Dp</td>
                <td></td>
                <td></td>
                <td>-</td>
                <td width="20%">
                    <select name="id_dp" id="data_dp" class="form-control select2bs4">
                        <option value="0">- Pilih DP -</option>
                        <?php foreach ($dp as $dp) : ?>
                        <option value="<?= $dp->id_dp ?>">
                            <?= $dp->kd_dp ?> |
                            <?= $dp->ket ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="id_dp" id="id_dp">
                    <input type="hidden" name="round" class="round" value="<?= $round ?>">
                    <input type="hidden" name="round2" class="round2" value="<?= $round ?>">
                    <input type="hidden" name="sDiskon" class="sDiskon" value="<?= $round ?>">
                    <input type="hidden" name="vDiskon" class="vDiskon" value="">
                    <input type="hidden" id="jumlah_dp">

                    <input type="hidden" name="kode_dp" id="kode_dp">
                </td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5"></td>
                <td>
                    <input type="text" id="view_dp" name="dp" class="form-control" readonly>
                </td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">Gosend</td>
                <td></td>
                <td></td>
                <td width="20%"><input type="text" name="ket_gosen" class="form-control" placeholder="keterangan"></td>
                <td width="20%"><input type="number" class="form-control" name="gosen" id="gosen" value="0"></td>
                <td></td>
            </tr>

            <tr>
                <td style="font-weight: bold;" colspan="2">Grand Total</td>
                <td></td>
                <td style="font-weight: bold;"></td>
                <td width="20%"></td>
                <td width="20%">

                    <input type="number" id="total1" name="total_dibayar" class="form-control" value="<?= $f  ?>"
                        readonly>
                    <input type="hidden" id="totalTetap" name="totalTetap" class="form-control" value="<?= $f ?>"
                        readonly>
                    <input type="hidden" id="tvoucher" name="tvoucher" class="form-control" value="<?= $f ?>" readonly>
                    <input type="hidden" id="total2" name="total_orderan" class="form-control" value="<?= $f ?>"
                        readonly>


                </td>
                <td></td>
            </tr>
            <input type="hidden" id="kembalian1" name="kembalian1" class="form-control" value="0" readonly>


            <tr>
                <td style="font-weight: bold;" colspan="3">Cash</td>
                <td>:</td>
                <td colspan="2"><input type="number" id="cash" name="pembayaran[]" value="0"
                        class="form-control pembayaran">
                    <input type="hidden" name="id_akun[]" value="13">
                    <input type="hidden" class="form-control" placeholder="Nama pengirim" name="nm_pengirim[]">
                </td>
                <td><button id="btn_bayar" class="btn btn-info btn-sm save_btn" disabled><i
                            class="fas fa-cash-register"></i> Save</button></td>
    
            </tr>
            @foreach ($klasifikasi_pembayaran as $k)
                <tr>
                    <td style="font-weight: bold;" colspan="6">
                        {{ $k->nm_klasifikasi }}
                        <button type="button" class="btn btn-info btn-sm btn-buka float-right"
                            @click="{{ $k->nm_klasifikasi }} = ! {{ $k->nm_klasifikasi }}">
                            <i class="fas fa-caret-down"></i>
                        </button>
                    </td>
                </tr>
                @php
                    $akun = DB::table('akun_pembayaran')
                        ->where('id_klasifikasi', $k->id_klasifikasi_pembayaran)
                        ->get();
                @endphp
    
                @foreach ($akun as $a)
                    <tr x-show="{{ $k->nm_klasifikasi }}">
                        <td colspan="3">{{ $a->nm_akun }}</td>
                        <td>:</td>
                        <td colspan="2"><input type="number" value="0" name="pembayaran[]"
                                class="form-control pembayaran">
                        </td>
                        <td>
                            <input type="hidden" name="id_akun[]" value="{{ $a->id_akun_pembayaran }}">
                            <input type="text" class="form-control "
                                {{ $k->id_klasifikasi_pembayaran == '4' ? '' : 'hidden' }} placeholder="Nama pengirim"
                                name="nm_pengirim[]">
                        </td>
                    </tr>
                @endforeach
            @endforeach

    </tbody>

</table>