<div class="row">
    @foreach ($menu2 as $t)
        <div class="col-md-3 mb-3">
            <div class="card card-order h-100" style="cursor: pointer;" data-id_harga="{{ $t->id_harga }}"
                data-id_menu="{{ $t->id_menu }}" data-nm_menu="{{ $t->nm_menu }}" data-harga="{{ $t->harga }}"
                data-tipe="{{ $t->tipe }}">

                <div class="card-header p-0" style="background-color: rgba(0, 0, 0, 0.5); position: relative;">
                    <h6 style="font-weight: bold; color:#fff; padding: 10px 5px;" class="text-center m-0">
                        {{ ucwords(Str::lower($t->nm_menu)) }}
                    </h6>
                </div>

                <div class="card-body d-flex flex-column justify-content-center align-items-center" style="padding:0.5rem;">
                    <p class="m-0 text-center demoname" style="font-size:16px; color: #787878;">
                        <strong>Rp. {{ number_format($t->harga) }}</strong>
                    </p>
                </div>

                <div class="card-footer p-1 bg-transparent border-0 text-right">
                    <a href="javascript:void(0)" class="input_cart2 btn-edit-order" data-toggle="modal"
                        data-target="#myModal" id_harga="{{ $t->id_harga }}" id_dis="{{ $id_dis }}">
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="col-lg-12">
    <center>

        {{ $menu2->links() }}
    </center>
</div>