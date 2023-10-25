
<style>
    .invoice {
        margin: auto;
        width: 87mm;
        background: #FFF;
    }
</style>
<div class="invoice">
    <center>
        <?php if (Session::get('id_lokasi') == 1) { ?>
        <img width="100" src="{{ asset('public/assets') }}/pages/login/img/Takemori_new.jpg" alt="">
        <h3 align="center" style="margin-top: -1px;">TAKEMORI</h3>
        <?php } else { ?>
        <img width="100" src="{{ asset('public/assets') }}/pages/login/img/soondobu.jpg" alt="">
        <h3 align="center" style="margin-top: -1px;">SOONDOBU</h3>
        <?php } ?>

    </center>
    <p style="font-size: 20px;" align="center" style="margin-top: -10px;">Jl. M.T. Haryono No.16, Hotel Aria Barito
        Lantai 1</p>
    <p style="font-size: 20px;" align="center" style="margin-top: -10px;">081151-88779</p>
    <hr>
    <table width="100%">
        <tr>
            <td>
                #
                <?= substr($no, 5) ?><br><br>
                <?= Auth::user()->nama ?>
            </td>
            <td>

                <!-- Pax<br><br>
                <?= $pesan_2[0]->orang ?> -->
            </td>

            <td style="text-align: right;">
                <?= $pesan_2[0]->nm_meja ?>
            </td>

        </tr>
    </table>

    <hr>

    <table width="100%">
        <?php
        $s_total = 0;
        $qty = 0;
        $harga = 0;
        foreach ($order  as $d) :

       
            $s_total += $d->harga * $d->qty_produk;
            $qty = $d->qty_produk;
            $harga += $d->harga;
            $dis = $d->id_distribusi;
        ?>
        @if ($d->nm_menu == '')
        @else
            <tr>
                <td style="text-align: left;" width="6%">
                    <?= $d->qty_produk ?>
                </td>
                <td style="font-size: 20px;">
                    <?= ucwords(strtolower($d->nm_menu)) ?>
                </td>
                <td width="23%" style="font-size: 20px;">
                    <?= number_format($d->harga * $d->qty_produk) ?>
                </td>

                <td width="15%" align="right" style="white-space: nowrap;">
                    <?= $d->selisih . ' / ' . $d->selisih2 ?>
                </td>
            </tr>
        @endif
        <?php endforeach ?>

        <?php
        $s_total_majo = 0;
        foreach ($majo as $m):
        $s_total_majo += $m->harga * $m->jumlah;
        ?>
        <tr>
            <td style="text-align: left;" width="6%">
                <?= $m->jumlah ?>
            </td>
            <td style="font-size: 20px;">
                <?= ucwords(strtolower($m->nm_produk)) ?>
            </td>
            <td width="23%" style="font-size: 20px;">
                <?= number_format($m->harga * $m->jumlah) ?>
            </td>

            <td width="15%" align="right" style="white-space: nowrap;">

            </td>
        </tr>
        <?php endforeach ?>

        <?php $tb_dis = DB::table('tb_distribusi')
            ->where('id_distribusi', $dis)
            ->first(); ?>
    </table>
    <table width="100%">
        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px;"></td>
            <td style="font-weight: bold;" width="22%"></td>
            <td width="15%" align="right"></td>
        </tr>
        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px;">
                <span style="font-weight: bold;"> SUBTOTAL </span>
            </td>
            <td style="font-weight: bold; font-size: 20px; " width="8%">
                <?= number_format($s_total + $s_total_majo) ?>
            </td>

            <td width="15%" align="right">

            </td>
        </tr>

        <?php if ($transaksi->voucher) : ?>
        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px;">
                Voucher
            </td>
            <td width="22%" style="font-size: 20px;">
                <?= number_format($transaksi->voucher) ?>
            </td>

            <td width="15%" align="right">

            </td>
        </tr>
        <?php endif; ?>
        <?php if ($transaksi->diskon_bank) : ?>
        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px;">
                Promo Bank
            </td>
            <td width="22%" style="font-size: 20px;">
                <?= number_format($transaksi->diskon_bank) ?>
            </td>

            <td width="15%" align="right">

            </td>
        </tr>
        <?php endif; ?>
        @php
            $totO = $s_total + $s_total_majo - $transaksi->voucher - $transaksi->diskon_bank;
        @endphp
        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px;">
                <span style="font-weight: bold;">DISC VOUCHER </span>
            </td>
            <td width="22%" style="font-size: 20px;">
                <?php if ($totO < 0) : ?>
                <span style="font-weight: bold;">0</span>
                <?php else : ?>
                <span style="font-weight: bold;">
                    <?= number_format($totO) ?>
                </span>
                <?php endif ?>

            </td>

            <td width="15%" align="right">

            </td>
        </tr>

        <?php if ($tb_dis->service == 'Y') : ?>
        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px;">
                Service Charge
            </td>
            <td width="22%" style="font-size: 20px;">
                <?= number_format($transaksi->service, 0) ?>
            </td>

            <td width="15%" align="right">

            </td>
        </tr>
        <?php else : ?>
        <?php endif ?>
        <?php if ($tb_dis->ongkir == 'Y') : ?>
        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px;">
                Ongkir
            </td>
            <td width="22%" style="font-size: 20px;">
                <?= number_format($transaksi->ongkir, 0) ?>
            </td>

            <td width="15%" align="right">

            </td>
        </tr>
        <?php else : ?>
        <?php endif ?>

        <?php if ($tb_dis->tax == 'Y') : ?>
        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px;">
                TAX
            </td>
            <td width="22%" style="font-size: 20px;">
                <?= number_format($transaksi->tax) ?>
            </td>

            <td width="15%" align="right">

            </td>
        </tr>
        <?php endif; ?>
        @php
            // $totalan = $s_total + $s_total_majo - $transaksi->voucher + $transaksi->service + $transaksi->tax + $transaksi->ongkir;
            // $a = $totalan;
            // $b = number_format(substr($a, -3), 0);

            // if ($b == '00') {
            //     $c = $a;
            //     $round = '00';
            // } elseif ($b < 1000) {
            //     $c = $a - $b + 1000;
            //     $round = 1000 - $b;
            // }
            $c = $totO + $transaksi->service + $transaksi->tax + $transaksi->ongkir + $transaksi->round
        @endphp
      
        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px; font-weight: bold;">
                TOTAL
            </td>
            <td style="font-weight: bold; font-size: 20px;" width="22%">
                <?= number_format($c) ?>
            </td>
            <td width="15%" align="right">
            </td>
        </tr>
        <!-- <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px;">
                Discount
            </td>
            <td width="22%" style="font-size: 20px;">
                0
            </td>
            <td width="15%" align="right">
            </td>
        </tr> -->




        <?php if ($transaksi->discount) : ?>
        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px;">
                Discount
            </td>
            <td width="22%" style="font-size: 20px;">
                ({{ number_format($c * ($transaksi->discount / 100), 0) }})
                {{--
                <?= number_format($transaksi->discount) ?> % --}}
            </td>

            <td width="15%" align="right">

            </td>
        </tr>
        <?php endif; ?>
        <?php if ($transaksi->dp) : ?>
        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px;">
                DP
            </td>
            <td width="22%" style="font-size: 20px;">
                <?= number_format($transaksi->dp) ?>
            </td>

            <td width="15%" align="right">

            </td>
        </tr>
        <?php endif; ?>

        <?php if ($transaksi->gosen) : ?>
        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px;">
                Gosend
            </td>
            <td width="22%" style="font-size: 20px;">
                <?= number_format($transaksi->gosen) ?>
            </td>

            <td width="15%" align="right">

            </td>
        </tr>
        <?php endif; ?>

        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px; font-weight: bold;">
                TOTAL TAGIHAN
            </td>
            <td style="font-weight: bold; font-size: 20px;" width="22%">
                <?= number_format($transaksi->total_bayar <= 0 ? 0 : $transaksi->total_bayar, 0) ?>
            </td>

            <td width="15%" align="right">

            </td>
        </tr>
        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px;">
                {{-- <?php if (empty($transaksi->cash)) : ?>
                <?php else : ?>
                Cash <div style="margin-top: 5px;"></div>
                <?php endif ?>
                <?php if (empty($transaksi->d_bca)) : ?>
                <?php else : ?>
                Debit BCA <div style="margin-top: 5px;"></div>
                <?php endif ?>
                <?php if (empty($transaksi->k_bca)) : ?>
                <?php else : ?>
                Kredit BCA <div style="margin-top: 5px;"></div>
                <?php endif ?>
                <?php if (empty($transaksi->d_mandiri)) : ?>
                <?php else : ?>
                Debit MANDIRI <div style="margin-top: 5px;"></div>
                <?php endif ?>
                <?php if (empty($transaksi->k_mandiri)) : ?>
                <?php else : ?>
                Kredit MANDIRI <div style="margin-top: 5px;"></div>
                <?php endif ?> --}}

                @foreach ($pembayaran as $p)
                    {{ $p->nm_akun }} {{ $p->nm_klasifikasi }} <div style="margin-top: 5px;"></div> 
                @endforeach
            </td>
            <td width="22%" style="font-size: 20px;">
                {{-- <?php if (empty($transaksi->cash)) : ?>
                <?php else : ?>
                <?= number_format($transaksi->cash) ?>
                <div style="margin-top: 5px;"></div>
                <?php endif ?>
                <?php if (empty($transaksi->d_bca)) : ?>
                <?php else : ?>
                <?= number_format($transaksi->d_bca) ?>
                <div style="margin-top: 5px;"></div>
                <?php endif ?>
                <?php if (empty($transaksi->k_bca)) : ?>
                <?php else : ?>
                <?= number_format($transaksi->k_bca) ?>
                <div style="margin-top: 5px;"></div>
                <?php endif ?>
                <?php if (empty($transaksi->d_mandiri)) : ?>
                <?php else : ?>
                <?= number_format($transaksi->d_mandiri) ?>
                <div style="margin-top: 5px;"></div>
                <?php endif ?>
                <?php if (empty($transaksi->k_mandiri)) : ?>
                <?php else : ?>
                <?= number_format($transaksi->k_mandiri) ?>
                <div style="margin-top: 5px;"></div>
                <?php endif ?> --}}
                @php
                    $total_p = 0;
                @endphp
                @foreach ($pembayaran as $p)
                    {{ number_format($p->nominal, 0) }}<div style="margin-top: 5px;"></div>
                    @php
                        $total_p += $p->nominal;
                    @endphp
                @endforeach
            </td>
            <td width="15%" align="right">

            </td>
        </tr>
        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px;">
                @foreach ($pembayaran as $p)
                    {{ $p->pengirim }} <div style="margin-top: 5px;"></div> 
                @endforeach
            </td>
           
            <td width="22%" style="font-size: 20px;">
                
            </td>
            <td width="15%" align="right">

            </td>
            
        </tr>
        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px; font-weight: bold;">
                TOTAL BAYAR
            </td>
            <td style="font-weight: bold; font-size: 20px" width="22%">
                <?= number_format($total_p, 0) ?>
            </td>

            <td width="15%" align="right">

            </td>
        </tr>


        <tr>
            <td style="text-align: left;" width="6%"></td>
            <td style="font-size: 20px;">
                Change
            </td>
            <td width="22%">
                @if ($transaksi->kembalian > 0)
                    <?= number_format($transaksi->kembalian, 0) ?>
                @else
                    <?= number_format($total_p - $transaksi->total_bayar, 0) ?>
                @endif
            </td>

            <td width="15%" align="right" style="font-size: 20px;">

            </td>
        </tr>
    </table>
    <hr>
    <?php $Weddingdate = new DateTime($pesan_2[0]->j_mulai); ?>
    <p align="center">
        <?= $Weddingdate->format('h:i a') ?><br>
        Closed
        <?php
        date_default_timezone_set('Asia/Makassar');
        echo date('M j, Y h:i a'); ?>
    </p>
    <hr>
    <p align="center"> ** Thank you. See you next time! **</p>
</div>
<!-- ======================================================== conten ======================================================= -->

<!-- ======================================================== conten ======================================================= -->
<script>
    window.print();
</script>
