<ul class="nav nav-pills mb-3 custom-scrollbar-css" id="pills-tab" role="tablist">
    @foreach ($distribusi as $d)
        <li class="nav-item">
            <a href="{{ route('head', ['id' => $d->id_distribusi]) }}"
               class="nav-link {{ $id == $d->id_distribusi ? 'active' : '' }} {{ !empty($d->jumlah) ? 'badge-notif' : '' }}"
               data-badge="{{ $d->jumlah ?? '' }}">
                <strong>{{ $d->nm_distribusi }}</strong>
            </a>
        </li>
    @endforeach
</ul>

<input type="hidden" id="jumlah1" value="{{ $orderan[0]->jml_order ?? '' }}">

