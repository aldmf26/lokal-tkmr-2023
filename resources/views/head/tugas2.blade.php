@php
    $setMenit = DB::table('tb_menit')->where('id_lokasi', $lokasi)->first();
@endphp
@foreach ($menu as $m)
    @continue($m->nm_menu == '')
    <tr class="header">
        <td></td>
        <td style="white-space:nowrap;text-transform: lowercase;">
            {{ $m->nm_menu }}
            <span class="text-danger">({{ $m->ttlMenuSemua }})</span>
        </td>
        <td>{{ $m->request }}</td>
        <td>{{ $m->qty }}</td>
        @if ($m->selesai == 'dimasak')
        <td>
            <a kode="{{ $m->id_order }}" class="btn btn-info btn-sm selesai" id_meja="{{ $m->id_meja }}">
                <i class="fas fa-thumbs-up"></i>
            </a>
        </td>
         
            
            <td style="font-weight: bold;">
                {{ date('H:i', strtotime($m->j_mulai)) }}
            </td>
        @else
            <td style="text-decoration: line-through;">
                <a href="{{ url('orderan/order', $m->no_order) }}" style="color:black;">SELESAI</a>
            </td>
          
            <td>
                <b style="color: {{ date('H:i', strtotime($m->j_selesai)) < date('H:i', strtotime($m->j_mulai . '+' . $setMenit->menit . 'minutes')) ? 'blue' : 'red' }};">
                    {{ date('H:i', strtotime($m->j_selesai)) }}
                </b>
            </td>
        @endif
    </tr>
@endforeach
{{-- @foreach ($majo_hide as $m)
    <tr class="header">
        <td></td>
        <td style="white-space:nowrap;text-transform: lowercase;">
            {{ $m->nm_produk }}
        </td>
        <td></td>
        <td>{{ $m->jumlah }}</td>
       
    </tr>
@endforeach --}}
