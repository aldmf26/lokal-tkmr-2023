<?php
$total_tagihan = $dt_pembayaran->total_bayar;
// Ambil semua pembayaran yang sudah disimpan
$existing_payments = DB::table('pembayaran as a')
    ->join('akun_pembayaran as b', 'a.id_akun_pembayaran', '=', 'b.id_akun_pembayaran')
    ->join('klasifikasi_pembayaran as c', 'b.id_klasifikasi', '=', 'c.id_klasifikasi_pembayaran')
    ->select('a.*', 'b.nm_akun', 'c.nm_klasifikasi')
    ->where('a.no_nota', $no_order)
    ->get();

// Index existing payments by id_akun
$existing_map = [];
foreach ($existing_payments as $ep) {
    $existing_map[$ep->id_akun_pembayaran] = $ep->nominal;
}

// Ambil semua akun pembayaran (pakai leftJoin agar Cash tanpa klasifikasi tetap muncul)
$akun_pembayaran = DB::table('akun_pembayaran as a')
    ->leftJoin('klasifikasi_pembayaran as b', 'a.id_klasifikasi', '=', 'b.id_klasifikasi_pembayaran')
    ->select('a.*', DB::raw("COALESCE(b.nm_klasifikasi, 'Cash') as nm_klasifikasi"))
    ->orderBy('a.id_akun_pembayaran')
    ->get();

// Hitung total yang sudah dibayar
$total_sudah_dibayar = array_sum($existing_map);
?>

<div class="row">
    <input type="hidden" id="no_order" name="no_order" value="<?= $no_order ?>">
    <input type="hidden" id="edit_total_tagihan" value="<?= $total_tagihan ?>">

    <div class="col-12 text-center mb-3">
        <div class="p-3 rounded" style="background: #e1f5fe; border: 1px solid #01579b;">
            <label class="mb-1 text-muted">Total Tagihan:</label>
            <h3 class="font-weight-bold" style="color: #0d47a1;">Rp <?= number_format($total_tagihan) ?></h3>
        </div>
    </div>

    <div class="col-12">
        <label style="font-weight: 800; color: #555;" class="mb-2">Nominal Pembayaran per Metode:</label>
        <?php foreach ($akun_pembayaran as $akun): ?>
        <div class="form-group mb-2">
            <label style="font-size:0.85rem; color:#666;"><?= $akun->nm_klasifikasi ?> — <?= $akun->nm_akun ?></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Rp</span>
                </div>
                <input
                    type="number"
                    name="nominal_akun[<?= $akun->id_akun_pembayaran ?>]"
                    class="form-control nominal-edit-input"
                    value="<?= $existing_map[$akun->id_akun_pembayaran] ?? 0 ?>"
                    min="0"
                    placeholder="0"
                >
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="col-12 mt-2">
        <div class="p-2 rounded" style="background: #f5f5f5; border: 1px solid #ddd;">
            <div class="d-flex justify-content-between">
                <span style="font-weight: 600;">Total Diinput:</span>
                <span id="edit_total_input" style="font-weight: 700; color: #1565c0;">Rp <?= number_format($total_sudah_dibayar) ?></span>
            </div>
            <div class="d-flex justify-content-between mt-1">
                <span style="font-weight: 600;">Kembalian:</span>
                <span id="edit_kembalian" style="font-weight: 700; color: #2e7d32;">Rp <?= number_format(max(0, $total_sudah_dibayar - $total_tagihan)) ?></span>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    function hitungEditPembayaran() {
        var total_tagihan = parseInt(document.getElementById('edit_total_tagihan').value) || 0;
        var inputs = document.querySelectorAll('.nominal-edit-input');
        var total_input = 0;
        inputs.forEach(function(inp) {
            total_input += parseInt(inp.value) || 0;
        });
        document.getElementById('edit_total_input').textContent = 'Rp ' + total_input.toLocaleString('id-ID');
        var kembalian = total_input - total_tagihan;
        document.getElementById('edit_kembalian').textContent = 'Rp ' + Math.max(0, kembalian).toLocaleString('id-ID');

        // Enable/disable tombol edit berdasarkan apakah total input >= total tagihan
        var btnEdit = document.getElementById('btn_e_pembayaran');
        if (btnEdit) {
            if (total_input >= total_tagihan) {
                btnEdit.removeAttribute('disabled');
            } else {
                btnEdit.setAttribute('disabled', 'true');
            }
        }
    }

    // Attach listener setelah DOM ready
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('nominal-edit-input')) {
            hitungEditPembayaran();
        }
    });

    // Inisialisasi
    setTimeout(hitungEditPembayaran, 100);
})();
</script>
