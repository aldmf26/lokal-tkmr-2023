a<table class="table" width="100%">
    <thead>
        <tr class="header">
            <th class="sticky-top th-atas">Meja</th>
            <th class="sticky-top th-atas">Menu</th>
            <th class="sticky-top th-atas">Request</th>
            <th class="sticky-top th-atas">Qty</th>
            <th class="sticky-top th-atas">Status</th>

            <th class="sticky-top th-atas">Time In / Durasi</th>
        </tr>
    </thead>
    <tbody style="font-size: 18px;" id="tugas_head">
        @foreach ($meja as $m)
            <tr class="header">
                <td class="bg-info">
                    Meja {{ $m->nm_meja }}
                </td>
                <td class="bg-info" style="vertical-align: middle;">
                    <a class="muncul muncul{{ $m->id_meja }} btn btn-primary btn-sm" id_meja="{{ $m->id_meja }}"
                        no_order="{{ $m->no_order }}">View</a>
                    <a class="hilang hilang{{ $m->id_meja }} btn btn-primary btn-sm" id_meja="{{ $m->id_meja }}"
                        style="display:none">View</a>
                </td>
                <td class="bg-info"></td>
                <td class="bg-info"></td>
                <td class="bg-info"></td>

                <td colspan="50" class="bg-info"></td>
            </tr>

            @php
                $menus = DB::select("SELECT b.nm_menu, c.nm_meja, c.id_meja,a.request,a.qty,a.selesai,a.id_order,a.j_mulai,f.ttlMenuSemua FROM tb_order AS a LEFT JOIN view_menu AS b ON b.id_harga = a.id_harga
                    LEFT JOIN (SELECT d.id_harga, COUNT(id_harga) as ttlMenuSemua FROM `tb_order` as d where d.id_lokasi = '$lokasi' and d.selesai = 'dimasak' and aktif = '1' and void = 0 GROUP BY d.id_harga) as f on b.id_harga = f.id_harga
                    LEFT JOIN tb_meja AS c ON c.id_meja = a.id_meja where a.id_lokasi = '$lokasi' and a.id_meja = '$m->id_meja' and a.selesai = 'dimasak' and aktif = '1' and void = 0 ORDER BY a.id_order");

                $majos = DB::select("SELECT a.jumlah, a.id_pembelian, c.nm_produk
                        FROM tb_pembelian AS a
                        LEFT JOIN tb_produk AS c ON c.id_produk = a.id_produk
                        WHERE a.no_nota = '$m->no_order' AND a.lokasi = '$lokasi' and a.selesai = 'diantar'
                        GROUP BY a.id_pembelian");
            @endphp
            <tr class="header">
                <tbody class="load_menu_s{{ $m->id_meja }}"></tbody>
            </tr>

            <tr class="header">
                <tbody class="addmeja{{ $m->id_meja }}"></tbody>
            </tr>
    @foreach ($menus as $menu)
        @if ($menu->nm_menu != '')
            <tr class="header meja{{ $menu->id_meja }}">
                <td></td>
                <td style="white-space:nowrap;text-transform: lowercase;">
                    {{ $menu->nm_menu }} <span class="text-danger">({{ $menu->ttlMenuSemua }})</span>
                </td>
                <td>
                    {{ $menu->request }}
                </td>
                <td>
                    {{ $menu->qty }}
                </td>
                @if ($menu->selesai == 'dimasak')
                    <td>
                        <a kode="{{ $menu->id_order }}" class="btn btn-info btn-sm selesai"
                            id_meja="{{ $menu->id_meja }}"><i class="fas fa-thumbs-up"></i></a>
                    </td>

                    <td style="font-weight: bold;">
                        {{ date('H:i', strtotime($menu->j_mulai)) }}
                    </td>
                @else
                    <td style="text-decoration: line-through;"><a href="{{ url('orderan/order', $menu->no_order) }}"
                            style="color:black;">SELESAI</a></td>

                    <td><b
                            style="color: {{ date('H:i', strtotime($menu->j_selesai)) < date('H:i', strtotime($menu->j_mulai . '+' . $setMenit->menit . 'minutes')) ? 'blue' : 'red' }};">
                            {{ date('H:i', strtotime($menu->j_selesai)) }}
                        </b></td>
                @endif
            </tr>
        @endif
    @endforeach
    @foreach ($majos as $j)
        @if ($j->nm_produk != '')
            <tr class="header meja">
                <td></td>
                <td>
                    {{ $j->nm_produk }}
                </td>
                <td></td>
                <td>
                    {{ $j->jumlah }}
                </td>
                <td>
                    <a kode="{{ $j->id_pembelian }}" no_order="{{ $m->no_order }}"
                        class="btn btn-info btn-sm selesai_majo"><i class="fas fa-thumbs-up"></i></a>
                </td>
            </tr>
        @endif
    @endforeach
    @endforeach
    </tbody>
</table>


<script src="{{ asset_custom('plugins/jquery/jquery.min.js') }}"></script>

<script>
    $(document).ready(function() {
        var ua = navigator.userAgent,
            event = (ua.match(/iPad/i)) ? "touchstart" : "click";
        if ($('.table').length > 0) {
            $('.table .header').on(event, function() {
                $(this).toggleClass("active", "").nextUntil('.header').css('display', function(i, v) {
                    return this.style.display === 'table-row' ? 'none' : 'table-row';
                });
            });
        }
    })
</script>
