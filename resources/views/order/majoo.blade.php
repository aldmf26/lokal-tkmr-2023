<div class="row">
    @foreach ($produk as $p)
    <div class="col-md-3">
        @if ($p->debit - ($p->kredit + $p->kredit_penjualan) < 1) <a class="input_cart2 stok_habis">
            <div class="card" style="background: rgba(0, 0, 0, 0.3);">
                <div style="background-color: rgba(0, 0, 0, 0.5); padding:5px 0 5px;">

                    <h6 style="font-weight: bold; color:#fff;" class="text-center">
                        {{ ucwords(Str::lower($p->nm_produk)) }}
                    </h6>
                </div>
                <div class="card-body" style="padding:0.2rem;">
                    <p class="mt-2 text-center demoname" style="font-size:15px; color: #787878;"><strong>Rp.
                            {{ number_format($p->harga) }}</strong></p>
                </div>
            </div>
            </a>
            @else
            <a href="" class="input_cart3" data-toggle="modal" data-target="#modal_majo"
                id_produk="{{ $p->id_produk }}">
                <div class="card">
                    <div style="background-color: rgba(0, 0, 0, 0.5); padding:5px 0 5px;">
                        <h6 style="font-weight: bold; color:#fff;" class="text-center">
                            {{ ucwords(Str::lower($p->nm_produk)) }}

                        </h6>
                    </div>
                    <div class="card-body" style="padding:0.2rem;">
                        <p class="mt-2 text-center demoname" style="font-size:15px; color: #787878;"><strong>Rp.
                                {{ number_format($p->harga) }}</strong></p>
                    </div>
                </div>
            </a>
            @endif

    </div>
    @endforeach
</div>
