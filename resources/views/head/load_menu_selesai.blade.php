@php
    $setMenit = DB::table('tb_menit')
        ->where('id_lokasi', $lokasi)
        ->first();
@endphp
@foreach ($menu2 as $m)
    @continue($m->nm_menu == '')
    <tr>
        <td></td>
        <td style="text-transform: lowercase;">
            {{ $m->nm_menu }} <span class="text-danger">({{ $m->ttlMenuSemua }})</span>
        </td>
        <td>{{ $m->request }}</td>
        <td>{{ $m->qty }}</td>
        <td>
            <a kode="{{ $m->id_order }}" class="btn btn-warning text-light btn-sm cancel" id_meja="{{ $m->id_meja }}">
                <i class="fas fa-times"></i>
            </a>
        </td>
       
        @php
            $mulai = new DateTime($m->j_mulai);
            $selesai = new DateTime($m->j_selesai);
            $menit = $selesai->diff($mulai);
        @endphp
        @if (date('H:i', strtotime($m->j_selesai)) < date('H:i', strtotime($m->j_mulai . '+' . $setMenit->menit . 'minutes')))
            <td><b style="color:blue;">{{ date('H:i', strtotime($m->j_selesai)) }} / {{ $menit->i }} Menit / {{ $menit->s }} Detik</b></td>
        @else
            <td><b style="color:red;">{{ date('H:i', strtotime($m->j_selesai)) }} / {{ $menit->i }} Menit / {{ $menit->s }} Detik</b></td>
        @endif
    </tr>
@endforeach
@foreach ($majo_hide as $m)
    <tr>
        <td></td>
        <td style="text-transform: lowercase;">
            {{ $m->nm_produk }} 
        </td>
        <td></td>
        <td>{{ $m->jumlah }}</td>
        <td colspan="2">
            {{-- <a no_order="{{ $m->no_nota }}" class="btn btn-warning text-light btn-sm cancelMajo">
                <i class="fas fa-times"></i>
            </a> --}}
        </td>
    
    </tr>
@endforeach

