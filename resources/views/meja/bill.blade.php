<!-- ======================================================== conten ======================================================= -->
<style>
    .invoice {
        margin: auto;
        width: 87mm;
        background: #FFF;
    }
</style>
<div style="font-size: 14px;" class="invoice">
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
                <?= substr($no_order, 5) ?><br><br>
                <?= Auth::user()->nama ?>
            </td>
            <td>
                {{-- Pax<br><br>
                {{$pesan_2->orang }} --}}
            </td>

            <td>
                {{$pesan_2->nm_meja }}
            </td>

        </tr>
    </table>
    <!-- <table class="table" align="center" style="font-size: 14px;" width="100%">
        <tbody>
            <tr>
                <td colspan="3">
                    <b>#
                        {{substr($no_order, 5)}}
                    </b>
                </td>
            </tr>
            <tr>
                <td>
                    <?= Auth::user()->nama ?>
                </td>
                <td style="white-space: nowrap;text-align: center;" width="20%">
                    <?php
                    $Weddingdate = new DateTime($pesan_2->j_mulai);
                    echo $Weddingdate->format("M j, h:i:s a");
                    ?>
                    <br>
                </td>
                <td>
                    {{$pesan_2->nm_meja }}
                </td>
            </tr>
        </tbody>
    </table> -->
    <hr>

    <table width="100%">
        <tbody>
            <?php $bayar = 0;
            foreach ($order  as $d) :
                $bayar += $d->harga * $d->qty;
                $dis = $d->id_distribusi
            ?>
            @if ($d->nm_menu == '')

            @else
            <tr>
                <td style="text-align: left;" width="6%">
                    <?= $d->qty ?>
                </td>
                <td style="font-size: 20px;">
                    <?= ucwords(strtolower($d->nm_menu)) ?>
                </td>
                <td width="23%" style="font-size: 20px;">
                    <?= number_format($d->harga * $d->qty) ?>
                </td>
            </tr>
            @endif

            <?php $ongkir = $d->ongkir ?>
            <?php endforeach ?>

            @php
            $t_majo = 0;
            @endphp
            @foreach ($majo as $m)
            @php
            $t_majo += $m->harga * $m->jumlah;
            @endphp
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
            </tr>
            @endforeach

        </tbody>
        @php
        $tb_dis = DB::table('tb_distribusi')
        ->where('id_distribusi', $dis)
        ->first();
        @endphp
        <?php if ($tb_dis->ongkir == 'Y') : ?>

        <?php if ($bayar < $batas->rupiah) : ?>
        <?php $ongkir = $ongkir2->rupiah ?>
        <?php else : ?>
        <?php $ongkir = 0 ?>
        <?php endif ?>
        <?php else : ?>
        <?php $ongkir = 0 ?>
        <?php endif ?>

        <?php if ($tb_dis->service == 'Y') : ?>
        <?php $service = $bayar * 0.07;  ?>

        <?php else : ?>
        <?php $service = 0  ?>
        <?php endif ?>

        <?php if ($tb_dis->tax == 'Y') {
            $tax = ($bayar + $service + $ongkir + $t_majo) * 0.1;
        } else {
            $tax = 0;
        } ?>


        <?php $total = $bayar + $t_majo + $service + $tax + $ongkir; ?>

        <tfoot>
            <tr>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td style="text-align: left;" width="6%"></td>
                <td style="font-weight: bold; font-size: 20px; ">SUBTOTAL</td>
                <td style="font-weight: bold; font-size: 20px;">
                    {{number_format($bayar + $t_majo )}}
                </td>
            </tr>
            <?php if ($tb_dis->ongkir == 'Y') : ?>
            <tr>
                <td style="text-align: left;" width="6%"></td>
                <td style="font-weight: bold; font-size: 20px; ">ONGKIR</td>
                <td style="font-weight: bold; font-size: 20px; ">
                    {{number_format($ongkir, 0) }}
                </td>
            </tr>
            <?php else : ?>
            <?php endif ?>

            <?php if ($tb_dis->service == 'Y') : ?>
            <tr>
                <td style="text-align: left;" width="6%"></td>
                <td style=" font-size: 20px; ">Service Charge</td>
                <td style=" font-size: 20px; ">
                    {{number_format($service)}}
                </td>
            </tr>
            <?php else : ?>
            <?php endif ?>

            <?php if ($tb_dis->tax == 'Y') : ?>
            <tr>
                <td> </td>
                <td style=" font-size: 20px; ">Tax</td>
                <td style=" font-size: 20px; ">
                    {{number_format($tax )}}
                </td>
            </tr>
            <?php else : ?>
            <?php endif ?>


            <tr style="font-weight: bold; font-size: 20px;">
                <?php
                $a = round($total);
                $b = number_format(substr($a, -3), 0);

                if ($b == '000') {
                    $c = $a;
                    $round = '000';
                } elseif ($b < 1000) {
                    $c = $a - $b + 1000;
                    $round = 1000 - $b;
                }
                ?>
                <td style="text-align: left;" width="6%"></td>
                <td>TOTAL</td>
                <td>
                    {{number_format($c , 0)}}
                </td>
            </tr>
            @if (empty($disc->jenis))
                
            @else
            <tr>
                <td style="text-align: left;" width="6%"></td>
                <td>Discount</td>
                <td>
                    @if ($c < $disc->minimum_rp)
                        0
                    @else
                    ({{ $disc->jenis == 'Persen' ? $disc->disc . '%' : 'Rp.' . $disc->disc }})
                    @endif
                    

                </td>
            </tr>
            <tr style="font-weight: bold; font-size: 20px;">
                <?php
                if ($c < $disc->minimum_rp) {
                    $gran_total = $c;
                    }else {
                        if ($disc->jenis == 'Persen') {
                            $gran_total = $c * ((100 - $disc->disc) / 100);
                        } else {
                            $gran_total = $c * $disc->disc;
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
                <td style="text-align: left;" width="6%"></td>
                <td>GRAND TOTAL</td>
                <td>
                    {{number_format($f , 0)}}
                </td>
            </tr>
            @endif
            
            
        </tfoot>
    </table>

    <hr>
    <input type="hidden" id="kode" value="{{$no_order }}">
    <?php $Weddingdate = new DateTime($pesan_2->j_mulai); ?>
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
<script>
    window.print();
</script>