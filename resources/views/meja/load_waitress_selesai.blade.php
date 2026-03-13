<h5>{{ $meja }}</h5>
<table class="table table-bordered table-striped">
    <thead class="bg-info text-white">
        <tr>
            <th>Menu</th>
            <th>Request</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($menu2 as $m)
            @php
                if ($m->nm_menu == '') {
                    continue;
                }
                $isSelesai = $m->selesai == 'selesai';
                $statusText = $isSelesai ? 'Selesai' : 'Dimasak';
                $statusColor = $isSelesai ? 'success' : 'warning';
            @endphp
            <tr>
                <td style="text-transform: capitalize; font-weight: 600;">{{ $m->nm_menu }}</td>
                <td style="text-transform: lowercase; font-style: italic;">{{ $m->request }}</td>
                <td class="text-center">{{ $m->qty }}</td>
                <td class="text-center">
                    <span class="badge badge-{{ $statusColor }}">{{ $statusText }}</span>
                </td>
            </tr>
        @endforeach
        @foreach ($majo_hide as $m)
            <tr>
                <td style="text-transform: capitalize; font-weight: 600;">{{ $m->nm_produk }}</td>
                <td></td>
                <td class="text-center">{{ $m->jumlah }}</td>
                <td class="text-center">
                    <span class="badge badge-success">Selesai</span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
