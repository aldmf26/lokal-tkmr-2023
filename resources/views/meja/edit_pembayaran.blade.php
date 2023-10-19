<?php
$total_tagihan = $dt_pembayaran->total_bayar;
?>
<div class="row">
    <input type="hidden" id="no_order" name="no_order" value="<?= $no_order ?>">

    <div class="col-12">
        <div class="form-group">
            <label>Total Tagihan</label>
            <input type="number" class="form-control" id="total_tagihan" value="<?= $total_tagihan ?>" disabled>
        </div>
    </div>
    <table width="100%" style="padding: 2px;">
        @php
            $cash = DB::selectOne(" SELECT a.id_akun_pembayaran, a.nm_akun, b.nominal
                FROM akun_pembayaran as a 
                left join pembayaran as b on b.id_akun_pembayaran = a.id_akun_pembayaran and b.no_nota = '$no_order'
                where a.id_akun_pembayaran = '13'
                ");
        @endphp
        <tr>
            <td>{{ $cash->nm_akun }}</td>
            <td width="5%">:</td>
            <td>
                <input type="number" name="pembayaran[]" value="{{ empty($cash->nominal) ? '0' : $cash->nominal }}"
                    class="form-control pembayaran ">
            </td>
            <td>
                <input type="hidden" name="id_akun[]" value="{{ $cash->id_akun_pembayaran }}">
            </td>
        </tr>
        @foreach ($klasifikasi_pembayaran as $k)
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td style="font-weight: bold; text-align: center" colspan="3">
                    {{ $k->nm_klasifikasi }}
                </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            @php
                $akun = DB::select(" SELECT a.id_akun_pembayaran, a.nm_akun, b.nominal
                FROM akun_pembayaran as a 
                left join pembayaran as b on b.id_akun_pembayaran = a.id_akun_pembayaran and b.no_nota = '$no_order'
                where a.id_klasifikasi = '$k->id_klasifikasi_pembayaran'
                ");
            @endphp
            @foreach ($akun as $a)
                <tr>
                    <td>{{ $a->nm_akun }}</td>
                    <td width="5%">:</td>
                    <td>
                        <input type="number" name="pembayaran[]" value="{{ empty($a->nominal) ? '0' : $a->nominal }}"
                            class="form-control pembayaran ">
                    </td>
                    <td>
                        <input type="hidden" name="id_akun[]" value="{{ $a->id_akun_pembayaran }}">
                    </td>
                </tr>
            @endforeach
        @endforeach
    </table>


    {{-- <div class="col-12">
        <div class="form-group">
            <label>Cash </label>
            <input type="number" name="cash" id="cash" value="<?= $dt_pembayaran->cash ?>"
                class="form-control input_edit_pembayaran">
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>BCA Debit</label>
            <input type="number" name="d_bca" id="d_bca" value="<?= $dt_pembayaran->d_bca ?>"
                class="form-control input_edit_pembayaran">
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>BCA Kredit</label>
            <input type="number" name="k_bca" id="k_bca" value="<?= $dt_pembayaran->k_bca ?>"
                class="form-control input_edit_pembayaran">
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>Mandiri Debit</label>
            <input type="number" name="d_mandiri" id="d_mandiri" value="<?= $dt_pembayaran->d_mandiri ?>"
                class="form-control input_edit_pembayaran">
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>Mandiri Kredit</label>
            <input type="number" name="k_mandiri" id="k_mandiri" value="<?= $dt_pembayaran->k_mandiri ?>"
                class="form-control input_edit_pembayaran">
        </div>
    </div> --}}

</div>
