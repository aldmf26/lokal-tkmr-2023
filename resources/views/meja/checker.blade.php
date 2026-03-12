<!-- ======================================================== conten ======================================================= -->
@php
    $station = DB::table('tb_station')->where('id_lokasi', Session::get('id_lokasi'))->get();
    
@endphp
@foreach ($station as $s)                                                                                                                      
@php
    $order =  DB::select(
            DB::raw("SELECT a.id_order, b.tipe, b.nm_menu, SUM(a.qty) as qty, a.request, c.nama AS koki1 , d.nama AS koki2, e.nama AS koki3, 
            a.pengantar, a.id_meja, a.j_mulai, a.j_selesai, a.wait, a.selesai,
            timestampdiff(MINUTE, a.j_mulai,a.wait) AS selisih, a.no_checker , a.print, a.copy_print
            FROM tb_order as a 
            left join view_menu as b on a.id_harga = b.id_harga 
            left join tb_karyawan as c on c.id_karyawan = a.id_koki1
            left join tb_karyawan as d on d.id_karyawan = a.id_koki2
            left join tb_karyawan as e ON e.id_karyawan = a.id_koki3
            where a.aktif = '1' and a.print = 'T' and a.no_order = '$no_order' and b.id_station = '$s->id_station'
            GROUP BY a.id_order
            ORDER BY a.id_order DESC")
        );
      
    $pesan_2 =   DB::table('tb_order as a')
                ->select(DB::raw('a.*, sum(a.qty) as sum_qty ,  a.no_meja as nm_meja'))
                ->leftJoin('tb_meja as b', 'b.id_meja', '=', 'a.id_meja')
                ->leftJoin('view_menu as c', 'c.id_harga', '=', 'a.id_harga')
                ->where('a.no_order', $no_order)
                ->where('c.id_station' ,$s->id_station)
                ->where('a.no_checker', 'T')
                ->groupBy('a.no_order')
                ->first();
                
@endphp
@if (empty($pesan_2))
@else
<div style="font-size: 14px; page-break-before:always">
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
                
            </tr>
            {{-- disini looping --}}
            <tr>
            <td colspan="2" align="center" style="font-size: 16px; font-weight: bold">
                {{$pesan_2->nm_meja }} {{$pesan_2->warna}}
            </td>
            </tr>
        </tbody>
    </table>
    <hr>
    <table class="table" align="center" style="font-size: 14px;">
        <thead style="font-family: Footlight MT Light;">
            <tr>
                <th colspan="3" style="text-align: left">{{ $s->nm_station }}</th>
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
                        {{$d->nm_menu}} <br> *
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
@endif
@endforeach
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

@php
    $data1 = [
            'no_checker' => 'Y',
            'print' => 'Y'
        ];
    DB::table('tb_order')->where('no_order', $no_order)->update($data1);
    DB::table('tb_pembelian')->where('no_nota', $no_order)->update($data1);
@endphp



<!-- ======================================================== conten ======================================================= -->
<script>
    window.print();
</script>
