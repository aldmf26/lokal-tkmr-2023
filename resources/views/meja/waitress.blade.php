<table class="table" width="100%">
    <thead>
        <tr class="header">
            <th>Meja</th>
            <th>Menu</th>
            <th>Request</th>
            <th>Qty</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($meja as $m)
            @php
                $order = DB::table('tb_order2')->where('no_order', $m->no_order)->groupBy('no_order2')->get();
            @endphp

            <tr class="header">
                <td class="bg-info" width="8%" style="white-space: nowrap;">
                    Meja <?= $m->nm_meja ?>
                </td>
                <td class="bg-info" style="vertical-align: middle;">

                    <a class="muncul muncul{{ $m->id_meja }}   btn btn-primary btn-sm" data-toggle="modal"
                        id_meja="{{ $m->id_meja }}" no_order="{{ $m->no_order }}" href="#view_menu">View</a>

                    {{-- <a class="hilang hilang{{ $m->id_meja }}  btn btn-primary btn-sm" id_meja="{{ $m->id_meja }}"
                        style="display:none">View</a> --}}


                    <div class="dropdown d-inline-block mb-2">
                        <a no_meja="{{ $m->nm_meja }}" class="btn btn-success btn-sm dropdown-toggle plusPesanan"
                            type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-plus"></i> Pesanan
                        </a>
                        <div class="dropdown-menu" aria-labelledby="invoice">
                            <a data-toggle="modal" class="btn_tbh dropdown-item" no_order="<?= $m->no_order ?>"
                                href="#tbh_menu">Resto</a>
                            <a data-toggle="modal" class="btn_tbh_majo dropdown-item" no_order="<?= $m->no_order ?>"
                                href="#tbh_menu_majo">Stk</a>
                        </div>
                    </div>
                    <a target="_blank" href="{{ route('billing', ['no' => $m->no_order]) }}"
                        class="btn btn-success btn-sm "><i class="fas fa-print"></i> Bill</a>

                    @if ($m->prn == 'T')
                        <a href="{{ route('checker', ['no' => $m->no_order]) }}" class="btn btn-success btn-sm "><i
                                class="fas fa-print"></i> Checker</a>
                    @else
                        <a href="{{ route('copy_checker', ['no' => $m->no_order]) }}" class="btn btn-success btn-sm "><i
                                class="fas fa-print"></i> Copy Checker</a>
                    @endif

                    @if ($m->t_prn == 'T')
                        <a href="{{ route('checker_tamu', ['no' => $m->no_order]) }}" class="btn btn-success btn-sm "><i
                                class="fas fa-print"></i> Checker tamu</a>
                    @else
                        <a href="{{ route('copy_checker_tamu', ['no' => $m->no_order]) }}"
                            class="btn btn-success btn-sm "><i class="fas fa-print"></i> Copy Checker tamu</a>
                    @endif


                    <a href="{{ route('all_checker', ['no' => $m->no_order]) }}" class="btn btn-success btn-sm "><i
                            class="fas fa-print"></i> Print all</a>

                    @if ($m->qty1 - $m->qty2 != '0')
                        @if ($m->selesai != 'selesai')
                        @else
                            @foreach ($order as $l => $a)
                                <div class="dropdown d-inline-block ">
                                    <button class="btn btn-success btn-sm dropdown-toggle" type="button"
                                        id="invoice{{ $a->no_order2 }}" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class="fas fa-file-invoice"></i> invoice {{ $l + 1 }}
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="invoice{{ $a->no_order2 }}">

                                        <a target="_blank" class="dropdown-item"
                                            href="{{ route('print_nota', ['no' => $a->no_order2]) }}">Print</a>
                                        <a data-toggle="modal" href="#edit_pembayaran" no_order="{{ $a->no_order2 }}  "
                                            class="dropdown-item btn_edit_pembayaran">Edit</a>
                                    </div>
                                </div>
                            @endforeach
                            <a href="javascript:void(0)" class="btn btn-success btn-sm btn_pembayaran "
                                no_order="{{ $m->no_order }}"><i class="fas fa-funnel-dollar"></i> Pembayaran</a>
                        @endif
                    @else
                        <?php $i = 1;foreach ($order as $a) : ?>

                        <div class="dropdown d-inline-block ">
                            <button class="btn btn-success btn-sm dropdown-toggle" type="button"
                                id="invoice{{ $a->no_order2 }}" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="fas fa-file-invoice"></i> invoice {{ $i++ }}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="invoice{{ $a->no_order2 }}">
                                <a target="_blank" class="dropdown-item"
                                    href="{{ route('print_nota', ['no' => $a->no_order2]) }}">Print</a>
                                <a data-toggle="modal" href="#edit_pembayaran" no_order="{{ $a->no_order2 }}"
                                    class="dropdown-item btn_edit_pembayaran">Edit</a>
                            </div>
                        </div>
                        <?php endforeach ?>
                        <a class="btn btn-success btn-sm clear" kode="{{ $m->no_order }}"><i
                                class="fas fa-hand-sparkles"></i>
                            Clear up</a>
                    @endif

                </td>
                <td class="bg-info"></td>
                <td class="bg-info"></td>
                <td class="bg-info"></td>


            </tr>
            @php
                $menu = DB::table('menu1')->where('id_lokasi', $loc)->where('id_meja', $m->id_meja)->get();

                $majo = DB::select("SELECT a.*, c.nm_produk
                        FROM tb_pembelian AS a
                        LEFT JOIN tb_produk AS c ON c.id_produk = a.id_produk
                        WHERE a.no_nota = '$m->no_order' AND a.lokasi = '$loc' and a.selesai = 'diantar'
                        GROUP BY a.id_pembelian");

            @endphp
            {{-- <tr>
    <tbody class="load_menu_s{{ $m->id_meja }}"></tbody>
    </tr> --}}
            @foreach ($menu as $m)
                @if ($m->nm_menu != '')
                    <tr class="header">
                        <td></td>
                        <td style="white-space:nowrap;text-transform: lowercase;">{{ $m->nm_menu }}</td>
                        <td style="white-space:nowrap;text-transform: lowercase;">{{ $m->request }}</td>
                        <td> {{ $m->qty }}</td>
                        <td>
                            {{ $m->selesai }}
                        </td>
                    </tr>
                @else
                @endif
            @endforeach

            @foreach ($majo as $m)
                @if ($m->nm_produk == '')
                @else
                    <tr class="header">
                        <td></td>
                        <td style="white-space:nowrap;text-transform: lowercase;">{{ $m->nm_produk }}</td>
                        <td></td>
                        <td> {{ $m->jumlah }}</td>
                        <td>{{ $m->selesai == 'diantar' ? 'dimasak' : 'selesai' }}</td>
                    </tr>
                @endif
            @endforeach
        @endforeach

    </tbody>
</table>
