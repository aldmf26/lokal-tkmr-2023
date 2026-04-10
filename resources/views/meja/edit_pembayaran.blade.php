<?php
$total_tagihan = $dt_pembayaran->total_bayar;

// Ambil semua pembayaran yang sudah disimpan
$existing_payments = DB::table('pembayaran as a')
    ->leftJoin('akun_pembayaran as b', 'a.id_akun_pembayaran', '=', 'b.id_akun_pembayaran')
    ->select('a.*', 'b.nm_akun')
    ->where('a.no_nota', $no_order)
    ->get();

// Index existing payments by id_akun_pembayaran → nominal
$existing_map = [];
foreach ($existing_payments as $ep) {
    $existing_map[$ep->id_akun_pembayaran] = $ep->nominal;
}

// Ambil semua akun pembayaran (leftJoin agar Cash tetap muncul)
$akun_pembayaran = DB::table('akun_pembayaran as a')
    ->leftJoin('klasifikasi_pembayaran as b', 'a.id_klasifikasi', '=', 'b.id_klasifikasi_pembayaran')
    ->select('a.*', DB::raw("COALESCE(b.nm_klasifikasi, 'Cash') as nm_klasifikasi"))
    ->orderBy('a.id_akun_pembayaran')
    ->get();

$total_sudah_dibayar = $existing_map ? array_sum($existing_map) : 0;
$kembalian_awal = max(0, $total_sudah_dibayar - $total_tagihan);

// Assign icon per klasifikasi
$icons = [
    'Cash' => 'fas fa-money-bill-wave',
    'Debit' => 'fas fa-credit-card',
    'Kredit' => 'fas fa-credit-card',
    'Transfer' => 'fas fa-exchange-alt',
    'QRIS' => 'fas fa-qrcode',
    'GoPay' => 'fas fa-wallet',
    'OVO' => 'fas fa-wallet',
    'Dana' => 'fas fa-wallet',
];
$colors = [
    'Cash' => '#27ae60',
    'Debit' => '#2980b9',
    'Kredit' => '#8e44ad',
    'Transfer' => '#16a085',
    'QRIS' => '#e67e22',
    'GoPay' => '#00aed6',
    'OVO' => '#4c3494',
    'Dana' => '#118EEA',
];
function getAttr($map, $key, $default)
{
    foreach ($map as $k => $v) {
        if (stripos($key, $k) !== false)
            return $v;
    }
    return $default;
}
?>

<style>
    .ep-payment-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 10px;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
        cursor: text;
    }

    .ep-payment-card:focus-within {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
    }

    .ep-payment-card.has-value {
        border-color: #27ae60;
        background: #f0fdf4;
    }

    .ep-icon-badge {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #fff;
        flex-shrink: 0;
    }

    .ep-label-group {
        flex: 1;
    }

    .ep-label-primary {
        font-weight: 700;
        font-size: 0.85rem;
        color: #2c3e50;
        line-height: 1.2;
    }

    .ep-label-sub {
        font-size: 0.72rem;
        color: #7f8c8d;
    }

    .ep-input-wrap {
        width: 155px;
        flex-shrink: 0;
    }

    .ep-input-wrap .input-group-text {
        background: #f0f3f7;
        border-right: none;
        color: #555;
        font-weight: 700;
        font-size: 0.8rem;
    }

    .ep-input-wrap .form-control {
        border-left: none;
        font-size: 0.9rem;
        font-weight: 700;
        color: #2c3e50;
        padding-left: 4px;
    }

    .ep-input-wrap .form-control:focus {
        box-shadow: none;
        border-color: #ced4da;
    }

    .ep-summary-bar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 14px 16px;
        color: #fff;
        margin-top: 6px;
    }

    .ep-summary-bar .row-item {
        font-size: 0.8rem;
        opacity: 0.85;
    }

    .ep-summary-bar .row-value {
        font-size: 1rem;
        font-weight: 800;
    }

    .ep-summary-bar .kembalian-row .row-value {
        color: #a8f0c6;
    }

    .ep-tagihan-bar {
        background: linear-gradient(135deg, #2193b0, #6dd5ed);
        border-radius: 12px;
        padding: 12px 16px;
        color: #fff;
        margin-bottom: 12px;
    }
</style>

<input type="hidden" id="no_order" name="no_order" value="<?= $no_order ?>">
<input type="hidden" id="edit_total_tagihan" value="<?= $total_tagihan ?>">

{{-- Total Tagihan Header --}}
<div class="ep-tagihan-bar d-flex align-items-center justify-content-between">
    <div>
        <div style="font-size:0.75rem; opacity:0.85; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">
            Total Tagihan</div>
        <div style="font-size:1.4rem; font-weight:900; letter-spacing:0.5px;">Rp <?= number_format($total_tagihan) ?>
        </div>
    </div>
    <i class="fas fa-receipt" style="font-size:1.8rem; opacity:0.3;"></i>
</div>

{{-- Daftar Metode Pembayaran --}}
<div
    style="font-size:0.75rem; font-weight:700; color:#7f8c8d; text-transform:uppercase; letter-spacing:0.8px; margin-bottom:8px;">
    <i class="fas fa-pen mr-1"></i> Input Nominal per Metode
</div>

<?php foreach ($akun_pembayaran as $akun):
    $label = $akun->nm_klasifikasi;
    $icon = getAttr($icons, $label, 'fas fa-money-check-alt');
    $color = getAttr($colors, $label, '#95a5a6');
    $val = $existing_map[$akun->id_akun_pembayaran] ?? 0;
    $hasVal = $val > 0;
?>
<div class="ep-payment-card d-flex align-items-center gap-2 <?= $hasVal ? 'has-value' : '' ?>" style="gap:12px;">
    <div class="ep-icon-badge" style="background:<?= $color ?>;">
        <i class="<?= $icon ?>"></i>
    </div>
    <div class="ep-label-group">
        <div class="ep-label-primary"><?= $akun->nm_akun ?></div>
        <div class="ep-label-sub"><?= $label ?></div>
    </div>
    <div class="ep-input-wrap">
        <div class="input-group input-group-sm">
            <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
            </div>
            <input type="text" inputmode="numeric" name="nominal_akun[<?= $akun->id_akun_pembayaran ?>]"
                class="form-control nominal-edit-input text-right" value="<?= $val ?>" min="0" placeholder="0"
                step="1000">
        </div>
    </div>
</div>
<?php endforeach; ?>

{{-- Summary Bar --}}
<div class="ep-summary-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <div class="row-item">Total Diinput</div>
            <div class="row-value" id="edit_total_input">Rp <?= number_format($total_sudah_dibayar) ?></div>
        </div>
        <div class="text-right kembalian-row">
            <div class="row-item">Kembalian</div>
            <div class="row-value" id="edit_kembalian">Rp <?= number_format($kembalian_awal) ?></div>
        </div>
    </div>
</div>

<script>
    (function () {
        var allInputs = document.querySelectorAll('.nominal-edit-input');

        function hitungEditPembayaran() {
            var total_tagihan = parseInt(document.getElementById('edit_total_tagihan').value) || 0;
            var total_input = 0;

            allInputs.forEach(function (inp) {
                var val = parseInt(inp.value) || 0;
                total_input += val;
                var card = inp.closest('.ep-payment-card');
                if (card) card.classList.toggle('has-value', val > 0);
            });

            document.getElementById('edit_total_input').textContent = 'Rp ' + total_input.toLocaleString('id-ID');
            var kembalian = total_input - total_tagihan;
            document.getElementById('edit_kembalian').textContent = 'Rp ' + Math.max(0, kembalian).toLocaleString('id-ID');

            var btnEdit = document.getElementById('btn_e_pembayaran');
            if (btnEdit) {
                if (total_input >= total_tagihan) {
                    btnEdit.removeAttribute('disabled');
                } else {
                    btnEdit.setAttribute('disabled', 'true');
                }
            }
        }

        function applyExclusiveSelection(activeInput) {
            var val = parseInt(activeInput.value) || 0;

            if (val > 0) {
                // Ada nilai di input ini → reset & disable semua yang lain
                allInputs.forEach(function (inp) {
                    if (inp !== activeInput) {
                        inp.value = 0;
                        inp.setAttribute('disabled', 'true');
                        inp.closest('.ep-payment-card').classList.remove('has-value');
                        inp.closest('.ep-payment-card').style.opacity = '0.4';
                        inp.closest('.ep-payment-card').style.pointerEvents = 'none';
                    }
                });
            } else {
                // Input dikosongkan → re-enable semua
                allInputs.forEach(function (inp) {
                    inp.removeAttribute('disabled');
                    inp.closest('.ep-payment-card').style.opacity = '';
                    inp.closest('.ep-payment-card').style.pointerEvents = '';
                });
            }

            hitungEditPembayaran();
        }

        allInputs.forEach(function (inp) {
            inp.addEventListener('input', function () {
                applyExclusiveSelection(inp);
            });
        });

        // Inisialisasi state awal berdasarkan nilai yang sudah ada
        function initState() {
            var activeInput = null;
            allInputs.forEach(function (inp) {
                if ((parseInt(inp.value) || 0) > 0) activeInput = inp;
            });
            if (activeInput) applyExclusiveSelection(activeInput);
            hitungEditPembayaran();
        }

        setTimeout(initState, 100);
    })();
</script>