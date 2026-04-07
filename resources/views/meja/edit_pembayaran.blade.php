<?php
$total_tagihan = $dt_pembayaran->total_bayar;
// Cek akun yang saat ini terpakai (ambil satu saja karena sekarang single payment)
$current_payment = DB::table('pembayaran')->where('no_nota', $no_order)->first();
$current_id_akun = $current_payment ? $current_payment->id_akun_pembayaran : '13';
// Join dengan klasifikasi agar informatif
$akun_pembayaran = DB::table('akun_pembayaran as a')
    ->join('klasifikasi_pembayaran as b', 'a.id_klasifikasi', '=', 'b.id_klasifikasi_pembayaran')
    ->select('a.*', 'b.nm_klasifikasi')
    ->get();
?>
<div class="row">
    <input type="hidden" id="no_order" name="no_order" value="<?= $no_order ?>">
    <input type="hidden" name="nominal" value="<?= $total_tagihan ?>">

    <div class="col-12 text-center mb-3">
        <div class="p-3 rounded" style="background: #e1f5fe; border: 1px solid #01579b;">
            <label class="mb-1 text-muted">Total Tagihan:</label>
            <h3 class="font-weight-bold" style="color: #0d47a1;">Rp <?= number_format($total_tagihan) ?></h3>
        </div>
    </div>

    <div class="col-12">
        <div class="form-group border-top pt-3">
            <label style="font-weight: 800; color: #555;">Pilih Metode Pembayaran Baru:</label>
            <select name="id_akun" class="form-control select2-edit" style="width: 100%;">
                @foreach ($akun_pembayaran as $akun)
                    <option value="{{ $akun->id_akun_pembayaran }}" 
                        {{ $akun->id_akun_pembayaran == $current_id_akun ? 'selected' : '' }}>
                        {{ $akun->nm_klasifikasi }} - {{ $akun->nm_akun }}
                    </option>
                @endforeach
            </select>
            <small class="text-info mt-1 d-block"><i class="fas fa-info-circle mr-1"></i> Pilih metode untuk memindahkan seluruh nominal di atas.</small>
        </div>
    </div>
</div>


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
