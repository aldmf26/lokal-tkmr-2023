<!-- ======================================================== conten ======================================================= -->
<style>
    #background {
        position: absolute;
        z-index: 0;
        background: white;
        display: block;
        min-height: 50%;
        min-width: 50%;
        color: yellow;
    }

    #content {
        position: absolute;
        z-index: 1;
    }

    #bg-text {
        color: lightgrey;
        font-size: 120px;
        transform: rotate(300deg);
        -webkit-transform: rotate(300deg);
    }
</style>
<div id="background">
    <p id="bg-text">Copy</p>
</div>

<div id="content">
<?php if (empty($pesan_2)) : ?>
<?php else : ?>
    <div style="font-size: 14px;" id="content">
        <table align="center" class="table" style="font-size: 14px;">
            <tbody>
                <tr>
                    <td>
                        invoice #
                        <?= $no_order; ?><br>
                        Server :
                        {{Session::get('id_lokasi') == 1 ? 'TAKEMORI' : 'SOONDOBU'}}
                    </td>
                    <td>
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
        </table>
        <hr>
        <table class="table" align="center" style="font-size: 14px;">
            <thead style="font-family: Footlight MT Light;">
                <tr>
                    <th colspan="3" style="text-align: left">FOOD</th>
                </tr>
                <tr>

                    <th>QTY :
                        {{$pesan_2->sum_qty}}
                    </th>
                    <th>NAMA MENU :
                        {{$pesan_2->sum_qty}}
                    </th>
                    <th>Time: </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order  as $d) : ?>
                    <tr>
                        <td align="center">
                            {{$d->qty}}
                        </td>
                        <td>
                            {{$d->nm_menu}} <br> ***
                            {{$d->request}}
                        </td>
                        <td>
                            {{date('h:i a', strtotime($d->j_mulai))}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>

        <input type="hidden" id="kode" value="{{ $no_order }}">

    </div>



<?php endif ?>
<?php if (empty($pesan_3)) : ?>
<?php else : ?>
    <hr>
        <table align="center" class="table" style="font-size: 14px;">
            <tbody>
                <tr>
                    <td>
                        invoice #
                        <?= $no_order; ?><br>
                        Server :
                        {{Session::get('id_lokasi') == 1 ? 'TAKEMORI' : 'SOONDOBU'}}
                    </td>
                    <td>
                        <?php
                        $Weddingdate = new DateTime($pesan_3->j_mulai);
                        echo $Weddingdate->format("M j, h:i:s a");
                        ?>
                        <br>
                    </td>
                    
                </tr>
                <tr>
                <td colspan="2" align="center" style="font-size: 16px; font-weight: bold">
                    {{$pesan_3->nm_meja }} {{$pesan_3->warna}}
                </td>
                </tr>
            </tbody>
        </table>
       
        <hr>
        <table class="table" align="center" style="font-size: 14px;">
            <thead style="font-family: Footlight MT Light;">
                <tr>
                    <th colspan="3" style="text-align: left">DRINK</th>
                </tr>
                <tr>
                    <th>QTY :
                        {{$pesan_3->sum_qty}}
                    </th>
                    <th>NAMA MENU :
                        {{$pesan_3->sum_qty}}
                    </th>
                    <th>Time: </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order2  as $d) : ?>
                    <tr>
                        <td align="center">
                            {{$d->qty}}
                        </td>
                        <td>
                            {{$d->nm_menu}} <br> ***
                            {{$d->request}}
                        </td>
                        <td>
                            {{date('h:i a', strtotime($d->j_mulai))}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>

        <input type="hidden" id="kode" value="{{ $no_order }}">

    
<?php endif ?>

<?php if (empty($pesan_4)) : ?>
<?php else : ?>
    
    <table align="center" class="table" style="font-size: 14px;">
            <tbody>
                <tr>
                    <td>
                        invoice #
                        <?= $no_order; ?><br>
                        Server :
                        {{Session::get('id_lokasi') == 1 ? 'TAKEMORI' : 'SOONDOBU'}}
                    </td>
                    <td>
                        <?php
                        $Weddingdate = new DateTime($pesan_4->j_mulai);
                        echo $Weddingdate->format("M j, h:i:s a");
                        ?>
                        <br>
                    </td>
                    
                </tr>
                <tr>
                <td colspan="2" align="center" style="font-size: 16px; font-weight: bold">
                    {{$pesan_4->nm_meja }} {{$pesan_4->warna}}
                </td>
                </tr>
            </tbody>
        </table>
        
        <hr>
        <table class="table" align="center" style="font-size: 14px;">
            <thead style="font-family: Footlight MT Light;">
                <tr>
                    <th colspan="3" style="text-align: left">SUSHI</th>
                </tr>
                <tr>
                    <th>QTY :
                        {{$pesan_4->sum_qty}}
                    </th>
                    <th>NAMA MENU :
                        {{$pesan_4->sum_qty}}
                    </th>
                    <th>Time: </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order3  as $d) : ?>
                    <tr>
                        <td align="center">
                            {{$d->qty}}
                        </td>
                        <td>
                            {{$d->nm_menu}} <br> ***
                            {{$d->request}}
                        </td>
                        <td>
                            {{date('h:i a', strtotime($d->j_mulai))}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>

        <input type="hidden" id="kode" value="{{ $no_order }}">

    
<?php endif ?>
<?php if (empty($pesan_5)) : ?>
<?php else : ?>
    <table align="center" class="table" style="font-size: 14px;">
            <tbody>
                <tr>
                    <td>
                        invoice #
                        <?= $no_order; ?><br>
                        Server :
                        {{Session::get('id_lokasi') == 1 ? 'TAKEMORI' : 'SOONDOBU'}}
                    </td>
                    <td>
                        <?php
                        $Weddingdate = new DateTime($pesan_5->j_mulai);
                        echo $Weddingdate->format("M j, h:i:s a");
                        ?>
                        <br>
                    </td>
                
                </tr>
                <tr>
                <td colspan="2" align="center" style="font-size: 16px; font-weight: bold">
                    {{$pesan_5->nm_meja }} {{$pesan_5->warna}}
                </td>
                </tr>
            </tbody>
        </table>
    
        <hr>
        <table class="table" align="center" style="font-size: 14px;">
            <thead style="font-family: Footlight MT Light;">
                <tr>
                    <th colspan="3" style="text-align: left">Shabu-shabu dan Sukiyaki</th>
                </tr>
                <tr>
                    <th>QTY :
                        {{$pesan_5->sum_qty}}
                    </th>
                    <th>NAMA MENU :
                        {{$pesan_5->sum_qty}}
                    </th>
                    <th>Time: </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order4  as $d) : ?>
                    <tr>
                        <td align="center">
                            {{$d->qty}}
                        </td>
                        <td>
                            {{$d->nm_menu}} <br> ***
                            {{$d->request}}
                        </td>
                        <td>
                            {{date('h:i a', strtotime($d->j_mulai))}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>

        <input type="hidden" id="kode" value="{{ $no_order }}">

   
<?php endif ?>
<?php if (empty($majo)) : ?>
<?php else : ?>
<div style="font-size: 14px;page-break-before: always">
    <hr>
    <table align="center" class="table" style="font-size: 14px;">
        <tbody>
            <tr>
                <td>
                    invoice #
                    <?= $no_order; ?><br>
                    Server :
                    {{Session::get('id_lokasi') == 1 ? 'TAKEMORI' : 'SOONDOBU'}}
                </td>
                <td>
                    {{--
                    <?php
                        $Weddingdate = new DateTime($pesan_3->j_mulai);
                        echo $Weddingdate->format("M j, h:i:s a");
                        ?>
                    <br> --}}
                </td>

            </tr>
            <tr>
            <tr>
                <td colspan="2" align="center" style="font-size: 16px; font-weight: bold">
                    {{$meja->nm_meja }} {{$meja->warna}}
                </td>
            </tr>
            </tr>
        </tbody>
    </table>
    <hr>
    <table class="table" align="center" style="font-size: 14px;">
        <thead style="font-family: Footlight MT Light;">
            <tr>
                <th colspan="3" style="text-align: left">STK</th>
            </tr>
            <tr>
                <th>QTY :
                    {{$majo_ttl->sum_qty}}
                </th>
                <th>NAMA MENU :
                    {{$majo_ttl->sum_qty}}
                </th>
                <th>Time: </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($majo  as $m) : ?>
            <tr>
                <td align="center">
                    {{$m->jumlah}}
                </td>
                <td>
                    {{$m->nm_produk}} <br> ***
                </td>
                <td>

                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
            </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>

    <input type="hidden" id="kode" value="{{ $no_order }}">

</div>
<?php endif ?>
</div>
<!-- ======================================================== conten ======================================================= -->
<script>
    window.print();
</script>