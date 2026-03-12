<h5>{{ $meja }}</h5>
<table class="table table-bordered">
    <thead>
        <tr>

            <th>Menu</th>
            <th>Request</th>
            <th>Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($menu2 as $m)
            @php
                if ($m->nm_menu == '') {
                    continue;
                }
            @endphp
            <tr>

                <td style="text-transform: lowercase;">{{ $m->nm_menu }}</td>
                <td style="text-transform: lowercase;">{{ $m->request }}</td>
                <td>{{ $m->qty }}</td>
            </tr>
        @endforeach
        @foreach ($majo_hide as $m)
            <tr>

                <td style="text-transform: lowercase;">{{ $m->nm_produk }}</td>
                <td></td>
                <td>{{ $m->jumlah }}</td>
            </tr>
        @endforeach
    </tbody>

</table>
